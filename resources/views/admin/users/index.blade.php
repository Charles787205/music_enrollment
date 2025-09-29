@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1><i class="bi bi-people"></i> Manage Users</h1>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
      <div class="card-body">
        <form method="GET" action="{{ route('admin.users') }}" class="row g-3">
          <div class="col-md-6">
            <label for="search" class="form-label">Search</label>
            <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}"
              placeholder="Search users by name or email...">
          </div>
          <div class="col-md-4">
            <label for="user_type" class="form-label">User Type</label>
            <select class="form-select" id="user_type" name="user_type">
              <option value="">All Types</option>
              <option value="student" {{ request('user_type') == 'student' ? 'selected' : '' }}>Students</option>
              <option value="employee" {{ request('user_type') == 'employee' ? 'selected' : '' }}>Employees</option>
              <option value="admin" {{ request('user_type') == 'admin' ? 'selected' : '' }}>Administrators</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">&nbsp;</label>
            <div>
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Filter
              </button>
              <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Clear
              </a>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Users Table -->
    @if($users->count() > 0)
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Type</th>
                <th>Enrolled</th>
                <th>Joined</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($users as $user)
              <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->phone ?? 'N/A' }}</td>
                <td>
                  <span
                    class="badge bg-{{ $user->user_type === 'admin' ? 'danger' : ($user->user_type === 'employee' ? 'warning' : 'info') }}">
                    {{ ucfirst($user->user_type) }}
                  </span>
                </td>
                <td>
                  @if($user->isStudent())
                  <span class="badge bg-{{ $user->is_enrolled ? 'success' : 'secondary' }}">
                    {{ $user->is_enrolled ? 'Yes' : 'No' }}
                  </span>
                  @else
                  <span class="text-muted">N/A</span>
                  @endif
                </td>
                <td>{{ $user->created_at->format('M d, Y') }}</td>
                <td>
                  <div class="btn-group" role="group">
                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-primary">
                      <i class="bi bi-eye"></i> View
                    </a>
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-warning">
                      <i class="bi bi-pencil"></i> Edit
                    </a>
                    @if($user->id !== auth()->id())
                    <form method="POST" action="{{ route('admin.users.delete', $user) }}" class="d-inline"
                      onsubmit="return confirm('Are you sure you want to delete this user?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-trash"></i> Delete
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
          {{ $users->appends(request()->query())->links() }}
        </div>
      </div>
    </div>
    @else
    <div class="text-center py-5">
      <i class="bi bi-people display-1 text-muted"></i>
      <h3 class="text-muted mt-3">No users found</h3>
      <p class="text-muted">Try adjusting your search criteria.</p>
    </div>
    @endif
  </div>
</div>
@endsection