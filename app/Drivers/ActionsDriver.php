<?php

namespace App\Drivers;

use App\Colors;
use App\Driver;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ActionsDriver extends Driver
{
    protected $validated;
    protected $payload;

    protected const VALIDATION_RULES = [
        'organisation' => 'required|string',
        'repository' => 'required|string',
        'workflow_id' => 'required|integer',
    ];

    public function __construct(array $validated)
    {
        $this->validated = $validated;
        $this->payload = $this->getGitHubPayload();

        $this->commitHash = $this->payload->head_sha;
        $this->branch = $this->payload->head_branch;

        $this->embed = $this->create();
    }

    protected function create(): array
    {
        $commit = $this->getBuildCommitDetails($this->payload);

        return [
            'author' => [
                'icon_url' => $commit->author->avatar_url,
                'name' => $this->payload->head_commit->author->name,
                'url' => $commit->author->html_url,
            ],
            'color' => $this->getBuildColor(),
            'description' => $this->getBuildDescription($commit),
            'timestamp' => $this->payload->updated_at,
            'title' => $this->getBuildTitle(),
            'url' => $this->payload->html_url,
        ];
    }

    protected function getGitHubUri(): string
    {
        return 'https://api.github.com/repos/'
            . $this->validated['organisation'] . '/'
            . $this->validated['repository'] . '/'
            . 'actions/workflows/'
            . $this->validated['workflow_id'] . '/'
            . 'runs?per_page=1';
    }

    protected function getGitHubPayload(): object
    {
        $response = Http::get(
            $this->getGitHubUri()
        )->throw();

        return json_decode($response->body())
            ->workflow_runs[0];
    }

    protected function getBuildColor(): Colors
    {
        switch ($this->payload->conclusion) {
            case 'success':
                return Colors::PASSED();

            case 'failure':
                return Colors::FAILED();

            default:
                return Colors::BROKEN();
        }
    }

    protected function getBuildDescription(object $commit): string
    {
        $gitHash = substr($this->payload->head_sha, 0, 7);
        $commitUrl = $commit->html_url;
        $commitName = $this->payload->head_commit->message;

        return "[{$gitHash}]({$commitUrl}) {$commitName}";
    }

    protected function getBuildTitle(): string
    {
        $repoInfo = "[{$this->payload->repository->full_name}]:{$this->payload->head_branch}";
        $number = $this->payload->run_number;
        $status = Str::ucfirst($this->payload->conclusion);

        return "{$repoInfo} Build #{$number} {$status}";
    }

    protected function getBuildCommitDetails(): object
    {
        $response = Http::get(
            'https://api.github.com/repos/'
                . $this->payload->repository->full_name
                . '/commits/'
                . $this->payload->head_sha
        )->throw();

        return json_decode($response->body());
    }
}
