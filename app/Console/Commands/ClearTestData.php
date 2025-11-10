<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:clear {--keep-admin : Keep admin user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all test/seeded data from database (students, courses, payments, enrollments)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->confirm('This will delete ALL students, courses, payments, and enrollments. Are you sure?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $this->info('Clearing test data...');

        try {
            // Disable foreign key checks temporarily
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Clear payments first (has foreign keys)
            DB::table('payments')->truncate();
            $this->info('✓ Cleared payments table');

            // Clear course-student pivot table
            DB::table('course_student')->truncate();
            $this->info('✓ Cleared course enrollments');

            // Clear students
            DB::table('students')->truncate();
            $this->info('✓ Cleared students table');

            // Clear courses
            DB::table('courses')->truncate();
            $this->info('✓ Cleared courses table');

            // Clear student users (keep admin if flag is set)
            if ($this->option('keep-admin')) {
                DB::table('users')->where('role', 'student')->delete();
                $this->info('✓ Cleared student users (admin preserved)');
            } else {
                DB::table('users')->truncate();
                $this->info('✓ Cleared all users');
            }

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $this->info('');
            $this->info('✅ All test data cleared successfully!');
            
            if ($this->option('keep-admin')) {
                $this->warn('Admin user has been preserved. You can login with admin credentials.');
            } else {
                $this->warn('All users cleared. You will need to create a new admin user.');
            }

            return 0;
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->error('Error clearing data: ' . $e->getMessage());
            return 1;
        }
    }
}
