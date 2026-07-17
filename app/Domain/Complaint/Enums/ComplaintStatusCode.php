<?php

namespace App\Domain\Complaint\Enums;

enum ComplaintStatusCode: string
{
    case NEW = 'NEW';
    case VERIFIED = 'VERIFIED';
    case IN_PROGRESS = 'IN_PROGRESS';
    case WAITING_APPROVAL = 'WAITING_APPROVAL';
    case COMPLETED = 'COMPLETED';
    case CLOSED = 'CLOSED';
    case REJECTED = 'REJECTED';
    case RETURNED = 'RETURNED';

    public function label(): string
    {
        return match ($this) {
            self::NEW => 'Baru',
            self::VERIFIED => 'Terverifikasi',
            self::IN_PROGRESS => 'Dalam Proses',
            self::WAITING_APPROVAL => 'Menunggu Persetujuan',
            self::COMPLETED => 'Selesai',
            self::CLOSED => 'Ditutup',
            self::REJECTED => 'Ditolak',
            self::RETURNED => 'Dikembalikan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::NEW => '#6c757d',
            self::VERIFIED => '#0dcaf0',
            self::IN_PROGRESS => '#0d6efd',
            self::WAITING_APPROVAL => '#ffc107',
            self::COMPLETED => '#198754',
            self::CLOSED => '#212529',
            self::REJECTED => '#dc3545',
            self::RETURNED => '#fd7e14',
        };
    }

    public function isFinal(): bool
    {
        return match ($this) {
            self::CLOSED, self::REJECTED => true,
            default => false,
        };
    }
}
