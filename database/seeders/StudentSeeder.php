<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 15 students using the factory
        $students = Student::factory()->count(15)->create();

        // Create user accounts for each student
        foreach ($students as $index => $student) {
            User::create([
                'name' => $student->name,
                'email' => $student->email,
                'password' => Hash::make('password'),
                'role' => 'student',
                'student_id' => $student->id,
            ]);

            // Randomly enroll students in 1-4 courses
            $courses = Course::inRandomOrder()->limit(rand(1, 4))->get();
            $student->courses()->attach($courses->pluck('id'));
        }
    }
}
