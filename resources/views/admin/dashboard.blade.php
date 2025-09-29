@extends('layouts.app')

@section('content')
<div class="page-header-modern">
  <h1 class="page-title-modern">
    <i class="bi bi-speedometer2"></i> Admin Dashboard
  </h1>
  <p class="page-subtitle-modern">Welcome back, {{ auth()->user()->name }}! Here's what's happening today.</p>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
  <div class="col-lg-3 col-md-6 mb-4">
    <div class="stats-card">
      <div class="stats-card-icon primary">
        <i class="bi bi-people-fill"></i>
      </div>
      <div class="stats-card-value">{{ $stats['total_users'] }}</div>
      <div class="stats-card-label">Total Users</div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 mb-4">
    <div class="stats-card">
      <div class="stats-card-icon success">
        <i class="bi bi-mortarboard-fill"></i>
      </div>
      <div class="stats-card-value">{{ $stats['total_students'] }}</div>
      <div class="stats-card-label">Students</div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 mb-4">
    <div class="stats-card">
      <div class="stats-card-icon info">
        <i class="bi bi-book"></i>
      </div>
      <div class="stats-card-value">{{ $stats['active_courses'] }}</div>
      <div class="stats-card-label">Active Courses</div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 mb-4">
    <div class="stats-card">
      <div class="stats-card-icon warning">
        <i class="bi bi-journal-check"></i>
      </div>
      <div class="stats-card-value">{{ $stats['active_course_enrollments'] }}</div>
      <div class="stats-card-label">Course Enrollments</div>
    </div>
  </div>
</div>

<div class="row mb-4">
  <div class="col-lg-3 col-md-6 mb-4">
    <div class="stats-card">
      <div class="stats-card-icon danger">
        <i class="bi bi-briefcase-fill"></i>
      </div>
      <div class="stats-card-value">{{ $stats['total_employees'] }}</div>
      <div class="stats-card-label">Employees</div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 mb-4">
    <div class="stats-card">
      <div class="stats-card-icon info">
        <i class="bi bi-music-note-list"></i>
      </div>
      <div class="stats-card-value">{{ $stats['total_instruments'] }}</div>
      <div class="stats-card-label">Total Instruments</div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 mb-4">
    <div class="stats-card">
      <div class="stats-card-icon success">
        <i class="bi bi-check-circle-fill"></i>
      </div>
      <div class="stats-card-value">{{ $stats['available_instruments'] }}</div>
      <div class="stats-card-label">Available Instruments</div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 mb-4">
    <div class="stats-card">
      <div class="stats-card-icon primary">
        <i class="bi bi-box-arrow-down"></i>
      </div>
      <div class="stats-card-value">{{ $stats['active_instrument_borrows'] }}</div>
      <div class="stats-card-label">Active Borrows</div>
    </div>
  </div>
</div>

<!-- Recent Activity -->
<div class="row">
  <div class="col-lg-6 mb-4">
    <div class="modern-table">
      <div class="card-header d-flex justify-content-between align-items-center bg-white p-3 border-bottom">
        <h5 class="mb-0"><i class="bi bi-journal-check me-2"></i> Recent Course Enrollments</h5>
        <a href="{{ route('admin.enrollments') }}" class="btn btn-modern btn-primary btn-sm">View All</a>
      </div>
      <div class="card-body p-0">
        @if($recent_course_enrollments->count() > 0)
        <div class="table-responsive">
          <table class="table table-sm mb-0">
            <thead>
              <tr>
                <th class="px-3 py-3">Student</th>
                <th class="px-3 py-3">Course</th>
                <th class="px-3 py-3">Status</th>
                <th class="px-3 py-3">Date</th>
              </tr>
            </thead>
            <tbody>
              @foreach($recent_course_enrollments as $enrollment)
              <tr>
                <td class="px-3 py-3">{{ $enrollment->user->name }}</td>
                <td class="px-3 py-3">{{ Str::limit($enrollment->course->title, 30) }}</td>
                <td class="px-3 py-3">
                  <span
                    class="badge-modern badge-{{ $enrollment->status === 'active' ? 'success' : ($enrollment->status === 'pending' ? 'warning' : 'info') }}">
                    {{ ucfirst($enrollment->status) }}
                  </span>
                </td>
                <td class="px-3 py-3">{{ $enrollment->created_at->format('M d, Y') }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @else
        <div class="p-4 text-center">
          <p class="text-muted mb-0">No recent course enrollments found.</p>
        </div>
        @endif
      </div>
    </div>
  </div>

  <div class="col-lg-6 mb-4">
    <div class="modern-table">
      <div class="card-header d-flex justify-content-between align-items-center bg-white p-3 border-bottom">
        <h5 class="mb-0"><i class="bi bi-box-arrow-down me-2"></i> Recent Instrument Borrows</h5>
        <a href="{{ route('employee.enrollments') }}" class="btn btn-modern btn-primary btn-sm">View All</a>
      </div>
      <div class="card-body p-0">
        @if($recent_instrument_borrows->count() > 0)
        <div class="table-responsive">
          <table class="table table-sm mb-0">
            <thead>
              <tr>
                <th class="px-3 py-3">Student</th>
                <th class="px-3 py-3">Instrument</th>
                <th class="px-3 py-3">Status</th>
                <th class="px-3 py-3">Date</th>
              </tr>
            </thead>
            <tbody>
              @foreach($recent_instrument_borrows as $borrow)
              <tr>
                <td class="px-3 py-3">{{ $borrow->user->name }}</td>
                <td class="px-3 py-3">{{ $borrow->instrument->name }}</td>
                <td class="px-3 py-3">
                  <span
                    class="badge-modern badge-{{ $borrow->status === 'borrowed' ? 'success' : ($borrow->status === 'pending' ? 'warning' : ($borrow->status === 'overdue' ? 'danger' : 'info')) }}">
                    {{ ucfirst($borrow->status) }}
                  </span>
                </td>
                <td class="px-3 py-3">{{ $borrow->created_at->format('M d, Y') }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @else
        <div class="p-4 text-center">
          <p class="text-muted mb-0">No recent instrument borrows found.</p>
        </div>
        @endif
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-person-plus"></i> Recent Users</h5>
        <a href="{{ route('admin.users') }}" class="btn btn-sm btn-outline-primary">View All</a>
      </div>
      <div class="card-body">
        @if($recent_users->count() > 0)
        <div class="list-group list-group-flush">
          @foreach($recent_users as $user)
          <div class="list-group-item px-0 py-2">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <h6 class="mb-0">{{ $user->name }}</h6>
                <small class="text-muted">{{ $user->email }}</small>
              </div>
              <span
                class="badge bg-{{ $user->user_type === 'admin' ? 'danger' : ($user->user_type === 'employee' ? 'warning' : 'info') }}">
                {{ ucfirst($user->user_type) }}
              </span>
            </div>
          </div>
          @endforeach
        </div>
        @else
        <p class="text-muted mb-0">No recent users found.</p>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-lightning-fill"></i> Quick Actions</h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-2 mb-2">
            <a href="{{ route('admin.users') }}" class="btn btn-outline-primary w-100">
              <i class="bi bi-people"></i> Manage Users
            </a>
          </div>
          <div class="col-md-2 mb-2">
            <a href="{{ route('courses.index') }}" class="btn btn-outline-info w-100">
              <i class="bi bi-book"></i> Manage Courses
            </a>
          </div>
          <div class="col-md-2 mb-2">
            <a href="{{ route('admin.enrollments') }}" class="btn btn-outline-success w-100">
              <i class="bi bi-box-arrow-down"></i> Instrument Borrows
            </a>
          </div>
          <div class="col-md-2 mb-2">
            <a href="{{ route('instruments.index') }}" class="btn btn-outline-warning w-100">
              <i class="bi bi-music-note-list"></i> Instruments
            </a>
          </div>
          <div class="col-md-2 mb-2">
            <a href="{{ route('courses.create') }}" class="btn btn-outline-secondary w-100">
              <i class="bi bi-plus-circle"></i> Add Course
            </a>
          </div>
          <div class="col-md-2 mb-2">
            <a href="{{ route('instruments.create') }}" class="btn btn-outline-dark w-100">
              <i class="bi bi-plus-square"></i> Add Instrument
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection