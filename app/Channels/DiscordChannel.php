<?php

namespace App\Channels;

use App\Notifications\BuildNotification;

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
        $message = $notification->toArray($notifiable);

        // Send notification to the $notifiable instance...

    }
}
