<?php

namespace App\Modules\Payment\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePaymentRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return $this->user()->can('update', $this->route('payment'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array {
        return [
            'donation_id' => ['sometimes', 'integer', 'exists:donations,id'],
            'amount' => ['sometimes', 'numeric', 'min:0.01'],
            'status' => ['sometimes', 'string', Rule::in(['processing', 'completed', 'failed'])],
            'gateway' => ['sometimes', 'string', Rule::in(['stripe', 'paypal', 'mock'])],
            'transaction_reference' => ['required', 'string', 'max:255'],
            'metadata' => ['nullable', 'json'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array {
        return [
            'donation_id.exists' => 'The specified donation does not exist.',
            'amount.min' => 'The minimum payment amount is $0.01.',
            'status.in' => 'The status must be one of: processing, completed, or failed.',
            'gateway.in' => 'The gateway must be one of: stripe, paypal, or mock.',
            'transaction_reference.required' => 'The transaction reference is required for payment updates.',
            'metadata.json' => 'The metadata must be valid JSON.',
        ];
    }
}
