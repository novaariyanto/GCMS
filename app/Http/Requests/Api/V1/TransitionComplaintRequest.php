<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class TransitionComplaintRequest extends FormRequest
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
            'action_code' => ['required_without:transition_id', 'string', 'max:50'],
            'transition_id' => ['required_without:action_code', 'uuid', 'exists:workflow_transitions,id'],
            'remark' => ['nullable', 'string'],
            'to_user_id' => ['nullable', 'uuid', 'exists:users,id'],
        ];
    }
}
