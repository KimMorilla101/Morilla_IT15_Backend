<?php

namespace App\Http\Requests\Api;

use Illuminate\Validation\Rule;

class StudentRequest extends SanitizedFormRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $studentId = $this->route('student')?->id;
        $required = $this->isMethod('post') ? ['required'] : ['sometimes'];

        return [
            'student_number' => [
                ...$required,
                'string',
                'max:20',
                Rule::unique('students', 'student_number')->ignore($studentId),
            ],
            'first_name' => [...$required, 'string', 'max:100'],
            'last_name' => [...$required, 'string', 'max:100'],
            'email' => [
                ...$required,
                'email',
                'max:255',
                Rule::unique('students', 'email')->ignore($studentId),
            ],
            'gender' => [...$required, Rule::in(['male', 'female'])],
            'date_of_birth' => [...$required, 'date', 'before:today'],
            'department' => [...$required, 'string', 'max:100'],
            'year_level' => [...$required, 'integer', 'between:1,6'],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'status' => [...$required, Rule::in(['active', 'inactive', 'graduated', 'leave_of_absence'])],
            'course_ids' => ['nullable', 'array'],
            'course_ids.*' => ['integer', Rule::exists('courses', 'id')],
        ];
    }
}
