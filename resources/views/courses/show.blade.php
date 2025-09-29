@extends('layouts.app')

@section('title', $course->title)

@section('content')
<div class="course-detail-page">
  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  @if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  <div class="row g-4">
    <!-- Course Image and Basic Info -->
    <div class="col-lg-8">
      <div class="course-hero">
        @if($course->image)
        <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}" class="course-hero-image">
        @else
        <div class="course-hero-placeholder">
          <i class="bi bi-music-note-beamed"></i>
        </div>
        @endif

        <div class="course-hero-overlay">
          <div
            class="course-status-badge {{ $course->isFull() ? 'full' : ($course->current_enrolled > ($course->max_students * 0.8) ? 'almost-full' : 'available') }}">
            @if($course->isFull())
            <i class="bi bi-exclamation-triangle me-1"></i>Course Full
            @elseif($course->current_enrolled > ($course->max_students * 0.8))
            <i class="bi bi-hourglass-split me-1"></i>Almost Full
            @else
            <i class="bi bi-check-circle me-1"></i>Available
            @endif
          </div>
        </div>
      </div>

      <div class="course-content">
        <div class="d-flex justify-content-between align-items-start mb-3">
          <h1 class="course-title">{{ $course->title }}</h1>

          @if(auth()->user()->hasStaffAccess())
          <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
              <i class="bi bi-three-dots"></i>
            </button>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="{{ route('courses.edit', $course) }}">
                  <i class="bi bi-pencil me-1"></i>Edit Course
                </a></li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li>
                <form action="{{ route('courses.destroy', $course) }}" method="POST"
                  onsubmit="return confirm('Are you sure you want to delete this course?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="dropdown-item text-danger">
                    <i class="bi bi-trash me-1"></i>Delete Course
                  </button>
                </form>
              </li>
            </ul>
          </div>
          @endif
        </div>

        <div class="course-description">
          <h3>About This Course</h3>
          <p>{{ $course->description }}</p>
        </div>

        @if($course->enrolledStudents->count() > 0 && auth()->user()->hasStaffAccess())
        <div class="enrolled-students mt-4">
          <h4>Enrolled Students ({{ $course->enrolledStudents->count() }})</h4>
          <div class="row g-3">
            @foreach($course->enrolledStudents as $student)
            <div class="col-md-6">
              <div class="student-card">
                <div class="student-info">
                  <h6>{{ $student->name }}</h6>
                  <small class="text-muted">{{ $student->email }}</small>
                  <div class="student-meta">
                    <span class="badge bg-success">
                      Enrolled {{ $student->pivot->enrolled_at->format('M d, Y') }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
            @endforeach
          </div>
        </div>
        @endif
      </div>
    </div>

    <!-- Course Sidebar -->
    <div class="col-lg-4">
      <div class="course-sidebar">
        <!-- Enrollment Card -->
        <div class="enrollment-card">
          <div class="enrollment-header">
            <h4>Enrollment</h4>
          </div>

          <div class="enrollment-body">
            <div class="enrollment-stats">
              <div class="stat-item">
                <div class="stat-value">{{ $course->current_enrolled }}</div>
                <div class="stat-label">Enrolled</div>
              </div>
              <div class="stat-divider"></div>
              <div class="stat-item">
                <div class="stat-value">{{ $course->max_students }}</div>
                <div class="stat-label">Maximum</div>
              </div>
              <div class="stat-divider"></div>
              <div class="stat-item">
                <div class="stat-value">{{ $course->max_students - $course->current_enrolled }}</div>
                <div class="stat-label">Available</div>
              </div>
            </div>

            <div class="enrollment-progress mb-3">
              <div class="progress">
                <div class="progress-bar" role="progressbar"
                  style="width: {{ $course->max_students > 0 ? ($course->current_enrolled / $course->max_students) * 100 : 0 }}%">
                </div>
              </div>
              <small class="text-muted">
                {{ $course->max_students > 0 ? round(($course->current_enrolled / $course->max_students) * 100) : 0 }}%
                full
              </small>
            </div>

            @if(auth()->user()->isStudent())
            @if($isEnrolled)
            <form action="{{ route('courses.unenroll', $course) }}" method="POST">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-outline-danger w-100"
                onclick="return confirm('Are you sure you want to unenroll from this course?')">
                <i class="bi bi-x-circle me-2"></i>Unenroll
              </button>
            </form>
            <small class="text-success d-block mt-2 text-center">
              <i class="bi bi-check-circle me-1"></i>You are enrolled in this course
            </small>
            @else
            @if($course->isFull())
            <button class="btn btn-secondary w-100" disabled>
              <i class="bi bi-exclamation-triangle me-2"></i>Course Full
            </button>
            @elseif($course->status !== 'active')
            <button class="btn btn-secondary w-100" disabled>
              <i class="bi bi-pause-circle me-2"></i>Not Available
            </button>
            @else
            <form action="{{ route('courses.enroll', $course) }}" method="POST">
              @csrf
              <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-plus-circle me-2"></i>Enroll Now
              </button>
            </form>
            @endif
            @endif
            @endif
          </div>
        </div>

        <!-- Course Details Card -->
        <div class="details-card">
          <div class="details-header">
            <h4>Course Details</h4>
          </div>

          <div class="details-body">
            @if($course->price > 0)
            <div class="detail-item">
              <i class="bi bi-currency-dollar detail-icon"></i>
              <div>
                <strong>Price</strong>
                <div>${{ number_format($course->price, 2) }}</div>
              </div>
            </div>
            @else
            <div class="detail-item">
              <i class="bi bi-gift detail-icon"></i>
              <div>
                <strong>Price</strong>
                <div>Free</div>
              </div>
            </div>
            @endif

            @if($course->instructor)
            <div class="detail-item">
              <i class="bi bi-person detail-icon"></i>
              <div>
                <strong>Instructor</strong>
                <div>{{ $course->instructor }}</div>
              </div>
            </div>
            @endif

            @if($course->start_date)
            <div class="detail-item">
              <i class="bi bi-calendar-event detail-icon"></i>
              <div>
                <strong>Start Date</strong>
                <div>{{ $course->start_date->format('F j, Y') }}</div>
              </div>
            </div>
            @endif

            @if($course->end_date)
            <div class="detail-item">
              <i class="bi bi-calendar-check detail-icon"></i>
              <div>
                <strong>End Date</strong>
                <div>{{ $course->end_date->format('F j, Y') }}</div>
              </div>
            </div>
            @endif

            <div class="detail-item">
              <i class="bi bi-clock detail-icon"></i>
              <div>
                <strong>Created</strong>
                <div>{{ $course->created_at->format('F j, Y') }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="mt-4">
    <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-2"></i>Back to Courses
    </a>
  </div>
</div>

<style>
  .course-hero {
    position: relative;
    height: 400px;
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 2rem;
  }

  .course-hero-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .course-hero-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
    color: white;
  }

  .course-hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to bottom, rgba(0, 0, 0, 0.3) 0%, transparent 50%);
    display: flex;
    align-items: flex-start;
    justify-content: flex-end;
    padding: 1rem;
  }

  .course-status-badge {
    padding: 8px 16px;
    border-radius: 25px;
    font-size: 0.875rem;
    font-weight: 600;
    color: white;
    backdrop-filter: blur(10px);
  }

  .course-status-badge.available {
    background: rgba(40, 167, 69, 0.9);
  }

  .course-status-badge.almost-full {
    background: rgba(255, 193, 7, 0.9);
  }

  .course-status-badge.full {
    background: rgba(220, 53, 69, 0.9);
  }

  .course-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 1rem;
  }

  .course-content {
    background: white;
    padding: 2rem;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  }

  .course-description h3 {
    color: #4a5568;
    margin-bottom: 1rem;
  }

  .course-description p {
    color: #718096;
    line-height: 1.6;
    font-size: 1.1rem;
  }

  .course-sidebar {
    position: sticky;
    top: 2rem;
  }

  .enrollment-card,
  .details-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    margin-bottom: 1.5rem;
    overflow: hidden;
  }

  .enrollment-header,
  .details-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1rem 1.5rem;
    font-size: 1.1rem;
    font-weight: 600;
  }

  .enrollment-body,
  .details-body {
    padding: 1.5rem;
  }

  .enrollment-stats {
    display: flex;
    justify-content: space-around;
    margin-bottom: 1.5rem;
  }

  .stat-item {
    text-align: center;
  }

  .stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #667eea;
  }

  .stat-label {
    font-size: 0.875rem;
    color: #718096;
  }

  .stat-divider {
    width: 1px;
    background: #e2e8f0;
    margin: 0 1rem;
  }

  .enrollment-progress .progress {
    height: 10px;
    border-radius: 5px;
    background: #f7fafc;
  }

  .enrollment-progress .progress-bar {
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    border-radius: 5px;
  }

  .detail-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1rem;
  }

  .detail-icon {
    color: #667eea;
    font-size: 1.2rem;
    margin-right: 1rem;
    margin-top: 2px;
  }

  .detail-item strong {
    color: #4a5568;
    display: block;
    margin-bottom: 0.25rem;
  }

  .detail-item>div>div {
    color: #718096;
  }

  .student-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
  }

  .student-info h6 {
    margin-bottom: 0.25rem;
    color: #2d3748;
  }

  .student-meta {
    margin-top: 0.5rem;
  }

  @media (max-width: 992px) {
    .course-title {
      font-size: 2rem;
    }

    .course-sidebar {
      position: static;
    }
  }
</style>
@endsection