<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\Reporting\Services\ReportService;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ComplaintResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(private readonly ReportService $reports) {}

    public function complaints(Request $request): JsonResponse
    {
        $data = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'status_id' => ['nullable', 'uuid', 'exists:complaint_statuses,id'],
            'category_id' => ['nullable', 'uuid', 'exists:complaint_categories,id'],
        ]);

        $complaints = $this->reports->complaintsReport([
            'date_from' => $data['from'] ?? null,
            'date_to' => $data['to'] ?? null,
            'status_id' => $data['status_id'] ?? null,
            'category_id' => $data['category_id'] ?? null,
        ])->load(['category', 'priority', 'status', 'currentOpd']);

        return response()->json([
            'summary' => [
                'total' => $complaints->count(),
                'resolved' => $complaints->whereNotNull('resolved_at')->count(),
                'overdue' => $complaints->where('sla_due_at', '<', now())->whereNull('resolved_at')->count(),
            ],
            'data' => ComplaintResource::collection($complaints),
        ]);
    }
}
