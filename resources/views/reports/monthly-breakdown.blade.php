@extends('layouts.admin')

@section('title', 'Monthly Fee Breakdown')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="bi bi-calendar3"></i> Monthly Fee Breakdown</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Reports
            </a>
        </div>
    </div>

    <!-- Month/Year Selector -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.monthly-breakdown') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Year</label>
                    <select name="year" class="form-select">
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Month</label>
                    <select name="month" class="form-select">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $m, 1)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> View
                    </button>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <a href="{{ route('reports.export-monthly', ['year' => $year, 'month' => $month]) }}" class="btn btn-success w-100">
                        <i class="bi bi-download"></i> Export CSV
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Monthly Summary -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-primary">
                <div class="card-body">
                    <h6 class="text-muted">{{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }} Revenue</h6>
                    <h3 class="text-primary">₹{{ number_format($monthlyTotal, 2) }}</h3>
                    <small class="text-muted">{{ $payments->count() }} payment(s)</small>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-success">
                <div class="card-body">
                    <h6 class="text-muted">{{ $year }} Total Revenue</h6>
                    <h3 class="text-success">₹{{ number_format($yearlyTotal, 2) }}</h3>
                    <small class="text-muted">Entire year collection</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Yearly Breakdown Chart -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="bi bi-bar-chart"></i> {{ $year }} Monthly Breakdown</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Month</th>
                            <th class="text-center">Payments Count</th>
                            <th class="text-end">Total Revenue</th>
                            <th style="width: 50%;">Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $maxRevenue = max(array_column($yearlyBreakdown, 'total'));
                        @endphp
                        @foreach($yearlyBreakdown as $monthData)
                        <tr class="{{ $monthData['month_num'] == $month ? 'table-primary' : '' }}">
                            <td>
                                <strong>{{ $monthData['month'] }}</strong>
                                @if($monthData['month_num'] == $month)
                                    <span class="badge bg-primary ms-2">Current</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ $monthData['count'] }}</span>
                            </td>
                            <td class="text-end">
                                <strong>₹{{ number_format($monthData['total'], 2) }}</strong>
                            </td>
                            <td>
                                @php
                                    $percentage = $maxRevenue > 0 ? ($monthData['total'] / $maxRevenue) * 100 : 0;
                                @endphp
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $percentage }}%;" 
                                         aria-valuenow="{{ $percentage }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        {{ number_format($percentage, 1) }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th>Total</th>
                            <th class="text-center">
                                <span class="badge bg-primary">{{ array_sum(array_column($yearlyBreakdown, 'count')) }}</span>
                            </th>
                            <th class="text-end">₹{{ number_format($yearlyTotal, 2) }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Detailed Payments for Selected Month -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="bi bi-list-ul"></i> Payments in {{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }}
            </h5>
        </div>
        <div class="card-body p-0">
            @if($payments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Student</th>
                                <th>Course</th>
                                <th class="text-end">Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($payment->date_of_payment)->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('students.show', $payment->student) }}" class="text-decoration-none">
                                        {{ $payment->student->name }}
                                    </a>
                                </td>
                                <td>{{ $payment->course->name }}</td>
                                <td class="text-end text-success fw-bold">₹{{ number_format($payment->amount_paid, 2) }}</td>
                                <td>
                                    <a href="{{ route('students.show', $payment->student) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> View Student
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="text-end">Month Total:</th>
                                <th class="text-end text-success">₹{{ number_format($monthlyTotal, 2) }}</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="p-5 text-center text-muted">
                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                    <p class="mt-3 mb-0">No payments recorded for {{ \Carbon\Carbon::create($year, $month, 1)->format('F Y') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
