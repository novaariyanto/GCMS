<?php

namespace App\Domain\Complaint\Policies;

use App\Domain\Auth\Enums\UserRole;
use App\Domain\Complaint\Models\Complaint;
use App\Models\User;

class ComplaintPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('complaints.view')
            || $user->hasAnyRole($this->adminRoles());
    }

    public function view(User $user, Complaint $complaint): bool
    {
        return $this->isAdministrator($user)
            || $this->isReporter($user, $complaint)
            || $this->isCurrentPic($user, $complaint)
            || $this->matchesCurrentRole($user, $complaint)
            || $this->matchesCurrentOrganization($user, $complaint);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('complaints.create')
            || $user->hasAnyRole([...$this->adminRoles(), UserRole::MASYARAKAT->value]);
    }

    public function update(User $user, Complaint $complaint): bool
    {
        if ($this->isAdministrator($user)) {
            return true;
        }

        if ($this->isReporter($user, $complaint) && (bool) $complaint->currentNode?->can_edit) {
            return true;
        }

        return $user->hasPermissionTo('complaints.update')
            && (
                $this->isCurrentPic($user, $complaint)
                || $this->matchesCurrentRole($user, $complaint)
                || $this->matchesCurrentOrganization($user, $complaint)
            );
    }

    public function transition(User $user, Complaint $complaint): bool
    {
        if ($this->isAdministrator($user)) {
            return true;
        }

        return $user->hasPermissionTo('workflows.execute')
            && (
                $this->isCurrentPic($user, $complaint)
                || $this->matchesCurrentRole($user, $complaint)
                || $this->matchesCurrentOrganization($user, $complaint)
            );
    }

    /**
     * @return array<int, string>
     */
    private function adminRoles(): array
    {
        return [
            UserRole::SUPER_ADMIN->value,
            UserRole::ADMINISTRATOR->value,
        ];
    }

    private function isAdministrator(User $user): bool
    {
        return $user->hasAnyRole($this->adminRoles());
    }

    private function isReporter(User $user, Complaint $complaint): bool
    {
        return $complaint->reporter_id !== null && $complaint->reporter_id === $user->id;
    }

    private function isCurrentPic(User $user, Complaint $complaint): bool
    {
        return $complaint->current_pic_id !== null && $complaint->current_pic_id === $user->id;
    }

    private function matchesCurrentRole(User $user, Complaint $complaint): bool
    {
        return $complaint->current_role !== null && $user->hasRole($complaint->current_role);
    }

    private function matchesCurrentOrganization(User $user, Complaint $complaint): bool
    {
        return ($complaint->current_opd_id !== null && $complaint->current_opd_id === $user->opd_id)
            || ($complaint->current_unit_id !== null && $complaint->current_unit_id === $user->unit_id);
    }
}
