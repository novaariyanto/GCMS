<?php

namespace Database\Seeders;

use App\Domain\Auth\Enums\UserRole;
use App\Domain\Auth\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@gcms.local',
                'role' => UserRole::SUPER_ADMIN,
            ],
            [
                'name' => 'Administrator',
                'email' => 'admin@gcms.local',
                'role' => UserRole::ADMINISTRATOR,
            ],
            [
                'name' => 'Masyarakat',
                'email' => 'masyarakat@gcms.local',
                'role' => UserRole::MASYARAKAT,
            ],
        ];

        foreach ($users as $data) {
            $user = User::query()->firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );

            $role = Role::findByName($data['role']->value, 'web');
            $user->syncRoles([$role]);
        }
    }
}
