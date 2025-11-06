<?php

namespace App\Modules\Payment\Http\Requests;

use App\Modules\Payment\Models\Payment;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePaymentRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return $this->user()->can('create', Payment::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array {
        return [
            'donation_id' => ['required', 'integer', 'exists:donations,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'status' => ['sometimes', 'string', Rule::in(['processing', 'completed', 'failed'])],
            'gateway' => ['required', 'string', Rule::in(['stripe', 'paypal', 'mock'])],
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
            'donation_id.required' => 'The donation ID is required.',
            'donation_id.exists' => 'The specified donation does not exist.',
            'amount.required' => 'The payment amount is required.',
            'amount.min' => 'The minimum payment amount is $0.01.',
            'status.required' => 'The payment status is required.',
            'status.in' => 'The status must be one of: processing, completed, or failed.',
            'gateway.required' => 'The payment gateway is required.',
            'gateway.in' => 'The gateway must be one of: stripe, paypal, or mock.',
            'metadata.json' => 'The metadata must be valid JSON.',
        ];
    }
}
