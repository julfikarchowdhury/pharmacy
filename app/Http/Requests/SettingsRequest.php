<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingsRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'app_name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'currency_code' => 'required|string|max:3',
            'currency_icon' => 'nullable|string|max:255',
            'points_conversion' => 'required|numeric',
        ];
    }
    /**
     * Get the custom validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'app_name.required' => 'The application name is required.',
            'app_name.string' => 'The application name must be a valid string.',
            'logo.image' => 'The application logo must be an image file.',
            'logo.max' => 'The application logo must not exceed 2MB.',
            'currency_code.required' => 'The currency code is required.',
            'currency_code.max' => 'The currency code must be 3 characters.',
            'currency_icon.string' => 'The currency icon must be a valid string.',
            'currency_icon.max' => 'The currency icon must not exceed 255 characters.',
            'points_conversion.required' => 'The point conversion rate is required.',
            'points_conversion.numeric' => 'The point conversion rate must be a valid number.',
        ];
    }
}
