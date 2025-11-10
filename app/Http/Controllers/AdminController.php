<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Course;
use App\Models\Payment;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Get statistics
        $totalStudents = Student::count();
        $activeStudents = Student::where('status', 'active')->count();
        $totalCourses = Course::count();
        $totalRevenue = Payment::sum('amount_paid');
        
        // Get recent students
        $recentStudents = Student::with('courses')
            ->latest()
            ->take(5)
            ->get();
        
        // Get courses with enrollment counts
        $courses = Course::withCount('students')
            ->orderBy('students_count', 'desc')
            ->get();
        
        // Get recent payments
        $recentPayments = Payment::with(['student', 'course'])
            ->latest('date_of_payment')
            ->take(10)
            ->get();
        
        // Calculate total fees and pending amounts
        $totalFees = 0;
        $totalPaid = Payment::sum('amount_paid');
        $students = Student::with('courses')->get();
        
        foreach ($students as $student) {
            foreach ($student->courses as $course) {
                $totalFees += $course->duration * $course->fee_per_month;
            }
        }
        
        $pendingAmount = $totalFees - $totalPaid;
        
        // Get monthly revenue for last 6 months
        $monthlyRevenue = [];
        $monthLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthLabels[] = $date->format('M Y');
            $monthlyRevenue[] = Payment::whereYear('date_of_payment', $date->year)
                ->whereMonth('date_of_payment', $date->month)
                ->sum('amount_paid');
        }
        
        // Course-wise revenue data
        $courseRevenue = [];
        $courseNames = [];
        $coursePending = [];
        foreach ($courses as $course) {
            $courseNames[] = $course->name;
            $revenue = Payment::where('course_id', $course->id)->sum('amount_paid');
            $courseRevenue[] = $revenue;
            $expectedRevenue = $course->students_count * $course->duration * $course->fee_per_month;
            $coursePending[] = $expectedRevenue - $revenue;
        }
        
        // Student status distribution
        $studentStatus = [
            'active' => Student::where('status', 'active')->count(),
            'inactive' => Student::where('status', 'inactive')->count(),
            'suspended' => Student::where('status', 'suspended')->count(),
            'rejected' => Student::where('status', 'rejected')->count(),
        ];
        
        // Payment trends (last 7 days)
        $paymentTrends = [];
        $trendLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $trendLabels[] = $date->format('D, M d');
            $paymentTrends[] = Payment::whereDate('date_of_payment', $date->toDateString())
                ->sum('amount_paid');
        }
        
        return view('admin.dashboard', compact(
            'totalStudents',
            'activeStudents',
            'totalCourses',
            'totalRevenue',
            'recentStudents',
            'courses',
            'recentPayments',
            'totalFees',
            'totalPaid',
            'pendingAmount',
            'monthlyRevenue',
            'monthLabels',
            'courseRevenue',
            'courseNames',
            'coursePending',
            'studentStatus',
            'paymentTrends',
            'trendLabels'
        ));
    }
}
