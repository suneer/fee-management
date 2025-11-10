@extends('layouts.admin')

@section('title', 'Student Dashboard')
@section('page-title', 'My Dashboard')

@section('styles')
<script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<style>
    [v-cloak] {
        display: none;
    }
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(0, 0, 0, 0.1);
        border-radius: 50%;
        border-top-color: #007bff;
        animation: spin 1s ease-in-out infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    .stat-card {
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .course-card {
        transition: all 0.3s;
        border-left: 4px solid #007bff;
    }
    .course-card:hover {
        background-color: #f8f9fa;
    }
    .payment-badge {
        font-size: 0.85rem;
        padding: 0.4rem 0.8rem;
    }
</style>
@endsection

@section('content')
<div id="studentDashboard" v-cloak>
<div class="row mb-4">
    <div class="col-md-12">
        <div class="alert alert-info">
            <h4><i class="bi bi-person-circle"></i> Welcome, {{ auth()->user()->name }}!</h4>
            <p class="mb-0">View your enrolled courses, fee details, and payment history.</p>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-person-badge"></i> My Profile</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Name:</th>
                        <td>{{ $student->name }}</td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td>{{ $student->email }}</td>
                    </tr>
                    <tr>
                        <th>Phone:</th>
                        <td>{{ $student->phone }}</td>
                    </tr>
                    <tr>
                        <th>Date of Birth:</th>
                        <td>{{ $student->dob->format('d M, Y') }}</td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            <span class="status-badge status-{{ $student->status }}">
                                {{ ucfirst($student->status) }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-cash-stack"></i> Fee Summary</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="50%">Total Fees:</th>
                        <td class="text-end"><strong>₹{{ number_format($student->total_fee, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <th>Total Paid:</th>
                        <td class="text-end text-success">₹{{ number_format($student->total_paid, 2) }}</td>
                    </tr>
                    <tr class="border-top">
                        <th>Balance Due:</th>
                        <td class="text-end {{ $student->balance > 0 ? 'text-danger' : 'text-success' }}">
                            <h4>₹{{ number_format(abs($student->balance), 2) }}</h4>
                        </td>
                    </tr>
                </table>
                @if($student->balance > 0)
                    <div class="alert alert-warning mt-3">
                        <small><i class="bi bi-exclamation-triangle"></i> You have pending fees. Please contact admin for payment.</small>
                    </div>
                @else
                    <div class="alert alert-success mt-3">
                        <small><i class="bi bi-check-circle"></i> All fees are paid!</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-book"></i> My Enrolled Courses</h5>
            </div>
            <div class="card-body">
                @if($student->courses->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Course Name</th>
                                    <th>Duration</th>
                                    <th>Total Fee</th>
                                    <th>Paid</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($student->courses as $course)
                                    @php
                                        $totalCourseFee = $course->duration * $course->fee_per_month;
                                        $paidForCourse = $student->payments()->where('course_id', $course->id)->sum('amount_paid');
                                        $remainingBalance = $totalCourseFee - $paidForCourse;
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $course->name }}</strong>
                                            <br><small class="text-muted">₹{{ number_format($course->fee_per_month, 2) }}/month</small>
                                        </td>
                                        <td>{{ $course->duration }} months</td>
                                        <td>₹{{ number_format($totalCourseFee, 2) }}</td>
                                        <td class="text-success">₹{{ number_format($paidForCourse, 2) }}</td>
                                        <td>
                                            @if($remainingBalance > 0)
                                                <span class="text-danger">₹{{ number_format($remainingBalance, 2) }}</span>
                                            @else
                                                <span class="text-success">₹0.00</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($remainingBalance > 0)
                                                <span class="badge bg-warning">Pending</span>
                                            @else
                                                <span class="badge bg-success">Paid</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-book text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">You are not enrolled in any courses yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-receipt"></i> My Payment History</h5>
            </div>
            <div class="card-body">
                @if($student->payments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Course</th>
                                    <th>Amount Paid</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($student->payments->sortByDesc('date_of_payment') as $index => $payment)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $payment->date_of_payment->format('d M, Y') }}</td>
                                        <td>{{ $payment->course->name }}</td>
                                        <td class="text-success"><strong>₹{{ number_format($payment->amount_paid, 2) }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <th colspan="3">Total Paid</th>
                                    <th class="text-success">₹{{ number_format($student->payments->sum('amount_paid'), 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-receipt text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">No payment history available.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
