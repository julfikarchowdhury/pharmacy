<?php

namespace App\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

trait HandlesValidationErrors
{
    /**
     * Handle failed validation for API requests.
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        $formattedErrors = [];
        foreach ($errors->messages() as $field => $messages) {
            $formattedErrors[$field] = count($messages) > 1 ? $messages : $messages[0];
        }

        if ($this->expectsJson()) {
            throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $formattedErrors,
            ], 422));
        }

        parent::failedValidation($validator);
    }

}
