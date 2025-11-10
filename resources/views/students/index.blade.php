@extends('layouts.admin')

@section('title', 'Students List')
@section('page-title', 'Student Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>All Students</h2>
    <a href="{{ route('students.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add New Student
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Assigned Courses</th>
                        <th>Total Fee</th>
                        <th>Total Paid</th>
                        <th>Balance</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td>{{ $student->id }}</td>
                            <td>{{ $student->name }}</td>
                            <td>{{ $student->email }}</td>
                            <td>{{ $student->phone }}</td>
                            <td>
                                <span class="status-badge status-{{ $student->status }}">
                                    {{ ucfirst($student->status) }}
                                </span>
                            </td>
                            <td>
                                @if($student->courses->count() > 0)
                                    <ul class="list-unstyled mb-0">
                                        @foreach($student->courses as $course)
                                            <li><small>{{ $course->name }}</small></li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted">No courses assigned</span>
                                @endif
                            </td>
                            <td>₹{{ number_format($student->total_fee, 2) }}</td>
                            <td>₹{{ number_format($student->total_paid, 2) }}</td>
                            <td>
                                @php
                                    $balance = $student->balance;
                                @endphp
                                <span class="{{ $balance > 0 ? 'text-danger' : 'text-success' }}">
                                    ₹{{ number_format(abs($balance), 2) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('students.edit', $student) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $student->id }}" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                                <!-- Status Buttons -->
                                <div class="btn-group mt-1" role="group">
                                    @if($student->status === 'active')
                                        <form action="{{ route('students.toggle-status', $student) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-secondary" title="Deactivate">
                                                <i class="bi bi-toggle-off"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('students.activate', $student) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success" title="Activate">
                                                <i class="bi bi-toggle-on"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if($student->status !== 'suspended')
                                        <form action="{{ route('students.suspend', $student) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-warning" title="Suspend">
                                                <i class="bi bi-pause-circle"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if($student->status !== 'rejected')
                                        <form action="{{ route('students.reject', $student) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Reject">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $student->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirm Delete</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete {{ $student->name }}?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('students.destroy', $student) }}" method="POST">
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
                            <td colspan="10" class="text-center text-muted py-4">
                                No students found. <a href="{{ route('students.create') }}">Add your first student</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Links -->
        <div class="d-flex justify-content-center mt-3">
            {{ $students->links() }}
        </div>
    </div>
</div>
@endsection
