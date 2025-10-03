@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1><i class="bi bi-eye"></i> Course Enrollment Details</h1>
      <a href="{{ route('admin.course-enrollments') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to Enrollments
      </a>
    </div>

    <div class="row">
      <!-- Student Information -->
      <div class="col-md-6 mb-4">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-person"></i> Student Information</h5>
          </div>
          <div class="card-body">
            <dl class="row">
              <dt class="col-sm-4">Name:</dt>
              <dd class="col-sm-8">{{ $courseEnrollment->user->name }}</dd>

              <dt class="col-sm-4">Email:</dt>
              <dd class="col-sm-8">{{ $courseEnrollment->user->email }}</dd>

              <dt class="col-sm-4">User Type:</dt>
              <dd class="col-sm-8">
                <span class="badge bg-info">{{ ucfirst($courseEnrollment->user->user_type) }}</span>
              </dd>

              <dt class="col-sm-4">Joined:</dt>
              <dd class="col-sm-8">{{ $courseEnrollment->user->created_at->format('M d, Y') }}</dd>
            </dl>

            <div class="mt-3">
              <a href="{{ route('admin.users.show', $courseEnrollment->user) }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-person"></i> View Full Profile
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Course Information -->
      <div class="col-md-6 mb-4">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-book"></i> Course Information</h5>
          </div>
          <div class="card-body">
            <dl class="row">
              <dt class="col-sm-4">Course:</dt>
              <dd class="col-sm-8">{{ $courseEnrollment->course->title }}</dd>

              <dt class="col-sm-4">Description:</dt>
              <dd class="col-sm-8">{{ $courseEnrollment->course->description ?? 'No description' }}</dd>

              <dt class="col-sm-4">Fee:</dt>
              <dd class="col-sm-8">
                <strong>${{ number_format($courseEnrollment->course->fee, 2) }}</strong>
              </dd>

              <dt class="col-sm-4">Teacher:</dt>
              <dd class="col-sm-8">{{ $courseEnrollment->course->teacher->name ?? 'No Teacher Assigned' }}</dd>

              <dt class="col-sm-4">Duration:</dt>
              <dd class="col-sm-8">{{ $courseEnrollment->course->duration }} hours</dd>
            </dl>

            <div class="mt-3">
              <a href="{{ route('admin.courses.show', $courseEnrollment->course) }}"
                class="btn btn-outline-primary btn-sm">
                <i class="bi bi-book"></i> View Course Details
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Enrollment Details -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-clipboard-data"></i> Enrollment Details</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <dl class="row">
                  <dt class="col-sm-4">Status:</dt>
                  <dd class="col-sm-8">
                    @if($courseEnrollment->status === 'pending')
                    <span class="badge bg-warning">Pending</span>
                    @elseif($courseEnrollment->status === 'enrolled')
                    <span class="badge bg-success">Enrolled</span>
                    @elseif($courseEnrollment->status === 'completed')
                    <span class="badge bg-primary">Completed</span>
                    @elseif($courseEnrollment->status === 'dropped')
                    <span class="badge bg-danger">Dropped</span>
                    @endif
                  </dd>

                  <dt class="col-sm-4">Enrolled Date:</dt>
                  <dd class="col-sm-8">
                    {{ $courseEnrollment->enrolled_at ? $courseEnrollment->enrolled_at->format('M d, Y g:i A') : 'Not enrolled yet' }}
                  </dd>

                  <dt class="col-sm-4">Completed Date:</dt>
                  <dd class="col-sm-8">
                    {{ $courseEnrollment->completed_at ? $courseEnrollment->completed_at->format('M d, Y g:i A') : 'Not completed' }}
                  </dd>

                  <dt class="col-sm-4">Created:</dt>
                  <dd class="col-sm-8">{{ $courseEnrollment->created_at->format('M d, Y g:i A') }}</dd>
                </dl>
              </div>

              <div class="col-md-6">
                <dl class="row">
                  <dt class="col-sm-4">Payment Status:</dt>
                  <dd class="col-sm-8">
                    @if($courseEnrollment->payment_status === 'paid')
                    <span class="badge bg-success">Paid</span>
                    @elseif($courseEnrollment->payment_status === 'pending')
                    <span class="badge bg-warning">Pending</span>
                    @elseif($courseEnrollment->payment_status === 'overdue')
                    <span class="badge bg-danger">Overdue</span>
                    @else
                    <span class="badge bg-secondary">Unpaid</span>
                    @endif
                  </dd>

                  <dt class="col-sm-4">Grade:</dt>
                  <dd class="col-sm-8">{{ $courseEnrollment->grade ?? 'No grade assigned' }}</dd>

                  <dt class="col-sm-4">Teacher:</dt>
                  <dd class="col-sm-8">{{ $courseEnrollment->teacher->name ?? 'No teacher assigned' }}</dd>

                  <dt class="col-sm-4">Last Updated:</dt>
                  <dd class="col-sm-8">{{ $courseEnrollment->updated_at->format('M d, Y g:i A') }}</dd>
                </dl>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-4 pt-3 border-top">
              <h6>Quick Actions:</h6>
              <div class="btn-group" role="group">
                @if($courseEnrollment->status === 'pending')
                <form method="POST" action="{{ route('admin.course-enrollments.update-status', $courseEnrollment) }}"
                  class="d-inline">
                  @csrf
                  @method('PATCH')
                  <input type="hidden" name="status" value="enrolled">
                  <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle"></i> Enroll Student
                  </button>
                </form>
                <form method="POST" action="{{ route('admin.course-enrollments.update-status', $courseEnrollment) }}"
                  class="d-inline">
                  @csrf
                  @method('PATCH')
                  <input type="hidden" name="status" value="dropped">
                  <button type="submit" class="btn btn-danger">
                    <i class="bi bi-x-circle"></i> Drop Student
                  </button>
                </form>
                @elseif($courseEnrollment->status === 'enrolled')
                <form method="POST" action="{{ route('admin.course-enrollments.update-status', $courseEnrollment) }}"
                  class="d-inline">
                  @csrf
                  @method('PATCH')
                  <input type="hidden" name="status" value="completed">
                  <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-all"></i> Mark Completed
                  </button>
                </form>
                <form method="POST" action="{{ route('admin.course-enrollments.update-status', $courseEnrollment) }}"
                  class="d-inline">
                  @csrf
                  @method('PATCH')
                  <input type="hidden" name="status" value="dropped">
                  <button type="submit" class="btn btn-danger">
                    <i class="bi bi-x-circle"></i> Drop Student
                  </button>
                </form>
                @elseif($courseEnrollment->status === 'dropped')
                <form method="POST" action="{{ route('admin.course-enrollments.update-status', $courseEnrollment) }}"
                  class="d-inline">
                  @csrf
                  @method('PATCH')
                  <input type="hidden" name="status" value="enrolled">
                  <button type="submit" class="btn btn-warning">
                    <i class="bi bi-arrow-clockwise"></i> Re-enroll Student
                  </button>
                </form>
                @else
                <div class="text-muted">No actions available for completed enrollments.</div>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection