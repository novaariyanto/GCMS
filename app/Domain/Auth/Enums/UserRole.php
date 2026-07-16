<?php

namespace App\Domain\Auth\Enums;

enum UserRole: string
{
    case SUPER_ADMIN = 'SUPER_ADMIN';
    case ADMINISTRATOR = 'ADMINISTRATOR';
    case ADMIN_OPD = 'ADMIN_OPD';
    case ADMIN_UNIT = 'ADMIN_UNIT';
    case SUPERVISOR = 'SUPERVISOR';
    case PETUGAS = 'PETUGAS';
    case MASYARAKAT = 'MASYARAKAT';

    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Admin',
            self::ADMINISTRATOR => 'Administrator',
            self::ADMIN_OPD => 'Admin OPD',
            self::ADMIN_UNIT => 'Admin Unit',
            self::SUPERVISOR => 'Supervisor',
            self::PETUGAS => 'Petugas',
            self::MASYARAKAT => 'Masyarakat',
        };
    }
}
