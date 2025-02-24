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
            'pharmacy_id' => 'required|exists:pharmacies,id',
            'order_type' => 'required|in:manual,direct',
            'total' => 'required|numeric|min:0',
            'sub_total' => 'required|numeric|min:0',
            'delivery_address' => 'required|string|max:255',
            'delivery_lat' => 'required|numeric',
            'delivery_long' => 'required|numeric',
            'discount_by_points' => 'nullable|numeric|min:0',
            'pharmacy_discount' => 'nullable|numeric|min:0',
            'delivery_charge' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'date' => 'required|date',
            // 'payment_type' => 'required|in:cod,bkash,nagad',
            // 'payment_status' => 'required|in:paid,due',
            'note' => 'nullable|string' . ($this->order_type === 'manual' ? '|required' : ''),
            'prescription' => ($this->order_type === 'manual' ? 'required|' : 'nullable|') . 'image|mimes:jpeg,png,jpg,gif|max:2048',

            // Order details is required only if order_type is 'direct', nullable otherwise
            'order_details' => $this->order_type === 'manual' ? 'nullable|array' : 'required|array|min:1',
            'order_details.*.medicine_id' => $this->order_type === 'manual' ? 'nullable' : 'required|exists:medicines,id',
            'order_details.*.unit_id' => $this->order_type === 'manual' ? 'nullable' : 'required|exists:units,id',
            'order_details.*.qty' => $this->order_type === 'manual' ? 'nullable' : 'required|integer|min:1',
            'order_details.*.price' => $this->order_type === 'manual' ? 'nullable' : 'required|numeric|min:0',
            'order_details.*.discounted_price' => $this->order_type === 'manual' ? 'nullable' : 'required|numeric|min:0',
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
            'max' => 'The :attribute may not be greater than :max characters.',
            'exists' => 'The selected :attribute is invalid.',
            'array' => 'The :attribute must be an array.',
            'in' => 'The :attribute must be one of the following types: :values.',
            'image' => 'The :attribute must be a valid image file.',
            'mimes' => 'The :attribute must be a file of type: :values.',
        ];
    }
}
