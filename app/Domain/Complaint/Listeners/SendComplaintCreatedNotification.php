<?php

namespace App\Domain\Complaint\Listeners;

use App\Application\Notification\Services\NotificationService;
use App\Domain\Auth\Enums\UserRole;
use App\Domain\Complaint\Events\ComplaintCreated;
use App\Models\User;

class SendComplaintCreatedNotification
{
    public function __construct(private readonly NotificationService $notifications) {}

    public function handle(ComplaintCreated $event): void
    {
        $complaint = $event->complaint;
        $notified = [];

        if ($complaint->reporter instanceof User) {
            $this->notifications->notifyUser(
                $complaint->reporter,
                'Pengaduan diterima',
                "Pengaduan {$complaint->ticket_number} berhasil dibuat.",
                ['complaint_id' => $complaint->id, 'ticket_number' => $complaint->ticket_number],
            );

            $notified[] = $complaint->reporter->id;
        }

        User::role([UserRole::SUPER_ADMIN->value, UserRole::ADMINISTRATOR->value])
            ->whereNotIn('id', $notified)
            ->get()
            ->each(fn (User $admin) => $this->notifications->notifyUser(
                $admin,
                'Pengaduan baru',
                "Pengaduan baru {$complaint->ticket_number} membutuhkan verifikasi.",
                ['complaint_id' => $complaint->id, 'ticket_number' => $complaint->ticket_number],
            ));
    }
}
