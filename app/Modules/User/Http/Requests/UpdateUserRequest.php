<?php

namespace App\Modules\User\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return $this->user()->can('update', $this->route('user'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array {
        $userId = $this->route('user')->id;

        return [
            'firstname' => ['sometimes', 'required', 'string', 'max:255'],
            'lastname' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
            'password' => ['sometimes', 'nullable', 'string', Password::min(8)->mixedCase()->numbers(), 'confirmed'],
            'department' => ['sometimes', 'nullable', 'string', 'max:255'],
            'function' => ['sometimes', 'nullable', 'string', 'max:255'],
            'still_working' => ['sometimes', 'nullable', 'boolean'],
            'profile' => ['sometimes', 'nullable', 'integer', 'exists:images,id'],
            'roles' => ['sometimes', 'nullable', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
        ];
    }

    /**
     * Get custom messages for validation errors.
     *
     * @return array<string, string>
     */
    public function messages(): array {
        return [
            'email.unique' => 'This email address is already taken by another user.',
            'password.confirmed' => 'Password confirmation does not match.',
            'profile.exists' => 'The selected profile image does not exist.',
        ];
    }
}
