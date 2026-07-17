<?php

namespace Database\Seeders;

use App\Domain\Auth\Enums\UserRole;
use App\Domain\Auth\Models\Permission;
use App\Domain\Auth\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guard = 'web';

        $permissionGroups = [
            'complaints' => ['view', 'create', 'update', 'delete', 'verify', 'assign', 'export'],
            'workflows' => ['view', 'create', 'update', 'delete', 'execute'],
            'master' => ['view', 'create', 'update', 'delete'],
            'reports' => ['view', 'export'],
            'users' => ['view', 'create', 'update', 'delete', 'impersonate'],
            'settings' => ['view', 'update'],
        ];

        $allPermissions = [];

        foreach ($permissionGroups as $group => $actions) {
            foreach ($actions as $action) {
                $name = "{$group}.{$action}";
                $allPermissions[$name] = Permission::findOrCreate($name, $guard);
            }
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissionNames = array_keys($allPermissions);

        $rolePermissions = [
            UserRole::SUPER_ADMIN->value => $permissionNames,
            UserRole::ADMINISTRATOR->value => [
                'complaints.view', 'complaints.create', 'complaints.update', 'complaints.delete',
                'complaints.verify', 'complaints.assign', 'complaints.export',
                'workflows.view', 'workflows.create', 'workflows.update', 'workflows.delete', 'workflows.execute',
                'master.view', 'master.create', 'master.update', 'master.delete',
                'reports.view', 'reports.export',
                'users.view', 'users.create', 'users.update', 'users.delete',
                'settings.view', 'settings.update',
            ],
            UserRole::ADMIN_OPD->value => [
                'complaints.view', 'complaints.update', 'complaints.verify', 'complaints.assign', 'complaints.export',
                'workflows.view', 'workflows.execute',
                'master.view',
                'reports.view', 'reports.export',
                'users.view', 'users.create', 'users.update',
            ],
            UserRole::ADMIN_UNIT->value => [
                'complaints.view', 'complaints.update', 'complaints.assign',
                'workflows.view', 'workflows.execute',
                'master.view',
                'reports.view',
                'users.view',
            ],
            UserRole::SUPERVISOR->value => [
                'complaints.view', 'complaints.update', 'complaints.verify', 'complaints.assign', 'complaints.export',
                'workflows.view', 'workflows.execute',
                'reports.view', 'reports.export',
            ],
            UserRole::PETUGAS->value => [
                'complaints.view', 'complaints.update',
                'workflows.view', 'workflows.execute',
            ],
            UserRole::MASYARAKAT->value => [
                'complaints.view', 'complaints.create', 'complaints.update',
            ],
        ];

        foreach (UserRole::cases() as $userRole) {
            $role = Role::findOrCreate($userRole->value, $guard);

            $names = $rolePermissions[$userRole->value] ?? [];
            $role->syncPermissions(
                collect($names)
                    ->map(fn (string $name) => $allPermissions[$name])
                    ->all()
            );
        }
    }
}
