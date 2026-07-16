<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\Dashboard\Services\DashboardService;
use App\Domain\Complaint\Models\Complaint;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\DashboardStatsResource;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboard) {}

    public function stats(Request $request): DashboardStatsResource
    {
        $serviceStats = $this->dashboard->stats($request->user('api'));
        $totalComplaints = collect($serviceStats['totals_by_status'] ?? [])->sum('total');

        $stats = [
            'total_complaints' => $totalComplaints,
            'open_complaints' => Complaint::query()
                ->whereHas('status', fn ($query) => $query->where('is_final', false))
                ->count(),
            'resolved_complaints' => Complaint::query()->whereNotNull('resolved_at')->count(),
            'closed_complaints' => Complaint::query()->whereNotNull('closed_at')->count(),
            'overdue_complaints' => Complaint::query()
                ->whereNotNull('sla_due_at')
                ->where('sla_due_at', '<', now())
                ->whereHas('status', fn ($query) => $query->where('is_final', false))
                ->count(),
            'by_status' => $serviceStats['totals_by_status'] ?? [],
            'by_category' => $serviceStats['top_categories'] ?? [],
            'sla_achievement_percentage' => $serviceStats['sla_achievement_percentage'] ?? 0,
            'trends_last_7_days' => $serviceStats['trends_last_7_days'] ?? [],
            'top_opds' => $serviceStats['top_opds'] ?? [],
            'recent_complaints' => Complaint::query()
                ->with(['category', 'priority', 'status'])
                ->latest()
                ->limit(5)
                ->get(),
        ];

        return new DashboardStatsResource($stats);
    }
}
