<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\WorkflowNotification;
use Illuminate\Support\Facades\Notification;
use InvalidArgumentException;

class WorkflowNotificationService
{
    public function send(
        array $payload,
        array $recipientIds,
        array $channels = ['database']
    ): int {
        $users = User::query()
            ->whereIn('id', array_unique($recipientIds))
            ->get();

        return $this->sendToUsers($payload, $users, $channels);
    }

    public function sendToUsers(
        array $payload,
        iterable $users,
        array $channels = ['database']
    ): int {
        $channels = array_values(array_unique($channels));

        $unsupported = array_diff($channels, ['database', 'mail']);

        if ($unsupported) {
            throw new InvalidArgumentException(
                'Unsupported notification channel(s): ' .
                implode(', ', $unsupported)
            );
        }

        if (!$channels) {
            throw new InvalidArgumentException(
                'At least one notification channel is required.'
            );
        }

        $users = collect($users)
            ->filter(fn ($user) => $user instanceof User)
            ->unique('id')
            ->values();

        if ($users->isEmpty()) {
            return 0;
        }

        Notification::send(
            $users,
            new WorkflowNotification($payload, $channels)
        );

        return $users->count();
    }

    public function sendToPermission(
        array $payload,
        string $permission,
        array $channels = ['database']
    ): int {
        return $this->sendToUsers(
            $payload,
            User::permission($permission)->get(),
            $channels
        );
    }

    public function sendToRole(
        array $payload,
        string $role,
        array $channels = ['database']
    ): int {
        return $this->sendToUsers(
            $payload,
            User::role($role)->get(),
            $channels
        );
    }
}