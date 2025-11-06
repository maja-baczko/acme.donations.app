<?php

namespace App\Modules\Campaign\Http\Requests;

use App\Modules\Campaign\Models\Campaign;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCampaignRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return $this->user()->can('create', Campaign::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:campaigns,slug', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'description' => ['required', 'string'],
            'goal_amount' => ['required', 'numeric', 'min:0.01'],
            'status' => ['required', 'string', Rule::in(['draft', 'active', 'paused', 'completed'])],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'beneficiary_name' => ['required', 'string', 'max:255'],
            'beneficiary_website' => ['required', 'url', 'max:255'],
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
            'category_id.required' => 'Please select a category for your campaign.',
            'category_id.exists' => 'The selected category does not exist.',
            'slug.regex' => 'The slug must be lowercase and contain only letters, numbers, and hyphens.',
            'slug.unique' => 'This slug is already taken. Please choose a different one.',
            'goal_amount.min' => 'The goal amount must be at least $0.01.',
            'start_date.after_or_equal' => 'The campaign cannot start in the past.',
            'end_date.after' => 'The end date must be after the start date.',
            'beneficiary_website.url' => 'Please provide a valid website URL.',
        ];
    }
}
