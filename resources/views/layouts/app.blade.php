<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Music Enrollment System') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 70px;
            --primary-color: #6366f1;
            --primary-dark: #4f46e5;
            --sidebar-bg: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            --sidebar-text: #e2e8f0;
            --sidebar-text-active: #ffffff;
            --sidebar-hover: rgba(99, 102, 241, 0.1);
            --content-bg: #f8fafc;
            --border-color: #e2e8f0;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--content-bg);
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 100;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-shrink: 0;
        }

        .sidebar-logo {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .sidebar-title {
            color: var(--sidebar-text-active);
            font-weight: 600;
            font-size: 1.125rem;
            white-space: nowrap;
            opacity: 1;
            transition: opacity 0.3s;
        }

        .sidebar.collapsed .sidebar-title {
            opacity: 0;
        }

        .sidebar-nav {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 1rem 0;
            overflow-y: auto;
        }

        .nav-section {
            margin-bottom: 2rem;
        }

        .nav-section-title {
            color: #94a3b8;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0 1.5rem;
            margin-bottom: 0.75rem;
            white-space: nowrap;
            opacity: 1;
            transition: opacity 0.3s;
        }

        .sidebar.collapsed .nav-section-title {
            opacity: 0;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            color: var(--sidebar-text);
            text-decoration: none;
            transition: all 0.2s;
            position: relative;
            border: none;
            background: none;
            width: 100%;
        }

        .nav-link:hover {
            color: var(--sidebar-text-active);
            background: var(--sidebar-hover);
        }

        .nav-link.active {
            color: var(--sidebar-text-active);
            background: var(--sidebar-hover);
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: var(--primary-color);
        }

        .nav-link.logout-btn:hover {
            color: #ef4444 !important;
            background: rgba(239, 68, 68, 0.1) !important;
        }

        .nav-icon {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .nav-text {
            white-space: nowrap;
            opacity: 1;
            transition: opacity 0.3s;
        }

        .sidebar.collapsed .nav-text {
            opacity: 0;
        }

        .nav-badge {
            margin-left: auto;
            background: var(--primary-color);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: 500;
            opacity: 1;
            transition: opacity 0.3s;
        }

        .sidebar.collapsed .nav-badge {
            opacity: 0;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 100vh;
            position: relative;
            z-index: 1;
        }

        .sidebar.collapsed+.main-content {
            margin-left: var(--sidebar-collapsed-width);
        }

        .topbar {
            background: white;
            padding: 1rem 2rem;
            border-bottom: 1px solid var(--border-color);
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 10;
        }

        .sidebar-toggle {
            background: none;
            border: none;
            color: #6b7280;
            font-size: 1.25rem;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .sidebar-toggle:hover {
            background: #f3f4f6;
            color: #374151;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            gap: 0.125rem;
        }

        .user-name {
            font-weight: 600;
            color: #111827;
            font-size: 0.875rem;
        }

        .user-role {
            font-size: 0.75rem;
            color: #6b7280;
            text-transform: capitalize;
        }

        .content-area {
            padding: 2rem;
            position: relative;
            z-index: 1;
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-family: 'Poppins', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: #6b7280;
            font-size: 1rem;
        }

        .card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .alert {
            border: none;
            border-radius: 10px;
            font-weight: 500;
        }

        .alert-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .alert-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-lg);
            background: linear-gradient(135deg, var(--primary-dark), #3730a3);
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                z-index: 1050;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar.collapsed+.main-content {
                margin-left: 0;
            }
        }

        .dropdown-menu {
            border: none;
            box-shadow: var(--shadow-lg);
            border-radius: 10px;
            padding: 0.5rem;
        }

        .dropdown-item {
            border-radius: 6px;
            padding: 0.75rem 1rem;
            font-weight: 500;
        }

        .dropdown-item:hover {
            background: var(--primary-color);
            color: white;
        }
    </style>
</head>

<body>
    @auth
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <i class="bi bi-music-note-beamed"></i>
            </div>
            <div class="sidebar-title">Music School</div>
        </div>

        <nav class="sidebar-nav">
            <!-- Main Navigation -->
            <div class="nav-section">
                <div class="nav-section-title">Main</div>
                <a href="{{ route('instruments.index') }}"
                    class="nav-link {{ request()->routeIs('instruments.*') ? 'active' : '' }}">
                    <div class="nav-icon">
                        <i class="bi bi-music-note-list"></i>
                    </div>
                    <span class="nav-text">Instruments</span>
                </a>

                <a href="{{ route('courses.index') }}"
                    class="nav-link {{ request()->routeIs('courses.*') ? 'active' : '' }}">
                    <div class="nav-icon">
                        <i class="bi bi-book"></i>
                    </div>
                    <span class="nav-text">Courses</span>
                </a>

                @if(auth()->user()->isStudent())
                <a href="{{ route('course-enrollments.index') }}"
                    class="nav-link {{ request()->routeIs('course-enrollments.*') ? 'active' : '' }}">
                    <div class="nav-icon">
                        <i class="bi bi-journal-check"></i>
                    </div>
                    <span class="nav-text">My Course Enrollments</span>
                </a>

                <a href="{{ route('instrument-borrows.index') }}"
                    class="nav-link {{ request()->routeIs('instrument-borrows.*') ? 'active' : '' }}">
                    <div class="nav-icon">
                        <i class="bi bi-box-arrow-down"></i>
                    </div>
                    <span class="nav-text">My Borrowed Instruments</span>
                </a>
                @endif

                @if(auth()->user()->isStudent())
                <a href="{{ route('instrument-borrows.create') }}"
                    class="nav-link {{ request()->routeIs('instrument-borrows.create') ? 'active' : '' }}">
                    <div class="nav-icon">
                        <i class="bi bi-plus-circle"></i>
                    </div>
                    <span class="nav-text">Borrow Instrument</span>
                </a>
                @endif
            </div>

            <!-- Admin Section -->
            @if(auth()->user()->isAdmin())
            <div class="nav-section">
                <div class="nav-section-title">Administration</div>
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <div class="nav-icon">
                        <i class="bi bi-speedometer2"></i>
                    </div>
                    <span class="nav-text">Dashboard</span>
                </a>

                <a href="{{ route('admin.users') }}"
                    class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                    <div class="nav-icon">
                        <i class="bi bi-people"></i>
                    </div>
                    <span class="nav-text">Manage Users</span>
                </a>

                <a href="{{ route('admin.enrollments') }}"
                    class="nav-link {{ request()->routeIs('admin.enrollments') ? 'active' : '' }}">
                    <div class="nav-icon">
                        <i class="bi bi-clipboard-data"></i>
                    </div>
                    <span class="nav-text">Enrollments</span>
                </a>

                <a href="{{ route('instruments.create') }}"
                    class="nav-link {{ request()->routeIs('instruments.create') ? 'active' : '' }}">
                    <div class="nav-icon">
                        <i class="bi bi-plus-square"></i>
                    </div>
                    <span class="nav-text">Add Instrument</span>
                </a>
            </div>
            @endif

            <!-- Employee Section -->
            @if(auth()->user()->isEmployee())
            <div class="nav-section">
                <div class="nav-section-title">Employee</div>
                <a href="{{ route('employee.dashboard') }}"
                    class="nav-link {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
                    <div class="nav-icon">
                        <i class="bi bi-house-door"></i>
                    </div>
                    <span class="nav-text">Dashboard</span>
                </a>

                <a href="{{ route('employee.students') }}"
                    class="nav-link {{ request()->routeIs('employee.students') ? 'active' : '' }}">
                    <div class="nav-icon">
                        <i class="bi bi-mortarboard"></i>
                    </div>
                    <span class="nav-text">Students</span>
                </a>

                <a href="{{ route('employee.enrollments') }}"
                    class="nav-link {{ request()->routeIs('employee.enrollments') ? 'active' : '' }}">
                    <div class="nav-icon">
                        <i class="bi bi-box-arrow-down-left"></i>
                    </div>
                    <span class="nav-text">Manage Instrument Borrows</span>
                </a>

                <a href="{{ route('employee.payments') }}"
                    class="nav-link {{ request()->routeIs('employee.payments*') ? 'active' : '' }}">
                    <div class="nav-icon">
                        <i class="bi bi-credit-card"></i>
                    </div>
                    <span class="nav-text">Collect Payments</span>
                </a>
            </div>
            @endif

            <!-- Logout Section -->
            <div class="nav-section" style="margin-top: auto; padding-bottom: 1rem;">
                <div class="nav-section-title">Account</div>

                <!-- Change Password Link -->
                <a href="{{ route('password.change') }}"
                    class="nav-link {{ request()->routeIs('password.change*') ? 'active' : '' }}">
                    <div class="nav-icon">
                        <i class="bi bi-key"></i>
                    </div>
                    <span class="nav-text">Change Password</span>
                </a>

                <form method="POST" action="{{ route('logout') }}" class="d-inline w-100">
                    @csrf
                    <button type="submit" class="nav-link logout-btn w-100 text-start border-0 bg-transparent"
                        style="color: var(--sidebar-text);">
                        <div class="nav-icon">
                            <i class="bi bi-box-arrow-right"></i>
                        </div>
                        <span class="nav-text">Logout</span>
                    </button>
                </form>
            </div>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="topbar">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>

            <div class="user-menu">
                <div class="user-info">
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-role">{{ ucfirst(Auth::user()->user_type) }}</div>
                </div>
                <div class="dropdown">
                    <button class="user-avatar" type="button" data-bs-toggle="dropdown">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @if(auth()->user()->isAdmin())
                        <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a></li>
                        @elseif(auth()->user()->isEmployee())
                        <li><a class="dropdown-item" href="{{ route('employee.dashboard') }}">
                                <i class="bi bi-house-door me-2"></i> Dashboard
                            </a></li>
                        @endif

                        @if(auth()->user()->isStudent())
                        <li><a class="dropdown-item" href="{{ route('course-enrollments.index') }}">
                                <i class="bi bi-journal-check me-2"></i> My Course Enrollments
                            </a></li>
                        <li><a class="dropdown-item" href="{{ route('instrument-borrows.index') }}">
                                <i class="bi bi-box-arrow-down me-2"></i> My Borrowed Instruments
                            </a></li>
                        @endif
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="content-area">
            <!-- Flash Messages -->
            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @yield('content')
        </div>
    </div>
    @else
    <!-- Guest Layout -->
    @yield('content')
    @endauth

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Sidebar toggle functionality
        document.getElementById('sidebarToggle')?.addEventListener('click', function () {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');

            // Store sidebar state
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        });

        // Restore sidebar state
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

            if (isCollapsed && sidebar) {
                sidebar.classList.add('collapsed');
            }
        });

        // Mobile sidebar toggle
        document.addEventListener('click', function (e) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.getElementById('sidebarToggle');

            if (window.innerWidth <= 768) {
                if (!sidebar?.contains(e.target) && !toggle?.contains(e.target)) {
                    sidebar?.classList.remove('show');
                }
            }
        });

        // Mobile sidebar show
        document.getElementById('sidebarToggle')?.addEventListener('click', function () {
            if (window.innerWidth <= 768) {
                const sidebar = document.getElementById('sidebar');
                sidebar?.classList.toggle('show');
            }
        });
    </script>
</body>

</html>