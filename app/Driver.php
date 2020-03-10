<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

abstract class Driver
{
    use Notifiable {
        notify as protected traitNotify;
    }

    protected const VALIDATION_RULES = self::VALIDATION_RULES;

    protected const STATUSES_CANCELED = self::STATUSES_CANCELED;
    protected const STATUSES_FAILED = self::STATUSES_FAILED;
    protected const STATUSES_PASSED = self::STATUSES_PASSED;
    protected const STATUSES_PENDING = self::STATUSES_PENDING;

    public $embed = [];
    public $commitHash = '';
    public $branch = '';

    public function wasSuccessful(): bool
    {
        return $this->embed['color'] == Colors::PASSED();
    }

    public static function validate(Request $request)
    {
        $validator = Validator::make($request->all(), static::VALIDATION_RULES);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    public function wasAlreadySent(): bool
    {
        $builds = Build::where('commit_hash', $this->commitHash)->get();

        // If we already have a commit with that hash, then don't post it again.
        if (count($builds) != 0) {
            return true;
        }

        /**
         * @var null|\App\Build $build
         */
        $build = Build::where('branch', $this->branch)->latest()->first();

        // If the last build was successful and the new build was sucessful too, then ignore.
        if ($build && $build->successful && $this->wasSuccessful()) {
            return true;
        }

        return false;
    }

    public function notify($notification)
    {
        $this->traitNotify($notification);

        Build::create([
            'commit_hash' => $this->commitHash,
            'branch' => $this->branch,
            'successful' => $this->wasSuccessful(),
        ]);
    }

    public function routeNotificationForSlack($notification)
    {
        return config('services.slack.hook', '');
    }

    public function getBuildColor(string $status): Colors
    {
        $canceled = explode('|', $this::STATUSES_CANCELED);
        $failed = explode('|', $this::STATUSES_FAILED);
        $passed = explode('|', $this::STATUSES_PASSED);
        $pending = explode('|', $this::STATUSES_PENDING);

        if (in_array($status, $canceled)) {
            return Colors::CANCELED();
        }

        if (in_array($status, $failed)) {
            return Colors::FAILED();
        }

        if (in_array($status, $passed)) {
            return Colors::PASSED();
        }

        if (in_array($status, $pending)) {
            return Colors::PENDING();
        }

        return Colors::CANCELED();
    }
}
