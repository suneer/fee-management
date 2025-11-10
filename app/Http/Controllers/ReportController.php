<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Course;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Get all students with their courses and payments
        $query = Student::with(['courses', 'payments']);
        
        // Filter by pending balance
        if ($request->has('filter') && $request->filter == 'pending') {
            $query->whereHas('courses');
        }
        
        // Search by name or email
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        $students = $query->get()->map(function($student) {
            $totalFee = 0;
            foreach ($student->courses as $course) {
                $totalFee += $course->duration * $course->fee_per_month;
            }
            
            $totalPaid = $student->payments->sum('amount_paid');
            $balance = $totalFee - $totalPaid;
            
            return [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'phone' => $student->phone,
                'status' => $student->status,
                'courses_count' => $student->courses->count(),
                'total_fee' => $totalFee,
                'total_paid' => $totalPaid,
                'balance' => $balance
            ];
        });
        
        // Filter students with pending balance
        if ($request->has('filter') && $request->filter == 'pending') {
            $students = $students->filter(function($student) {
                return $student['balance'] > 0;
            });
        }
        
        // Calculate totals
        $totalRevenue = Payment::sum('amount_paid');
        $totalFees = 0;
        $allStudents = Student::with('courses')->get();
        foreach ($allStudents as $student) {
            foreach ($student->courses as $course) {
                $totalFees += $course->duration * $course->fee_per_month;
            }
        }
        $totalPending = $totalFees - $totalRevenue;
        
        return view('reports.index', compact('students', 'totalRevenue', 'totalFees', 'totalPending'));
    }
    
    public function monthlyBreakdown(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));
        
        // Get payments for the selected month
        $payments = Payment::with(['student', 'course'])
            ->whereYear('date_of_payment', $year)
            ->whereMonth('date_of_payment', $month)
            ->orderBy('date_of_payment', 'desc')
            ->get();
        
        $monthlyTotal = $payments->sum('amount_paid');
        
        // Get monthly breakdown for the entire year
        $yearlyBreakdown = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthPayments = Payment::whereYear('date_of_payment', $year)
                ->whereMonth('date_of_payment', $m)
                ->sum('amount_paid');
            
            $yearlyBreakdown[] = [
                'month' => Carbon::create($year, $m, 1)->format('F'),
                'month_num' => $m,
                'total' => $monthPayments,
                'count' => Payment::whereYear('date_of_payment', $year)
                    ->whereMonth('date_of_payment', $m)
                    ->count()
            ];
        }
        
        $yearlyTotal = array_sum(array_column($yearlyBreakdown, 'total'));
        
        return view('reports.monthly-breakdown', compact(
            'payments',
            'monthlyTotal',
            'yearlyBreakdown',
            'yearlyTotal',
            'year',
            'month'
        ));
    }
    
    public function courseRevenue()
    {
        $courses = Course::withCount('students')
            ->with(['payments'])
            ->get()
            ->map(function($course) {
                $totalFees = $course->students_count * $course->duration * $course->fee_per_month;
                $totalPaid = $course->payments->sum('amount_paid');
                $pending = $totalFees - $totalPaid;
                
                return [
                    'id' => $course->id,
                    'name' => $course->name,
                    'duration' => $course->duration,
                    'fee_per_month' => $course->fee_per_month,
                    'students_count' => $course->students_count,
                    'total_fees' => $totalFees,
                    'total_paid' => $totalPaid,
                    'pending' => $pending,
                    'collection_rate' => $totalFees > 0 ? ($totalPaid / $totalFees) * 100 : 0
                ];
            });
        
        $totalRevenue = $courses->sum('total_paid');
        $totalExpected = $courses->sum('total_fees');
        $totalPending = $courses->sum('pending');
        
        return view('reports.course-revenue', compact(
            'courses',
            'totalRevenue',
            'totalExpected',
            'totalPending'
        ));
    }
    
    public function exportStudents(Request $request)
    {
        $query = Student::with(['courses', 'payments']);
        
        if ($request->has('filter') && $request->filter == 'pending') {
            $query->whereHas('courses');
        }
        
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        $students = $query->get();
        
        $csv = "Name,Email,Phone,Status,Total Fees,Amount Paid,Balance\n";
        
        foreach ($students as $student) {
            $totalFee = 0;
            foreach ($student->courses as $course) {
                $totalFee += $course->duration * $course->fee_per_month;
            }
            
            $totalPaid = $student->payments->sum('amount_paid');
            $balance = $totalFee - $totalPaid;
            
            if ($request->filter == 'pending' && $balance <= 0) {
                continue;
            }
            
            $csv .= sprintf(
                '"%s","%s","%s","%s",%s,%s,%s' . "\n",
                $student->name,
                $student->email,
                $student->phone,
                $student->status,
                number_format($totalFee, 2, '.', ''),
                number_format($totalPaid, 2, '.', ''),
                number_format($balance, 2, '.', '')
            );
        }
        
        $filename = 'students_report_' . date('Y-m-d_His') . '.csv';
        
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
    
    public function exportMonthly(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('m'));
        
        $payments = Payment::with(['student', 'course'])
            ->whereYear('date_of_payment', $year)
            ->whereMonth('date_of_payment', $month)
            ->orderBy('date_of_payment', 'desc')
            ->get();
        
        $csv = "Date,Student,Course,Amount\n";
        
        foreach ($payments as $payment) {
            $csv .= sprintf(
                '"%s","%s","%s",%s' . "\n",
                Carbon::parse($payment->date_of_payment)->format('Y-m-d'),
                $payment->student->name,
                $payment->course->name,
                number_format($payment->amount_paid, 2, '.', '')
            );
        }
        
        $monthName = Carbon::create($year, $month, 1)->format('F_Y');
        $filename = 'monthly_report_' . $monthName . '_' . date('His') . '.csv';
        
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
    
    public function exportCourseRevenue()
    {
        $courses = Course::withCount('students')
            ->with(['payments'])
            ->get();
        
        $csv = "Course Name,Duration (Months),Fee Per Month,Total Students,Expected Revenue,Collected Revenue,Pending Amount,Collection Rate\n";
        
        foreach ($courses as $course) {
            $totalFees = $course->students_count * $course->duration * $course->fee_per_month;
            $totalPaid = $course->payments->sum('amount_paid');
            $pending = $totalFees - $totalPaid;
            $collectionRate = $totalFees > 0 ? ($totalPaid / $totalFees) * 100 : 0;
            
            $csv .= sprintf(
                '"%s",%d,%s,%d,%s,%s,%s,%.2f%%' . "\n",
                $course->name,
                $course->duration,
                number_format($course->fee_per_month, 2, '.', ''),
                $course->students_count,
                number_format($totalFees, 2, '.', ''),
                number_format($totalPaid, 2, '.', ''),
                number_format($pending, 2, '.', ''),
                $collectionRate
            );
        }
        
        $filename = 'course_revenue_report_' . date('Y-m-d_His') . '.csv';
        
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
