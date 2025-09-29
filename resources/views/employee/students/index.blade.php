@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1><i class="bi bi-mortarboard"></i> Students</h1>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
      <div class="card-body">
        <form method="GET" action="{{ route('employee.students') }}" class="row g-3">
          <div class="col-md-8">
            <label for="search" class="form-label">Search</label>
            <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}"
              placeholder="Search students by name or email...">
          </div>
          <div class="col-md-3">
            <label for="enrollment_status" class="form-label">Enrollment Status</label>
            <select class="form-select" id="enrollment_status" name="enrollment_status">
              <option value="">All Students</option>
              <option value="enrolled" {{ request('enrollment_status') == 'enrolled' ? 'selected' : '' }}>Enrolled
              </option>
              <option value="not_enrolled" {{ request('enrollment_status') == 'not_enrolled' ? 'selected' : '' }}>Not
                Enrolled</option>
            </select>
          </div>
          <div class="col-md-1">
            <label class="form-label">&nbsp;</label>
            <div>
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Filter
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Students Table -->
    @if($students->count() > 0)
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Enrolled</th>
                <th>Active Enrollments</th>
                <th>Joined</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($students as $student)
              <tr>
                <td>{{ $student->name }}</td>
                <td>{{ $student->email }}</td>
                <td>{{ $student->phone ?? 'N/A' }}</td>
                <td>
                  <span class="badge bg-{{ $student->is_enrolled ? 'success' : 'secondary' }}">
                    {{ $student->is_enrolled ? 'Yes' : 'No' }}
                  </span>
                </td>
                <td>
                  <span class="badge bg-info">
                    {{ $student->enrollments()->where('status', 'active')->count() }}
                  </span>
                </td>
                <td>{{ $student->created_at->format('M d, Y') }}</td>
                <td>
                  <a href="{{ route('employee.students.show', $student) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-eye"></i> View
                  </a>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
          {{ $students->appends(request()->query())->links() }}
        </div>
      </div>
    </div>
    @else
    <div class="text-center py-5">
      <i class="bi bi-mortarboard display-1 text-muted"></i>
      <h3 class="text-muted mt-3">No students found</h3>
      <p class="text-muted">Try adjusting your search criteria.</p>
    </div>
    @endif
  </div>
</div>
@endsection