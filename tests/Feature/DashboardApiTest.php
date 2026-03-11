<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\SchoolDay;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_endpoint_returns_visualization_payload(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-suite')->plainTextToken;

        $course = Course::factory()->create([
            'department' => 'Information Technology',
            'code' => 'IT250',
            'title' => 'API Integration',
        ]);

        $student = Student::factory()->create([
            'department' => 'Information Technology',
            'gender' => 'female',
            'year_level' => 2,
        ]);
        $student->courses()->attach($course->id);

        SchoolDay::factory()->create([
            'type' => 'class',
            'attendance_rate' => 93.5,
            'is_school_open' => true,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/dashboard');

        $response
            ->assertOk()
            ->assertJsonPath('overview.students_enrolled', 1)
            ->assertJsonPath('overview.courses_offered', 1)
            ->assertJsonStructure([
                'overview' => [
                    'students_enrolled',
                    'courses_offered',
                    'school_days_total',
                    'average_attendance',
                ],
                'students_by_gender',
                'students_by_department',
                'students_by_year_level',
                'courses_by_department',
                'top_courses',
                'school_days_by_type',
                'monthly_attendance',
                'recent_calendar',
            ]);
    }
}
