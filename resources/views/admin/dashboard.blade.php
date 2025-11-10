@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-3">
                <i class="bi bi-speedometer2"></i> Admin Dashboard
            </h1>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <!-- Total Students -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Students</h6>
                            <h2 class="mb-0">{{ $totalStudents }}</h2>
                            <small class="text-success">
                                <i class="bi bi-check-circle"></i> {{ $activeStudents }} Active
                            </small>
                        </div>
                        <div class="text-primary" style="font-size: 3rem;">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-primary bg-opacity-10">
                    <a href="{{ route('students.index') }}" class="text-primary text-decoration-none">
                        View All <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Total Courses -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Courses</h6>
                            <h2 class="mb-0">{{ $totalCourses }}</h2>
                            <small class="text-muted">Available Programs</small>
                        </div>
                        <div class="text-success" style="font-size: 3rem;">
                            <i class="bi bi-book-fill"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-success bg-opacity-10">
                    <a href="{{ route('courses.index') }}" class="text-success text-decoration-none">
                        View All <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Revenue</h6>
                            <h2 class="mb-0">₹{{ number_format($totalRevenue, 2) }}</h2>
                            <small class="text-muted">Collected</small>
                        </div>
                        <div class="text-info" style="font-size: 3rem;">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-info bg-opacity-10">
                    <a href="{{ route('payments.index') }}" class="text-info text-decoration-none">
                        View Payments <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Pending Amount -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Pending Fees</h6>
                            <h2 class="mb-0">₹{{ number_format($pendingAmount, 2) }}</h2>
                            <small class="text-warning">
                                <i class="bi bi-exclamation-triangle"></i> Outstanding
                            </small>
                        </div>
                        <div class="text-warning" style="font-size: 3rem;">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-warning bg-opacity-10">
                    <span class="text-warning">
                        Total Fees: ₹{{ number_format($totalFees, 2) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphical Reports -->
    <div class="row g-3 mt-3">
        <!-- Monthly Revenue Trend -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up-arrow"></i> Monthly Revenue Trend (Last 6 Months)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyRevenueChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Course Revenue Distribution -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-pie-chart"></i> Course Revenue Distribution
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="courseRevenueChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-3">
        <!-- Student Status Distribution -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-people-fill"></i> Student Status
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="studentStatusChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Payment Trends (Last 7 Days) -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-calendar-week"></i> Payment Trends (Last 7 Days)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="paymentTrendsChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-3">
        <!-- Revenue vs Pending Comparison -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-bar-chart-fill"></i> Course-wise Revenue vs Pending Fees
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="revenuePendingChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-3">
        <!-- Recent Students -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-person-plus"></i> Recent Students
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($recentStudents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Courses</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentStudents as $student)
                                    <tr>
                                        <td>
                                            <a href="{{ route('students.show', $student) }}" class="text-decoration-none">
                                                {{ $student->name }}
                                            </a>
                                        </td>
                                        <td>{{ $student->email }}</td>
                                        <td>
                                            @if($student->status == 'active')
                                                <span class="badge bg-success">Active</span>
                                            @elseif($student->status == 'inactive')
                                                <span class="badge bg-secondary">Inactive</span>
                                            @elseif($student->status == 'suspended')
                                                <span class="badge bg-warning">Suspended</span>
                                            @else
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $student->courses->count() }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                            <p class="mb-0 mt-2">No students yet</p>
                        </div>
                    @endif
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('students.index') }}" class="btn btn-sm btn-primary">
                        View All Students <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Course Enrollment -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up"></i> Course Enrollment
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($courses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Course Name</th>
                                        <th>Duration</th>
                                        <th>Fee/Month</th>
                                        <th>Students</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($courses as $course)
                                    <tr>
                                        <td>
                                            <a href="{{ route('courses.show', $course) }}" class="text-decoration-none">
                                                {{ $course->name }}
                                            </a>
                                        </td>
                                        <td>{{ $course->duration }} months</td>
                                        <td>₹{{ number_format($course->fee_per_month, 2) }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ $course->students_count }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-book" style="font-size: 3rem;"></i>
                            <p class="mb-0 mt-2">No courses available</p>
                        </div>
                    @endif
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('courses.index') }}" class="btn btn-sm btn-success">
                        View All Courses <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Payments -->
    <div class="row g-3 mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-credit-card"></i> Recent Payments
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($recentPayments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Student</th>
                                        <th>Course</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentPayments as $payment)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($payment->date_of_payment)->format('d M Y') }}</td>
                                        <td>
                                            <a href="{{ route('students.show', $payment->student) }}" class="text-decoration-none">
                                                {{ $payment->student->name }}
                                            </a>
                                        </td>
                                        <td>{{ $payment->course->name }}</td>
                                        <td class="fw-bold text-success">₹{{ number_format($payment->amount_paid, 2) }}</td>
                                        <td>
                                            <a href="{{ route('students.show', $payment->student) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-wallet2" style="font-size: 3rem;"></i>
                            <p class="mb-0 mt-2">No payments recorded yet</p>
                        </div>
                    @endif
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('payments.index') }}" class="btn btn-sm btn-info">
                        View All Payments <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Monthly Revenue Trend Chart
    const monthlyRevenueCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
    new Chart(monthlyRevenueCtx, {
        type: 'line',
        data: {
            labels: @json($monthLabels),
            datasets: [{
                label: 'Revenue (₹)',
                data: @json($monthlyRevenue),
                backgroundColor: 'rgba(13, 110, 253, 0.2)',
                borderColor: 'rgba(13, 110, 253, 1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Revenue: ₹' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₹' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Course Revenue Distribution Pie Chart
    const courseRevenueCtx = document.getElementById('courseRevenueChart').getContext('2d');
    new Chart(courseRevenueCtx, {
        type: 'pie',
        data: {
            labels: @json($courseNames),
            datasets: [{
                label: 'Revenue',
                data: @json($courseRevenue),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                    'rgba(255, 159, 64, 0.8)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            return label + ': ₹' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Student Status Distribution Doughnut Chart
    const studentStatusCtx = document.getElementById('studentStatusChart').getContext('2d');
    new Chart(studentStatusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Active', 'Inactive', 'Suspended', 'Rejected'],
            datasets: [{
                label: 'Students',
                data: [
                    @json($studentStatus['active']),
                    @json($studentStatus['inactive']),
                    @json($studentStatus['suspended']),
                    @json($studentStatus['rejected'])
                ],
                backgroundColor: [
                    'rgba(25, 135, 84, 0.8)',
                    'rgba(108, 117, 125, 0.8)',
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(220, 53, 69, 0.8)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Payment Trends Bar Chart
    const paymentTrendsCtx = document.getElementById('paymentTrendsChart').getContext('2d');
    new Chart(paymentTrendsCtx, {
        type: 'bar',
        data: {
            labels: @json($trendLabels),
            datasets: [{
                label: 'Daily Payments (₹)',
                data: @json($paymentTrends),
                backgroundColor: 'rgba(255, 193, 7, 0.8)',
                borderColor: 'rgba(255, 193, 7, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Amount: ₹' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₹' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Revenue vs Pending Comparison Chart
    const revenuePendingCtx = document.getElementById('revenuePendingChart').getContext('2d');
    new Chart(revenuePendingCtx, {
        type: 'bar',
        data: {
            labels: @json($courseNames),
            datasets: [
                {
                    label: 'Revenue Collected (₹)',
                    data: @json($courseRevenue),
                    backgroundColor: 'rgba(25, 135, 84, 0.8)',
                    borderColor: 'rgba(25, 135, 84, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Pending Fees (₹)',
                    data: @json($coursePending),
                    backgroundColor: 'rgba(220, 53, 69, 0.8)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ₹' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₹' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
</script>
@endsection
