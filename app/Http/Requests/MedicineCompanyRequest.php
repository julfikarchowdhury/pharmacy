<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MedicineCompanyRequest extends FormRequest
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
        $medicineCompanyId = $this->medicine_company?->id;
        return [
            'name_bn' => 'required|string|max:255|unique:medicine_companies,name_bn' . ($medicineCompanyId ? ",{$medicineCompanyId}" : ''),
            'name_en' => 'required|string|max:255|unique:medicine_companies,name_en' . ($medicineCompanyId ? ",{$medicineCompanyId}" : ''),
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
            'name_bn.required' => 'The Bangla name is required.',
            'name_bn.unique' => 'The Bangla name must be unique.',
            'name_en.required' => 'The English name is required.',
            'name_en.unique' => 'The English name must be unique.'
        ];
    }
}
