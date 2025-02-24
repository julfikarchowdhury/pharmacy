<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
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
        return [
            'customer_id' => 'required|exists:users,id',
            'total' => 'required|numeric',
            'delivery_address' => 'required|string',
            'delivery_lat' => 'required|numeric',
            'delivery_long' => 'required|numeric',
            'pharmacy_id' => 'required|exists:pharmacies,id',
            'date' => 'required|date',
            'order_details' => 'required|array',
            'order_details.*.medicine_id' => 'required|exists:medicines,id',
            'order_details.*.unit_id' => 'required|exists:units,id',
            'order_details.*.qty' => 'required|integer|min:1',
            'order_details.*.price' => 'required|numeric|min:0',
            'order_details.*.discounted_price' => 'required|numeric|min:0',
        ];
    }

    /**
     * Custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'required' => 'The :attribute field is required.',
            'numeric' => 'The :attribute must be a valid number.',
            'date' => 'The :attribute must be a valid date.',
            'string' => 'The :attribute must be a valid text string.',
            'integer' => 'The :attribute must be a valid integer.',
            'min' => 'The :attribute must be at least :min.',
            'exists' => 'The selected :attribute is invalid.',
            'array' => 'The :attribute must be an array.',
        ];
    }
}
