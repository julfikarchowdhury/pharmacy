<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'current_password' => 'required|string|min:6|current_password',
            'password' => 'required|string|confirmed|min:6',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'current_password.required' => 'Please enter your current password.',
            'current_password.current_password' => 'The current password is incorrect.',
            'password.required' => 'Please enter a new password.',
            'password.confirmed' => 'The confirmation password does not match.',
            'password.min' => 'The password must be at least 8 characters.',
        ];
    }
}
