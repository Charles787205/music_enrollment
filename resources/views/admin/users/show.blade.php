@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-12">
    <!-- Back Button -->
    <div class="mb-3">
      <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Users
      </a>
    </div>

    <!-- User Information Card -->
    <div class="card mb-4">
      <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
          <h2 class="card-title mb-0">
            <i class="bi bi-person-circle"></i> {{ $user->name }}
          </h2>
          <div class="btn-group">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning">
              <i class="bi bi-pencil"></i> Edit User
            </a>
            @if($user->id !== auth()->id())
            <form method="POST" action="{{ route('admin.users.delete', $user) }}" class="d-inline"
              onsubmit="return confirm('Are you sure you want to delete this user?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-danger">
                <i class="bi bi-trash"></i> Delete User
              </button>
            </form>
            @endif
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <h5>Personal Information</h5>
            <p><strong>Full Name:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Phone:</strong> {{ $user->phone ?? 'Not provided' }}</p>
            <p><strong>Address:</strong> {{ $user->address ?? 'Not provided' }}</p>
            <p><strong>Date of Birth:</strong>
              {{ $user->date_of_birth ? $user->date_of_birth->format('F d, Y') : 'Not provided' }}</p>
          </div>
          <div class="col-md-6">
            <h5>Account Information</h5>
            <p>
              <strong>User Type:</strong>
              <span
                class="badge bg-{{ $user->user_type === 'admin' ? 'danger' : ($user->user_type === 'employee' ? 'warning' : 'info') }}">
                {{ ucfirst($user->user_type) }}
              </span>
            </p>
            @if($user->isStudent())
            <p>
              <strong>Enrollment Status:</strong>
              <span class="badge bg-{{ $user->is_enrolled ? 'success' : 'secondary' }}">
                {{ $user->is_enrolled ? 'Enrolled' : 'Not Enrolled' }}
              </span>
            </p>
            @endif
            <p><strong>Account Created:</strong> {{ $user->created_at->format('F d, Y \a\t g:i A') }}</p>
            <p><strong>Last Updated:</strong> {{ $user->updated_at->format('F d, Y \a\t g:i A') }}</p>
            @if($user->email_verified_at)
            <p><strong>Email Verified:</strong> {{ $user->email_verified_at->format('F d, Y \a\t g:i A') }}</p>
            @else
            <p><strong>Email Verified:</strong> <span class="text-danger">Not verified</span></p>
            @endif
          </div>
        </div>
      </div>
    </div>

    <!-- Activity Overview Cards -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="card bg-primary text-white">
          <div class="card-body text-center">
            <h4>{{ $courseEnrollments->count() }}</h4>
            <p class="mb-0">Course Enrollments</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card bg-success text-white">
          <div class="card-body text-center">
            <h4>{{ $courseEnrollments->where('status', 'active')->count() }}</h4>
            <p class="mb-0">Active Courses</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card bg-info text-white">
          <div class="card-body text-center">
            <h4>{{ $instrumentBorrows->count() }}</h4>
            <p class="mb-0">Instrument Borrows</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card bg-warning text-white">
          <div class="card-body text-center">
            <h4>{{ $instrumentBorrows->where('status', 'active')->count() }}</h4>
            <p class="mb-0">Active Borrows</p>
          </div>
        </div>
      </div>
    </div>

    @if($user->isStudent())
    <!-- Course Enrollments -->
    <div class="card mb-4">
      <div class="card-header">
        <h3 class="card-title mb-0">
          <i class="bi bi-journal-check"></i> Course Enrollments
        </h3>
      </div>
      <div class="card-body">
        @if($courseEnrollments->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Course</th>
                <th>Teacher</th>
                <th>Status</th>
                <th>Enrolled Date</th>
                <th>Completion Date</th>
                <th>Progress</th>
              </tr>
            </thead>
            <tbody>
              @foreach($courseEnrollments as $enrollment)
              <tr>
                <td>
                  <strong>{{ $enrollment->course->name }}</strong>
                  @if($enrollment->course->description)
                  <br><small class="text-muted">{{ Str::limit($enrollment->course->description, 80) }}</small>
                  @endif
                </td>
                <td>
                  @if($enrollment->teacher)
                  {{ $enrollment->teacher->name }}
                  <br><small class="text-muted">{{ $enrollment->teacher->email }}</small>
                  @else
                  <span class="text-muted">Not assigned</span>
                  @endif
                </td>
                <td>
                  @if($enrollment->status === 'active')
                  <span class="badge bg-success">Active</span>
                  @elseif($enrollment->status === 'completed')
                  <span class="badge bg-primary">Completed</span>
                  @elseif($enrollment->status === 'dropped')
                  <span class="badge bg-danger">Dropped</span>
                  @else
                  <span class="badge bg-secondary">{{ ucfirst($enrollment->status) }}</span>
                  @endif
                </td>
                <td>{{ $enrollment->created_at->format('M d, Y') }}</td>
                <td>
                  @if($enrollment->completion_date)
                  {{ $enrollment->completion_date->format('M d, Y') }}
                  @else
                  <span class="text-muted">In progress</span>
                  @endif
                </td>
                <td>
                  @if($enrollment->progress_percentage !== null)
                  <div class="progress" style="height: 20px;">
                    <div class="progress-bar" role="progressbar" style="width: {{ $enrollment->progress_percentage }}%"
                      aria-valuenow="{{ $enrollment->progress_percentage }}" aria-valuemin="0" aria-valuemax="100">
                      {{ $enrollment->progress_percentage }}%
                    </div>
                  </div>
                  @else
                  <span class="text-muted">Not tracked</span>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @else
        <div class="text-center py-4">
          <i class="bi bi-journal-x display-4 text-muted"></i>
          <h5 class="text-muted mt-3">No course enrollments</h5>
          <p class="text-muted">This user has not enrolled in any courses yet.</p>
        </div>
        @endif
      </div>
    </div>

    <!-- Instrument Borrows -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title mb-0">
          <i class="bi bi-music-note-list"></i> Instrument Borrowing History
        </h3>
      </div>
      <div class="card-body">
        @if($instrumentBorrows->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Instrument</th>
                <th>Type</th>
                <th>Brand</th>
                <th>Status</th>
                <th>Borrowed Date</th>
                <th>Due Date</th>
                <th>Returned Date</th>
                <th>Duration</th>
              </tr>
            </thead>
            <tbody>
              @foreach($instrumentBorrows as $borrow)
              <tr>
                <td>
                  <strong>{{ $borrow->instrument->name }}</strong>
                  @if($borrow->instrument->serial_number)
                  <br><small class="text-muted">SN: {{ $borrow->instrument->serial_number }}</small>
                  @endif
                </td>
                <td>{{ $borrow->instrument->type }}</td>
                <td>{{ $borrow->instrument->brand ?? 'N/A' }}</td>
                <td>
                  @if($borrow->status === 'active')
                  <span class="badge bg-success">Active</span>
                  @elseif($borrow->status === 'returned')
                  <span class="badge bg-secondary">Returned</span>
                  @elseif($borrow->status === 'overdue')
                  <span class="badge bg-danger">Overdue</span>
                  @else
                  <span class="badge bg-warning">{{ ucfirst($borrow->status) }}</span>
                  @endif
                </td>
                <td>{{ $borrow->created_at->format('M d, Y') }}</td>
                <td>
                  @if($borrow->due_date)
                  {{ $borrow->due_date->format('M d, Y') }}
                  @if($borrow->status === 'active' && $borrow->due_date->isPast())
                  <span class="text-danger ms-2">
                    <i class="bi bi-exclamation-triangle"></i> Overdue
                  </span>
                  @endif
                  @else
                  N/A
                  @endif
                </td>
                <td>
                  @if($borrow->returned_at)
                  {{ $borrow->returned_at->format('M d, Y') }}
                  @else
                  <span class="text-muted">Not returned</span>
                  @endif
                </td>
                <td>
                  @if($borrow->returned_at)
                  {{ $borrow->created_at->diffInDays($borrow->returned_at) }} days
                  @elseif($borrow->status === 'active')
                  {{ $borrow->created_at->diffInDays(now()) }} days (ongoing)
                  @else
                  N/A
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @else
        <div class="text-center py-4">
          <i class="bi bi-music-note-list display-4 text-muted"></i>
          <h5 class="text-muted mt-3">No borrowing history</h5>
          <p class="text-muted">This user has not borrowed any instruments yet.</p>
        </div>
        @endif
      </div>
    </div>
    @else
    <!-- Non-Student Users -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title mb-0">
          <i class="bi bi-info-circle"></i> {{ ucfirst($user->user_type) }} Information
        </h3>
      </div>
      <div class="card-body">
        <div class="text-center py-4">
          <i class="bi bi-{{ $user->user_type === 'admin' ? 'shield-check' : 'briefcase' }} display-4 text-muted"></i>
          <h5 class="text-muted mt-3">{{ ucfirst($user->user_type) }} Account</h5>
          <p class="text-muted">
            @if($user->user_type === 'admin')
            This user has administrative privileges and can manage the entire system.
            @else
            This user is an employee and can manage students and their enrollments.
            @endif
          </p>
        </div>
      </div>
    </div>
    @endif
  </div>
</div>
@endsection