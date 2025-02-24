<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TipRequest extends FormRequest
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
        $tipId = $this->tip?->id;
        $rules = [
            'type' => 'required|in:gym,health',
            'title_en' => 'required|string|max:255',
            'title_bn' => 'required|string|max:255',
            'instruction_en' => 'required|string',
            'instruction_bn' => 'required|string',
            'status' => 'required|in:active,inactive',
            'image' => $tipId ? 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048' : 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'video' => 'nullable|mimes:mp4,mov,avi,wmv|max:10240'
        ];

        return $rules;
    }

    /**
     * Custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'type.required' => 'The type is required.',
            'type.in' => 'Invalid type selected.',
            'title_en.required' => 'The English title is required.',
            'title_bn.required' => 'The Bengali title is required.',
            'instruction_en.required' => 'The English instruction is required.',
            'instruction_bn.required' => 'The Bengali instruction is required.',
            'status.required' => 'The status is required.',
            'status.in' => 'Invalid status selected.',
            'image.required' => 'The image is required.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'Only JPEG, PNG, JPG, GIF, and WebP images are allowed.',
            'image.max' => 'The image size should not exceed 2MB.',
            'video.mimes' => 'Only MP4, MOV, AVI, and WMV video formats are allowed.',
            'video.max' => 'The video size should not exceed 10MB.',
        ];
    }
}
