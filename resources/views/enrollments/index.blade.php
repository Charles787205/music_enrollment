@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1><i class="bi bi-person-check"></i> My Enrollments</h1>
      <a href="{{ route('enrollments.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> New Enrollment
      </a>
    </div>

    @if($enrollments->count() > 0)
    <div class="row">
      @foreach($enrollments as $enrollment)
      <div class="col-lg-6 col-md-12 mb-4">
        <div class="card h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <h5 class="card-title">{{ $enrollment->instrument->name }}</h5>
              <span class="badge bg-{{ 
                                        $enrollment->status === 'active' ? 'success' : 
                                        ($enrollment->status === 'pending' ? 'warning' : 
                                        ($enrollment->status === 'completed' ? 'info' : 'danger')) 
                                    }}">
                {{ ucfirst($enrollment->status) }}
              </span>
            </div>

            <div class="mb-2">
              <span class="badge bg-info">{{ ucfirst($enrollment->instrument->category) }}</span>
              <span class="badge bg-secondary">{{ ucfirst($enrollment->instrument->difficulty_level) }}</span>
            </div>

            <div class="row mb-3">
              <div class="col-sm-6">
                <small class="text-muted">Start Date</small>
                <div>{{ $enrollment->start_date->format('M d, Y') }}</div>
              </div>
              @if($enrollment->end_date)
              <div class="col-sm-6">
                <small class="text-muted">End Date</small>
                <div>{{ $enrollment->end_date->format('M d, Y') }}</div>
              </div>
              @endif
            </div>

            @if($enrollment->total_fee)
            <div class="mb-3">
              <small class="text-muted">Monthly Fee</small>
              <div class="text-success">
                <strong>${{ number_format($enrollment->total_fee, 2) }}</strong>
              </div>
            </div>
            @endif

            @if($enrollment->notes)
            <div class="mb-3">
              <small class="text-muted">Notes</small>
              <div>{{ $enrollment->notes }}</div>
            </div>
            @endif

            <div class="text-muted">
              <small>Enrolled on {{ $enrollment->created_at->format('M d, Y') }}</small>
            </div>
          </div>

          <div class="card-footer bg-transparent">
            <div class="d-flex gap-2">
              <a href="{{ route('enrollments.show', $enrollment) }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-eye"></i> Details
              </a>

              @if(in_array($enrollment->status, ['pending', 'active']))
              <a href="{{ route('enrollments.edit', $enrollment) }}" class="btn btn-outline-warning btn-sm">
                <i class="bi bi-pencil"></i> Edit
              </a>

              <form method="POST" action="{{ route('enrollments.destroy', $enrollment) }}" class="d-inline"
                onsubmit="return confirm('Are you sure you want to drop this enrollment?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm">
                  <i class="bi bi-x-circle"></i> Drop
                </button>
              </form>
              @endif
            </div>
          </div>
        </div>
      </div>
      @endforeach
    </div>

    <!-- Summary Statistics -->
    <div class="mt-4">
      <div class="row">
        @php
        $activeCount = $enrollments->where('status', 'active')->count();
        $pendingCount = $enrollments->where('status', 'pending')->count();
        $totalFee = $enrollments->whereIn('status', ['active', 'pending'])->sum('total_fee');
        @endphp

        <div class="col-md-4">
          <div class="card bg-success text-white">
            <div class="card-body text-center">
              <h3>{{ $activeCount }}</h3>
              <p class="mb-0">Active Enrollments</p>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card bg-warning text-white">
            <div class="card-body text-center">
              <h3>{{ $pendingCount }}</h3>
              <p class="mb-0">Pending Approval</p>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card bg-info text-white">
            <div class="card-body text-center">
              <h3>${{ number_format($totalFee, 2) }}</h3>
              <p class="mb-0">Total Monthly Fee</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    @else
    <div class="text-center py-5">
      <i class="bi bi-person-x display-1 text-muted"></i>
      <h3 class="text-muted mt-3">No Enrollments Yet</h3>
      <p class="text-muted">You haven't enrolled in any instruments yet. Start your musical journey!</p>
      <div class="mt-4">
        <a href="{{ route('instruments.index') }}" class="btn btn-primary me-2">
          <i class="bi bi-music-note-list"></i> Browse Instruments
        </a>
        <a href="{{ route('enrollments.create') }}" class="btn btn-success">
          <i class="bi bi-plus-circle"></i> Enroll Now
        </a>
      </div>
    </div>
    @endif
  </div>
</div>
@endsection