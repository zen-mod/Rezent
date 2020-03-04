<?php

namespace App\Drivers;

use App\Colors;
use App\Driver;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ActionsDriver extends Driver
{
    public $embed;

    protected const VALIDATION_RULES = [
        'organisation' => 'required|string',
        'repository' => 'required|string',
        'workflow_id' => 'required|integer',
    ];

    public function __construct(array $validated)
    {
        $this->embed = $this->create($validated);
    }

    protected function create(array $validated): array
    {
        $payload = $this->getGitHubPayload($validated);
        $commit = $this->getBuildCommitDetails($payload);

        return [
            'author' => [
                'icon_url' => $commit->author->avatar_url,
                'name' => $payload->head_commit->author->name,
                'url' => $commit->author->html_url,
            ],
            'color' => $this->getBuildColor($payload),
            'description' => $this->getBuildDescription($payload, $commit),
            'timestamp' => Carbon::now(),
            'title' => $this->getBuildTitle($payload),
            'url' => $payload->html_url,
        ];
    }

    protected function getGitHubUri(array $validated): string
    {
        return 'https://api.github.com/repos/'
            . $validated['organisation'] . '/'
            . $validated['repository'] . '/'
            . 'actions/workflows/'
            . $validated['workflow_id'] . '/'
            . 'runs?per_page=1';
    }

    protected function getGitHubPayload(array $validated): object
    {
        $response = Http::get(
            $this->getGitHubUri($validated)
        )->throw();

        return json_decode($response->body())
            ->workflow_runs[0];
    }

    protected function getBuildColor(object $payload): Colors
    {
        switch ($payload->conclusion) {
            case 'success':
                return Colors::PASSED();

            case 'failure':
                return Colors::FAILED();

            default:
                Colors::CANCELED();
        }
    }

    protected function getBuildDescription(object $payload, object $commit): string
    {
        $gitHash = substr($payload->head_sha, 0, 7);
        $commitUrl = $commit->html_url;
        $commitName = $payload->head_commit->message;

        return "[{$gitHash}]({$commitUrl}) {$commitName}";
    }

    protected function getBuildTitle(object $payload): string
    {
        $repoInfo = "[{$payload->repository->full_name}]:{$payload->head_branch}";
        $number = $payload->run_number;
        $status = Str::ucfirst($payload->conclusion);

        return "{$repoInfo} Build #{$number} {$status}";
    }

    protected function getBuildCommitDetails(object $payload): object
    {
        $response = Http::get(
            'https://api.github.com/repos/'
                . $payload->repository->full_name
                . '/commits/'
                . $payload->head_sha
        )->throw();

        return json_decode($response->body());
    }

    public function wasSuccessful(): bool
    {
        return $this->embed->color == Colors::PASSED();
    }

    public function wasAlreadySent(): bool
    {
        return false;
    }
}
