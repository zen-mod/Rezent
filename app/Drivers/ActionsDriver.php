<?php

namespace App\Drivers;

use App\Driver;
use Carbon\Carbon;
use App\Colors;
use Illuminate\Support\Facades\Http;

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

        /**
         * Author:
         *  - icon_url
         *  - name
         *  - url
         * Color
         * Description: [{gitHash}]({commitUrl}) {commitName}
         * Timestamp
         * Title: {repo} Build #{number} {status}
         * Url
         */
        return [
            'author' => [
                'icon_url' => '',
                'name' => $payload->head_commit->author->name,
                'url' => '',
            ],
            'color' => $this->getBuildColor($payload),
            'description' => $this->getBuildDescription($payload),
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
        );

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

    protected function getBuildDescription(object $payload): string
    {
        $gitHash = substr($payload->head_sha, 0, 7);
        $commitUrl = $payload->html_url;
        $commitName = $payload->head_commit->message;

        return "[{$gitHash}]({$commitUrl}) {$commitName}";
    }

    protected function getBuildTitle(object $payload): string
    {
        $repoInfo = "[{$payload->repository->full_name}]:{$payload->head_branch}";
        $number = $payload->run_number;
        $status = $payload->conclusion;

        return "{$repoInfo} Build #{$number} {$status}";
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
