@extends('layouts.admin')

@section('title', 'Course Revenue Report')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="bi bi-graph-up"></i> Course Revenue Report</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('reports.export-course-revenue') }}" class="btn btn-success me-2">
                <i class="bi bi-download"></i> Export CSV
            </a>
            <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Reports
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-primary">
                <div class="card-body">
                    <h6 class="text-muted">Total Expected Revenue</h6>
                    <h3 class="text-primary">₹{{ number_format($totalExpected, 2) }}</h3>
                    <small class="text-muted">From all courses</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-body">
                    <h6 class="text-muted">Total Revenue Collected</h6>
                    <h3 class="text-success">₹{{ number_format($totalRevenue, 2) }}</h3>
                    <small class="text-success">
                        @if($totalExpected > 0)
                            {{ number_format(($totalRevenue / $totalExpected) * 100, 1) }}% collected
                        @else
                            0% collected
                        @endif
                    </small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-warning">
                <div class="card-body">
                    <h6 class="text-muted">Total Pending</h6>
                    <h3 class="text-warning">₹{{ number_format($totalPending, 2) }}</h3>
                    <small class="text-muted">Outstanding amount</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Revenue Table -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Course-wise Revenue Breakdown</h5>
        </div>
        <div class="card-body p-0">
            @if($courses->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Course Name</th>
                                <th class="text-center">Duration</th>
                                <th class="text-end">Fee/Month</th>
                                <th class="text-center">Students</th>
                                <th class="text-end">Expected Fees</th>
                                <th class="text-end">Collected</th>
                                <th class="text-end">Pending</th>
                                <th class="text-center">Collection Rate</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courses as $course)
                            <tr>
                                <td>
                                    <strong>{{ $course['name'] }}</strong>
                                </td>
                                <td class="text-center">{{ $course['duration'] }} months</td>
                                <td class="text-end">₹{{ number_format($course['fee_per_month'], 2) }}</td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $course['students_count'] }}</span>
                                </td>
                                <td class="text-end">₹{{ number_format($course['total_fees'], 2) }}</td>
                                <td class="text-end text-success fw-bold">₹{{ number_format($course['total_paid'], 2) }}</td>
                                <td class="text-end">
                                    @if($course['pending'] > 0)
                                        <span class="text-danger fw-bold">₹{{ number_format($course['pending'], 2) }}</span>
                                    @else
                                        <span class="text-success fw-bold">₹0.00</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="progress" style="height: 25px; min-width: 100px;">
                                        @php
                                            $rate = $course['collection_rate'];
                                            $color = $rate >= 80 ? 'success' : ($rate >= 50 ? 'warning' : 'danger');
                                        @endphp
                                        <div class="progress-bar bg-{{ $color }}" role="progressbar" 
                                             style="width: {{ $rate }}%;" 
                                             aria-valuenow="{{ $rate }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            {{ number_format($rate, 1) }}%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('courses.show', $course['id']) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="4" class="text-end">Totals:</th>
                                <th class="text-end">₹{{ number_format($totalExpected, 2) }}</th>
                                <th class="text-end text-success">₹{{ number_format($totalRevenue, 2) }}</th>
                                <th class="text-end text-danger">₹{{ number_format($totalPending, 2) }}</th>
                                <th class="text-center">
                                    @if($totalExpected > 0)
                                        <strong>{{ number_format(($totalRevenue / $totalExpected) * 100, 1) }}%</strong>
                                    @else
                                        <strong>0%</strong>
                                    @endif
                                </th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Revenue Chart -->
                <div class="p-4">
                    <h5 class="mb-3">Revenue Comparison</h5>
                    <div class="row g-3">
                        @foreach($courses as $course)
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $course['name'] }}</h6>
                                    <div class="mb-2">
                                        <small class="text-muted">Expected vs Collected</small>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Expected:</span>
                                        <strong>₹{{ number_format($course['total_fees'], 2) }}</strong>
                                    </div>
                                    <div class="progress mb-2" style="height: 20px;">
                                        @php
                                            $rate = $course['collection_rate'];
                                            $color = $rate >= 80 ? 'success' : ($rate >= 50 ? 'warning' : 'danger');
                                        @endphp
                                        <div class="progress-bar bg-{{ $color }}" role="progressbar" 
                                             style="width: {{ $rate }}%;">
                                            {{ number_format($rate, 1) }}%
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Collected:</span>
                                        <strong class="text-success">₹{{ number_format($course['total_paid'], 2) }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Pending:</span>
                                        <strong class="text-danger">₹{{ number_format($course['pending'], 2) }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="p-5 text-center text-muted">
                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                    <p class="mt-3 mb-0">No courses available</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
