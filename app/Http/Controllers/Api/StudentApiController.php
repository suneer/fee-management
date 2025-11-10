<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentApiController extends Controller
{
    /**
     * Get all students (paginated)
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $students = Student::with('courses')->paginate($perPage);
            
            $students->getCollection()->transform(function($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                    'phone' => $student->phone,
                    'dob' => $student->dob,
                    'address' => $student->address,
                    'status' => $student->status,
                    'courses' => $student->courses->map(function($course) {
                        return [
                            'id' => $course->id,
                            'name' => $course->name,
                            'duration' => $course->duration,
                            'fee_per_month' => $course->fee_per_month,
                            'total_fee' => $course->duration * $course->fee_per_month
                        ];
                    }),
                    'created_at' => $student->created_at,
                    'updated_at' => $student->updated_at
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Students retrieved successfully',
                'data' => $students->items(),
                'pagination' => [
                    'total' => $students->total(),
                    'per_page' => $students->perPage(),
                    'current_page' => $students->currentPage(),
                    'last_page' => $students->lastPage(),
                    'from' => $students->firstItem(),
                    'to' => $students->lastItem()
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve students',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific student by ID
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $student = Student::with('courses', 'payments')->find($id);

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Student retrieved successfully',
                'data' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                    'phone' => $student->phone,
                    'dob' => $student->dob,
                    'address' => $student->address,
                    'status' => $student->status,
                    'courses' => $student->courses->map(function($course) {
                        return [
                            'id' => $course->id,
                            'name' => $course->name,
                            'duration' => $course->duration,
                            'fee_per_month' => $course->fee_per_month,
                            'total_fee' => $course->duration * $course->fee_per_month
                        ];
                    }),
                    'payments' => $student->payments,
                    'created_at' => $student->created_at,
                    'updated_at' => $student->updated_at
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new student
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:students,email',
                'phone' => 'required|string|max:20',
                'dob' => 'required|date',
                'address' => 'required|string',
                'status' => 'in:active,inactive,suspended,rejected'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $student = Student::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'dob' => $request->dob,
                'address' => $request->address,
                'status' => $request->status ?? 'active'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Student created successfully',
                'data' => $student
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a student
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $student = Student::find($id);

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'string|max:255',
                'email' => 'email|unique:students,email,' . $id,
                'phone' => 'string|max:20',
                'dob' => 'date',
                'address' => 'string',
                'status' => 'in:active,inactive,suspended,rejected'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $student->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Student updated successfully',
                'data' => $student
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a student
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $student = Student::find($id);

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found'
                ], 404);
            }

            $student->delete();

            return response()->json([
                'success' => true,
                'message' => 'Student deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get fee details for a specific student
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFeeDetails($id)
    {
        try {
            $student = Student::with('courses', 'payments')->find($id);

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found'
                ], 404);
            }

            // Calculate total fees
            $totalFee = 0;
            $courseDetails = [];
            foreach ($student->courses as $course) {
                $courseFee = $course->duration * $course->fee_per_month;
                $totalFee += $courseFee;
                
                // Get payments for this course
                $coursePaid = $student->payments()
                    ->where('course_id', $course->id)
                    ->sum('amount_paid');
                
                $courseDetails[] = [
                    'course_id' => $course->id,
                    'course_name' => $course->name,
                    'duration' => $course->duration,
                    'fee_per_month' => $course->fee_per_month,
                    'total_fee' => $courseFee,
                    'amount_paid' => $coursePaid,
                    'balance' => $courseFee - $coursePaid
                ];
            }

            // Calculate total paid
            $totalPaid = $student->payments->sum('amount_paid');
            $balance = $totalFee - $totalPaid;

            return response()->json([
                'success' => true,
                'message' => 'Fee details retrieved successfully',
                'data' => [
                    'student_id' => $student->id,
                    'student_name' => $student->name,
                    'student_email' => $student->email,
                    'total_fee' => $totalFee,
                    'total_paid' => $totalPaid,
                    'balance' => $balance,
                    'courses' => $courseDetails,
                    'payments' => $student->payments->map(function($payment) {
                        return [
                            'id' => $payment->id,
                            'course_id' => $payment->course_id,
                            'course_name' => $payment->course->name,
                            'amount_paid' => $payment->amount_paid,
                            'date_of_payment' => $payment->date_of_payment,
                            'created_at' => $payment->created_at
                        ];
                    })
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve fee details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign courses to a student
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignCourses(Request $request, $id)
    {
        try {
            $student = Student::find($id);

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'course_ids' => 'required|array',
                'course_ids.*' => 'exists:courses,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $student->courses()->sync($request->course_ids);

            return response()->json([
                'success' => true,
                'message' => 'Courses assigned successfully',
                'data' => [
                    'student_id' => $student->id,
                    'assigned_courses' => $student->courses
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign courses',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
