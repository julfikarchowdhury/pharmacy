<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SliderRequest extends FormRequest
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
        $sliderId = $this->slider?->id;

        return [
            'title' => 'required|string|max:255|unique:sliders,title' . ($sliderId ? ",{$sliderId}" : ''),
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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
            'required' => 'The :attribute is required.',
            'unique' => 'The :attribute has already been taken.',
            'max' => 'The :attribute must not exceed :max characters.',
            'image' => 'The :attribute must be a valid image file.',
            'in' => 'The :attribute must be either active or inactive.',
        ];
    }
}
