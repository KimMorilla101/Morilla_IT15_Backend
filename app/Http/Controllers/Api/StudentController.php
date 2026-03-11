<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StudentRequest;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'department' => ['nullable', 'string', 'max:100'],
            'year_level' => ['nullable', 'integer', 'between:1,6'],
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'per_page' => ['nullable', 'integer', 'between:1,100'],
        ]);

        $students = Student::query()
            ->with(['courses:id,code,title,department'])
            ->withCount('courses')
            ->when($validated['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($studentQuery) use ($search): void {
                    $studentQuery
                        ->where('student_number', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($validated['department'] ?? null, function ($query, string $department): void {
                $query->where('department', $department);
            })
            ->when($validated['year_level'] ?? null, function ($query, int $yearLevel): void {
                $query->where('year_level', $yearLevel);
            })
            ->when($validated['course_id'] ?? null, function ($query, int $courseId): void {
                $query->whereHas('courses', function ($courseQuery) use ($courseId): void {
                    $courseQuery->whereKey($courseId);
                });
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate($validated['per_page'] ?? 15)
            ->withQueryString();

        return response()->json($students);
    }

    public function store(StudentRequest $request): JsonResponse
    {
        $data = $request->validated();
        $courseIds = $data['course_ids'] ?? [];
        unset($data['course_ids']);

        $student = Student::query()->create($data);
        $student->courses()->sync($courseIds);

        return response()->json([
            'message' => 'Student created successfully.',
            'student' => $student->load('courses')->loadCount('courses'),
        ], 201);
    }

    public function show(Student $student): JsonResponse
    {
        return response()->json([
            'student' => $student->load('courses')->loadCount('courses'),
        ]);
    }

    public function update(StudentRequest $request, Student $student): JsonResponse
    {
        $data = $request->validated();
        $courseIds = $data['course_ids'] ?? null;
        unset($data['course_ids']);

        $student->update($data);

        if ($courseIds !== null) {
            $student->courses()->sync($courseIds);
        }

        return response()->json([
            'message' => 'Student updated successfully.',
            'student' => $student->fresh()->load('courses')->loadCount('courses'),
        ]);
    }

    public function destroy(Student $student): JsonResponse
    {
        $student->delete();

        return response()->json([
            'message' => 'Student deleted successfully.',
        ]);
    }
}
