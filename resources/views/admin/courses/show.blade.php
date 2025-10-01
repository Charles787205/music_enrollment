@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-12">
    <!-- Back Button -->
    <div class="mb-3">
      <a href="{{ route('admin.courses') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Courses
      </a>
    </div>

    <!-- Course Information Card -->
    <div class="card mb-4">
      <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
          <h2 class="card-title mb-0">
            <i class="bi bi-book-half"></i> {{ $course->name }}
          </h2>
          <div class="btn-group">
            <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-info">
              <i class="bi bi-eye"></i> View Course
            </a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <h5>Course Details</h5>
            <p><strong>Description:</strong> {{ $course->description ?? 'No description provided' }}</p>
            <p><strong>Duration:</strong>
              {{ $course->duration_weeks ? $course->duration_weeks . ' weeks' : 'Not specified' }}</p>
            <p><strong>Fee:</strong> {{ $course->fee ? '$' . number_format($course->fee, 2) : 'Free' }}</p>
            <p><strong>Prerequisites:</strong> {{ $course->prerequisites ?? 'None' }}</p>
          </div>
          <div class="col-md-6">
            <h5>Status & Statistics</h5>
            <p>
              <strong>Status:</strong>
              @if($course->status === 'active')
              <span class="badge bg-success">Active</span>
              @elseif($course->status === 'inactive')
              <span class="badge bg-secondary">Inactive</span>
              @elseif($course->status === 'completed')
              <span class="badge bg-primary">Completed</span>
              @else
              <span class="badge bg-warning">{{ ucfirst($course->status) }}</span>
              @endif
            </p>
            <p><strong>Total Students:</strong> {{ $course->enrollments->count() }}</p>
            <p><strong>Active Enrollments:</strong> {{ $course->enrollments->where('status', 'active')->count() }}</p>
            <p><strong>Pending Enrollments:</strong> {{ $course->enrollments->where('status', 'pending')->count() }}</p>
            <p><strong>Created:</strong> {{ $course->created_at->format('F d, Y') }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Teacher Assignment Card -->
    <div class="card mb-4">
      <div class="card-header">
        <h3 class="card-title mb-0">
          <i class="bi bi-person-badge"></i> Teacher Assignment
        </h3>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.courses.update-teacher', $course) }}">
          @csrf
          @method('PUT')
          <div class="row">
            <div class="col-md-6">
              <label for="teacher_id" class="form-label">Assign Teacher</label>
              <select class="form-select" id="teacher_id" name="teacher_id">
                <option value="">No teacher assigned</option>
                @foreach($teachers as $teacher)
                <option value="{{ $teacher->id }}" {{ $course->teacher_id == $teacher->id ? 'selected' : '' }}>
                  {{ $teacher->name }} ({{ $teacher->specialization ?? 'General' }})
                </option>
                @endforeach
              </select>
              <div class="form-text">Select a teacher to assign to this course.</div>
            </div>
            <div class="col-md-6 d-flex align-items-end">
              <button type="submit" class="btn btn-warning">
                <i class="bi bi-person-check"></i> Update Teacher
              </button>
            </div>
          </div>
        </form>

        @if($course->teacher)
        <div class="mt-3 p-3 bg-light rounded">
          <h6>Current Teacher</h6>
          <div class="d-flex align-items-center">
            <div class="me-3">
              <div
                class="user-avatar bg-warning text-white rounded-circle d-flex align-items-center justify-content-center"
                style="width: 40px; height: 40px;">
                {{ substr($course->teacher->name, 0, 1) }}
              </div>
            </div>
            <div>
              <strong>{{ $course->teacher->name }}</strong>
              @if($course->teacher->specialization)
              <br><small class="text-muted">{{ $course->teacher->specialization }}</small>
              @endif
              @if($course->teacher->email)
              <br><small class="text-muted">{{ $course->teacher->email }}</small>
              @endif
            </div>
          </div>
        </div>
        @endif
      </div>
    </div>

    <!-- Student Assignment Card -->
    <div class="card mb-4">
      <div class="card-header">
        <h3 class="card-title mb-0">
          <i class="bi bi-person-plus"></i> Assign New Student
        </h3>
      </div>
      <div class="card-body">
        @if($availableStudents->count() > 0)
        <form method="POST" action="{{ route('admin.courses.assign-student', $course) }}">
          @csrf
          <div class="row g-3">
            <div class="col-md-4">
              <label for="student_id" class="form-label">Select Student</label>
              <select class="form-select" id="student_id" name="student_id" required>
                <option value="">Choose a student...</option>
                @foreach($availableStudents as $student)
                <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2">
              <label for="status" class="form-label">Status</label>
              <select class="form-select" id="status" name="status" required>
                <option value="pending">Pending</option>
                <option value="active">Active</option>
              </select>
            </div>
            <div class="col-md-2">
              <label for="total_fee" class="form-label">Total Fee</label>
              <input type="number" class="form-control" id="total_fee" name="total_fee" value="{{ $course->fee }}"
                step="0.01" min="0" required>
            </div>
            <div class="col-md-2">
              <label for="payment_due_date" class="form-label">Payment Due</label>
              <input type="date" class="form-control" id="payment_due_date" name="payment_due_date"
                value="{{ now()->addDays(30)->format('Y-m-d') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
              <button type="submit" class="btn btn-success w-100">
                <i class="bi bi-person-plus"></i> Assign
              </button>
            </div>
          </div>
        </form>
        @else
        <div class="text-center py-3">
          <i class="bi bi-people display-4 text-muted"></i>
          <h6 class="text-muted mt-2">No available students</h6>
          <p class="text-muted mb-0">All students are already enrolled in this course.</p>
        </div>
        @endif
      </div>
    </div>

    <!-- Enrolled Students Card -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title mb-0">
          <i class="bi bi-people"></i> Enrolled Students ({{ $course->enrollments->count() }})
        </h3>
      </div>
      <div class="card-body">
        @if($course->enrollments->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Student</th>
                <th>Status</th>
                <th>Payment Status</th>
                <th>Amount Paid</th>
                <th>Due Date</th>
                <th>Enrolled Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($course->enrollments as $enrollment)
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="me-3">
                      <div
                        class="user-avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 35px; height: 35px; font-size: 0.8rem;">
                        {{ substr($enrollment->user->name, 0, 1) }}
                      </div>
                    </div>
                    <div>
                      <strong>{{ $enrollment->user->name }}</strong>
                      <br><small class="text-muted">{{ $enrollment->user->email }}</small>
                    </div>
                  </div>
                </td>
                <td>
                  @if($enrollment->status === 'active')
                  <span class="badge bg-success">Active</span>
                  @elseif($enrollment->status === 'pending')
                  <span class="badge bg-warning">Pending</span>
                  @elseif($enrollment->status === 'completed')
                  <span class="badge bg-primary">Completed</span>
                  @elseif($enrollment->status === 'dropped')
                  <span class="badge bg-danger">Dropped</span>
                  @else
                  <span class="badge bg-secondary">{{ ucfirst($enrollment->status) }}</span>
                  @endif
                </td>
                <td>
                  @if($enrollment->payment_status === 'paid')
                  <span class="badge bg-success">Paid</span>
                  @elseif($enrollment->payment_status === 'partial')
                  <span class="badge bg-warning">Partial</span>
                  @elseif($enrollment->payment_status === 'pending')
                  <span class="badge bg-danger">Pending</span>
                  @else
                  <span class="badge bg-secondary">{{ ucfirst($enrollment->payment_status) }}</span>
                  @endif
                </td>
                <td>
                  ${{ number_format($enrollment->amount_paid, 2) }}
                  @if($enrollment->total_fee > 0)
                  <br><small class="text-muted">of ${{ number_format($enrollment->total_fee, 2) }}</small>
                  @endif
                </td>
                <td>
                  @if($enrollment->payment_due_date)
                  {{ $enrollment->payment_due_date->format('M d, Y') }}
                  @if($enrollment->payment_due_date->isPast() && $enrollment->payment_status !== 'paid')
                  <br><small class="text-danger">Overdue</small>
                  @endif
                  @else
                  <span class="text-muted">No due date</span>
                  @endif
                </td>
                <td>
                  @if($enrollment->enrolled_at)
                  {{ $enrollment->enrolled_at->format('M d, Y') }}
                  @else
                  <span class="text-muted">Not enrolled</span>
                  @endif
                </td>
                <td>
                  <div class="btn-group" role="group">
                    <a href="{{ route('admin.users.show', $enrollment->user) }}" class="btn btn-sm btn-outline-primary">
                      <i class="bi bi-eye"></i>
                    </a>
                    <form method="POST"
                      action="{{ route('admin.courses.remove-student', [$course, $enrollment->user]) }}"
                      class="d-inline"
                      onsubmit="return confirm('Are you sure you want to remove this student from the course?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-person-dash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @else
        <div class="text-center py-4">
          <i class="bi bi-people display-4 text-muted"></i>
          <h5 class="text-muted mt-3">No students enrolled</h5>
          <p class="text-muted">Use the form above to assign students to this course.</p>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection