<?php

namespace App\Jobs;

use App\Notifications\BuildNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBuildNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $driver;
    protected $validated;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $driver, array $validated)
    {
        $this->driver = $driver;
        $this->validated = $validated;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /**
         * @var \App\Driver\Driver $driver
         */
        $driver = new $this->driver($this->validated);

        if (!$driver->wasAlreadySent()) {
            $driver->notify(new BuildNotification());
        }
    }
}
