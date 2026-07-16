<?php

namespace App\Http\Controllers\Web;

use App\Application\Dashboard\Services\DashboardService;
use App\Domain\Complaint\Models\Complaint;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortalDashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboard) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $stats = $this->dashboard->stats($user);

        return view('portal.dashboard', [
            'totalComplaints' => collect($stats['totals_by_status'] ?? [])->sum('total'),
            'openComplaints' => Complaint::query()
                ->where('reporter_id', $user->id)
                ->whereHas('status', fn ($query) => $query->where('is_final', false))
                ->count(),
            'resolvedComplaints' => Complaint::query()
                ->where('reporter_id', $user->id)
                ->whereNotNull('resolved_at')
                ->count(),
            'recentComplaints' => Complaint::query()
                ->with(['category', 'status'])
                ->where('reporter_id', $user->id)
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }
}
