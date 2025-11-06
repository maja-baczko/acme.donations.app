<?php

namespace App\Modules\Donation\Http\Requests;

use App\Modules\Donation\Models\Donation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateDonationRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return $this->user()->can('create', Donation::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array {
        return [
            'campaign_id' => ['required', 'integer', 'exists:campaigns,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'status' => ['required', 'string', Rule::in(['pending', 'completed', 'failed'])],
            'payment_method' => ['nullable', 'string', 'max:255'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'is_anonymous' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array {
        return [
            'campaign_id.required' => 'Please select a campaign to donate to.',
            'campaign_id.exists' => 'The selected campaign does not exist.',
            'amount.required' => 'Please enter a donation amount.',
            'amount.min' => 'The minimum donation amount is $0.01.',
            'comment.max' => 'Your comment cannot exceed 1000 characters.',
        ];
    }
}
