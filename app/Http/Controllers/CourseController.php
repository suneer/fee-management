<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of courses with enrolled students count.
     */
    public function index()
    {
        $courses = Course::withCount('students')->paginate(5);
        return view('courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new course.
     */
    public function create()
    {
        return view('courses.create');
    }

    /**
     * Store a newly created course in the database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'fee_per_month' => 'required|numeric|min:0.01',
        ], [
            'fee_per_month.min' => 'Fee per month must be a positive number greater than zero.'
        ]);

        $course = Course::create($validated);

        return redirect()->route('courses.index')
                        ->with('success', 'Course added successfully!');
    }

    /**
     * Display the specified course.
     */
    public function show(Course $course)
    {
        $course->load('students');
        return view('courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit(Course $course)
    {
        return view('courses.edit', compact('course'));
    }

    /**
     * Update the specified course in the database.
     */
    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'fee_per_month' => 'required|numeric|min:0.01',
        ], [
            'fee_per_month.min' => 'Fee per month must be a positive number greater than zero.'
        ]);

        $course->update($validated);

        return redirect()->route('courses.index')
                        ->with('success', 'Course updated successfully!');
    }

    /**
     * Remove the specified course from the database.
     */
    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->route('courses.index')
                        ->with('success', 'Course deleted successfully!');
    }
}
