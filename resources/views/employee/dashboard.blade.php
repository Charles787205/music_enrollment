@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <h1 class="mb-4">
            <i class="bi bi-person-badge"></i> Employee Dashboard
            <span class="text-muted fs-6">Welcome back, {{ auth()->user()->name }}!</span>
        </h1>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $stats['total_students'] }}</h4>
                        <p class="card-text">Total Students</p>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-mortarboard-fill display-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $stats['enrolled_students'] }}</h4>
                        <p class="card-text">Enrolled Students</p>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-check-circle-fill display-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $stats['active_borrows'] }}</h4>
                        <p class="card-text">Active Borrows</p>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-journal-check display-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $stats['pending_borrows'] }}</h4>
                        <p class="card-text">Pending Borrows</p>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-hourglass-split display-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $stats['total_instruments'] }}</h4>
                        <p class="card-text">Total Instruments</p>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-music-note-list display-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-white bg-secondary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $stats['available_instruments'] }}</h4>
                        <p class="card-text">Available Instruments</p>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-music-note-beamed display-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-white bg-danger">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">{{ $stats['overdue_borrows'] }}</h4>
                        <p class="card-text">Overdue Borrows</p>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-exclamation-triangle display-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pending Borrows Alert -->
@if($pending_borrows->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-warning">
            <h5 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Pending Instrument Borrows</h5>
            <p class="mb-0">You have {{ $pending_borrows->count() }} instrument borrow request(s) waiting for approval.
            </p>
            <a href="{{ route('employee.enrollments') }}" class="btn btn-warning btn-sm mt-2">Review Now</a>
        </div>
    </div>
</div>
@endif

<!-- Recent Activity -->
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Instrument Borrows</h5>
                <a href="{{ route('employee.enrollments') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                @if($recent_borrows->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Instrument</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recent_borrows as $borrow)
                            <tr>
                                <td>{{ $borrow->user->name }}</td>
                                <td>{{ $borrow->instrument->name }}</td>
                                <td>
                                    @php
                                    $statusColors = [
                                    'pending' => 'warning',
                                    'borrowed' => 'success',
                                    'returned' => 'info',
                                    'overdue' => 'danger'
                                    ];
                                    $color = $statusColors[$borrow->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }}">
                                        {{ ucfirst($borrow->status) }}
                                    </span>
                                </td>
                                <td>{{ $borrow->created_at->format('M d, Y') }}</td>
                                <td>
                                    @if($borrow->status === 'pending')
                                    <form method="POST"
                                        action="{{ route('employee.enrollments.update-status', $borrow) }}"
                                        class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="borrowed">
                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted mb-0">No recent instrument borrows found.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-list-check"></i> Pending Actions</h5>
            </div>
            <div class="card-body">
                @if($pending_borrows->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($pending_borrows->take(5) as $borrow)
                    <div class="list-group-item px-0 py-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">{{ $borrow->user->name }}</h6>
                                <small class="text-muted">{{ $borrow->instrument->name }}</small>
                            </div>
                            <div class="btn-group-vertical btn-group-sm">
                                <form method="POST" action="{{ route('employee.enrollments.update-status', $borrow) }}"
                                    class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="borrowed">
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="bi bi-check"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('employee.enrollments.update-status', $borrow) }}"
                                    class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="overdue">
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-muted mb-0">No pending actions.</p>
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
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('employee.students') }}" class="btn btn-outline-primary w-100">
                            <i class="bi bi-mortarboard"></i> View Students
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('employee.enrollments') }}" class="btn btn-outline-success w-100">
                            <i class="bi bi-box-arrow-down-left"></i> Manage Borrows
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('instruments.index') }}" class="btn btn-outline-info w-100">
                            <i class="bi bi-music-note-list"></i> View Instruments
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="{{ route('employee.enrollments') }}?status=pending"
                            class="btn btn-outline-warning w-100">
                            <i class="bi bi-hourglass-split"></i> Pending Approvals
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection