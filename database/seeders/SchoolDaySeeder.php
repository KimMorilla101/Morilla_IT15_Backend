<?php

namespace Database\Seeders;

use App\Models\SchoolDay;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;

class SchoolDaySeeder extends Seeder
{
    public function run(): void
    {
        $holidayTitles = [
            'Founders Day',
            'Midterm Break',
            'National Holiday',
            'Semester Recess',
        ];

        $eventTitles = [
            'Orientation Program',
            'Research Colloquium',
            'Career Development Day',
            'Student Leadership Summit',
            'Community Outreach Program',
        ];

        $startDate = CarbonImmutable::now()->startOfYear()->addWeek();
        $endDate = $startDate->addMonths(5)->endOfMonth();
        $instructionalDay = 0;
        $holidayIndex = 0;
        $eventIndex = 0;

        foreach (CarbonPeriod::create($startDate, $endDate) as $date) {
            if ($date->isWeekend()) {
                continue;
            }

            $instructionalDay++;
            $dayDate = $date->toDateString();

            if ($instructionalDay % 18 === 0) {
                $title = $holidayTitles[$holidayIndex % count($holidayTitles)];
                $holidayIndex++;

                SchoolDay::query()->updateOrCreate([
                    'date' => $dayDate,
                ], [
                    'title' => $title,
                    'type' => 'holiday',
                    'description' => $title.' is marked as a non-instructional holiday on the academic calendar.',
                    'attendance_rate' => null,
                    'is_school_open' => false,
                ]);

                continue;
            }

            if ($instructionalDay % 9 === 0) {
                $title = $eventTitles[$eventIndex % count($eventTitles)];
                $eventIndex++;

                SchoolDay::query()->updateOrCreate([
                    'date' => $dayDate,
                ], [
                    'title' => $title,
                    'type' => 'event',
                    'description' => $title.' is scheduled as a special academic event day.',
                    'attendance_rate' => fake()->randomFloat(2, 72, 96),
                    'is_school_open' => true,
                ]);

                continue;
            }

            SchoolDay::query()->updateOrCreate([
                'date' => $dayDate,
            ], [
                'title' => 'Regular Class Day',
                'type' => 'class',
                'description' => 'Standard instructional day for enrolled students.',
                'attendance_rate' => fake()->randomFloat(2, 80, 99),
                'is_school_open' => true,
            ]);
        }
    }
}
