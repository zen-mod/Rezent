<?php

namespace App\Drivers;

use App\Driver;
use App\Support\GitHub;

class ActionsDriver extends Driver
{
    protected $validated;
    protected $payload;

    protected $gitHub;

    protected const VALIDATION_RULES = [
        'organisation' => 'required|string',
        'repository' => 'required|string',
        'workflow_id' => 'required|integer',
    ];

    protected const STATUSES_CANCELED = '';
    protected const STATUSES_FAILED = 'failure';
    protected const STATUSES_PASSED = 'success';
    protected const STATUSES_PENDING = '';

    public function __construct(array $validated)
    {
        $this->validated = $validated;

        $this->gitHub = new GitHub;
        $this->payload = $this->gitHub->getLatestActionWorkflowRun(
            $validated['organisation'],
            $validated['repository'],
            $validated['workflow_id']
        );

        $this->commitHash = $this->payload->head_sha;
        $this->branch = $this->payload->head_branch;

        $this->embed = $this->create();
    }

    protected function create(): array
    {
        $commit = $this->gitHub->getCommitDetails(
            $this->payload->repository->full_name,
            $this->payload->head_sha
        );

        return [
            'author' => [
                'icon_url' => $commit->author->avatar_url,
                'name' => $this->payload->head_commit->author->name,
                'url' => $commit->author->html_url,
            ],
            'color' => $this->getBuildColor($this->payload->conclusion),
            'description' => $this->getBuildDescription(
                $this->payload->head_sha,
                $commit->html_url,
                $this->payload->head_commit->message
            ),
            'timestamp' => $this->payload->updated_at,
            'title' => $this->getBuildTitle(
                $this->payload->repository->full_name,
                $this->payload->head_branch,
                $this->payload->run_number,
                $this->payload->conclusion
            ),
            'url' => $this->payload->html_url,
        ];
    }
}
