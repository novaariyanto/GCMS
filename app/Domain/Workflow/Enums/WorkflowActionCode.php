<?php

namespace App\Domain\Workflow\Enums;

enum WorkflowActionCode: string
{
    case VERIFY = 'VERIFY';
    case FORWARD = 'FORWARD';
    case ASSIGN = 'ASSIGN';
    case DELEGATE = 'DELEGATE';
    case ESCALATE = 'ESCALATE';
    case RETURN = 'RETURN';
    case REJECT = 'REJECT';
    case APPROVE = 'APPROVE';
    case CLOSE = 'CLOSE';
    case REQUEST_REVISION = 'REQUEST_REVISION';

    public function label(): string
    {
        return match ($this) {
            self::VERIFY => 'Verifikasi',
            self::FORWARD => 'Teruskan',
            self::ASSIGN => 'Tugaskan',
            self::DELEGATE => 'Delegasikan',
            self::ESCALATE => 'Eskalasi',
            self::RETURN => 'Kembalikan',
            self::REJECT => 'Tolak',
            self::APPROVE => 'Setujui',
            self::CLOSE => 'Tutup',
            self::REQUEST_REVISION => 'Minta Revisi',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::VERIFY => '#0dcaf0',
            self::FORWARD => '#0d6efd',
            self::ASSIGN => '#6610f2',
            self::DELEGATE => '#6f42c1',
            self::ESCALATE => '#fd7e14',
            self::RETURN => '#ffc107',
            self::REJECT => '#dc3545',
            self::APPROVE => '#198754',
            self::CLOSE => '#212529',
            self::REQUEST_REVISION => '#20c997',
        };
    }

    public function requiresRemark(): bool
    {
        return match ($this) {
            self::RETURN, self::REJECT, self::REQUEST_REVISION, self::ESCALATE => true,
            default => false,
        };
    }
}
