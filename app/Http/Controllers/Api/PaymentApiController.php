<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Mail\PaymentRecorded;
use Illuminate\Support\Facades\Mail;

class PaymentApiController extends Controller
{
    /**
     * Get all payments (paginated)
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 20);
            $payments = Payment::with(['student', 'course'])->latest()->paginate($perPage);
            
            $payments->getCollection()->transform(function($payment) {
                return [
                    'id' => $payment->id,
                    'student_id' => $payment->student_id,
                    'student_name' => $payment->student->name,
                    'student_email' => $payment->student->email,
                    'course_id' => $payment->course_id,
                    'course_name' => $payment->course->name,
                    'amount_paid' => $payment->amount_paid,
                    'date_of_payment' => $payment->date_of_payment,
                    'created_at' => $payment->created_at,
                    'updated_at' => $payment->updated_at
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Payments retrieved successfully',
                'data' => $payments->items(),
                'pagination' => [
                    'total' => $payments->total(),
                    'per_page' => $payments->perPage(),
                    'current_page' => $payments->currentPage(),
                    'last_page' => $payments->lastPage(),
                    'from' => $payments->firstItem(),
                    'to' => $payments->lastItem()
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve payments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific payment by ID
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $payment = Payment::with(['student', 'course'])->find($id);

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment retrieved successfully',
                'data' => [
                    'id' => $payment->id,
                    'student' => [
                        'id' => $payment->student->id,
                        'name' => $payment->student->name,
                        'email' => $payment->student->email
                    ],
                    'course' => [
                        'id' => $payment->course->id,
                        'name' => $payment->course->name,
                        'duration' => $payment->course->duration,
                        'fee_per_month' => $payment->course->fee_per_month
                    ],
                    'amount_paid' => $payment->amount_paid,
                    'date_of_payment' => $payment->date_of_payment,
                    'created_at' => $payment->created_at,
                    'updated_at' => $payment->updated_at
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Record a new payment
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'student_id' => 'required|exists:students,id',
                'course_id' => 'required|exists:courses,id',
                'amount_paid' => 'required|numeric|min:0.01',
                'date_of_payment' => 'required|date'
            ], [
                'amount_paid.min' => 'Payment amount must be a positive number greater than zero.'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Verify student is enrolled in the course
            $student = Student::with('courses')->find($request->student_id);
            $isEnrolled = $student->courses->contains($request->course_id);

            if (!$isEnrolled) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student is not enrolled in this course'
                ], 400);
            }

            // Calculate total course fee and check if payment exceeds remaining balance
            $course = Course::find($request->course_id);
            $totalCourseFee = $course->duration * $course->fee_per_month;
            $totalPaidForCourse = Payment::where('student_id', $request->student_id)
                ->where('course_id', $request->course_id)
                ->sum('amount_paid');
            $remainingBalance = $totalCourseFee - $totalPaidForCourse;

            // Validate that payment doesn't exceed remaining balance
            if ($request->amount_paid > $remainingBalance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount exceeds the remaining balance',
                    'data' => [
                        'total_course_fee' => $totalCourseFee,
                        'total_paid' => $totalPaidForCourse,
                        'remaining_balance' => $remainingBalance,
                        'attempted_payment' => $request->amount_paid,
                        'excess_amount' => $request->amount_paid - $remainingBalance
                    ]
                ], 400);
            }

            $payment = Payment::create([
                'student_id' => $request->student_id,
                'course_id' => $request->course_id,
                'amount_paid' => $request->amount_paid,
                'date_of_payment' => $request->date_of_payment
            ]);

            // Send email notification to student
            try {
                Mail::to($student->email)->send(new PaymentRecorded($payment));
            } catch (\Exception $e) {
                \Log::error('Failed to send payment email via API: ' . $e->getMessage());
                // Continue execution even if email fails
            }

            // Recalculate updated fee details after payment
            $totalPaidForCourse = Payment::where('student_id', $request->student_id)
                ->where('course_id', $request->course_id)
                ->sum('amount_paid');
            $remainingBalance = $totalCourseFee - $totalPaidForCourse;

            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully',
                'data' => [
                    'payment' => [
                        'id' => $payment->id,
                        'student_id' => $payment->student_id,
                        'course_id' => $payment->course_id,
                        'amount_paid' => $payment->amount_paid,
                        'date_of_payment' => $payment->date_of_payment,
                        'created_at' => $payment->created_at
                    ],
                    'fee_summary' => [
                        'total_course_fee' => $totalCourseFee,
                        'total_paid' => $totalPaidForCourse,
                        'remaining_balance' => $remainingBalance,
                        'payment_status' => $remainingBalance <= 0 ? 'Fully Paid' : 'Pending'
                    ]
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to record payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a payment
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $payment = Payment::find($id);

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'amount_paid' => 'numeric|min:0.01',
                'date_of_payment' => 'date'
            ], [
                'amount_paid.min' => 'Payment amount must be a positive number greater than zero.'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // If amount_paid is being updated, validate it doesn't exceed course fee
            if ($request->has('amount_paid')) {
                $course = Course::find($payment->course_id);
                $totalCourseFee = $course->duration * $course->fee_per_month;
                
                // Calculate total paid excluding the current payment being updated
                $totalPaidForCourse = Payment::where('student_id', $payment->student_id)
                    ->where('course_id', $payment->course_id)
                    ->where('id', '!=', $id)
                    ->sum('amount_paid');
                
                $remainingBalance = $totalCourseFee - $totalPaidForCourse;

                // Validate that new payment amount doesn't exceed remaining balance
                if ($request->amount_paid > $remainingBalance) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Updated payment amount exceeds the remaining balance',
                        'data' => [
                            'total_course_fee' => $totalCourseFee,
                            'total_paid' => $totalPaidForCourse,
                            'remaining_balance' => $remainingBalance,
                            'attempted_payment' => $request->amount_paid,
                            'excess_amount' => $request->amount_paid - $remainingBalance
                        ]
                    ], 400);
                }
            }

            $payment->update($request->only(['amount_paid', 'date_of_payment']));

            return response()->json([
                'success' => true,
                'message' => 'Payment updated successfully',
                'data' => $payment
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a payment
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $payment = Payment::find($id);

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found'
                ], 404);
            }

            $payment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Payment deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payments for a specific student
     * 
     * @param int $studentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStudentPayments($studentId)
    {
        try {
            $student = Student::find($studentId);

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found'
                ], 404);
            }

            $payments = Payment::with('course')
                ->where('student_id', $studentId)
                ->orderBy('date_of_payment', 'desc')
                ->get()
                ->map(function($payment) {
                    return [
                        'id' => $payment->id,
                        'course_id' => $payment->course_id,
                        'course_name' => $payment->course->name,
                        'amount_paid' => $payment->amount_paid,
                        'date_of_payment' => $payment->date_of_payment,
                        'created_at' => $payment->created_at
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Student payments retrieved successfully',
                'data' => [
                    'student_id' => $studentId,
                    'student_name' => $student->name,
                    'total_payments' => $payments->count(),
                    'total_amount_paid' => $payments->sum('amount_paid'),
                    'payments' => $payments
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve student payments',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
