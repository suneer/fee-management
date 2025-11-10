@extends('layouts.app')

@section('title', 'Add New Course')
@section('page-title', 'Add New Course')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Add New Course</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('courses.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Course Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Enter the full name of the course</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="duration" class="form-label">Duration (in months) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('duration') is-invalid @enderror" 
                                       id="duration" name="duration" value="{{ old('duration') }}" min="1" required>
                                @error('duration')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">How long is the course?</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fee_per_month" class="form-label">Fee per Month (₹) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control @error('fee_per_month') is-invalid @enderror" 
                                       id="fee_per_month" name="fee_per_month" value="{{ old('fee_per_month') }}" min="0" required>
                                @error('fee_per_month')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Monthly fee amount</small>
                            </div>
                        </div>
                    </div>

                    <!-- Calculated Total Fee Display -->
                    <div class="alert alert-info" id="totalFeeDisplay" style="display: none;">
                        <strong>Total Course Fee: </strong>₹<span id="totalFeeAmount">0.00</span>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('courses.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Course
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
    // Calculate and display total course fee
    function calculateTotalFee() {
        const duration = parseFloat(document.getElementById('duration').value) || 0;
        const feePerMonth = parseFloat(document.getElementById('fee_per_month').value) || 0;
        const totalFee = duration * feePerMonth;
        
        if (duration > 0 && feePerMonth > 0) {
            document.getElementById('totalFeeAmount').textContent = totalFee.toFixed(2);
            document.getElementById('totalFeeDisplay').style.display = 'block';
        } else {
            document.getElementById('totalFeeDisplay').style.display = 'none';
        }
    }

    document.getElementById('duration').addEventListener('input', calculateTotalFee);
    document.getElementById('fee_per_month').addEventListener('input', calculateTotalFee);
</script>
@endsection
