<?php

namespace App\Modules\Administration\Http\Requests;

use App\Modules\Administration\Models\SystemSetting;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateSystemSettingRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return $this->user()->can('create', SystemSetting::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array {
        return [
            'key' => ['required', 'string', 'max:255', 'unique:system_settings,key'],
            'value' => ['required', 'string'],
            'type' => ['required', 'string', Rule::in(['string', 'integer', 'boolean', 'json'])],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_public' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array {
        return [
            'key.required' => 'The setting key is required.',
            'key.unique' => 'This setting key already exists.',
            'value.required' => 'The setting value is required.',
            'type.required' => 'The setting type is required.',
            'type.in' => 'The type must be one of: string, integer, boolean, or json.',
        ];
    }
}
