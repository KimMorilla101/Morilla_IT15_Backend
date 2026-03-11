<?php

namespace App\Http\Requests\Api;

use Illuminate\Validation\Rule;

class SchoolDayRequest extends SanitizedFormRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $schoolDayId = $this->route('school_day')?->id;
        $required = $this->isMethod('post') ? ['required'] : ['sometimes'];

        return [
            'date' => [
                ...$required,
                'date',
                Rule::unique('school_days', 'date')->ignore($schoolDayId),
            ],
            'title' => [...$required, 'string', 'max:150'],
            'type' => [...$required, Rule::in(['class', 'holiday', 'event'])],
            'description' => ['nullable', 'string', 'max:1000'],
            'attendance_rate' => ['nullable', 'numeric', 'between:0,100'],
            'is_school_open' => [...$required, 'boolean'],
        ];
    }
}
