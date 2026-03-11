<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

abstract class SanitizedFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge($this->sanitizeInput($this->all()));
    }

    /**
     * Recursively trim string input so controllers only handle clean data.
     *
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    protected function sanitizeInput(array $input): array
    {
        $sanitized = [];

        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeInput($value);
                continue;
            }

            if (! is_string($value)) {
                $sanitized[$key] = $value;
                continue;
            }

            $trimmedValue = trim($value);
            $sanitized[$key] = $trimmedValue === '' ? null : $trimmedValue;
        }

        return $sanitized;
    }
}
