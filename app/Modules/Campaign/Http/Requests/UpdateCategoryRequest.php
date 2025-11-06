<?php

namespace App\Modules\Campaign\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return $this->user()->can('update', $this->route('category'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array {
        $categoryId = $this->route('category')->id;

        return [
            'name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($categoryId),
            ],
            'slug' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')->ignore($categoryId),
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            ],
            'icon' => ['nullable', 'integer', 'exists:images,id'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array {
        return [
            'name.unique' => 'This category name already exists.',
            'slug.regex' => 'The slug must be lowercase and contain only letters, numbers, and hyphens.',
            'slug.unique' => 'This slug is already taken. Please choose a different one.',
            'icon.exists' => 'The selected icon image does not exist.',
        ];
    }
}
