<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Fee Management System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .status-active {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        .status-inactive {
            background-color: #f8d7da;
            color: #842029;
        }
        .status-suspended {
            background-color: #fff3cd;
            color: #664d03;
        }
        .status-rejected {
            background-color: #f8d7da;
            color: #842029;
        }
        .sidebar {
            min-height: 100vh;
            background-color: #212529;
        }
        .sidebar a {
            color: #adb5bd;
            text-decoration: none;
            padding: 0.75rem 1rem;
            display: block;
        }
        .sidebar a:hover {
            background-color: #343a40;
            color: #fff;
        }
        .sidebar a.active {
            background-color: #0d6efd;
            color: #fff;
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-0">
                <div class="p-3 text-white">
                    <h4>Fee Management</h4>
                </div>
                <nav>
                    <a href="{{ route('students.index') }}" class="{{ request()->routeIs('students.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> Students
                    </a>
                    <a href="{{ route('courses.index') }}" class="{{ request()->routeIs('courses.*') ? 'active' : '' }}">
                        <i class="bi bi-book"></i> Courses
                    </a>
                    <a href="{{ route('payments.create') }}" class="{{ request()->routeIs('payments.*') ? 'active' : '' }}">
                        <i class="bi bi-cash-coin"></i> Payments
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10">
                <nav class="navbar navbar-light bg-light">
                    <div class="container-fluid">
                        <span class="navbar-brand mb-0 h1">@yield('page-title', 'Dashboard')</span>
                    </div>
                </nav>

                <div class="container-fluid p-4">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
