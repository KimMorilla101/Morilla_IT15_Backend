<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CourseRequest;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'department' => ['nullable', 'string', 'max:100'],
            'semester' => ['nullable', 'string', 'in:first,second,summer'],
            'active' => ['nullable', 'boolean'],
            'per_page' => ['nullable', 'integer', 'between:1,100'],
        ]);

        $courses = Course::query()
            ->withCount('students')
            ->when($validated['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($courseQuery) use ($search): void {
                    $courseQuery
                        ->where('code', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($validated['department'] ?? null, function ($query, string $department): void {
                $query->where('department', $department);
            })
            ->when($validated['semester'] ?? null, function ($query, string $semester): void {
                $query->where('semester', $semester);
            })
            ->when(array_key_exists('active', $validated), function ($query) use ($validated): void {
                $query->where('is_active', filter_var($validated['active'], FILTER_VALIDATE_BOOLEAN));
            })
            ->orderBy('department')
            ->orderBy('code')
            ->paginate($validated['per_page'] ?? 15)
            ->withQueryString();

        return response()->json($courses);
    }

    public function store(CourseRequest $request): JsonResponse
    {
        $course = Course::query()->create($request->validated());

        return response()->json([
            'message' => 'Course created successfully.',
            'course' => $course->loadCount('students'),
        ], 201);
    }

    public function show(Course $course): JsonResponse
    {
        return response()->json([
            'course' => $course->loadCount('students')->load([
                'students:id,student_number,first_name,last_name,email,department,year_level',
            ]),
        ]);
    }

    public function update(CourseRequest $request, Course $course): JsonResponse
    {
        $course->update($request->validated());

        return response()->json([
            'message' => 'Course updated successfully.',
            'course' => $course->fresh()->loadCount('students'),
        ]);
    }

    public function destroy(Course $course): JsonResponse
    {
        $course->delete();

        return response()->json([
            'message' => 'Course deleted successfully.',
        ]);
    }
}
