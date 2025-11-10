<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $courses = [
            'Web Development',
            'Mobile App Development',
            'Data Science',
            'Machine Learning',
            'Cloud Computing',
            'Cybersecurity',
            'Digital Marketing',
            'UI/UX Design',
            'DevOps Engineering',
            'Blockchain Development',
            'Game Development',
            'Python Programming',
            'JavaScript Fundamentals',
            'React Development',
            'Laravel Backend',
            'Vue.js Framework',
            'Angular Development',
            'Node.js Backend',
            'Database Management',
            'Software Testing'
        ];

        return [
            'name' => fake()->randomElement($courses) . ' ' . fake()->randomElement(['Bootcamp', 'Course', 'Masterclass', 'Training', 'Program']),
            'duration' => fake()->numberBetween(3, 12),
            'fee_per_month' => fake()->randomFloat(2, 3000, 10000),
        ];
    }
}
