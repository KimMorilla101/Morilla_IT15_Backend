<?php

namespace Database\Factories;

use App\Models\SchoolDay;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SchoolDay>
 */
class SchoolDayFactory extends Factory
{
    protected $model = SchoolDay::class;

    public function definition(): array
    {
        $type = fake()->randomElement(['class', 'holiday', 'event']);
        $isSchoolOpen = $type !== 'holiday';

        return [
            'date' => fake()->unique()->dateTimeBetween('-1 month', '+3 months'),
            'title' => match ($type) {
                'holiday' => fake()->randomElement(['Founders Day', 'Community Holiday', 'Midterm Break']),
                'event' => fake()->randomElement(['Research Expo', 'Career Day', 'Student Assembly']),
                default => 'Regular Class Day',
            },
            'type' => $type,
            'description' => fake()->sentence(12),
            'attendance_rate' => $type === 'holiday' ? null : fake()->randomFloat(2, 70, 99),
            'is_school_open' => $isSchoolOpen,
        ];
    }
}
