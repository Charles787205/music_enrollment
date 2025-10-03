@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1><i class="bi bi-book"></i> Manage Course Enrollments</h1>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
      <div class="card-body">
        <form method="GET" action="{{ route('admin.course-enrollments') }}" class="row g-3">
          <div class="col-md-6">
            <label for="search" class="form-label">Search</label>
            <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}"
              placeholder="Search by student or course...">
          </div>
          <div class="col-md-4">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status">
              <option value="">All Statuses</option>
              <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
              <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
              <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
              <option value="dropped" {{ request('status') == 'dropped' ? 'selected' : '' }}>Dropped</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">&nbsp;</label>
            <div>
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Filter
              </button>
              <a href="{{ route('admin.course-enrollments') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Clear
              </a>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Course Enrollments Table -->
    @if($courseEnrollments->count() > 0)
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Student</th>
                <th>Course</th>
                <th>Teacher</th>
                <th>Status</th>
                <th>Enrolled Date</th>
                <th>Completed Date</th>
                <th>Payment Status</th>
                <th>Grade</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($courseEnrollments as $enrollment)
              <tr>
                <td>
                  <div>
                    <strong>{{ $enrollment->user->name }}</strong>
                    <br>
                    <small class="text-muted">{{ $enrollment->user->email }}</small>
                  </div>
                </td>
                <td>
                  <div>
                    <strong>{{ $enrollment->course->title }}</strong>
                    <br>
                    <small class="text-muted">${{ number_format($enrollment->course->fee, 2) }}</small>
                  </div>
                </td>
                <td>{{ $enrollment->course->teacher->name ?? 'No Teacher' }}</td>
                <td>
                  <form method="POST" action="{{ route('admin.course-enrollments.update-status', $enrollment) }}"
                    class="d-inline">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                      <option value="pending" {{ $enrollment->status === 'pending' ? 'selected' : '' }}>Pending</option>
                      <option value="active" {{ $enrollment->status === 'active' ? 'selected' : '' }}>Active</option>
                      <option value="completed" {{ $enrollment->status === 'completed' ? 'selected' : '' }}>Completed</option>
                      <option value="dropped" {{ $enrollment->status === 'dropped' ? 'selected' : '' }}>Dropped</option>
                    </select>
                  </form>
                </td>
                <td>{{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('M d, Y') : 'N/A' }}</td>
                <td>{{ $enrollment->completed_at ? $enrollment->completed_at->format('M d, Y') : 'N/A' }}</td>
                <td>
                  @if($enrollment->payment_status === 'paid')
                    <span class="badge bg-success">Paid</span>
                  @elseif($enrollment->payment_status === 'pending')
                    <span class="badge bg-warning">Pending</span>
                  @elseif($enrollment->payment_status === 'overdue')
                    <span class="badge bg-danger">Overdue</span>
                  @else
                    <span class="badge bg-secondary">Unpaid</span>
                  @endif
                </td>
                <td>{{ $enrollment->grade ?? 'N/A' }}</td>
                <td>
                  <div class="btn-group" role="group">
                    <a href="{{ route('course-enrollments.show', $enrollment) }}" class="btn btn-sm btn-outline-primary">
                      <i class="bi bi-eye"></i> View
                    </a>
                    <a href="{{ route('admin.users.show', $enrollment->user) }}" class="btn btn-sm btn-outline-info">
                      <i class="bi bi-person"></i> Student
                    </a>
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
          {{ $courseEnrollments->appends(request()->query())->links() }}
        </div>
      </div>
    </div>
    @else
    <div class="text-center py-5">
      <i class="bi bi-journal-x display-1 text-muted"></i>
      <h3 class="text-muted mt-3">No course enrollments found</h3>
      <p class="text-muted">Try adjusting your search criteria.</p>
    </div>
    @endif
  </div>
</div>
@endsection