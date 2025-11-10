<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run seeders in order to maintain referential integrity
        $this->call([
            UserSeeder::class,      // Creates admin user
            CourseSeeder::class,    // Creates courses
            StudentSeeder::class,   // Creates students, user accounts, and enrolls them in courses
            PaymentSeeder::class,   // Creates payment records for enrolled students
        ]);
    }
}
