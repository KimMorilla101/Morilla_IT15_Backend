<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use App\Models\Course;
use App\Models\SchoolDay;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_generates_required_enrollment_data(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertGreaterThanOrEqual(500, Student::query()->count());
        $this->assertSame(0, Student::query()
            ->where(function ($query): void {
                $query
                    ->whereNull('gender')
                    ->orWhereNull('date_of_birth')
                    ->orWhereNull('department')
                    ->orWhereNull('year_level')
                    ->orWhereNull('phone_number')
                    ->orWhereNull('address')
                    ->orWhereNull('status');
            })
            ->count());
        $this->assertSame(0, Student::query()->whereNotIn('gender', ['male', 'female'])->count());

        $this->assertGreaterThanOrEqual(20, Course::query()->count());
        $this->assertGreaterThanOrEqual(4, Course::query()->distinct('department')->count('department'));

        $this->assertGreaterThan(0, SchoolDay::query()->count());
        $this->assertTrue(
            SchoolDay::query()
                ->where('type', 'class')
                ->whereNotNull('attendance_rate')
                ->where('is_school_open', true)
                ->exists()
        );
        $this->assertTrue(
            SchoolDay::query()
                ->where('type', 'event')
                ->whereNotNull('attendance_rate')
                ->where('is_school_open', true)
                ->exists()
        );
        $this->assertTrue(
            SchoolDay::query()
                ->where('type', 'holiday')
                ->whereNull('attendance_rate')
                ->where('is_school_open', false)
                ->exists()
        );
    }
}
