<?php

namespace App\Modules\Administration\Http\Requests;

use App\Modules\Administration\Models\AuditLog;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateAuditLogRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return $this->user()->can('create', AuditLog::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'action' => ['required', 'string', Rule::in(['created', 'updated', 'deleted'])],
            'entity_type' => ['required', 'string', 'max:255'],
            'entity_id' => ['required', 'integer'],
            'old_value' => ['nullable', 'json'],
            'new_value' => ['nullable', 'json'],
            'ip_address' => ['nullable', 'ip'],
            'user_agent' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array {
        return [
            'user_id.required' => 'The user ID is required.',
            'user_id.exists' => 'The specified user does not exist.',
            'action.required' => 'The action type is required.',
            'action.in' => 'The action must be one of: created, updated, or deleted.',
            'entity_type.required' => 'The entity type is required.',
            'entity_id.required' => 'The entity ID is required.',
            'old_value.json' => 'The old value must be valid JSON.',
            'new_value.json' => 'The new value must be valid JSON.',
            'ip_address.ip' => 'Please provide a valid IP address.',
        ];
    }
}
