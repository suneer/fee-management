@extends('layouts.app')

@section('title', 'Course Details')
@section('page-title', 'Course Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>{{ $course->name }}</h2>
    <div>
        <a href="{{ route('courses.edit', $course) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
        <a href="{{ route('courses.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Course Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Course Name:</th>
                        <td><strong>{{ $course->name }}</strong></td>
                    </tr>
                    <tr>
                        <th>Duration:</th>
                        <td>{{ $course->duration }} months</td>
                    </tr>
                    <tr>
                        <th>Fee per Month:</th>
                        <td>₹{{ number_format($course->fee_per_month, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Total Course Fee:</th>
                        <td><strong class="text-primary">₹{{ number_format($course->duration * $course->fee_per_month, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <th>Enrolled Students:</th>
                        <td>
                            <span class="badge bg-primary">
                                {{ $course->students->count() }} 
                                {{ $course->students->count() == 1 ? 'Student' : 'Students' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Created At:</th>
                        <td>{{ $course->created_at->format('d M, Y') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Revenue Summary</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="50%">Monthly Revenue:</th>
                        <td class="text-end">
                            <strong>₹{{ number_format($course->students->count() * $course->fee_per_month, 2) }}</strong>
                        </td>
                    </tr>
                    <tr>
                        <th>Potential Total Revenue:</th>
                        <td class="text-end text-success">
                            <strong>₹{{ number_format($course->students->count() * $course->duration * $course->fee_per_month, 2) }}</strong>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Enrolled Students</h5>
            </div>
            <div class="card-body">
                @if($course->students->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Enrolled At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($course->students as $student)
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
                                        <td>{{ $student->pivot->enrolled_at ?? 'N/A' }}</td>
                                        <td>
                                            <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center py-4">No students enrolled in this course yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
