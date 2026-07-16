<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['required', 'uuid', 'exists:complaint_categories,id'],
            'sub_category_id' => ['nullable', 'uuid', 'exists:complaint_sub_categories,id'],
            'priority_id' => ['nullable', 'uuid', 'exists:priorities,id'],
            'district_id' => ['nullable', 'uuid', 'exists:districts,id'],
            'village_id' => ['nullable', 'uuid', 'exists:villages,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'address' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'reporter_name' => ['nullable', 'string', 'max:255'],
            'reporter_phone' => ['nullable', 'string', 'max:30'],
            'reporter_email' => ['nullable', 'email', 'max:255'],
            'is_anonymous' => ['sometimes', 'boolean'],
        ];
    }
}
