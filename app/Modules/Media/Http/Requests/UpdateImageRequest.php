<?php

namespace App\Modules\Media\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateImageRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return $this->user()->can('update', $this->route('image'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array {
        return [
            'type' => ['sometimes', 'string', Rule::in(['profile', 'category', 'campaign'])],
            'entity_type' => ['sometimes', 'string', 'max:255'],
            'entity_id' => ['sometimes', 'integer'],
            'file' => ['sometimes', 'file', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
            'is_primary' => ['sometimes', 'boolean'],
            'alt_text' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array {
        return [
            'type.in' => 'The image type must be one of: profile, category, or campaign.',
            'file.image' => 'The file must be an image.',
            'file.mimes' => 'The image must be a file of type: jpeg, jpg, png, gif, or webp.',
            'file.max' => 'The image size must not exceed 5MB.',
        ];
    }
}
