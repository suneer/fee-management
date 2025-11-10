@extends('layouts.admin')

@section('title', 'No Profile')
@section('page-title', 'Student Profile Not Found')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="alert alert-warning">
            <h4><i class="bi bi-exclamation-triangle"></i> No Student Profile Found</h4>
            <p>Your account is not linked to a student profile. Please contact the administrator to set up your student profile.</p>
            <hr>
            <p class="mb-0"><strong>Admin Email:</strong> admin@feemanagement.com</p>
        </div>
    </div>
</div>
@endsection
