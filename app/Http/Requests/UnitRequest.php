<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnitRequest extends FormRequest
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
        $unitId = $this->unit?->id;
        return [
            'value' => 'required|string|max:255|unique:units,value' . ($unitId ? ",{$unitId}" : ''),
        ];
    }    /**
         * Get custom error messages for validation rules.
         *
         * @return array
         */
    public function messages()
    {
        return [
            'value.required' => 'The value is required.',
            'value.unique' => 'The value must be unique.'
        ];
    }
}
