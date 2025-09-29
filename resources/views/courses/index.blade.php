@extends('layouts.app')

@section('title', 'Courses')

@section('content')
<div class="courses-page">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Available Courses</h2>
    @if(auth()->user()->hasStaffAccess())
    <a href="{{ route('courses.create') }}" class="btn btn-primary">
      <i class="bi bi-plus-circle me-2"></i>Add New Course
    </a>
    @endif
  </div>

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

  @if($courses->count() > 0)
  <div class="row g-4">
    @foreach($courses as $course)
    <div class="col-lg-4 col-md-6">
      <div class="course-card h-100">
        <div class="course-image-container">
          @if($course->image)
          <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}" class="course-image">
          @else
          <div class="course-image-placeholder">
            <i class="bi bi-music-note-beamed"></i>
          </div>
          @endif

          @if($course->isFull())
          <div class="course-status-badge full">
            <i class="bi bi-exclamation-triangle me-1"></i>Full
          </div>
          @elseif($course->current_enrolled > ($course->max_students * 0.8))
          <div class="course-status-badge almost-full">
            <i class="bi bi-hourglass-split me-1"></i>Almost Full
          </div>
          @else
          <div class="course-status-badge available">
            <i class="bi bi-check-circle me-1"></i>Available
          </div>
          @endif
        </div>

        <div class="course-card-body">
          <h5 class="course-title">{{ $course->title }}</h5>
          <p class="course-description">
            {{ Str::limit($course->description, 120) }}
          </p>

          <div class="course-meta">
            <div class="meta-item">
              <i class="bi bi-people me-1"></i>
              {{ $course->current_enrolled }}/{{ $course->max_students }} students
            </div>

            @if($course->instructor)
            <div class="meta-item">
              <i class="bi bi-person me-1"></i>
              {{ $course->instructor }}
            </div>
            @endif

            @if($course->price > 0)
            <div class="meta-item">
              <i class="bi bi-currency-dollar me-1"></i>
              ${{ number_format($course->price, 2) }}
            </div>
            @else
            <div class="meta-item">
              <i class="bi bi-gift me-1"></i>
              Free
            </div>
            @endif

            @if($course->start_date)
            <div class="meta-item">
              <i class="bi bi-calendar me-1"></i>
              {{ $course->start_date->format('M d, Y') }}
            </div>
            @endif
          </div>

          <div class="course-progress">
            <div class="progress">
              <div class="progress-bar" role="progressbar"
                style="width: {{ $course->max_students > 0 ? ($course->current_enrolled / $course->max_students) * 100 : 0 }}%">
              </div>
            </div>
            <small class="text-muted mt-1">
              {{ $course->max_students > 0 ? round(($course->current_enrolled / $course->max_students) * 100) : 0 }}%
              full
            </small>
          </div>
        </div>

        <div class="course-card-footer">
          <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-primary flex-fill me-2">
            <i class="bi bi-eye me-1"></i>View Details
          </a>

          @if(auth()->user()->hasStaffAccess())
          <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
              <i class="bi bi-three-dots"></i>
            </button>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="{{ route('courses.edit', $course) }}">
                  <i class="bi bi-pencil me-1"></i>Edit
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
                    <i class="bi bi-trash me-1"></i>Delete
                  </button>
                </form>
              </li>
            </ul>
          </div>
          @endif
        </div>
      </div>
    </div>
    @endforeach
  </div>

  <div class="mt-4">
    {{ $courses->links() }}
  </div>
  @else
  <div class="empty-state">
    <div class="empty-icon">
      <i class="bi bi-music-note-list"></i>
    </div>
    <h4>No courses available</h4>
    <p class="text-muted">There are currently no courses to display.</p>
    @if(auth()->user()->hasStaffAccess())
    <a href="{{ route('courses.create') }}" class="btn btn-primary">
      <i class="bi bi-plus-circle me-2"></i>Create First Course
    </a>
    @endif
  </div>
  @endif
</div>

<style>
  :root {
    --course-card-radius: 16px;
    --course-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    --course-hover-shadow: 0 12px 48px rgba(0, 0, 0, 0.15);
  }

  .course-card {
    background: white;
    border-radius: var(--course-card-radius);
    box-shadow: var(--course-shadow);
    overflow: hidden;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
  }

  .course-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--course-hover-shadow);
  }

  .course-image-container {
    position: relative;
    height: 200px;
    overflow: hidden;
  }

  .course-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
  }

  .course-card:hover .course-image {
    transform: scale(1.05);
  }

  .course-image-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: white;
  }

  .course-status-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    padding: 4px 8px;
    border-radius: 20px;
    font-size: 0.75rem;
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

  .course-card-body {
    padding: 1.5rem;
    flex-grow: 1;
  }

  .course-title {
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0.75rem;
    line-height: 1.3;
  }

  .course-description {
    color: #718096;
    margin-bottom: 1rem;
    line-height: 1.5;
  }

  .course-meta {
    margin-bottom: 1rem;
  }

  .meta-item {
    display: flex;
    align-items: center;
    font-size: 0.875rem;
    color: #4a5568;
    margin-bottom: 0.5rem;
  }

  .meta-item i {
    color: #667eea;
    width: 16px;
  }

  .course-progress {
    margin-bottom: 1rem;
  }

  .course-progress .progress {
    height: 8px;
    border-radius: 4px;
    background: #f7fafc;
  }

  .course-progress .progress-bar {
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    border-radius: 4px;
  }

  .course-card-footer {
    padding: 1rem 1.5rem;
    background: #f8f9fa;
    display: flex;
    gap: 0.5rem;
    border-top: 1px solid #e2e8f0;
  }

  .empty-state {
    text-align: center;
    padding: 4rem 2rem;
  }

  .empty-icon {
    font-size: 4rem;
    color: #cbd5e0;
    margin-bottom: 1rem;
  }

  .empty-state h4 {
    color: #4a5568;
    margin-bottom: 0.5rem;
  }

  @media (max-width: 768px) {
    .course-card-footer {
      flex-direction: column;
    }

    .course-card-footer .btn {
      margin-right: 0 !important;
    }
  }
</style>
@endsection