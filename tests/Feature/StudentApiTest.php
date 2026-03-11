<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_creation_sanitizes_and_persists_expected_data(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();
        $token = $user->createToken('test-suite')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/students', [
                'student_number' => '  STU900001  ',
                'first_name' => '  Maria  ',
                'last_name' => '  Santos  ',
                'email' => '  maria.santos@example.com  ',
                'gender' => 'female',
                'date_of_birth' => '2005-04-10',
                'department' => '  Computer Science  ',
                'year_level' => 3,
                'phone_number' => '  09171234567  ',
                'address' => '  Quezon City  ',
                'status' => 'active',
                'course_ids' => [$course->id],
            ]);

        $response
            ->assertCreated()
            ->assertJsonPath('student.student_number', 'STU900001')
            ->assertJsonPath('student.first_name', 'Maria')
            ->assertJsonPath('student.last_name', 'Santos')
            ->assertJsonPath('student.email', 'maria.santos@example.com')
            ->assertJsonPath('student.department', 'Computer Science')
            ->assertJsonPath('student.courses_count', 1);

        $this->assertDatabaseHas('students', [
            'student_number' => 'STU900001',
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'email' => 'maria.santos@example.com',
            'department' => 'Computer Science',
        ]);
    }
}
