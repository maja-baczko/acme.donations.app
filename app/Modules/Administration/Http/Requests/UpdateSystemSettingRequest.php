<?php

namespace App\Modules\Administration\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSystemSettingRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return $this->user()->can('update', $this->route('systemSetting'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array {
        $settingId = $this->route('systemSetting')->id;

        return [
            'key' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('system_settings', 'key')->ignore($settingId),
            ],
            'value' => ['sometimes', 'string'],
            'type' => ['sometimes', 'string', Rule::in(['string', 'integer', 'boolean', 'json'])],
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
            'key.unique' => 'This setting key already exists.',
            'type.in' => 'The type must be one of: string, integer, boolean, or json.',
        ];
    }
}
