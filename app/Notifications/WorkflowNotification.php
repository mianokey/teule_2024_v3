<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class WorkflowNotification extends Notification
{
    use Queueable;

    protected array $payload;
    protected array $channels;

    public function __construct(
        array $payload,
        array $channels = ['database']
    ) {
        $this->payload = $payload;
        $this->channels = $channels;
    }

    public function via(object $notifiable): array
    {
        $supportedChannels = [
            'database',
            'mail',
            'webpush',
        ];

        $selectedChannels = array_values(array_intersect(
            array_unique($this->channels),
            $supportedChannels
        ));

        $result = [];

        foreach ($selectedChannels as $channel) {
            $result[] = match ($channel) {
                'database' => 'database',
                'mail' => 'mail',
                'webpush' => WebPushChannel::class,
            };
        }

        return $result;
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'event' => $this->payload['event'] ?? 'general',
            'title' => $this->payload['title'] ?? 'Notification',
            'message' => $this->payload['message'] ?? '',
            'url' => $this->payload['url'] ?? null,
            'icon' => $this->payload['icon'] ?? 'bell',
            'priority' => $this->payload['priority'] ?? 'normal',
            'metadata' => $this->payload['metadata'] ?? [],
            'created_by' => $this->payload['created_by'] ?? null,
        ];
    }

    public function toWebPush(
        object $notifiable,
        object $notification
    ): WebPushMessage {
        $message = $this->payload['message']
            ?? 'You have a new notification.';

        $push = (new WebPushMessage())
            ->title(
                $this->payload['title']
                    ?? 'Teule Management System'
            )
            ->body($message)
            ->icon('/icons/icon-192.png')
            ->badge('/icons/icon-192.png')
            ->data([
                'url' => $this->payload['url'] ?? '/admin',
                'notification_id' => $notification->id ?? null,
            ]);

        return $push;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $name = $notifiable->name ?? '';

        $mail = (new MailMessage)
            ->subject(
                $this->payload['email_subject']
                    ?? $this->payload['title']
                    ?? 'Teule Kenya Notification'
            )
            ->greeting('Hello' . ($name !== '' ? ' ' . $name : '') . ',')
            ->line(
                $this->payload['message']
                    ?? 'You have a new notification.'
            );

        if (!empty($this->payload['url'])) {
            $mail->action(
                $this->payload['action_text'] ?? 'View Details',
                $this->payload['url']
            );
        }

        return $mail->line('Teule Kenya');
    }
}
