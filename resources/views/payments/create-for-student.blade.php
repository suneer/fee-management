@extends('layouts.admin')

@section('title', 'Payment for ' . $student->name)
@section('page-title', 'Record Payment')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Record Payment</h4>
                <p class="mb-0 text-muted"><small>Student: {{ $student->name }} | Course: {{ $course->name }}</small></p>
            </div>
            <div class="card-body">
                <!-- Fee Summary -->
                <div class="alert alert-info mb-4">
                    <h5 class="mb-3">Course Fee Summary</h5>
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td width="50%"><strong>Course:</strong></td>
                            <td>{{ $course->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Duration:</strong></td>
                            <td>{{ $course->duration }} months</td>
                        </tr>
                        <tr>
                            <td><strong>Fee per Month:</strong></td>
                            <td>₹{{ number_format($course->fee_per_month, 2) }}</td>
                        </tr>
                        <tr class="border-top">
                            <td><strong>Total Course Fee:</strong></td>
                            <td><strong>₹{{ number_format($courseFee, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <td><strong>Already Paid:</strong></td>
                            <td class="text-success">₹{{ number_format($totalPaid, 2) }}</td>
                        </tr>
                        <tr class="border-top">
                            <td><strong>Remaining Balance:</strong></td>
                            <td class="text-danger"><h5>₹{{ number_format($remainingFee, 2) }}</h5></td>
                        </tr>
                    </table>
                </div>

                @if($remainingFee > 0)
                    <form action="{{ route('payments.store') }}" method="POST" id="paymentForm">
                        @csrf
                        <input type="hidden" name="student_id" value="{{ $student->id }}">
                        <input type="hidden" name="course_id" value="{{ $course->id }}">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="amount_paid" class="form-label">Amount to Pay (₹) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control @error('amount_paid') is-invalid @enderror" 
                                           id="amount_paid" name="amount_paid" 
                                           value="{{ old('amount_paid') }}" 
                                           min="0" 
                                           max="{{ $remainingFee }}" 
                                           required>
                                    @error('amount_paid')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Maximum: ₹{{ number_format($remainingFee, 2) }}</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="date_of_payment" class="form-label">Payment Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('date_of_payment') is-invalid @enderror" 
                                           id="date_of_payment" name="date_of_payment" 
                                           value="{{ old('date_of_payment', date('Y-m-d')) }}" 
                                           required>
                                    @error('date_of_payment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Quick Payment Buttons -->
                        <div class="mb-3">
                            <label class="form-label">Quick Pay:</label>
                            <div class="btn-group w-100" role="group">
                                <button type="button" class="btn btn-outline-primary quick-pay" data-amount="{{ $remainingFee }}">
                                    Full Amount (₹{{ number_format($remainingFee, 2) }})
                                </button>
                                <button type="button" class="btn btn-outline-primary quick-pay" data-amount="{{ $remainingFee / 2 }}">
                                    Half (₹{{ number_format($remainingFee / 2, 2) }})
                                </button>
                                <button type="button" class="btn btn-outline-primary quick-pay" data-amount="{{ $course->fee_per_month }}">
                                    Monthly Fee (₹{{ number_format($course->fee_per_month, 2) }})
                                </button>
                            </div>
                        </div>

                        <!-- EMI Calculator -->
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="bi bi-calculator"></i> EMI Calculator</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <label for="number_of_emis" class="form-label">Number of Monthly Installments (EMIs)</label>
                                        <input type="number" class="form-control" id="number_of_emis" min="2" max="24" value="3">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="button" class="btn btn-info w-100" id="calculateEmiBtn">
                                            <i class="bi bi-calculator"></i> Calculate
                                        </button>
                                    </div>
                                </div>
                                <div id="emiResults" style="display: none;" class="mt-3">
                                    <div class="alert alert-success">
                                        <h6>Suggested EMI Plan:</h6>
                                        <p class="mb-1"><strong>Monthly EMI Amount: </strong>₹<span id="emiAmount">0.00</span></p>
                                        <p class="mb-0"><small class="text-muted">Pay this amount for <span id="emiCount">0</span> months</small></p>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-success" id="useEmiAmount">
                                        Use First EMI Amount
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('students.show', $student) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-cash-coin"></i> Record Payment
                            </button>
                        </div>
                    </form>
                @else
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> <strong>Course Fully Paid!</strong>
                        <p class="mb-0">This student has completed all payments for this course.</p>
                    </div>
                    <a href="{{ route('students.show', $student) }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Student
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Quick pay buttons
    document.querySelectorAll('.quick-pay').forEach(button => {
        button.addEventListener('click', function() {
            const amount = this.getAttribute('data-amount');
            document.getElementById('amount_paid').value = parseFloat(amount).toFixed(2);
        });
    });

    // EMI Calculator
    document.getElementById('calculateEmiBtn').addEventListener('click', function() {
        const numberOfEmis = document.getElementById('number_of_emis').value;
        const remainingFee = {{ $remainingFee }};
        
        if (numberOfEmis < 2 || numberOfEmis > 24) {
            alert('Please enter number of EMIs between 2 and 24');
            return;
        }
        
        const emiAmount = remainingFee / numberOfEmis;
        
        document.getElementById('emiAmount').textContent = emiAmount.toFixed(2);
        document.getElementById('emiCount').textContent = numberOfEmis;
        document.getElementById('emiResults').style.display = 'block';
    });

    // Use EMI amount button
    document.getElementById('useEmiAmount').addEventListener('click', function() {
        const emiAmount = document.getElementById('emiAmount').textContent;
        document.getElementById('amount_paid').value = emiAmount;
    });

    // Validate amount doesn't exceed remaining fee
    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        const amount = parseFloat(document.getElementById('amount_paid').value);
        const remainingFee = {{ $remainingFee }};
        
        if (amount > remainingFee) {
            e.preventDefault();
            alert(`Amount cannot exceed remaining balance of ₹${remainingFee.toFixed(2)}`);
        }
    });
</script>
@endsection
