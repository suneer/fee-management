<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            [
                'name' => 'Web Development Fundamentals',
                'duration' => 6,
                'fee_per_month' => 5000.00,
            ],
            [
                'name' => 'Advanced JavaScript',
                'duration' => 4,
                'fee_per_month' => 6000.00,
            ],
            [
                'name' => 'React & Modern Frontend',
                'duration' => 5,
                'fee_per_month' => 7000.00,
            ],
            [
                'name' => 'Laravel & PHP Backend',
                'duration' => 6,
                'fee_per_month' => 6500.00,
            ],
            [
                'name' => 'Python for Data Science',
                'duration' => 8,
                'fee_per_month' => 7500.00,
            ],
            [
                'name' => 'Mobile App Development',
                'duration' => 7,
                'fee_per_month' => 8000.00,
            ],
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }
    }
}
