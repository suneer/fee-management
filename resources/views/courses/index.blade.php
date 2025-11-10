@extends('layouts.admin')

@section('title', 'Courses List')
@section('page-title', 'Course Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>All Courses</h2>
    <a href="{{ route('courses.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add New Course
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Course Name</th>
                        <th>Duration (Months)</th>
                        <th>Fee per Month</th>
                        <th>Total Fee</th>
                        <th>Enrolled Students</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                        <tr>
                            <td>{{ $course->id }}</td>
                            <td>
                                <strong>{{ $course->name }}</strong>
                            </td>
                            <td>{{ $course->duration }} months</td>
                            <td>₹{{ number_format($course->fee_per_month, 2) }}</td>
                            <td>
                                <strong>₹{{ number_format($course->duration * $course->fee_per_month, 2) }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-primary">
                                    {{ $course->students_count }} 
                                    {{ $course->students_count == 1 ? 'Student' : 'Students' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('courses.show', $course) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('courses.edit', $course) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $course->id }}" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $course->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirm Delete</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Are you sure you want to delete <strong>{{ $course->name }}</strong>?</p>
                                                @if($course->students_count > 0)
                                                    <div class="alert alert-warning">
                                                        <i class="bi bi-exclamation-triangle"></i>
                                                        This course has {{ $course->students_count }} enrolled student(s). 
                                                        Deleting it will remove all enrollments.
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('courses.destroy', $course) }}" method="POST">
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
                            <td colspan="7" class="text-center text-muted py-4">
                                No courses found. <a href="{{ route('courses.create') }}">Add your first course</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Links -->
        <div class="d-flex justify-content-center mt-3">
            {{ $courses->links() }}
        </div>
    </div>
</div>

<!-- Summary Card -->
@if($courses->total() > 0)
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6>Total Courses</h6>
                <h2>{{ $courses->total() }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6>Total Enrollments</h6>
                <h2>{{ \App\Models\Course::withCount('students')->get()->sum('students_count') }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6>Avg Fee/Month</h6>
                <h2>₹{{ number_format(\App\Models\Course::avg('fee_per_month'), 0) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h6>Highest Fee</h6>
                <h2>₹{{ number_format(\App\Models\Course::max('fee_per_month'), 0) }}</h2>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
