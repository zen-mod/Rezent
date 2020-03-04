<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

abstract class Driver
{
    use Notifiable;

    protected const VALIDATION_RULES = self::VALIDATION_RULES;
    public $embed = [];

    abstract public function wasSuccessful(): bool;
    abstract public function wasAlreadySent(): bool;

    public static function validate(Request $request)
    {
        $validator = Validator::make($request->all(), static::VALIDATION_RULES);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }
}
