<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        $departments = [
            'Computer Science',
            'Information Technology',
            'Business Administration',
            'Education',
            'Engineering',
            'Mathematics',
        ];

        $department = fake()->randomElement($departments);
        $departmentCode = collect(explode(' ', $department))
            ->map(fn (string $word): string => strtoupper($word[0]))
            ->join('');

        return [
            'code' => $departmentCode.fake()->unique()->numberBetween(100, 499),
            'title' => fake()->sentence(3),
            'department' => $department,
            'description' => fake()->sentence(12),
            'credits' => fake()->numberBetween(2, 5),
            'semester' => fake()->randomElement(['first', 'second', 'summer']),
            'capacity' => fake()->numberBetween(25, 80),
            'is_active' => fake()->boolean(90),
        ];
    }
}
