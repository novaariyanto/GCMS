<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkflowResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'is_default' => (bool) $this->is_default,
            'is_active' => (bool) $this->is_active,
            'category' => $this->whenLoaded('category', fn () => $this->lookup($this->category)),
            'sub_category' => $this->whenLoaded('subCategory', fn () => $this->lookup($this->subCategory)),
            'nodes' => $this->whenLoaded('nodes', fn () => $this->nodes->map(fn ($node) => [
                'id' => $node->id,
                'code' => $node->code,
                'name' => $node->name,
                'role_name' => $node->role_name,
                'sequence' => $node->sequence,
                'is_start' => $node->is_start,
                'is_end' => $node->is_end,
            ])),
            'transitions' => $this->whenLoaded('transitions', fn () => $this->transitions->map(fn ($transition) => [
                'id' => $transition->id,
                'from_node' => $this->lookup($transition->fromNode),
                'to_node' => $this->lookup($transition->toNode),
                'action' => $this->lookup($transition->action),
                'target_status' => $this->lookup($transition->targetStatus),
                'is_active' => $transition->is_active,
            ])),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function lookup(mixed $model): ?array
    {
        if (! $model) {
            return null;
        }

        return [
            'id' => $model->id,
            'code' => $model->code ?? null,
            'name' => $model->name ?? $model->label ?? null,
        ];
    }
}
