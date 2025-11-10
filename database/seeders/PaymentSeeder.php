<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Course;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all students with their enrolled courses
        $students = Student::with('courses')->get();

        foreach ($students as $student) {
            // For each enrolled course, create 1-3 payments
            foreach ($student->courses as $course) {
                $numberOfPayments = rand(1, 3);
                
                for ($i = 0; $i < $numberOfPayments; $i++) {
                    // Calculate a reasonable payment amount (not exceeding total course fee)
                    $totalCourseFee = $course->duration * $course->fee_per_month;
                    $maxPayment = min($totalCourseFee, $course->fee_per_month * 2);
                    $minPayment = min(1000, $totalCourseFee);
                    
                    Payment::factory()
                        ->forStudent($student->id)
                        ->forCourse($course->id)
                        ->create([
                            'amount_paid' => fake()->randomFloat(2, $minPayment, $maxPayment),
                            'date_of_payment' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
                        ]);
                }
            }
        }
    }
}
