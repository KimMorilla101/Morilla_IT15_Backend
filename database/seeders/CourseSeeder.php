<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            ['code' => 'CS101', 'title' => 'Introduction to Computing', 'department' => 'Computer Science', 'credits' => 3, 'semester' => 'first'],
            ['code' => 'CS201', 'title' => 'Data Structures', 'department' => 'Computer Science', 'credits' => 3, 'semester' => 'second'],
            ['code' => 'CS301', 'title' => 'Database Systems', 'department' => 'Computer Science', 'credits' => 4, 'semester' => 'first'],
            ['code' => 'CS401', 'title' => 'Software Engineering', 'department' => 'Computer Science', 'credits' => 4, 'semester' => 'second'],
            ['code' => 'IT101', 'title' => 'Fundamentals of Networking', 'department' => 'Information Technology', 'credits' => 3, 'semester' => 'first'],
            ['code' => 'IT202', 'title' => 'Web Application Development', 'department' => 'Information Technology', 'credits' => 4, 'semester' => 'second'],
            ['code' => 'IT303', 'title' => 'Cloud Infrastructure', 'department' => 'Information Technology', 'credits' => 3, 'semester' => 'first'],
            ['code' => 'IT404', 'title' => 'Cybersecurity Operations', 'department' => 'Information Technology', 'credits' => 4, 'semester' => 'second'],
            ['code' => 'BA101', 'title' => 'Principles of Management', 'department' => 'Business Administration', 'credits' => 3, 'semester' => 'first'],
            ['code' => 'BA202', 'title' => 'Marketing Strategy', 'department' => 'Business Administration', 'credits' => 3, 'semester' => 'second'],
            ['code' => 'BA303', 'title' => 'Financial Management', 'department' => 'Business Administration', 'credits' => 3, 'semester' => 'first'],
            ['code' => 'BA404', 'title' => 'Business Analytics', 'department' => 'Business Administration', 'credits' => 4, 'semester' => 'second'],
            ['code' => 'ED101', 'title' => 'Foundations of Education', 'department' => 'Education', 'credits' => 3, 'semester' => 'first'],
            ['code' => 'ED202', 'title' => 'Assessment and Evaluation', 'department' => 'Education', 'credits' => 3, 'semester' => 'second'],
            ['code' => 'ED303', 'title' => 'Curriculum Development', 'department' => 'Education', 'credits' => 4, 'semester' => 'first'],
            ['code' => 'ED404', 'title' => 'Classroom Research', 'department' => 'Education', 'credits' => 3, 'semester' => 'second'],
            ['code' => 'MTH101', 'title' => 'College Algebra', 'department' => 'Mathematics', 'credits' => 3, 'semester' => 'first'],
            ['code' => 'MTH202', 'title' => 'Statistics and Probability', 'department' => 'Mathematics', 'credits' => 3, 'semester' => 'second'],
            ['code' => 'ENG201', 'title' => 'Engineering Drawing', 'department' => 'Engineering', 'credits' => 3, 'semester' => 'first'],
            ['code' => 'ENG302', 'title' => 'Project Management for Engineers', 'department' => 'Engineering', 'credits' => 4, 'semester' => 'second'],
        ];

        foreach ($courses as $index => $course) {
            Course::query()->updateOrCreate([
                'code' => $course['code'],
            ], [
                'title' => $course['title'],
                'department' => $course['department'],
                'credits' => $course['credits'],
                'semester' => $course['semester'],
                'description' => sprintf('%s for %s students.', $course['title'], $course['department']),
                'capacity' => 30 + ($index % 5) * 10,
                'is_active' => true,
            ]);
        }
    }
}
