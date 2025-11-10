<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of all payments.
     */
    public function index()
    {
        $payments = Payment::with(['student', 'course'])->latest()->paginate(20);
        return view('payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new payment.
     */
    public function create()
    {
        $students = Student::where('status', 'active')->get();
        $courses = Course::all();
        return view('payments.create', compact('students', 'courses'));
    }

    /**
     * Store a newly created payment in the database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'amount_paid' => 'required|numeric|min:0',
            'date_of_payment' => 'required|date',
        ]);

        // Verify student is enrolled in the course
        $student = Student::findOrFail($validated['student_id']);
        if (!$student->courses->contains($validated['course_id'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Student is not enrolled in the selected course!');
        }

        // Create payment
        $payment = Payment::create($validated);

        return redirect()->route('students.show', $student)
            ->with('success', 'Payment recorded successfully!');
    }

    /**
     * Display the specified payment.
     */
    public function show(Payment $payment)
    {
        $payment->load(['student', 'course']);
        return view('payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified payment.
     */
    public function edit(Payment $payment)
    {
        $students = Student::all();
        $courses = Course::all();
        return view('payments.edit', compact('payment', 'students', 'courses'));
    }

    /**
     * Update the specified payment in the database.
     */
    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'amount_paid' => 'required|numeric|min:0',
            'date_of_payment' => 'required|date',
        ]);

        $payment->update($validated);

        return redirect()->route('students.show', $payment->student)
            ->with('success', 'Payment updated successfully!');
    }

    /**
     * Remove the specified payment from the database.
     */
    public function destroy(Payment $payment)
    {
        $studentId = $payment->student_id;
        $payment->delete();

        return redirect()->route('students.show', $studentId)
            ->with('success', 'Payment deleted successfully!');
    }

    /**
     * Show payment form for a specific student and course.
     */
    public function createForStudent(Student $student, Course $course)
    {
        // Verify student is enrolled in the course
        if (!$student->courses->contains($course->id)) {
            return redirect()->back()
                ->with('error', 'Student is not enrolled in this course!');
        }

        // Calculate course fee details
        $courseFee = $course->duration * $course->fee_per_month;
        $totalPaid = $student->payments()->where('course_id', $course->id)->sum('amount_paid');
        $remainingFee = $courseFee - $totalPaid;

        return view('payments.create-for-student', compact('student', 'course', 'courseFee', 'totalPaid', 'remainingFee'));
    }

    /**
     * Calculate EMI details for a course.
     */
    public function calculateEmi(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'number_of_emis' => 'required|integer|min:1|max:24',
        ]);

        $student = Student::findOrFail($validated['student_id']);
        $course = Course::findOrFail($validated['course_id']);

        // Calculate total course fee
        $totalCourseFee = $course->duration * $course->fee_per_month;

        // Calculate total paid for this course
        $totalPaid = $student->payments()->where('course_id', $course->id)->sum('amount_paid');

        // Calculate remaining fee
        $remainingFee = $totalCourseFee - $totalPaid;

        // Calculate EMI amount
        $numberOfEmis = $validated['number_of_emis'];
        $emiAmount = $remainingFee / $numberOfEmis;

        // Generate EMI schedule
        $emiSchedule = [];
        for ($i = 1; $i <= $numberOfEmis; $i++) {
            $emiSchedule[] = [
                'installment_number' => $i,
                'amount' => $emiAmount,
                'due_date' => now()->addMonths($i)->format('Y-m-d'),
            ];
        }

        return response()->json([
            'total_course_fee' => $totalCourseFee,
            'total_paid' => $totalPaid,
            'remaining_fee' => $remainingFee,
            'number_of_emis' => $numberOfEmis,
            'emi_amount' => round($emiAmount, 2),
            'emi_schedule' => $emiSchedule,
        ]);
    }

    /**
     * Get course fee details for AJAX requests.
     */
    public function getCourseFeeDetails(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        $student = Student::findOrFail($validated['student_id']);
        $course = Course::findOrFail($validated['course_id']);

        // Check if student is enrolled
        if (!$student->courses->contains($validated['course_id'])) {
            return response()->json(['error' => 'Student is not enrolled in this course'], 400);
        }

        // Calculate fees
        $totalCourseFee = $course->duration * $course->fee_per_month;
        $totalPaid = $student->payments()->where('course_id', $course->id)->sum('amount_paid');
        $remainingFee = $totalCourseFee - $totalPaid;

        return response()->json([
            'course_name' => $course->name,
            'duration' => $course->duration,
            'fee_per_month' => $course->fee_per_month,
            'total_course_fee' => $totalCourseFee,
            'total_paid' => $totalPaid,
            'remaining_fee' => $remainingFee,
            'is_fully_paid' => $remainingFee <= 0,
        ]);
    }
}
