<?php

namespace App\Http\Requests;

use App\Traits\HandlesValidationErrors;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    use HandlesValidationErrors;

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
            'name' => 'nullable|string|max:255|unique:users,name,' . $this->user()->id,
            'phone' => 'nullable|string|max:15|unique:users,phone,' . $this->user()->id,
            'email' => 'nullable|email|max:255|unique:users,email,' . $this->user()->id,
            'address' => 'nullable|string|max:255',
            'lat' => 'nullable|numeric',
            'long' => 'nullable|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'nullable' => 'The :attribute is optional.',
            'unique' => 'The :attribute has already been taken.',
            'max' => 'The :attribute must not exceed :max characters.',
            'email' => 'The :attribute must be a valid email address.',
            'numeric' => 'The :attribute must be a valid number.',
            'image' => 'The :attribute must be an image.',
            'mimes' => 'The :attribute must be a file of type: :values.',
        ];
    }
}
