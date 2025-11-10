@extends('layouts.admin')

@section('title', 'Student Details')
@section('page-title', 'Student Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>{{ $student->name }}</h2>
    <div>
        <a href="{{ route('students.edit', $student) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('students.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Personal Information</h5>
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
                        <th>Address:</th>
                        <td>{{ $student->address }}</td>
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

                <div class="mt-3">
                    <h6>Status Actions:</h6>
                    <div class="btn-group" role="group">
                        @if($student->status === 'active')
                            <form action="{{ route('students.toggle-status', $student) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-secondary">
                                    <i class="bi bi-toggle-off"></i> Deactivate
                                </button>
                            </form>
                        @else
                            <form action="{{ route('students.activate', $student) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-toggle-on"></i> Activate
                                </button>
                            </form>
                        @endif

                        @if($student->status !== 'suspended')
                            <form action="{{ route('students.suspend', $student) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-pause-circle"></i> Suspend
                                </button>
                            </form>
                        @endif

                        @if($student->status !== 'rejected')
                            <form action="{{ route('students.reject', $student) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-x-circle"></i> Reject
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Fee Summary</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="50%">Total Fee (Monthly):</th>
                        <td class="text-end"><strong>₹{{ number_format($student->total_fee, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <th>Total Paid:</th>
                        <td class="text-end text-success">₹{{ number_format($student->total_paid, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Balance Due:</th>
                        <td class="text-end {{ $student->balance > 0 ? 'text-danger' : 'text-success' }}">
                            <strong>₹{{ number_format(abs($student->balance), 2) }}</strong>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Assigned Courses</h5>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#assignCoursesModal">
                    <i class="bi bi-plus-circle"></i> Assign Courses
                </button>
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
                                    <th>Action</th>
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
                                                <span class="text-danger"><strong>₹{{ number_format($remainingBalance, 2) }}</strong></span>
                                            @else
                                                <span class="badge bg-success">Paid Full</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($remainingBalance > 0)
                                                <a href="{{ route('payments.create-for-student', [$student, $course]) }}" class="btn btn-sm btn-success">
                                                    <i class="bi bi-cash-coin"></i> Pay Fee
                                                </a>
                                            @else
                                                <span class="text-success"><i class="bi bi-check-circle-fill"></i> Completed</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <th colspan="2">Total</th>
                                    <th>₹{{ number_format($student->courses->sum(fn($c) => $c->duration * $c->fee_per_month), 2) }}</th>
                                    <th class="text-success">₹{{ number_format($student->total_paid, 2) }}</th>
                                    <th class="text-danger">₹{{ number_format($student->balance, 2) }}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center py-4">No courses assigned yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-receipt"></i> Payment History</h5>
                @if($student->courses->count() > 0)
                    <a href="{{ route('payments.create') }}" class="btn btn-sm btn-success">
                        <i class="bi bi-plus-circle"></i> Record Payment
                    </a>
                @endif
            </div>
            <div class="card-body">
                @if($payments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Payment Date</th>
                                    <th>Course</th>
                                    <th>Amount Paid</th>
                                    <th>Payment Method</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments as $index => $payment)
                                    <tr>
                                        <td>{{ ($payments->currentPage() - 1) * $payments->perPage() + $loop->iteration }}</td>
                                        <td>{{ $payment->date_of_payment->format('d M, Y') }}</td>
                                        <td>{{ $payment->course->name }}</td>
                                        <td><strong class="text-success">₹{{ number_format($payment->amount_paid, 2) }}</strong></td>
                                        <td><span class="badge bg-info">Cash/Online</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <th colspan="3">Total Payments Made (All Pages)</th>
                                    <th colspan="2" class="text-success">₹{{ number_format($student->payments->sum('amount_paid'), 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <!-- Pagination Links -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $payments->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-receipt text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">No payment history found.</p>
                        @if($student->courses->count() > 0)
                            <a href="{{ route('payments.create') }}" class="btn btn-primary">
                                <i class="bi bi-cash-coin"></i> Record First Payment
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Assign Courses Modal -->
<div class="modal fade" id="assignCoursesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('students.assign-courses', $student) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Assign Courses to {{ $student->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if($courses->count() > 0)
                        <div class="mb-3">
                            <label class="form-label">Select Courses:</label>
                            @foreach($courses as $course)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="course_ids[]" 
                                           value="{{ $course->id }}" id="course{{ $course->id }}"
                                           {{ $student->courses->contains($course->id) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="course{{ $course->id }}">
                                        {{ $course->name }} (₹{{ number_format($course->fee_per_month, 2) }}/month)
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No courses available. Please add courses first.</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" {{ $courses->count() == 0 ? 'disabled' : '' }}>
                        Assign Courses
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
