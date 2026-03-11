<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

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

        return [
            'student_number' => 'STU'.fake()->unique()->numerify('######'),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'gender' => fake()->randomElement(['male', 'female', 'non-binary', 'prefer_not_to_say']),
            'date_of_birth' => fake()->dateTimeBetween('-24 years', '-16 years'),
            'department' => fake()->randomElement($departments),
            'year_level' => fake()->numberBetween(1, 4),
            'phone_number' => fake()->numerify('09#########'),
            'address' => fake()->streetAddress().', '.fake()->city(),
            'status' => fake()->randomElement(['active', 'active', 'active', 'inactive', 'graduated', 'leave_of_absence']),
        ];
    }
}
