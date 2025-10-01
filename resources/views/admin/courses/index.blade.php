@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1><i class="bi bi-book-half"></i> Manage Courses</h1>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
      <div class="card-body">
        <form method="GET" action="{{ route('admin.courses') }}" class="row g-3">
          <div class="col-md-6">
            <label for="search" class="form-label">Search</label>
            <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}"
              placeholder="Search courses by name or description...">
          </div>
          <div class="col-md-4">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status">
              <option value="">All Statuses</option>
              <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
              <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
              <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">&nbsp;</label>
            <div>
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Filter
              </button>
              <a href="{{ route('admin.courses') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Clear
              </a>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Courses Table -->
    @if($courses->count() > 0)
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Course Name</th>
                <th>Teacher</th>
                <th>Status</th>
                <th>Enrolled Students</th>
                <th>Duration</th>
                <th>Fee</th>
                <th>Created</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($courses as $course)
              <tr>
                <td>
                  <strong>{{ $course->name }}</strong>
                  @if($course->description)
                  <br><small class="text-muted">{{ Str::limit($course->description, 60) }}</small>
                  @endif
                </td>
                <td>
                  @if($course->teacher)
                  <div>
                    <strong>{{ $course->teacher->name }}</strong>
                    @if($course->teacher->specialization)
                    <br><small class="text-muted">{{ $course->teacher->specialization }}</small>
                    @endif
                  </div>
                  @else
                  <span class="text-muted">No teacher assigned</span>
                  @endif
                </td>
                <td>
                  @if($course->status === 'active')
                  <span class="badge bg-success">Active</span>
                  @elseif($course->status === 'inactive')
                  <span class="badge bg-secondary">Inactive</span>
                  @elseif($course->status === 'completed')
                  <span class="badge bg-primary">Completed</span>
                  @else
                  <span class="badge bg-warning">{{ ucfirst($course->status) }}</span>
                  @endif
                </td>
                <td>
                  <span class="badge bg-info">
                    {{ $course->enrollments->count() }} students
                  </span>
                  <br><small class="text-muted">
                    Active: {{ $course->enrollments->where('status', 'active')->count() }}
                  </small>
                </td>
                <td>
                  @if($course->duration_weeks)
                  {{ $course->duration_weeks }} weeks
                  @else
                  <span class="text-muted">Not specified</span>
                  @endif
                </td>
                <td>
                  @if($course->fee)
                  ${{ number_format($course->fee, 2) }}
                  @else
                  <span class="text-muted">Free</span>
                  @endif
                </td>
                <td>{{ $course->created_at->format('M d, Y') }}</td>
                <td>
                  <div class="btn-group" role="group">
                    <a href="{{ route('admin.courses.show', $course) }}" class="btn btn-sm btn-primary">
                      <i class="bi bi-gear"></i> Manage
                    </a>
                    <a href="{{ route('courses.show', $course) }}" class="btn btn-sm btn-outline-info">
                      <i class="bi bi-eye"></i> View
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
          {{ $courses->appends(request()->query())->links() }}
        </div>
      </div>
    </div>
    @else
    <div class="text-center py-5">
      <i class="bi bi-book-half display-1 text-muted"></i>
      <h3 class="text-muted mt-3">No courses found</h3>
      <p class="text-muted">Try adjusting your search criteria.</p>
    </div>
    @endif
  </div>
</div>
@endsection