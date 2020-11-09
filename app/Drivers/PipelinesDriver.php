<?php

namespace App\Drivers;

use App\Driver;
use App\Support\GitHub;
use App\Support\Pipelines;
use Illuminate\Support\Str;

class PipelinesDriver extends Driver
{
    protected $validated;
    protected $repositoryFullName;

    protected $commit;
    protected $details;

    protected const VALIDATION_RULES = [
        'resource.finishTime' => 'required|date',
        'resource.status' => 'required|string',
        'resource.id' => 'required|string',
        'resource.url' => 'required|url'
    ];

    protected const STATUSES_CANCELED = 'stopped';
    protected const STATUSES_FAILED = 'failed|partiallySucceeded';
    protected const STATUSES_PASSED = 'succeeded';
    protected const STATUSES_PENDING = '';

    public function __construct(array $validated)
    {
        $this->validated = $validated;

        $this->details = (new Pipelines)->getResourceDetails($validated['resource']['url']);

        $explodedId = explode('/', $this->details->repository->id);
        $this->repositoryFullName = $explodedId[0] . '/' . $explodedId[1];

        $this->commitHash = $this->details->sourceVersion;
        $this->branch = explode('/', $this->details->sourceBranch)[2];

        $this->commit = (new GitHub)->getCommitDetails($this->repositoryFullName, $this->commitHash);

        $this->embed = $this->create();
    }

    protected function create(): array
    {
        return [
            'author' => [
                'icon_url' => $this->commit->author->avatar_url,
                'name' => $this->commit->author->name,
                'url' => $this->commit->author->html_url,
            ],
            'color' => $this->getBuildColor($this->validated['resource']['status']),
            'description' => $this->getBuildDescription(
                $this->commit->sha,
                $this->commit->html_url,
                Str::limit($this->commit->commit->message, 1500)
            ),
            'timestamp' => $this->validated['resource']['finishTime'],
            'title' => $this->getBuildTitle(
                $this->repositoryFullName,
                $this->branch,
                $this->validated['resource']['id'],
                $this->validated['resource']['status']
            ),
            'url' => $this->details->_links->web->href,
        ];
    }
}
