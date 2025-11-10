@extends('layouts.admin')

@section('title', 'Student Fee Reports')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="bi bi-file-earmark-bar-graph"></i> Student Fee Reports</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('reports.monthly-breakdown') }}" class="btn btn-info">
                <i class="bi bi-calendar3"></i> Monthly Breakdown
            </a>
            <a href="{{ route('reports.course-revenue') }}" class="btn btn-success">
                <i class="bi bi-graph-up"></i> Course Revenue
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-primary">
                <div class="card-body">
                    <h6 class="text-muted">Total Expected Fees</h6>
                    <h3 class="text-primary">₹{{ number_format($totalFees, 2) }}</h3>
                    <small class="text-muted">From all enrolled courses</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-body">
                    <h6 class="text-muted">Total Revenue Collected</h6>
                    <h3 class="text-success">₹{{ number_format($totalRevenue, 2) }}</h3>
                    <small class="text-muted">All payments received</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-warning">
                <div class="card-body">
                    <h6 class="text-muted">Total Pending Balance</h6>
                    <h3 class="text-warning">₹{{ number_format($totalPending, 2) }}</h3>
                    <small class="text-muted">Outstanding amount</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Export -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Search Student</label>
                    <input type="text" name="search" class="form-control" placeholder="Name or Email" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Filter</label>
                    <select name="filter" class="form-select">
                        <option value="">All Students</option>
                        <option value="pending" {{ request('filter') == 'pending' ? 'selected' : '' }}>Pending Balance Only</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search"></i> Search
                    </button>
                    <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <a href="{{ route('reports.export-students', request()->all()) }}" class="btn btn-success w-100">
                        <i class="bi bi-download"></i> Export CSV
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Students Table -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Student Fee Details</h5>
        </div>
        <div class="card-body p-0">
            @if($students->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Student Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Courses</th>
                                <th class="text-end">Total Fees</th>
                                <th class="text-end">Amount Paid</th>
                                <th class="text-end">Balance</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                            <tr>
                                <td>
                                    <strong>{{ $student['name'] }}</strong>
                                </td>
                                <td>{{ $student['email'] }}</td>
                                <td>{{ $student['phone'] }}</td>
                                <td>
                                    @if($student['status'] == 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($student['status'] == 'inactive')
                                        <span class="badge bg-secondary">Inactive</span>
                                    @elseif($student['status'] == 'suspended')
                                        <span class="badge bg-warning">Suspended</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $student['courses_count'] }} Course(s)</span>
                                </td>
                                <td class="text-end">₹{{ number_format($student['total_fee'], 2) }}</td>
                                <td class="text-end text-success">₹{{ number_format($student['total_paid'], 2) }}</td>
                                <td class="text-end">
                                    @if($student['balance'] > 0)
                                        <span class="text-danger fw-bold">₹{{ number_format($student['balance'], 2) }}</span>
                                    @elseif($student['balance'] < 0)
                                        <span class="text-info fw-bold">₹{{ number_format(abs($student['balance']), 2) }} (Excess)</span>
                                    @else
                                        <span class="text-success fw-bold">₹0.00 (Paid)</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('students.show', $student['id']) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="5" class="text-end">Totals:</th>
                                <th class="text-end">₹{{ number_format($students->sum('total_fee'), 2) }}</th>
                                <th class="text-end text-success">₹{{ number_format($students->sum('total_paid'), 2) }}</th>
                                <th class="text-end text-danger">₹{{ number_format($students->sum('balance'), 2) }}</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="p-5 text-center text-muted">
                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                    <p class="mt-3 mb-0">No students found matching your criteria</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
