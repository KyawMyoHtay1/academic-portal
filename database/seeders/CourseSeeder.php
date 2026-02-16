<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Course::create([
            'course_code' => 'COMP101',
            'title' => 'Introduction to Computing',
            'credits' => 3,
            'semester' => 'Semester 1',
        ]);

        Course::create([
            'course_code' => 'COMP102',
            'title' => 'Database Systems',
            'credits' => 4,
            'semester' => 'Semester 1',
        ]);

        Course::create([
            'course_code' => 'COMP201',
            'title' => 'Web Development',
            'credits' => 4,
            'semester' => 'Semester 2',
        ]);
    }
}
