<?php

namespace App\Drivers;

use App\Driver;
use App\Support\GitHub;

class TravisDriver extends Driver
{
    protected $validated;
    protected $commit;

    protected const VALIDATION_RULES = [
        'finished_at' => 'required|date',
        'build_url' => 'required|url',
        'state' => 'required|string',
        'status_message' => 'required|string',
        'message' => 'required|string',
        'commit' => 'required|string',
        'compare_url' => 'required|url',
        'branch' => 'required|string',
        'number' => 'required|string',
        'author_name' => 'required|string',
        'repository.name' => 'required|string',
        'repository.owner_name' => 'required|string',
    ];

    protected const STATUSES_CANCELED = 'canceled';
    protected const STATUSES_FAILED = 'broken|failed|stillFailing';
    protected const STATUSES_PASSED = 'fixed|passed';
    protected const STATUSES_PENDING = 'pending';

    public function __construct(array $validated)
    {
        $this->validated = $validated;
        $this->commit = (new GitHub)->getCommitDetails(
            $this->getFullRepositoryName(),
            $validated->commit
        );

        $this->commitHash = $this->validated->commit;
        $this->branch = $this->validated->branch;

        $this->embed = $this->create();
    }

    protected function create(): array
    {
        return [
            'author' => [
                'icon_url' => $this->commit->author->avatar_url,
                'name' => $this->validated->author_name,
                'url' => $this->commit->author->html_url,
            ],
            'color' => $this->getBuildColor($this->payload->state),
            'description' => $this->getBuildDescription(
                $this->validated->commit,
                $this->validated->compare_url,
                $this->validated->message
            ),
            'timestamp' => $this->validated->finished_at,
            'title' => $this->getBuildTitle(
                $this->getFullRepositoryName(),
                $this->validated->branch,
                $this->validated->number,
                $this->validated->status_message
            ),
            'url' => $this->validated->build_url,
        ];
    }

    protected function getFullRepositoryName()
    {
        return $this->validated->repository->owner_name
            . '/'
            . $this->validated->repository->name;
    }
}
