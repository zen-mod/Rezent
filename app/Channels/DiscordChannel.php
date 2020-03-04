<?php

namespace App\Channels;

use App\Notifications\BuildNotification;
use Illuminate\Support\Facades\Http;

class DiscordChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, BuildNotification $notification)
    {
        $message = $notification->toDiscord($notifiable);

        $response = Http::post(
            $this->generateUri(),
            $message
        );

        // If failed to post, throw exception.
        $response->throw();
    }

    protected function generateUri(): string
    {
        $id = config('services.discord.id');
        $token = config('services.discord.token');

        return 'https://discordapp.com/api/webhooks/' . $id . '/' . $token;
    }
}
