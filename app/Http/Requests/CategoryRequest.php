<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
       
        $categoryId = $this->category?->id;
        return [
            'name_bn' => 'required|string|max:255|unique:categories,name_bn' . ($categoryId ? ",{$categoryId}" : ''),
            'name_en' => 'required|string|max:255|unique:categories,name_en' . ($categoryId ? ",{$categoryId}" : ''),
            'icon' => $categoryId
                ? 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
                : 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|in:active,inactive',
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
            'name_en.unique' => 'The English name must be unique.',
            'icon.required' => 'The icon field is required.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The status must be either active or inactive.',
        ];
    }
}
