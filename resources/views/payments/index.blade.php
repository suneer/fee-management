@extends('layouts.admin')

@section('title', 'Payments List')
@section('page-title', 'Payment Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>All Payments</h2>
    <a href="{{ route('payments.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Record New Payment
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Receipt #</th>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td>#{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <a href="{{ route('students.show', $payment->student) }}">
                                    {{ $payment->student->name }}
                                </a>
                            </td>
                            <td>{{ $payment->course->name }}</td>
                            <td class="text-success fw-bold">₹{{ number_format($payment->amount_paid, 2) }}</td>
                            <td>{{ \Carbon\Carbon::parse($payment->date_of_payment)->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('students.show', $payment->student) }}" class="btn btn-sm btn-info" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $payment->id }}" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $payment->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirm Delete</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete this payment record?
                                                <br><br>
                                                <strong>Details:</strong><br>
                                                Student: {{ $payment->student->name }}<br>
                                                Course: {{ $payment->course->name }}<br>
                                                Amount: ₹{{ number_format($payment->amount_paid, 2) }}
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('payments.destroy', $payment) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                No payments found. <a href="{{ route('payments.create') }}">Record your first payment</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Links -->
        <div class="d-flex justify-content-center mt-3">
            {{ $payments->links() }}
        </div>
    </div>
</div>
@endsection
