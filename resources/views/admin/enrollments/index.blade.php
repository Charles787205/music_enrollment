@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1><i class="bi bi-box-arrow-up"></i> Manage Instrument Borrows</h1>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
      <div class="card-body">
        <form method="GET" action="{{ route('admin.enrollments') }}" class="row g-3">
          <div class="col-md-6">
            <label for="search" class="form-label">Search</label>
            <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}"
              placeholder="Search by student or instrument...">
          </div>
          <div class="col-md-4">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status">
              <option value="">All Statuses</option>
              <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
              <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Borrowed</option>
              <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
              <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">&nbsp;</label>
            <div>
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Filter
              </button>
              <a href="{{ route('admin.enrollments') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Clear
              </a>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Enrollments Table -->
    @if($enrollments->count() > 0)
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Student</th>
                <th>Instrument</th>
                <th>Status</th>
                <th>Borrowed Date</th>
                <th>Due Date</th>
                <th>Returned Date</th>
                <th>Requested</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($enrollments as $enrollment)
              <tr>
                <td>
                  <div>
                    <strong>{{ $enrollment->user->name }}</strong>
                    <br>
                    <small class="text-muted">{{ $enrollment->user->email }}</small>
                  </div>
                </td>
                <td>{{ $enrollment->instrument->name }}</td>
                <td>
                  <form method="POST" action="{{ route('admin.enrollments.update-status', $enrollment) }}"
                    class="d-inline">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                      <option value="pending" {{ $enrollment->status === 'pending' ? 'selected' : '' }}>Pending</option>
                      <option value="borrowed" {{ $enrollment->status === 'borrowed' ? 'selected' : '' }}>Borrowed
                      </option>
                      <option value="returned" {{ $enrollment->status === 'returned' ? 'selected' : '' }}>Returned
                      </option>
                      <option value="overdue" {{ $enrollment->status === 'overdue' ? 'selected' : '' }}>Overdue</option>
                    </select>
                  </form>
                </td>
                <td>{{ $enrollment->borrowed_at ? $enrollment->borrowed_at->format('M d, Y') : 'N/A' }}</td>
                <td>{{ $enrollment->due_date ? $enrollment->due_date->format('M d, Y') : 'N/A' }}</td>
                <td>{{ $enrollment->returned_at ? $enrollment->returned_at->format('M d, Y') : 'N/A' }}</td>
                <td>{{ $enrollment->created_at->format('M d, Y') }}</td>
                <td>
                  <div class="btn-group" role="group">
                    <a href="{{ route('admin.enrollments.show', $enrollment) }}" class="btn btn-sm btn-outline-primary">
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
          {{ $enrollments->appends(request()->query())->links() }}
        </div>
      </div>
    </div>
    @else
    <div class="text-center py-5">
      <i class="bi bi-journal-x display-1 text-muted"></i>
      <h3 class="text-muted mt-3">No enrollments found</h3>
      <p class="text-muted">Try adjusting your search criteria.</p>
    </div>
    @endif
  </div>
</div>
@endsection