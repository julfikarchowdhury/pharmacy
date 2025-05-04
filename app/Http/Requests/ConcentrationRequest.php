<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConcentrationRequest extends FormRequest
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

        $concentrationId = $this->concentration?->id;
        return [
            'value' => 'required|string|max:255|unique:concentrations,value' . ($concentrationId ? ",{$concentrationId}" : ''),
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'required' => 'The :attribute is required.',
            'unique' => 'The :attribute has already been taken.',
            'max' => 'The :attribute must not exceed :max characters.',
        ];
    }
}
