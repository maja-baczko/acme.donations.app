<?php

namespace App\Modules\Media\Http\Requests;

use App\Modules\Media\Models\Image;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateImageRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return $this->user()->can('create', Image::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array {
        return [
            'type' => ['required', 'string', Rule::in(['profile', 'category', 'campaign'])],
            'entity_type' => ['required', 'string', 'max:255'],
            'entity_id' => ['required', 'integer'],
            'file' => ['required', 'file', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
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
            'type.required' => 'Please specify the image type.',
            'type.in' => 'The image type must be one of: profile, category, or campaign.',
            'entity_type.required' => 'The entity type is required.',
            'entity_id.required' => 'The entity ID is required.',
            'file.required' => 'Please select an image file to upload.',
            'file.image' => 'The file must be an image.',
            'file.mimes' => 'The image must be a file of type: jpeg, jpg, png, gif, or webp.',
            'file.max' => 'The image size must not exceed 5MB.',
        ];
    }
}
