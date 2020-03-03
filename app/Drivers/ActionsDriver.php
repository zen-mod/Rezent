<?php

namespace App\Drivers;

use App\Driver;

class ActionsDriver extends Driver
{
    protected const VALIDATION_RULES = [
        'id' => 'required|integer',
    ];

    public function create(array $validated): self
    {
        return $this;
    }

    public function wasSuccessful(): bool
    {
        return false;
    }

    public function wasAlreadySent(): bool
    {
        return false;
    }
}
