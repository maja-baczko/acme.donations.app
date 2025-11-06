<?php

namespace App\Modules\Donation\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDonationRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return $this->user()->can('update', $this->route('donation'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array {
        return [
            'campaign_id' => ['sometimes', 'integer', 'exists:campaigns,id'],
            'amount' => ['sometimes', 'numeric', 'min:0.01'],
            'status' => ['sometimes', 'string', Rule::in(['pending', 'completed', 'failed'])],
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
            'campaign_id.exists' => 'The selected campaign does not exist.',
            'amount.min' => 'The minimum donation amount is $0.01.',
            'comment.max' => 'Your comment cannot exceed 1000 characters.',
        ];
    }
}
