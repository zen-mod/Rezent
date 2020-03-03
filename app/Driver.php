<?php

namespace App;

use App\Notifications\BuildNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

abstract class Driver
{
    protected const VALIDATION_RULES = self::VALIDATION_RULES;

    abstract public function create(array $validated);

    abstract public function wasSuccessful(): bool;
    abstract public function wasAlreadySent(): bool;

    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), static::VALIDATION_RULES);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    public function send(): bool
    {
        // ToDo: Create new Notification
        return (new BuildNotification())->
    }
}
