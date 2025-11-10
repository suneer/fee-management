<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Course;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of students with their courses and fee details.
     */
    public function index()
    {
        $students = Student::with(['courses', 'payments'])->paginate(15);
        return view('students.index', compact('students'));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        return view('students.create');
    }

    /**
     * Store a newly created student in the database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email',
            'phone' => 'required|string|max:20',
            'dob' => 'required|date',
            'address' => 'required|string',
        ]);

        $validated['status'] = 'active';

        $student = Student::create($validated);

        return redirect()->route('students.index')
                        ->with('success', 'Student added successfully!');
    }

    /**
     * Display the specified student.
     */
    public function show(Student $student)
    {
        $student->load(['courses']);
        
        // Paginate payments (10 per page)
        $payments = $student->payments()
            ->with('course')
            ->orderBy('date_of_payment', 'desc')
            ->paginate(10);
        
        $courses = Course::all();
        
        return view('students.show', compact('student', 'courses', 'payments'));
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(Student $student)
    {
        return view('students.edit', compact('student'));
    }

    /**
     * Update the specified student in the database.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email,' . $student->id,
            'phone' => 'required|string|max:20',
            'dob' => 'required|date',
            'address' => 'required|string',
        ]);

        $student->update($validated);

        return redirect()->route('students.index')
                        ->with('success', 'Student updated successfully!');
    }

    /**
     * Remove the specified student from the database.
     */
    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('students.index')
                        ->with('success', 'Student deleted successfully!');
    }

    /**
     * Assign courses to a student.
     */
    public function assignCourses(Request $request, Student $student)
    {
        $validated = $request->validate([
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
        ]);

        $student->courses()->sync($validated['course_ids']);

        return redirect()->route('students.show', $student)
                        ->with('success', 'Courses assigned successfully!');
    }

    /**
     * Toggle student status (active/inactive).
     */
    public function toggleStatus(Student $student)
    {
        $student->status = $student->status === 'active' ? 'inactive' : 'active';
        $student->save();

        return redirect()->back()
                        ->with('success', 'Student status updated successfully!');
    }

    /**
     * Suspend a student.
     */
    public function suspend(Student $student)
    {
        $student->status = 'suspended';
        $student->save();

        return redirect()->back()
                        ->with('success', 'Student suspended successfully!');
    }

    /**
     * Reject a student.
     */
    public function reject(Student $student)
    {
        $student->status = 'rejected';
        $student->save();

        return redirect()->back()
                        ->with('success', 'Student rejected successfully!');
    }

    /**
     * Activate a student.
     */
    public function activate(Student $student)
    {
        $student->status = 'active';
        $student->save();

        return redirect()->back()
                        ->with('success', 'Student activated successfully!');
    }
}
