<?php

namespace App\Drivers;

use App\Driver;
use GitHub;

class PipelinesDriver extends Driver
{
    protected $validated;
    protected $commit;

    protected const VALIDATION_RULES = [
        'resource.finishTime' => 'required|date',
        'resource.status' => 'required|string',
        'resource.id' => 'required|string',
        'resource.url' => 'required|url'
    ];

    protected const STATUSES_CANCELED = 'canceled';
    protected const STATUSES_FAILED = 'broken|failed|stillFailing';
    protected const STATUSES_PASSED = 'fixed|passed|succeeded';
    protected const STATUSES_PENDING = 'pending';

    public function __construct(array $validated)
    {
        $this->validated = $validated;

        $orgAndRepo = '';

        $this->commitHash = '';
        $this->branch = '';

        $this->commit = (new GitHub)->getCommitDetails($orgAndRepo, $this->commitHash);
    }
}
