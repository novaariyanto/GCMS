<?php

namespace App\Application\Dashboard\Services;

use App\Domain\Auth\Enums\UserRole;
use App\Domain\Complaint\Models\Complaint;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * @return array<string, mixed>
     */
    public function stats(?User $user = null): array
    {
        $baseQuery = $this->scopeForUser(Complaint::query(), $user);
        $totalWithSla = (clone $baseQuery)->whereNotNull('sla_due_at')->count();
        $achievedSla = (clone $baseQuery)
            ->whereNotNull('sla_due_at')
            ->whereNotNull('resolved_at')
            ->whereColumn('resolved_at', '<=', 'sla_due_at')
            ->count();

        return [
            'totals_by_status' => $this->totalsByStatus($baseQuery),
            'sla_achievement_percentage' => $totalWithSla > 0
                ? round(($achievedSla / $totalWithSla) * 100, 2)
                : 0.0,
            'trends_last_7_days' => $this->trendsLastSevenDays($baseQuery),
            'top_categories' => $this->topCategories($baseQuery),
            'top_opds' => $this->topOpds($baseQuery),
        ];
    }

    private function scopeForUser(Builder $query, ?User $user): Builder
    {
        if (! $user instanceof User || $user->hasAnyRole([UserRole::SUPER_ADMIN->value, UserRole::ADMINISTRATOR->value])) {
            return $query;
        }

        if ($user->hasRole(UserRole::MASYARAKAT->value)) {
            return $query->where('reporter_id', $user->id);
        }

        return $query->where(function (Builder $nested) use ($user): void {
            $nested->where('current_pic_id', $user->id);

            if ($user->opd_id !== null) {
                $nested->orWhere('current_opd_id', $user->opd_id);
            }

            if ($user->unit_id !== null) {
                $nested->orWhere('current_unit_id', $user->unit_id);
            }
        });
    }

    /**
     * @return array<int, array{code: string|null, name: string|null, total: int}>
     */
    private function totalsByStatus(Builder $baseQuery): array
    {
        return (clone $baseQuery)
            ->join('complaint_statuses', 'complaint_statuses.id', '=', 'complaints.status_id')
            ->select('complaint_statuses.code', 'complaint_statuses.name', DB::raw('COUNT(*) as total'))
            ->groupBy('complaint_statuses.id', 'complaint_statuses.code', 'complaint_statuses.name')
            ->orderBy('complaint_statuses.sort_order')
            ->get()
            ->map(fn ($row): array => [
                'code' => $row->code,
                'name' => $row->name,
                'total' => (int) $row->total,
            ])
            ->all();
    }

    /**
     * @return array<int, array{date: string, total: int}>
     */
    private function trendsLastSevenDays(Builder $baseQuery): array
    {
        $start = now()->subDays(6)->startOfDay();
        $rows = (clone $baseQuery)
            ->where('complaints.created_at', '>=', $start)
            ->selectRaw('DATE(complaints.created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        return Collection::times(7, fn (int $day): array => [
            'date' => $start->copy()->addDays($day - 1)->toDateString(),
            'total' => (int) ($rows[$start->copy()->addDays($day - 1)->toDateString()] ?? 0),
        ])->all();
    }

    /**
     * @return array<int, array{id: string|null, name: string|null, total: int}>
     */
    private function topCategories(Builder $baseQuery): array
    {
        return (clone $baseQuery)
            ->with('category')
            ->select('category_id')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(fn (Complaint $complaint): array => [
                'id' => $complaint->category_id,
                'name' => $complaint->category?->name,
                'total' => (int) $complaint->total,
            ])
            ->all();
    }

    /**
     * @return array<int, array{id: string|null, name: string|null, total: int}>
     */
    private function topOpds(Builder $baseQuery): array
    {
        return (clone $baseQuery)
            ->with('currentOpd')
            ->whereNotNull('current_opd_id')
            ->select('current_opd_id')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('current_opd_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(fn (Complaint $complaint): array => [
                'id' => $complaint->current_opd_id,
                'name' => $complaint->currentOpd?->name,
                'total' => (int) $complaint->total,
            ])
            ->all();
    }
}
