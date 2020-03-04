<?php

namespace App\Notifications;

use App\Channels\DiscordChannel;
use App\Driver;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BuildNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @param  Driver  $notifiable
     * @return array
     */
    public function via(Driver $notifiable)
    {
        return [DiscordChannel::class];
    }

    /**
     * Get the Discord representation of the notification.
     *
     * @param  Driver  $notifiable
     * @return array
     */
    public function toDiscord(Driver $notifiable)
    {
        return [
            'embeds' => [
                $notifiable->embed,
            ]
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  Driver  $notifiable
     * @return array
     */
    public function toArray(Driver $notifiable)
    {
        return $notifiable->embed;
    }
}
