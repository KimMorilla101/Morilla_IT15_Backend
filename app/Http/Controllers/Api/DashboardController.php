<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\SchoolDay;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $averageAttendance = round((float) SchoolDay::query()
            ->whereNotNull('attendance_rate')
            ->avg('attendance_rate'), 2);

        $monthlyAttendance = SchoolDay::query()
            ->whereNotNull('attendance_rate')
            ->orderBy('date')
            ->get()
            ->groupBy(fn (SchoolDay $schoolDay): string => $schoolDay->date->format('Y-m'))
            ->map(function (Collection $schoolDays, string $month): array {
                return [
                    'month' => $month,
                    'average_attendance' => round((float) $schoolDays->avg('attendance_rate'), 2),
                ];
            })
            ->values();

        return response()->json([
            'overview' => [
                'students_enrolled' => Student::query()->count(),
                'courses_offered' => Course::query()->count(),
                'school_days_total' => SchoolDay::query()->count(),
                'average_attendance' => $averageAttendance,
            ],
            'students_by_gender' => $this->formatGroupedData(
                Student::query()
                    ->selectRaw('gender as label, COUNT(*) as value')
                    ->groupBy('gender')
                    ->orderBy('gender')
                    ->get()
            ),
            'students_by_department' => $this->formatGroupedData(
                Student::query()
                    ->selectRaw('department as label, COUNT(*) as value')
                    ->groupBy('department')
                    ->orderByDesc('value')
                    ->get()
            ),
            'students_by_year_level' => $this->formatGroupedData(
                Student::query()
                    ->selectRaw('year_level as label, COUNT(*) as value')
                    ->groupBy('year_level')
                    ->orderBy('year_level')
                    ->get(),
                prefix: 'Year '
            ),
            'courses_by_department' => $this->formatGroupedData(
                Course::query()
                    ->selectRaw('department as label, COUNT(*) as value')
                    ->groupBy('department')
                    ->orderByDesc('value')
                    ->get()
            ),
            'top_courses' => Course::query()
                ->withCount('students')
                ->orderByDesc('students_count')
                ->orderBy('code')
                ->take(5)
                ->get(['id', 'code', 'title', 'department']),
            'school_days_by_type' => $this->formatGroupedData(
                SchoolDay::query()
                    ->selectRaw('type as label, COUNT(*) as value')
                    ->groupBy('type')
                    ->orderBy('type')
                    ->get()
            ),
            'monthly_attendance' => $monthlyAttendance,
            'recent_calendar' => SchoolDay::query()
                ->orderByDesc('date')
                ->take(10)
                ->get(['id', 'date', 'title', 'type', 'attendance_rate', 'is_school_open']),
        ]);
    }

    /**
     * @param  Collection<int, object>  $rows
     * @return Collection<int, array{label: string, value: int}>
     */
    private function formatGroupedData(Collection $rows, string $prefix = ''): Collection
    {
        return $rows->map(function (object $row) use ($prefix): array {
            return [
                'label' => $prefix.(string) $row->label,
                'value' => (int) $row->value,
            ];
        })->values();
    }
}
