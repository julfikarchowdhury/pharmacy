<?php

namespace App\Http\Requests;

use App\Traits\HandlesValidationErrors;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MedicineRequest extends FormRequest
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
        $medicineId = $this->medicine?->id;
        $removedImage = empty(json_decode($this->removed_images));

        return [
            'user_id' => 'required|exists:users,id',
            'name_en' => [
                'required',
                'string',
                'max:255',
                Rule::unique('medicines', 'name_en')
                    ->where('medicine_company_id', $this->medicine_company_id)
                    ->where('medicine_generic_id', $this->medicine_generic_id)
                    ->where('concentration_id', $this->concentration_id)
                    ->ignore($medicineId)
            ],
            'name_bn' => [
                'required',
                'string',
                'max:255',
                Rule::unique('medicines', 'name_bn')
                    ->where('medicine_company_id', $this->medicine_company_id)
                    ->where('medicine_generic_id', $this->medicine_generic_id)
                    ->where('concentration_id', $this->concentration_id)
                    ->ignore($medicineId)
            ],
            'description_en' => 'required|string',
            'description_bn' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'concentration_id' => 'required|exists:concentrations,id',
            'medicine_company_id' => 'required|exists:medicine_companies,id',
            'medicine_generic_id' => 'required|exists:medicine_generics,id',
            'unit_price' => 'required|numeric|min:0',
            'strip_price' => 'required|numeric|min:0',
            'status' => 'in:active,inactive',
            'images' => $medicineId && $removedImage ? 'nullable|array' : 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'units' => 'required|array',
            'units.*' => 'required|exists:units,id',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'The :attribute is required.',
            'exists' => 'The selected :attribute is invalid.',
            'string' => 'The :attribute must be a string.',
            'max' => 'The :attribute must not exceed :max characters.',
            'min' => 'The :attribute must be at least :min characters.',
            'numeric' => 'The :attribute must be a valid number.',
            'mimes' => 'The :attribute must be a file of type: :values.',
            'image' => 'The :attribute must be an image.',
            'array' => 'The :attribute must be an array.',
            'in' => 'The :attribute must be one of the following values: :values.',
        ];
    }

}
