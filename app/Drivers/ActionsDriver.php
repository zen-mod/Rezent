<?php

namespace App\Drivers;

use App\Driver;

class ActionsDriver extends Driver
{
    protected const VALIDATION_RULES = [
        'title' => 'required|string',
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
