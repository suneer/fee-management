<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'course_id' => Course::factory(),
            'amount_paid' => fake()->randomFloat(2, 1000, 8000),
            'date_of_payment' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
        ];
    }

    /**
     * Set specific student for the payment.
     */
    public function forStudent($studentId): static
    {
        return $this->state(fn (array $attributes) => [
            'student_id' => $studentId,
        ]);
    }

    /**
     * Set specific course for the payment.
     */
    public function forCourse($courseId): static
    {
        return $this->state(fn (array $attributes) => [
            'course_id' => $courseId,
        ]);
    }
}
