<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MedicineGenericRequest extends FormRequest
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
        $medicineGenericId = $this->medicine_generic?->id;
        return [
            'title_bn' => 'required|string|max:255|unique:medicine_generics,title_bn' . ($medicineGenericId ? ",{$medicineGenericId}" : ''),
            'title_en' => 'required|string|max:255|unique:medicine_generics,title_en' . ($medicineGenericId ? ",{$medicineGenericId}" : ''),
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
            'title_bn.required' => 'The Bangla name is required.',
            'title_bn.unique' => 'The Bangla name must be unique.',
            'title_en.required' => 'The English name is required.',
            'title_en.unique' => 'The English name must be unique.'
        ];
    }
}
