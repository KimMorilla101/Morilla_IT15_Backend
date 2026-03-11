<?php

namespace App\Http\Requests\Api;

use Illuminate\Validation\Rule;

class CourseRequest extends SanitizedFormRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $courseId = $this->route('course')?->id;
        $required = $this->isMethod('post') ? ['required'] : ['sometimes'];

        return [
            'code' => [
                ...$required,
                'string',
                'max:20',
                Rule::unique('courses', 'code')->ignore($courseId),
            ],
            'title' => [...$required, 'string', 'max:150'],
            'department' => [...$required, 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'credits' => [...$required, 'integer', 'between:1,6'],
            'semester' => [...$required, Rule::in(['first', 'second', 'summer'])],
            'capacity' => [...$required, 'integer', 'between:10,500'],
            'is_active' => [...$required, 'boolean'],
        ];
    }
}
