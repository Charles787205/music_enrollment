@extends('layouts.app')

@section('title', 'Course Enrollment Details')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Course Enrollment Details</h1>
                <a href="{{ route('course-enrollments.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Back to My Enrollments
                </a>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-4 mb-md-0">
                            @if($courseEnrollment->course->image)
                            <img src="{{ asset('storage/' . $courseEnrollment->course->image) }}"
                                alt="{{ $courseEnrollment->course->title }}" class="img-fluid rounded shadow-sm mb-3"
                                style="max-height: 300px; object-fit: cover;">
                            @else
                            <div class="bg-secondary rounded d-flex align-items-center justify-content-center mb-3 mx-auto"
                                style="width: 200px; height: 200px;">
                                <i class="bi bi-book text-white" style="font-size: 4rem;"></i>
                            </div>
                            @endif

                            <div class="badge bg-{{ $courseEnrollment->statusBadgeClass }} fs-6 px-3 py-2">
                                <i
                                    class="bi bi-{{ $courseEnrollment->status === 'pending' ? 'clock' : ($courseEnrollment->status === 'active' ? 'check-circle' : ($courseEnrollment->status === 'completed' ? 'trophy' : 'x-circle')) }} me-1"></i>
                                {{ ucfirst($courseEnrollment->status) }}
                            </div>

                            @if($courseEnrollment->grade !== null)
                            <div class="mt-2">
                                <div
                                    class="badge bg-{{ $courseEnrollment->grade >= 70 ? 'success' : ($courseEnrollment->grade >= 50 ? 'warning' : 'danger') }} fs-6 px-3 py-2">
                                    Grade: {{ $courseEnrollment->formattedGrade }}
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="col-md-8">
                            <h2 class="h4 mb-3">{{ $courseEnrollment->course->title }}</h2>

                            <div class="mb-3">
                                <strong>Description:</strong>
                                <p class="text-muted">{{ $courseEnrollment->course->description }}</p>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-6">
                                    <strong>Price:</strong>
                                    <p class="text-muted mb-2">${{ number_format($courseEnrollment->course->price, 2) }}
                                    </p>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Max Students:</strong>
                                    <p class="text-muted mb-2">{{ $courseEnrollment->course->max_students }}</p>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-6">
                                    <strong>Request Date:</strong>
                                    <p class="text-muted mb-2">
                                        {{ $courseEnrollment->created_at->format('F j, Y \a\t g:i A') }}
                                        <br><small>({{ $courseEnrollment->created_at->diffForHumans() }})</small>
                                    </p>
                                </div>
                                @if($courseEnrollment->enrolled_at)
                                <div class="col-sm-6">
                                    <strong>Enrolled Date:</strong>
                                    <p class="text-muted mb-2">
                                        {{ $courseEnrollment->enrolled_at->format('F j, Y') }}
                                        <br><small>({{ $courseEnrollment->enrolled_at->diffForHumans() }})</small>
                                    </p>
                                </div>
                                @endif
                            </div>

                            @if($courseEnrollment->completed_at)
                            <div class="row mb-3">
                                <div class="col-sm-6">
                                    <strong>Completed Date:</strong>
                                    <p class="text-muted mb-2">
                                        {{ $courseEnrollment->completed_at->format('F j, Y') }}
                                        <br><small>({{ $courseEnrollment->completed_at->diffForHumans() }})</small>
                                    </p>
                                </div>
                            </div>
                            @endif

                            @if($courseEnrollment->notes)
                            <div class="mb-3">
                                <strong>Notes:</strong>
                                <div class="bg-light rounded p-3 mt-1">
                                    {{ $courseEnrollment->notes }}
                                </div>
                            </div>
                            @endif

                            <div class="d-flex gap-2 mt-4">
                                <a href="{{ route('courses.show', $courseEnrollment->course) }}"
                                    class="btn btn-primary">
                                    <i class="bi bi-book me-1"></i>View Course Details
                                </a>

                                @if($courseEnrollment->status === 'pending')
                                <form method="POST" action="{{ route('courses.unenroll', $courseEnrollment->course) }}"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('Are you sure you want to cancel this enrollment request?')">
                                        <i class="bi bi-trash me-1"></i>Cancel Request
                                    </button>
                                </form>
                                @elseif($courseEnrollment->status === 'active')
                                <form method="POST" action="{{ route('courses.unenroll', $courseEnrollment->course) }}"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-warning"
                                        onclick="return confirm('Are you sure you want to drop this course?')">
                                        <i class="bi bi-x-circle me-1"></i>Drop Course
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($courseEnrollment->status === 'pending')
            <div class="alert alert-info mt-4">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Pending Approval:</strong> Your enrollment request is waiting for staff approval. You will be
                notified once it's processed.
            </div>
            @elseif($courseEnrollment->status === 'active')
            <div class="alert alert-success mt-4">
                <i class="bi bi-check-circle me-2"></i>
                <strong>Enrolled:</strong> You are successfully enrolled in this course. Keep up the good work!
            </div>
            @elseif($courseEnrollment->status === 'completed')
            <div class="alert alert-primary mt-4">
                <i class="bi bi-trophy me-2"></i>
                <strong>Course Completed:</strong> Congratulations on completing this course!
                @if($courseEnrollment->grade !== null)
                Your final grade is {{ $courseEnrollment->formattedGrade }}.
                @endif
            </div>
            @elseif($courseEnrollment->status === 'dropped')
            <div class="alert alert-secondary mt-4">
                <i class="bi bi-x-circle me-2"></i>
                <strong>Course Dropped:</strong> You have dropped this course.
            </div>
            @endif
        </div>
    </div>
</div>
@endsection