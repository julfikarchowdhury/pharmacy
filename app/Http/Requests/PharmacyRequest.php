<?php

namespace App\Http\Requests;

use App\Traits\HandlesValidationErrors;
use Illuminate\Foundation\Http\FormRequest;

class PharmacyRequest extends FormRequest
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
            'pharmacy_name' => 'required|string|max:255|unique:pharmacies,name',
            'pharmacy_address' => 'required|string|max:500',
            'pharmacy_lat' => 'required|numeric',
            'pharmacy_long' => 'required|numeric',
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
    public function messages()
    {
        return [
            'required' => 'The :attribute is required.',
            'unique' => 'The :attribute has already been taken.',
            'max' => 'The :attribute must not exceed :max characters.',
            'numeric' => 'The :attribute must be a valid number.',
            'image' => 'The :attribute must be an image.',
            'mimes' => 'The :attribute must be a file of type: :values.',
        ];
    }
}
