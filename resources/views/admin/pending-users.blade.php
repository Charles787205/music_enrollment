@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">
            <i class="bi bi-clock-history me-2"></i>
            Pending User Approvals
          </h4>
          <span class="badge bg-warning text-dark fs-6">
            {{ $pendingUsers->total() }} Pending
          </span>
        </div>

        <div class="card-body">
          @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
          @endif

          @if(session('warning'))
          <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
          @endif

          @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-x-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
          @endif

          @if($pendingUsers->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead class="table-light">
                <tr>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Phone</th>
                  <th>Registration Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($pendingUsers as $user)
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="avatar-circle me-2">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                      </div>
                      <strong>{{ $user->name }}</strong>
                    </div>
                  </td>
                  <td>{{ $user->email }}</td>
                  <td>
                    <span class="badge {{ $user->user_type === 'admin' ? 'bg-danger' : 'bg-primary' }}">
                      <i class="bi {{ $user->user_type === 'admin' ? 'bi-shield-fill' : 'bi-person-badge' }} me-1"></i>
                      {{ ucfirst($user->user_type) }}
                    </span>
                  </td>
                  <td>{{ $user->phone ?? 'Not provided' }}</td>
                  <td>
                    <div>{{ $user->created_at->format('M j, Y') }}</div>
                    <small class="text-muted">{{ $user->created_at->format('g:i A') }}</small>
                  </td>
                  <td>
                    <div class="btn-group" role="group">
                      <!-- Approve Button -->
                      <form method="POST" action="{{ route('admin.users.approve', $user) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm"
                          onclick="return confirm('Are you sure you want to approve {{ $user->name }}?')">
                          <i class="bi bi-check-circle me-1"></i>Approve
                        </button>
                      </form>

                      <!-- Reject Button -->
                      <form method="POST" action="{{ route('admin.users.reject', $user) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm"
                          onclick="return confirm('Are you sure you want to reject {{ $user->name }}? This will permanently delete their registration.')">
                          <i class="bi bi-x-circle me-1"></i>Reject
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="d-flex justify-content-center mt-4">
            {{ $pendingUsers->links() }}
          </div>
          @else
          <div class="text-center py-5">
            <div class="mb-3">
              <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
            </div>
            <h5>No Pending Approvals</h5>
            <p class="text-muted">All staff registrations have been processed.</p>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
              <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
            </a>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 1.1rem;
  }

  .table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
  }

  .btn-group .btn {
    margin-right: 0.25rem;
  }

  .btn-group .btn:last-child {
    margin-right: 0;
  }

  .badge {
    font-size: 0.85rem;
    padding: 0.5em 0.75em;
  }

  .alert {
    border: none;
    border-radius: 12px;
  }
</style>
@endsection