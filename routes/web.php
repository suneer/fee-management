<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin() 
            ? redirect()->route('admin.dashboard')
            : redirect()->route('student.dashboard');
    }
    return view('welcome');
});

// Admin Dashboard
Route::get('/admin/dashboard', function () {
    return redirect()->route('students.index');
})->middleware(['auth', 'admin'])->name('admin.dashboard');

// Student Dashboard
Route::get('/student/dashboard', function () {
    $student = auth()->user()->student;
    if (!$student) {
        return view('student.no-profile');
    }
    return view('student.dashboard', compact('student'));
})->middleware(['auth'])->name('student.dashboard');

// Admin Routes - Protected by auth and admin middleware
Route::middleware(['auth', 'admin'])->group(function () {
    // Student Management Routes
    Route::resource('students', StudentController::class);
    
    // Course Assignment
    Route::post('students/{student}/assign-courses', [StudentController::class, 'assignCourses'])
        ->name('students.assign-courses');
    
    // Student Status Management
    Route::patch('students/{student}/toggle-status', [StudentController::class, 'toggleStatus'])
        ->name('students.toggle-status');
    
    Route::patch('students/{student}/suspend', [StudentController::class, 'suspend'])
        ->name('students.suspend');
    
    Route::patch('students/{student}/reject', [StudentController::class, 'reject'])
        ->name('students.reject');
    
    Route::patch('students/{student}/activate', [StudentController::class, 'activate'])
        ->name('students.activate');
    
    // Course Management Routes
    Route::resource('courses', CourseController::class);
    
    // Payment Management Routes
    Route::resource('payments', PaymentController::class);
    
    // Payment for specific student and course
    Route::get('students/{student}/courses/{course}/pay', [PaymentController::class, 'createForStudent'])
        ->name('payments.create-for-student');
    
    // AJAX routes for fee calculations
    Route::post('payments/course-fee-details', [PaymentController::class, 'getCourseFeeDetails'])
        ->name('payments.course-fee-details');
    
    Route::post('payments/calculate-emi', [PaymentController::class, 'calculateEmi'])
        ->name('payments.calculate-emi');
});

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
