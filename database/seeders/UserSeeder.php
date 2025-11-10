<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@feemanagement.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Create some student users with linked student records
        $students = Student::all();
        
        if ($students->count() > 0) {
            // Create user accounts for existing students
            foreach ($students as $index => $student) {
                User::create([
                    'name' => $student->name,
                    'email' => $student->email,
                    'password' => Hash::make('student123'),
                    'role' => 'student',
                    'student_id' => $student->id,
                ]);
            }
        } else {
            // Create sample student users if no students exist
            for ($i = 1; $i <= 3; $i++) {
                // Create student record first
                $student = Student::create([
                    'name' => "Student $i",
                    'email' => "student{$i}@example.com",
                    'phone' => "123456789{$i}",
                    'dob' => now()->subYears(20)->subDays($i),
                    'address' => "Address $i, City, Country",
                    'status' => 'active',
                ]);

                // Create user account for this student
                User::create([
                    'name' => $student->name,
                    'email' => $student->email,
                    'password' => Hash::make('student123'),
                    'role' => 'student',
                    'student_id' => $student->id,
                ]);
            }
        }
    }
}
