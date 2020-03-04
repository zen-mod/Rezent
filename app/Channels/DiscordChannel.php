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

        Http::post(
            $this->generateUri(),
            $message
        )->throw();
    }

    protected function generateUri(): string
    {
        $id = config('services.discord.id');
        $token = config('services.discord.token');

        return 'https://discordapp.com/api/webhooks/' . $id . '/' . $token;
    }
}
