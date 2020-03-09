<?php

namespace App\Notifications;

use App\Channels\DiscordChannel;
use App\Driver;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
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
        $vias = [];

        if (config('rezent.notifications.discord.enabled')) {
            array_push($vias, DiscordChannel::class);
        }

        if (config('rezent.notifications.slack.enabled')) {
            array_push($vias, 'slack');
        }

        return $vias;
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
     * Get the Slack representation of the notification.
     *
     * @param Driver $notifiable
     * @return array
     */
    public function toSlack(Driver $notifiable)
    {
        $e = $notifiable->embed;

        return (new SlackMessage)
            ->attachment(function (SlackAttachment $attachment) use ($e) {
                $attachment->author(
                    $e['author']['name'],
                    $e['author']['url'],
                    $e['author']['icon_url']
                )
                    ->color('#' . dechex($e['color']->getValue()))
                    ->content(
                        $this->convertDiscordMarkdownToSlack($e['description'])
                    )
                    ->markdown(['text'])
                    ->timestamp(
                        Carbon::parse($e['timestamp'])
                    )
                    ->title($e['title'])
                    ->url = $e['url'];
            });
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

    protected function convertDiscordMarkdownToSlack(string $content): string
    {
        $uriPattern = '\(https:\/\/([a-zA-Z0-9.\/\-]*)\)';

        $pattern = '/\[[a-zA-Z0-9]\w{6}\]' . $uriPattern . '/';

        // Find Markdown URI (see $embed['description'])
        return preg_replace_callback($pattern, function (array $matches) use ($uriPattern) {
            // `[sha](uri)`
            $match = $matches[0];
            $uriMatches = [];

            // Only get the sha (drops [ and ])
            $sha = substr($match, 1, 7);

            // Get the URI from `[sha](uri)`
            preg_match('/' . $uriPattern . '/', $match, $uriMatches);
            $uri = $uriMatches[0];

            // Drop ( and )
            $uri = substr($uri, 1);
            $uri = rtrim($uri, ')');

            return '<' . $uri . '|' . $sha . '>';
        }, $content, 1);
    }
}
