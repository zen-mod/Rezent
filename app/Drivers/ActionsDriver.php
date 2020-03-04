<?php

namespace App\Drivers;

use App\Driver;
use Carbon\Carbon;
use App\Colors;

class ActionsDriver extends Driver
{
    public $embed;

    protected const VALIDATION_RULES = [
        'id' => 'required|integer',
    ];

    public function __construct(array $validated)
    {
        $this->embed = $this->create($validated);
    }

    protected function create(array $validated): array
    {
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
                'name' => '',
                'url' => '',
            ],
            'color' => Colors::PASSED(),
            'description' => '',
            'timestamp' => Carbon::now(),
            'title' => '',
            'url' => '',
        ];
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
