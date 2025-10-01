@extends('layouts.app')

@section('title', 'Manage Enrollments - Employee')

@section('content')
<div class="employee-enrollments-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">
            <i class="bi bi-journal-check me-2"></i>
            Manage Enrollments
        </h2>
        <div class="page-actions">
            <span class="badge bg-info">{{ $enrollments->total() }} Total Enrollments</span>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Search and Filter Controls -->
    <div class="controls-card mb-4">
        <form method="GET" action="{{ route('employee.enrollments') }}" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Search Students/Instruments</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}"
                        placeholder="Search by student or instrument name...">
                </div>
            </div>

            <div class="col-md-3">
                <label for="status" class="form-label">Filter by Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="borrowed" {{ request('status') === 'borrowed' ? 'selected' : '' }}>Borrowed</option>
                    <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Returned</option>
                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
                <a href="{{ route('employee.enrollments') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise me-1"></i>Reset
                </a>
            </div>
        </form>
    </div>

    @if($enrollments->count() > 0)
    <div class="enrollments-table-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Instrument</th>
                        <th>Status</th>
                        <th>Borrowed Date</th>
                        <th>Due Date</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($enrollments as $enrollment)
                    <tr>
                        <td>
                            <div class="student-info">
                                <div class="student-name">{{ $enrollment->user->name }}</div>
                                <small class="text-muted">{{ $enrollment->user->email }}</small>
                            </div>
                        </td>
                        <td>
                            <div class="instrument-info">
                                <strong>{{ $enrollment->instrument->name }}</strong>
                                @if($enrollment->instrument->category)
                                <br><small class="text-muted">{{ $enrollment->instrument->category }}</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="status-badge status-{{ $enrollment->status }}">
                                @switch($enrollment->status)
                                @case('pending')
                                <i class="bi bi-hourglass-split me-1"></i>Pending
                                @break
                                @case('borrowed')
                                <i class="bi bi-check-circle me-1"></i>Borrowed
                                @break
                                @case('returned')
                                <i class="bi bi-arrow-return-left me-1"></i>Returned
                                @break
                                @case('overdue')
                                <i class="bi bi-exclamation-triangle me-1"></i>Overdue
                                @break
                                @endswitch
                            </span>
                        </td>
                        <td>
                            @if($enrollment->borrowed_at)
                            {{ $enrollment->borrowed_at->format('M d, Y') }}
                            @else
                            <span class="text-muted">Not set</span>
                            @endif
                        </td>
                        <td>
                            @if($enrollment->due_date)
                            {{ $enrollment->due_date->format('M d, Y') }}
                            @else
                            <span class="text-muted">Not set</span>
                            @endif
                        </td>
                        <td>
                            @if($enrollment->notes)
                            {{ Str::limit($enrollment->notes, 50) }}
                            @else
                            <span class="text-muted">No notes</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <!-- Quick Status Update Buttons -->
                                @if($enrollment->status === 'pending')
                                <form method="POST"
                                    action="{{ route('employee.enrollments.update-status', $enrollment) }}"
                                    class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="borrowed">
                                    <button type="submit" class="btn btn-sm btn-success"
                                        onclick="return confirm('Approve this borrow request?')">
                                        <i class="bi bi-check me-1"></i>Approve Borrow
                                    </button>
                                </form>
                                <form method="POST"
                                    action="{{ route('employee.enrollments.update-status', $enrollment) }}"
                                    class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="overdue">
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Mark as overdue?')">
                                        <i class="bi bi-x me-1"></i>Mark Overdue
                                    </button>
                                </form>
                                @endif

                                <!-- Status Change Dropdown for Borrowed/Returned -->
                                @if(in_array($enrollment->status, ['borrowed', 'returned', 'overdue']))
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown">
                                        <i class="bi bi-gear"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @if($enrollment->status === 'borrowed')
                                        <li>
                                            <form method="POST"
                                                action="{{ route('employee.enrollments.update-status', $enrollment) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="returned">
                                                <button type="submit" class="dropdown-item">
                                                    <i class="bi bi-arrow-return-left me-1"></i>Mark Returned
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <form method="POST"
                                                action="{{ route('employee.enrollments.update-status', $enrollment) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="overdue">
                                                <button type="submit" class="dropdown-item text-warning"
                                                    onclick="return confirm('Mark as overdue?')">
                                                    <i class="bi bi-exclamation-triangle me-1"></i>Mark Overdue
                                                </button>
                                            </form>
                                        </li>
                                        @endif

                                        @if(in_array($enrollment->status, ['returned', 'overdue']))
                                        <li>
                                            <form method="POST"
                                                action="{{ route('employee.enrollments.update-status', $enrollment) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="borrowed">
                                                <button type="submit" class="dropdown-item">
                                                    <i class="bi bi-arrow-clockwise me-1"></i>Mark as Borrowed Again
                                                </button>
                                            </form>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $enrollments->links() }}
    </div>
    @else
    <div class="empty-state">
        <div class="empty-icon">
            <i class="bi bi-journal-x"></i>
        </div>
        <h4>No Enrollments Found</h4>
        @if(request('search') || request('status'))
        <p class="text-muted">No enrollments match your current filters.</p>
        <a href="{{ route('employee.enrollments') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-clockwise me-2"></i>Clear Filters
        </a>
        @else
        <p class="text-muted">There are no enrollments in the system yet.</p>
        @endif
    </div>
    @endif
</div>

<style>
    .controls-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
    }

    .enrollments-table-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
    }

    .table {
        margin-bottom: 0;
    }

    .table thead th {
        background: #f8f9fa;
        border-bottom: 2px solid #e2e8f0;
        font-weight: 600;
        color: #4a5568;
        padding: 1rem;
    }

    .table tbody td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }

    .table tbody tr:hover {
        background: #f8fafc;
    }

    .student-info .student-name {
        font-weight: 600;
        color: #2d3748;
    }

    .instrument-info strong {
        color: #2d3748;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .status-pending {
        background: #fef3cd;
        color: #856404;
    }

    .status-active {
        background: #d1e7dd;
        color: #0f5132;
    }

    .status-completed {
        background: #cff4fc;
        color: #055160;
    }

    .status-cancelled {
        background: #f8d7da;
        color: #721c24;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .action-buttons .btn {
        font-size: 0.875rem;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }

    .empty-icon {
        font-size: 4rem;
        color: #cbd5e0;
        margin-bottom: 1rem;
    }

    .empty-state h4 {
        color: #4a5568;
        margin-bottom: 0.5rem;
    }

    .page-actions .badge {
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
    }

    @media (max-width: 768px) {
        .controls-card .row {
            row-gap: 1rem;
        }

        .action-buttons {
            flex-direction: column;
            align-items: stretch;
        }

        .action-buttons .btn {
            width: 100%;
            margin-bottom: 0.25rem;
        }

        .table-responsive {
            font-size: 0.875rem;
        }

        .table thead th,
        .table tbody td {
            padding: 0.75rem 0.5rem;
        }
    }
</style>
@endsection