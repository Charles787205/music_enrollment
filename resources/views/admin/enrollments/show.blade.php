@extends('layouts.app')

@section('title', 'Enrollment Details')

@section('content')
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Enrollment Details</h1>
        <a href="{{ route('admin.enrollments') }}" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left"></i> Back to Enrollments
        </a>
      </div>

      <!-- Enrollment Information -->
      <div class="card mb-4">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0">
            <i class="bi bi-info-circle me-2"></i>
            Enrollment Information
          </h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <h6>Student Information</h6>
              <p><strong>Name:</strong> {{ $enrollment->user->name }}</p>
              <p><strong>Email:</strong> {{ $enrollment->user->email }}</p>
              <p><strong>Phone:</strong> {{ $enrollment->user->phone ?? 'N/A' }}</p>
              <p><strong>User Type:</strong>
                <span class="badge bg-info">{{ ucfirst($enrollment->user->user_type) }}</span>
              </p>
            </div>
            <div class="col-md-6">
              <h6>Instrument Information</h6>
              <p><strong>Instrument:</strong> {{ $enrollment->instrument->name }}</p>
              <p><strong>Brand:</strong> {{ $enrollment->instrument->brand ?? 'N/A' }}</p>
              <p><strong>Category:</strong> {{ ucfirst($enrollment->instrument->category) }}</p>
              <p><strong>Condition:</strong>
                <span
                  class="badge bg-{{ $enrollment->instrument->condition === 'excellent' ? 'success' : ($enrollment->instrument->condition === 'good' ? 'primary' : 'warning') }}">
                  {{ ucfirst($enrollment->instrument->condition) }}
                </span>
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Status and Timeline -->
      <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
          <h5 class="mb-0">
            <i class="bi bi-clock me-2"></i>
            Status and Timeline
          </h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <h6>Current Status</h6>
              <p>
                <span class="badge bg-{{ 
                                    $enrollment->status === 'pending' ? 'warning' : 
                                    ($enrollment->status === 'borrowed' ? 'success' : 
                                    ($enrollment->status === 'returned' ? 'primary' : 'danger')) 
                                }} fs-6">
                  {{ ucfirst($enrollment->status) }}
                </span>
              </p>

              @if($enrollment->status === 'borrowed' && $enrollment->due_date)
              @php
              $daysRemaining = now()->diffInDays($enrollment->due_date, false);
              $isOverdue = $daysRemaining < 0; @endphp <div
                class="alert alert-{{ $isOverdue ? 'danger' : ($daysRemaining <= 2 ? 'warning' : 'info') }} mt-3">
                @if($isOverdue)
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Overdue!</strong> This instrument was due {{ abs($daysRemaining) }} day(s) ago.
                @elseif($daysRemaining <= 2) <i class="bi bi-clock me-2"></i>
                  <strong>Due Soon!</strong> This instrument is due in {{ $daysRemaining }} day(s).
                  @else
                  <i class="bi bi-info-circle me-2"></i>
                  This instrument is due in {{ $daysRemaining }} day(s).
                  @endif
            </div>
            @endif
          </div>
          <div class="col-md-6">
            <h6>Important Dates</h6>
            <p><strong>Requested:</strong> {{ $enrollment->created_at->format('M d, Y g:i A') }}</p>
            @if($enrollment->borrowed_at)
            <p><strong>Borrowed:</strong> {{ $enrollment->borrowed_at->format('M d, Y g:i A') }}</p>
            @endif
            @if($enrollment->due_date)
            <p><strong>Due Date:</strong> {{ $enrollment->due_date->format('M d, Y') }}</p>
            @endif
            @if($enrollment->returned_at)
            <p><strong>Returned:</strong> {{ $enrollment->returned_at->format('M d, Y g:i A') }}</p>
            @endif
          </div>
        </div>
      </div>
    </div>

    <!-- Status Management -->
    <div class="card mb-4">
      <div class="card-header bg-warning text-dark">
        <h5 class="mb-0">
          <i class="bi bi-gear me-2"></i>
          Status Management
        </h5>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.enrollments.update-status', $enrollment) }}" class="d-inline">
          @csrf
          @method('PATCH')

          <div class="row align-items-end">
            <div class="col-md-4">
              <label for="status" class="form-label">Update Status</label>
              <select name="status" id="status" class="form-select" required>
                <option value="pending" {{ $enrollment->status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="borrowed" {{ $enrollment->status === 'borrowed' ? 'selected' : '' }}>Borrowed</option>
                <option value="returned" {{ $enrollment->status === 'returned' ? 'selected' : '' }}>Returned</option>
                <option value="overdue" {{ $enrollment->status === 'overdue' ? 'selected' : '' }}>Overdue</option>
              </select>
            </div>
            <div class="col-md-3">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle me-1"></i>
                Update Status
              </button>
            </div>
          </div>
        </form>

        <div class="mt-3">
          <small class="text-muted">
            <strong>Note:</strong> Changing status to "Borrowed" will set the borrowed date and due date automatically.
            Changing to "Returned" will record the return timestamp.
          </small>
        </div>
      </div>
    </div>

    <!-- Additional Actions -->
    <div class="card">
      <div class="card-header bg-info text-white">
        <h5 class="mb-0">
          <i class="bi bi-tools me-2"></i>
          Additional Actions
        </h5>
      </div>
      <div class="card-body">
        <div class="d-flex gap-2 flex-wrap">
          <a href="{{ route('admin.users.show', $enrollment->user) }}" class="btn btn-outline-primary">
            <i class="bi bi-person me-1"></i>
            View Student Profile
          </a>

          <a href="{{ route('instruments.show', $enrollment->instrument) }}" class="btn btn-outline-success">
            <i class="bi bi-music-note-list me-1"></i>
            View Instrument Details
          </a>

          @if($enrollment->status === 'pending')
          <form method="POST" action="{{ route('admin.enrollments.update-status', $enrollment) }}" class="d-inline">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="borrowed">
            <button type="submit" class="btn btn-success" onclick="return confirm('Approve this borrow request?')">
              <i class="bi bi-check-circle me-1"></i>
              Approve Borrow
            </button>
          </form>
          @endif

          @if(in_array($enrollment->status, ['borrowed', 'overdue']))
          <form method="POST" action="{{ route('admin.enrollments.update-status', $enrollment) }}" class="d-inline">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="returned">
            <button type="submit" class="btn btn-warning" onclick="return confirm('Mark this instrument as returned?')">
              <i class="bi bi-box-arrow-in-down me-1"></i>
              Mark as Returned
            </button>
          </form>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
</div>
@endsection