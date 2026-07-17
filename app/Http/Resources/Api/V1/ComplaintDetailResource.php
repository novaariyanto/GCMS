<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;

class ComplaintDetailResource extends ComplaintResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'reporter_phone' => $this->when(! $this->is_anonymous, $this->reporter_phone),
            'reporter_email' => $this->when(! $this->is_anonymous, $this->reporter_email),
            'district' => $this->whenLoaded('district', fn () => $this->simpleLookup($this->district)),
            'village' => $this->whenLoaded('village', fn () => $this->simpleLookup($this->village)),
            'workflow' => $this->whenLoaded('workflow', fn () => $this->simpleLookup($this->workflow)),
            'current_node' => $this->whenLoaded('currentNode', fn () => $this->simpleLookup($this->currentNode)),
            'attachments' => $this->whenLoaded('attachments', fn () => $this->attachments->map(fn ($attachment) => [
                'id' => $attachment->id,
                'original_name' => $attachment->original_name,
                'mime_type' => $attachment->mime_type,
                'size' => $attachment->size,
                'is_public' => $attachment->is_public,
                'created_at' => $attachment->created_at?->toISOString(),
            ])),
        ]);
    }
}
