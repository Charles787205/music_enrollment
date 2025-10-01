@extends('layouts.app')

@section('title', 'My Course Enrollments')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">My Course Enrollments</h1>
                <a href="{{ route('courses.index') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Browse Courses
                </a>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <div class="card">
                <div class="card-body">
                    @if($enrollments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Course</th>
                                    <th>Teacher</th>
                                    <th>Status</th>
                                    <th>Enrolled Date</th>
                                    <th>Grade</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($enrollments as $enrollment)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($enrollment->course->image)
                                            <img src="{{ asset('storage/' . $enrollment->course->image) }}"
                                                alt="{{ $enrollment->course->title }}" class="rounded me-2"
                                                style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                            <div class="bg-secondary rounded me-2 d-flex align-items-center justify-content-center"
                                                style="width: 40px; height: 40px;">
                                                <i class="bi bi-book text-white"></i>
                                            </div>
                                            @endif
                                            <div>
                                                <strong>{{ $enrollment->course->title }}</strong>
                                                <br>
                                                <small
                                                    class="text-muted">{{ Str::limit($enrollment->course->description, 50) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($enrollment->teacher)
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center"
                                                style="width: 32px; height: 32px;">
                                                <i class="bi bi-person-fill text-white" style="font-size: 14px;"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $enrollment->teacher->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $enrollment->teacher->email }}</small>
                                            </div>
                                        </div>
                                        @else
                                        <span class="text-muted">
                                            <i class="bi bi-clock me-1"></i>Teacher assignment pending
                                        </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $enrollment->statusBadgeClass }}">
                                            <i
                                                class="bi bi-{{ $enrollment->status === 'pending' ? 'clock' : ($enrollment->status === 'active' ? 'check-circle' : ($enrollment->status === 'completed' ? 'trophy' : 'x-circle')) }} me-1"></i>
                                            {{ ucfirst($enrollment->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($enrollment->enrolled_at)
                                        {{ $enrollment->enrolled_at->format('M j, Y') }}
                                        <br>
                                        <small
                                            class="text-muted">{{ $enrollment->enrolled_at->diffForHumans() }}</small>
                                        @else
                                        <span class="text-muted">Pending approval</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($enrollment->grade !== null)
                                        <span
                                            class="fw-bold {{ $enrollment->grade >= 70 ? 'text-success' : ($enrollment->grade >= 50 ? 'text-warning' : 'text-danger') }}">
                                            {{ $enrollment->formattedGrade }}
                                        </span>
                                        @else
                                        <span class="text-muted">Not graded</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('course-enrollments.show', $enrollment) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('courses.show', $enrollment->course) }}"
                                                class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-book"></i>
                                            </a>

                                            @if($enrollment->status === 'pending')
                                            <form method="POST"
                                                action="{{ route('courses.unenroll', $enrollment->course) }}"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Cancel this enrollment request?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                            @elseif($enrollment->status === 'active')
                                            <form method="POST"
                                                action="{{ route('courses.unenroll', $enrollment->course) }}"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-warning"
                                                    onclick="return confirm('Are you sure you want to drop this course?')">
                                                    <i class="bi bi-x-circle"></i>
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

                    <div class="d-flex justify-content-center">
                        {{ $enrollments->links() }}
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-book text-muted mb-3" style="font-size: 3rem;"></i>
                        <h5 class="text-muted">No Course Enrollments</h5>
                        <p class="text-muted mb-4">You haven't enrolled in any courses yet.</p>
                        <a href="{{ route('courses.index') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>Browse Courses to Enroll
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection