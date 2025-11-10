@extends('layouts.admin')

@section('title', 'Record Payment')
@section('page-title', 'Record Payment')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Record New Payment</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('payments.store') }}" method="POST" id="paymentForm">
                    @csrf

                    <div class="mb-3">
                        <label for="student_id" class="form-label">Select Student <span class="text-danger">*</span></label>
                        <select class="form-control @error('student_id') is-invalid @enderror" 
                                id="student_id" name="student_id" required>
                            <option value="">-- Select Student --</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                    {{ $student->name }} ({{ $student->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('student_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="course_id" class="form-label">Select Course <span class="text-danger">*</span></label>
                        <select class="form-control @error('course_id') is-invalid @enderror" 
                                id="course_id" name="course_id" required disabled>
                            <option value="">-- Select Course --</option>
                        </select>
                        @error('course_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Select a student first</small>
                    </div>

                    <!-- Fee Details Display -->
                    <div id="feeDetails" style="display: none;">
                        <div class="alert alert-info">
                            <h6 class="mb-2">Course Fee Details:</h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td><strong>Total Course Fee:</strong></td>
                                    <td class="text-end">₹<span id="totalCourseFee">0.00</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Already Paid:</strong></td>
                                    <td class="text-end text-success">₹<span id="totalPaid">0.00</span></td>
                                </tr>
                                <tr class="border-top">
                                    <td><strong>Remaining Balance:</strong></td>
                                    <td class="text-end text-danger"><strong>₹<span id="remainingFee">0.00</span></strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="amount_paid" class="form-label">Amount to Pay (₹) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control @error('amount_paid') is-invalid @enderror" 
                                       id="amount_paid" name="amount_paid" value="{{ old('amount_paid') }}" min="0" required>
                                @error('amount_paid')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_of_payment" class="form-label">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date_of_payment') is-invalid @enderror" 
                                       id="date_of_payment" name="date_of_payment" value="{{ old('date_of_payment', date('Y-m-d')) }}" required>
                                @error('date_of_payment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- EMI Calculator Section -->
                    <div class="card mb-3" id="emiCalculator" style="display: none;">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="bi bi-calculator"></i> EMI Calculator</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="number_of_emis" class="form-label">Number of EMIs</label>
                                    <input type="number" class="form-control" id="number_of_emis" min="1" max="24" value="3">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="button" class="btn btn-info w-100" id="calculateEmiBtn">
                                        <i class="bi bi-calculator"></i> Calculate EMI
                                    </button>
                                </div>
                            </div>
                            <div id="emiResults" style="display: none;" class="mt-3">
                                <div class="alert alert-success">
                                    <h6>EMI Plan:</h6>
                                    <p class="mb-1"><strong>EMI Amount: </strong>₹<span id="emiAmount">0.00</span></p>
                                    <p class="mb-0"><small class="text-muted">You can pay this amount in <span id="emiCount">0</span> monthly installments</small></p>
                                </div>
                                <div id="emiSchedule" class="table-responsive"></div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('students.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-cash-coin"></i> Record Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const studentSelect = document.getElementById('student_id');
    const courseSelect = document.getElementById('course_id');
    const feeDetails = document.getElementById('feeDetails');
    const emiCalculator = document.getElementById('emiCalculator');
    const amountInput = document.getElementById('amount_paid');
    
    let currentFeeData = null;

    // When student is selected, load their enrolled courses
    studentSelect.addEventListener('change', function() {
        const studentId = this.value;
        courseSelect.innerHTML = '<option value="">-- Select Course --</option>';
        courseSelect.disabled = true;
        feeDetails.style.display = 'none';
        emiCalculator.style.display = 'none';
        
        if (studentId) {
            // Fetch student's enrolled courses
            fetch(`/students/${studentId}`)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // This is a simplified approach - in production, use an API endpoint
                    courseSelect.disabled = false;
                    courseSelect.innerHTML = '<option value="">-- Select Course --</option>';
                    
                    @foreach($courses as $course)
                        courseSelect.innerHTML += `<option value="{{ $course->id }}">{{ $course->name }} - ₹{{ number_format($course->fee_per_month, 2) }}/month</option>`;
                    @endforeach
                });
        }
    });

    // When course is selected, fetch fee details
    courseSelect.addEventListener('change', function() {
        const studentId = studentSelect.value;
        const courseId = this.value;
        
        if (studentId && courseId) {
            fetch('{{ route("payments.course-fee-details") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    student_id: studentId,
                    course_id: courseId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }
                
                currentFeeData = data;
                
                // Display fee details
                document.getElementById('totalCourseFee').textContent = data.total_course_fee.toFixed(2);
                document.getElementById('totalPaid').textContent = data.total_paid.toFixed(2);
                document.getElementById('remainingFee').textContent = data.remaining_fee.toFixed(2);
                
                feeDetails.style.display = 'block';
                
                // Show EMI calculator if there's remaining balance
                if (data.remaining_fee > 0) {
                    emiCalculator.style.display = 'block';
                    amountInput.max = data.remaining_fee;
                    amountInput.placeholder = `Max: ₹${data.remaining_fee.toFixed(2)}`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        } else {
            feeDetails.style.display = 'none';
            emiCalculator.style.display = 'none';
        }
    });

    // EMI Calculator
    document.getElementById('calculateEmiBtn').addEventListener('click', function() {
        const studentId = studentSelect.value;
        const courseId = courseSelect.value;
        const numberOfEmis = document.getElementById('number_of_emis').value;
        
        if (!studentId || !courseId || !numberOfEmis) {
            alert('Please select student and course first');
            return;
        }
        
        fetch('{{ route("payments.calculate-emi") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                student_id: studentId,
                course_id: courseId,
                number_of_emis: numberOfEmis
            })
        })
        .then(response => response.json())
        .then(data => {
            // Display EMI results
            document.getElementById('emiAmount').textContent = data.emi_amount.toFixed(2);
            document.getElementById('emiCount').textContent = data.number_of_emis;
            
            // Display EMI schedule
            let scheduleHtml = '<table class="table table-sm table-striped"><thead><tr><th>Installment</th><th>Amount</th><th>Due Date</th></tr></thead><tbody>';
            data.emi_schedule.forEach(emi => {
                scheduleHtml += `<tr><td>EMI ${emi.installment_number}</td><td>₹${emi.amount.toFixed(2)}</td><td>${emi.due_date}</td></tr>`;
            });
            scheduleHtml += '</tbody></table>';
            
            document.getElementById('emiSchedule').innerHTML = scheduleHtml;
            document.getElementById('emiResults').style.display = 'block';
            
            // Auto-fill the amount with first EMI
            amountInput.value = data.emi_amount.toFixed(2);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to calculate EMI');
        });
    });
</script>
@endsection
