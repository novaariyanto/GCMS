<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardStatsResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'total_complaints' => $this->resource['total_complaints'] ?? 0,
            'open_complaints' => $this->resource['open_complaints'] ?? 0,
            'resolved_complaints' => $this->resource['resolved_complaints'] ?? 0,
            'closed_complaints' => $this->resource['closed_complaints'] ?? 0,
            'overdue_complaints' => $this->resource['overdue_complaints'] ?? 0,
            'by_status' => $this->resource['by_status'] ?? [],
            'by_category' => $this->resource['by_category'] ?? [],
            'sla_achievement_percentage' => $this->resource['sla_achievement_percentage'] ?? 0,
            'trends_last_7_days' => $this->resource['trends_last_7_days'] ?? [],
            'top_opds' => $this->resource['top_opds'] ?? [],
            'recent_complaints' => ComplaintResource::collection($this->resource['recent_complaints'] ?? collect()),
        ];
    }
}
