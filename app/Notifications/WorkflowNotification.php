<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

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
        return array_values(array_intersect(
            array_unique($this->channels),
            ['database', 'mail']
        ));
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