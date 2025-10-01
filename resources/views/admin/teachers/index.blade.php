@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1><i class="bi bi-person-badge"></i> Manage Teachers</h1>
      <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add New Teacher
      </a>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
      <div class="card-body">
        <form method="GET" action="{{ route('admin.teachers') }}" class="row g-3">
          <div class="col-md-6">
            <label for="search" class="form-label">Search</label>
            <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}"
              placeholder="Search teachers by name, email, or specialization...">
          </div>
          <div class="col-md-4">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status">
              <option value="">All Status</option>
              <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
              <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">&nbsp;</label>
            <div>
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Filter
              </button>
              <a href="{{ route('admin.teachers') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Clear
              </a>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Teachers Table -->
    @if($teachers->count() > 0)
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Name</th>
                <th>Contact</th>
                <th>Specialization</th>
                <th>Courses</th>
                <th>Status</th>
                <th>Added</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($teachers as $teacher)
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="me-3">
                      <div
                        class="user-avatar bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 40px; height: 40px;">
                        {{ substr($teacher->name, 0, 1) }}
                      </div>
                    </div>
                    <div>
                      <strong>{{ $teacher->name }}</strong>
                      @if($teacher->bio)
                      <br><small class="text-muted">{{ Str::limit($teacher->bio, 50) }}</small>
                      @endif
                    </div>
                  </div>
                </td>
                <td>
                  @if($teacher->email)
                  <div><i class="bi bi-envelope"></i> {{ $teacher->email }}</div>
                  @endif
                  @if($teacher->phone)
                  <div><i class="bi bi-telephone"></i> {{ $teacher->phone }}</div>
                  @endif
                  @if(!$teacher->email && !$teacher->phone)
                  <span class="text-muted">No contact info</span>
                  @endif
                </td>
                <td>
                  @if($teacher->specialization)
                  <span class="badge bg-info">{{ $teacher->specialization }}</span>
                  @else
                  <span class="text-muted">General</span>
                  @endif
                </td>
                <td>
                  <span class="badge bg-primary">
                    {{ $teacher->courses->count() }} courses
                  </span>
                </td>
                <td>
                  @if($teacher->is_active)
                  <span class="badge bg-success">Active</span>
                  @else
                  <span class="badge bg-secondary">Inactive</span>
                  @endif
                </td>
                <td>{{ $teacher->created_at->format('M d, Y') }}</td>
                <td>
                  <div class="btn-group" role="group">
                    <a href="{{ route('admin.teachers.edit', $teacher) }}" class="btn btn-sm btn-outline-warning">
                      <i class="bi bi-pencil"></i> Edit
                    </a>
                    @if($teacher->courses->count() == 0)
                    <form method="POST" action="{{ route('admin.teachers.delete', $teacher) }}" class="d-inline"
                      onsubmit="return confirm('Are you sure you want to delete this teacher?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-trash"></i> Delete
                      </button>
                    </form>
                    @else
                    <button class="btn btn-sm btn-outline-danger" disabled
                      title="Cannot delete teacher with assigned courses">
                      <i class="bi bi-trash"></i> Delete
                    </button>
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
          {{ $teachers->appends(request()->query())->links() }}
        </div>
      </div>
    </div>
    @else
    <div class="text-center py-5">
      <i class="bi bi-person-badge display-1 text-muted"></i>
      <h3 class="text-muted mt-3">No teachers found</h3>
      <p class="text-muted">Try adjusting your search criteria or add a new teacher.</p>
      <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add First Teacher
      </a>
    </div>
    @endif
  </div>
</div>
@endsection