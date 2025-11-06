<?php

namespace App\Modules\Campaign\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCampaignRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return $this->user()->can('update', $this->route('campaign'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array {
        $campaignId = $this->route('campaign')->id;

        return [
            'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'slug' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('campaigns', 'slug')->ignore($campaignId),
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            ],
            'description' => ['sometimes', 'string'],
            'goal_amount' => ['sometimes', 'numeric', 'min:0.01'],
            'status' => ['sometimes', 'string', Rule::in(['draft', 'active', 'paused', 'completed'])],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date', 'after:start_date'],
            'beneficiary_name' => ['sometimes', 'string', 'max:255'],
            'beneficiary_website' => ['sometimes', 'url', 'max:255'],
            'featured' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array {
        return [
            'category_id.exists' => 'The selected category does not exist.',
            'slug.regex' => 'The slug must be lowercase and contain only letters, numbers, and hyphens.',
            'slug.unique' => 'This slug is already taken. Please choose a different one.',
            'goal_amount.min' => 'The goal amount must be at least $0.01.',
            'end_date.after' => 'The end date must be after the start date.',
            'beneficiary_website.url' => 'Please provide a valid website URL.',
        ];
    }
}
