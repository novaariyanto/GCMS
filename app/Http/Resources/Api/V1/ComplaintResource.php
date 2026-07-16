<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComplaintResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ticket_number' => $this->ticket_number,
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->whenLoaded('category', fn () => $this->simpleLookup($this->category)),
            'sub_category' => $this->whenLoaded('subCategory', fn () => $this->simpleLookup($this->subCategory)),
            'priority' => $this->whenLoaded('priority', fn () => [
                'id' => $this->priority?->id,
                'code' => $this->priority?->code,
                'name' => $this->priority?->name,
                'color' => $this->priority?->color,
                'level' => $this->priority?->level,
            ]),
            'status' => $this->whenLoaded('status', fn () => [
                'id' => $this->status?->id,
                'code' => $this->status?->code,
                'name' => $this->status?->name,
                'color' => $this->status?->color,
                'is_final' => $this->status?->is_final,
            ]),
            'current_opd' => $this->whenLoaded('currentOpd', fn () => $this->simpleLookup($this->currentOpd)),
            'current_pic' => $this->whenLoaded('currentPic', fn () => $this->simpleLookup($this->currentPic)),
            'reporter_name' => $this->is_anonymous ? 'Anonim' : $this->reporter_name,
            'is_anonymous' => (bool) $this->is_anonymous,
            'sla_due_at' => $this->sla_due_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function simpleLookup(mixed $model): ?array
    {
        if (! $model) {
            return null;
        }

        return [
            'id' => $model->id,
            'code' => $model->code ?? null,
            'name' => $model->name,
        ];
    }
}
