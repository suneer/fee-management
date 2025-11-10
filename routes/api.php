<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StudentApiController;
use App\Http\Controllers\Api\PaymentApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Public API Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/

// Student API Endpoints
Route::prefix('students')->group(function () {
    Route::get('/', [StudentApiController::class, 'index']); // GET /api/students
    Route::get('/{id}', [StudentApiController::class, 'show']); // GET /api/students/{id}
    Route::post('/', [StudentApiController::class, 'store']); // POST /api/students
    Route::put('/{id}', [StudentApiController::class, 'update']); // PUT /api/students/{id}
    Route::delete('/{id}', [StudentApiController::class, 'destroy']); // DELETE /api/students/{id}
    
    // Fee Details
    Route::get('/{id}/fee-details', [StudentApiController::class, 'getFeeDetails']); // GET /api/students/{id}/fee-details
    
    // Course Assignment
    Route::post('/{id}/assign-courses', [StudentApiController::class, 'assignCourses']); // POST /api/students/{id}/assign-courses
    
    // Get Student Payments
    Route::get('/{id}/payments', [PaymentApiController::class, 'getStudentPayments']); // GET /api/students/{id}/payments
});

// Payment API Endpoints
Route::prefix('payments')->group(function () {
    Route::get('/', [PaymentApiController::class, 'index']); // GET /api/payments
    Route::get('/{id}', [PaymentApiController::class, 'show']); // GET /api/payments/{id}
    Route::post('/', [PaymentApiController::class, 'store']); // POST /api/payments
    Route::put('/{id}', [PaymentApiController::class, 'update']); // PUT /api/payments/{id}
    Route::delete('/{id}', [PaymentApiController::class, 'destroy']); // DELETE /api/payments/{id}
});
