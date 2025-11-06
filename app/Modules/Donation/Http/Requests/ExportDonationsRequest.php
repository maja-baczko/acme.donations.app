<?php

namespace App\Modules\Donation\Http\Requests;

use App\Modules\Donation\Models\Donation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExportDonationsRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return $this->user()->can('exportForAccounting', Donation::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array {
        return [
            'format' => ['sometimes', 'string', Rule::in(['csv', 'excel', 'json'])],
            'status' => ['sometimes', 'string', Rule::in(['pending', 'completed', 'failed'])],
            'campaign_id' => ['sometimes', 'integer', 'exists:campaigns,id'],
            'donor_id' => ['sometimes', 'integer', 'exists:users,id'],
            'date_from' => ['sometimes', 'date'],
            'date_to' => ['sometimes', 'date', 'after_or_equal:date_from'],
            'include_anonymous' => ['sometimes', 'boolean'],
            'with_payment_proof' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validation errors.
     *
     * @return array<string, string>
     */
    public function messages(): array {
        return [
            'format.in' => 'Export format must be one of: csv, excel, json',
            'status.in' => 'Status must be one of: pending, completed, failed',
            'campaign_id.exists' => 'The selected campaign does not exist.',
            'donor_id.exists' => 'The selected donor does not exist.',
            'date_to.after_or_equal' => 'End date must be after or equal to start date.',
        ];
    }

    /**
     * Get the default values for optional parameters
     */
    public function validated($key = null, $default = null): array {
        $validated = parent::validated();

        // Set defaults
        $validated['format'] = $validated['format'] ?? 'csv';
        $validated['include_anonymous'] = $validated['include_anonymous'] ?? false;
        $validated['with_payment_proof'] = $validated['with_payment_proof'] ?? true;

        return $validated;
    }
}
