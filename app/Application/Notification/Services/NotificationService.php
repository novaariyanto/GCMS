<?php

namespace App\Application\Notification\Services;

use App\Domain\Notification\Models\NotificationLog;
use App\Domain\Notification\Notifications\GenericNotification;
use App\Models\User;

class NotificationService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function notifyUser(User $user, string $title, string $body, array $data = []): NotificationLog
    {
        $user->notify(new GenericNotification($title, $body, $data));

        return NotificationLog::query()->create([
            'user_id' => $user->id,
            'channel' => 'database',
            'destination' => $user->email,
            'template' => GenericNotification::class,
            'payload' => [
                'title' => $title,
                'body' => $body,
                'data' => $data,
            ],
            'status' => 'queued',
            'sent_at' => null,
        ]);
    }
}
