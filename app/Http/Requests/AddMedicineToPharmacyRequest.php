<?php

namespace App\Http\Requests;

use App\Traits\HandlesValidationErrors;
use Illuminate\Foundation\Http\FormRequest;

class AddMedicineToPharmacyRequest extends FormRequest
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
            'medicines' => 'required|array',
            'medicines.*.*' => 'required|numeric|min:0|max:100',
        ];
    }

    public function messages()
    {
        return [
            'required' => 'The :attribute is required.',
            'max' => 'The :attribute must not exceed :max characters.',
            'numeric' => 'The :attribute must be a valid number.',
            'array' => 'The :attribute must be an array.',
        ];
    }
}
