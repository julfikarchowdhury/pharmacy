<?php

namespace App\Http\Requests;

use App\Traits\HandlesValidationErrors;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegistrationRequest extends FormRequest
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
    public function rules()
    {
        return [
            'name' => 'required|string',
            'phone' => 'required|string|unique:users,phone|max:15',
            'email' => 'required|email|unique:users,email|max:255',
            'address' => 'required|string|max:255',
            'lat' => 'nullable|numeric',
            'long' => 'nullable|numeric',
            'status' => 'in:active,inactive',
            'password' => 'required|min:6|confirmed',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'required' => 'The :attribute is required.',
            'unique' => 'The :attribute has already been taken.',
            'max' => 'The :attribute must not exceed :max characters.',
            'min' => 'The :attribute must be at least :min characters.',
            'confirmed' => 'The :attribute confirmation does not match.',
            'email' => 'The :attribute must be a valid email address.',
            'in' => 'The :attribute must be one of the following values: :values.',
            'numeric' => 'The :attribute must be a valid number.',
            'image' => 'The :attribute must be an image.',
            'mimes' => 'The :attribute must be a file of type: :values.',
        ];
    }


}
