<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $targetStudentCount = 500;
        $currentStudentCount = Student::query()->count();
        $missingStudents = max(0, $targetStudentCount - $currentStudentCount);

        if ($missingStudents > 0) {
            Student::factory($missingStudents)->create();
        }

        $courseIds = Course::query()->pluck('id');

        if ($courseIds->isEmpty()) {
            return;
        }

        Student::query()
            ->doesntHave('courses')
            ->chunkById(100, function ($students) use ($courseIds): void {
                foreach ($students as $student) {
                    $student->courses()->sync(
                        $courseIds
                            ->shuffle()
                            ->take(fake()->numberBetween(1, min(5, $courseIds->count())))
                            ->values()
                            ->all()
                    );
                }
            });
    }
}
