<?php

namespace App\Domain\Audit\Enums;

enum AuditAction: string
{
    case LOGIN = 'LOGIN';
    case LOGOUT = 'LOGOUT';
    case CREATE = 'CREATE';
    case UPDATE = 'UPDATE';
    case DELETE = 'DELETE';
    case FORWARD = 'FORWARD';
    case APPROVE = 'APPROVE';
    case REJECT = 'REJECT';
    case EXPORT = 'EXPORT';
    case DOWNLOAD = 'DOWNLOAD';
    case IMPERSONATE = 'IMPERSONATE';

    public function label(): string
    {
        return match ($this) {
            self::LOGIN => 'Login',
            self::LOGOUT => 'Logout',
            self::CREATE => 'Create',
            self::UPDATE => 'Update',
            self::DELETE => 'Delete',
            self::FORWARD => 'Forward',
            self::APPROVE => 'Approve',
            self::REJECT => 'Reject',
            self::EXPORT => 'Export',
            self::DOWNLOAD => 'Download',
            self::IMPERSONATE => 'Impersonate',
        };
    }
}
