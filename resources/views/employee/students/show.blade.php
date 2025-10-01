@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-12">
    <!-- Back Button -->
    <div class="mb-3">
      <a href="{{ route('employee.students') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Students
      </a>
    </div>

    <!-- Student Information Card -->
    <div class="card mb-4">
      <div class="card-header">
        <h2 class="card-title mb-0">
          <i class="bi bi-person-circle"></i> {{ $student->name }}
        </h2>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <h5>Contact Information</h5>
            <p><strong>Email:</strong> {{ $student->email }}</p>
            <p><strong>Phone:</strong> {{ $student->phone ?? 'Not provided' }}</p>
            <p><strong>Joined:</strong> {{ $student->created_at->format('F d, Y') }}</p>
          </div>
          <div class="col-md-6">
            <h5>Status</h5>
            <p>
              <strong>Account Status:</strong>
              <span class="badge bg-{{ $student->is_enrolled ? 'success' : 'secondary' }}">
                {{ $student->is_enrolled ? 'Enrolled' : 'Not Enrolled' }}
              </span>
            </p>
            <p>
              <strong>User Type:</strong>
              <span class="badge bg-primary">{{ ucfirst($student->user_type) }}</span>
            </p>
            <p>
              <strong>Active Enrollments:</strong>
              <span class="badge bg-info">
                {{ $enrollments->where('status', 'active')->count() }}
              </span>
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Instrument Borrowing History -->
    <div class="card">
      <div class="card-header">
        <h3 class="card-title mb-0">
          <i class="bi bi-music-note-list"></i> Instrument Borrowing History
        </h3>
      </div>
      <div class="card-body">
        @if($enrollments->count() > 0)
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
                <th>Days Borrowed</th>
              </tr>
            </thead>
            <tbody>
              @foreach($enrollments as $enrollment)
              <tr>
                <td>
                  <strong>{{ $enrollment->instrument->name }}</strong>
                  @if($enrollment->instrument->serial_number)
                  <br><small class="text-muted">SN: {{ $enrollment->instrument->serial_number }}</small>
                  @endif
                </td>
                <td>{{ $enrollment->instrument->type }}</td>
                <td>{{ $enrollment->instrument->brand ?? 'N/A' }}</td>
                <td>
                  @if($enrollment->status === 'active')
                  <span class="badge bg-success">Active</span>
                  @elseif($enrollment->status === 'returned')
                  <span class="badge bg-secondary">Returned</span>
                  @elseif($enrollment->status === 'overdue')
                  <span class="badge bg-danger">Overdue</span>
                  @else
                  <span class="badge bg-warning">{{ ucfirst($enrollment->status) }}</span>
                  @endif
                </td>
                <td>{{ $enrollment->created_at->format('M d, Y') }}</td>
                <td>
                  @if($enrollment->due_date)
                  {{ $enrollment->due_date->format('M d, Y') }}
                  @if($enrollment->status === 'active' && $enrollment->due_date->isPast())
                  <span class="text-danger ms-2">
                    <i class="bi bi-exclamation-triangle"></i> Overdue
                  </span>
                  @endif
                  @else
                  N/A
                  @endif
                </td>
                <td>
                  @if($enrollment->returned_at)
                  {{ $enrollment->returned_at->format('M d, Y') }}
                  @else
                  <span class="text-muted">Not returned</span>
                  @endif
                </td>
                <td>
                  @if($enrollment->returned_at)
                  {{ $enrollment->created_at->diffInDays($enrollment->returned_at) }} days
                  @elseif($enrollment->status === 'active')
                  {{ $enrollment->created_at->diffInDays(now()) }} days (ongoing)
                  @else
                  N/A
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <!-- Summary Statistics -->
        <div class="row mt-4">
          <div class="col-md-3">
            <div class="card bg-primary text-white">
              <div class="card-body text-center">
                <h4>{{ $enrollments->count() }}</h4>
                <p class="mb-0">Total Borrowings</p>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card bg-success text-white">
              <div class="card-body text-center">
                <h4>{{ $enrollments->where('status', 'active')->count() }}</h4>
                <p class="mb-0">Currently Active</p>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card bg-secondary text-white">
              <div class="card-body text-center">
                <h4>{{ $enrollments->where('status', 'returned')->count() }}</h4>
                <p class="mb-0">Returned</p>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card bg-danger text-white">
              <div class="card-body text-center">
                <h4>
                  {{ $enrollments->where('status', 'active')->filter(function($enrollment) {
                    return $enrollment->due_date && $enrollment->due_date->isPast();
                  })->count() }}
                </h4>
                <p class="mb-0">Overdue</p>
              </div>
            </div>
          </div>
        </div>
        @else
        <div class="text-center py-5">
          <i class="bi bi-music-note-list display-1 text-muted"></i>
          <h4 class="text-muted mt-3">No borrowing history</h4>
          <p class="text-muted">This student has not borrowed any instruments yet.</p>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection