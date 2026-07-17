<?php

namespace App\Application\Audit\Services;

use App\Domain\Audit\Enums\AuditAction;
use App\Domain\Audit\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AuditService
{
    /**
     * @param  array<string, mixed>|null  $old
     * @param  array<string, mixed>|null  $new
     * @param  array<string, mixed>  $requestMeta
     */
    public function log(
        AuditAction|string $action,
        ?User $user = null,
        ?Model $auditable = null,
        ?array $old = null,
        ?array $new = null,
        array $requestMeta = [],
    ): AuditLog {
        return AuditLog::query()->create([
            'user_id' => $user?->id,
            'action' => $action instanceof AuditAction ? $action->value : $action,
            'auditable_type' => $auditable?->getMorphClass(),
            'auditable_id' => $auditable?->getKey(),
            'old_values' => $old,
            'new_values' => $new,
            'ip_address' => $requestMeta['ip_address'] ?? request()?->ip(),
            'user_agent' => $requestMeta['user_agent'] ?? request()?->userAgent(),
            'url' => $requestMeta['url'] ?? request()?->fullUrl(),
            'description' => $requestMeta['description'] ?? null,
        ]);
    }
}
