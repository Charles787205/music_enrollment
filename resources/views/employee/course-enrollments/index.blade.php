@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Course Enrollment Management</h4>
                </div>

                <div class="card-body">
                    <!-- Payment Summary -->
                    @if($courseEnrollments->total() > 0)
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <h6 class="mb-2"><i class="bi bi-info-circle"></i> Payment Overview</h6>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>Paid:</strong> {{ $courseEnrollments->where('payment_status', 'paid')->count() }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Partial:</strong> {{ $courseEnrollments->where('payment_status', 'partial')->count() }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Pending:</strong> {{ $courseEnrollments->where('payment_status', 'pending')->count() }}
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Overdue:</strong> {{ $courseEnrollments->where('payment_status', 'pending')->where('payment_due_date', '<', now())->count() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Search and Filter Form -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Search by student or course name..." 
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-control">
                                    <option value="">All Statuses</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="payment_status" class="form-control">
                                    <option value="">All Payments</option>
                                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="partial" {{ request('payment_status') === 'partial' ? 'selected' : '' }}>Partial</option>
                                    <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="overdue" {{ request('payment_status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('employee.course-enrollments') }}" class="btn btn-secondary">Clear</a>
                            </div>
                        </div>
                    </form>

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($courseEnrollments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Course</th>
                                        <th>Teacher</th>
                                        <th>Status</th>
                                        <th>Payment Status</th>
                                        <th>Enrolled Date</th>
                                        <th>Approved Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($courseEnrollments as $enrollment)
                                        <tr>
                                            <td>
                                                <strong>{{ $enrollment->student->name }}</strong><br>
                                                <small class="text-muted">{{ $enrollment->student->email }}</small>
                                            </td>
                                            <td>
                                                <strong>{{ $enrollment->course->name }}</strong><br>
                                                <small class="text-muted">{{ $enrollment->course->description }}</small>
                                            </td>
                                            <td>
                                                @if($enrollment->course->teacher)
                                                    {{ $enrollment->course->teacher->name }}<br>
                                                    <small class="text-muted">{{ $enrollment->course->teacher->specialization }}</small>
                                                @else
                                                    <span class="text-muted">No teacher assigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                @switch($enrollment->status)
                                                    @case('pending')
                                                        <span class="badge bg-warning text-dark">Pending</span>
                                                        @break
                                                    @case('approved')
                                                        <span class="badge bg-success">Approved</span>
                                                        @break
                                                    @case('rejected')
                                                        <span class="badge bg-danger">Rejected</span>
                                                        @break
                                                    @case('completed')
                                                        <span class="badge bg-info">Completed</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ ucfirst($enrollment->status) }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                @if($enrollment->total_fee && $enrollment->total_fee > 0)
                                                    @switch($enrollment->payment_status)
                                                        @case('paid')
                                                            <span class="badge bg-success">Paid</span><br>
                                                            <small class="text-muted">${{ number_format($enrollment->total_fee, 2) }}</small>
                                                            @break
                                                        @case('partial')
                                                            <span class="badge bg-warning text-dark">Partial</span><br>
                                                            <small class="text-muted">${{ number_format($enrollment->amount_paid, 2) }} / ${{ number_format($enrollment->total_fee, 2) }}</small>
                                                            @break
                                                        @case('pending')
                                                            @if($enrollment->payment_due_date && $enrollment->payment_due_date->isPast())
                                                                <span class="badge bg-danger">Overdue</span>
                                                            @else
                                                                <span class="badge bg-warning text-dark">Pending</span>
                                                            @endif
                                                            <br>
                                                            <small class="text-muted">${{ number_format($enrollment->amount_paid ?? 0, 2) }} / ${{ number_format($enrollment->total_fee, 2) }}</small>
                                                            @if($enrollment->payment_due_date)
                                                                <br><small class="text-muted">Due: {{ $enrollment->payment_due_date->format('M j, Y') }}</small>
                                                            @endif
                                                            @break
                                                        @default
                                                            <span class="badge bg-secondary">{{ ucfirst($enrollment->payment_status ?? 'Unknown') }}</span><br>
                                                            <small class="text-muted">${{ number_format($enrollment->amount_paid ?? 0, 2) }} / ${{ number_format($enrollment->total_fee, 2) }}</small>
                                                    @endswitch
                                                @else
                                                    <span class="badge bg-light text-dark">Free</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $enrollment->created_at->format('M j, Y') }}<br>
                                                <small class="text-muted">{{ $enrollment->created_at->format('g:i A') }}</small>
                                            </td>
                                            <td>
                                                @if($enrollment->approved_at)
                                                    {{ $enrollment->approved_at->format('M j, Y') }}<br>
                                                    <small class="text-muted">{{ $enrollment->approved_at->format('g:i A') }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    @if($enrollment->status === 'pending')
                                                        <!-- Approve Button -->
                                                        <form method="POST" action="{{ route('employee.course-enrollments.update-status', $enrollment) }}" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="approved">
                                                            <button type="submit" class="btn btn-sm btn-success" 
                                                                    onclick="return confirm('Are you sure you want to approve this enrollment?')">
                                                                Approve
                                                            </button>
                                                        </form>

                                                        <!-- Reject Button -->
                                                        <form method="POST" action="{{ route('employee.course-enrollments.update-status', $enrollment) }}" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="rejected">
                                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                                    onclick="return confirm('Are you sure you want to reject this enrollment?')">
                                                                Reject
                                                            </button>
                                                        </form>
                                                    @elseif($enrollment->status === 'approved')
                                                        <!-- Complete Button -->
                                                        <form method="POST" action="{{ route('employee.course-enrollments.update-status', $enrollment) }}" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="completed">
                                                            <button type="submit" class="btn btn-sm btn-info" 
                                                                    onclick="return confirm('Mark this enrollment as completed?')">
                                                                Complete
                                                            </button>
                                                        </form>

                                                        <!-- Reset to Pending -->
                                                        <form method="POST" action="{{ route('employee.course-enrollments.update-status', $enrollment) }}" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="pending">
                                                            <button type="submit" class="btn btn-sm btn-outline-warning" 
                                                                    onclick="return confirm('Reset this enrollment to pending?')">
                                                                Reset
                                                            </button>
                                                        </form>
                                                    @elseif($enrollment->status === 'rejected')
                                                        <!-- Reset to Pending -->
                                                        <form method="POST" action="{{ route('employee.course-enrollments.update-status', $enrollment) }}" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="pending">
                                                            <button type="submit" class="btn btn-sm btn-outline-warning" 
                                                                    onclick="return confirm('Reset this enrollment to pending?')">
                                                                Reset
                                                            </button>
                                                        </form>
                                                    @elseif($enrollment->status === 'completed')
                                                        <!-- Reset to Approved -->
                                                        <form method="POST" action="{{ route('employee.course-enrollments.update-status', $enrollment) }}" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="approved">
                                                            <button type="submit" class="btn btn-sm btn-outline-success" 
                                                                    onclick="return confirm('Reset this enrollment to approved?')">
                                                                Reopen
                                                            </button>
                                                        </form>
                                                    @endif
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
                    @else
                        <div class="text-center py-4">
                            <h5>No course enrollments found</h5>
                            <p class="text-muted">
                                @if(request()->has('search') || request()->has('status'))
                                    Try adjusting your search criteria.
                                @else
                                    Course enrollments will appear here when students enroll in courses.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection