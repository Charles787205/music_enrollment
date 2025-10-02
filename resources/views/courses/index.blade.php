@extends('layouts.app')

@section('title', 'Courses')

@section('content')
<div class="courses-page-wrapper">
  <div class="container-fluid px-4 py-5">
    <!-- Header Section -->
    <div class="courses-header mb-5">
      <div class="row align-items-center">
        <div class="col-lg-8">
          <h1 class="display-5 fw-bold text-primary mb-2">Available Courses</h1>
          <p class="lead text-muted mb-0">Discover your musical passion with our expert-led courses</p>
        </div>
        <div class="col-lg-4 text-lg-end">
          @if(auth()->check() && auth()->user()->hasStaffAccess())
          <a href="{{ route('courses.create') }}" class="btn btn-primary btn-lg shadow-sm">
            <i class="bi bi-plus-circle me-2"></i>Add New Course
          </a>
          @endif
        </div>
      </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($courses->count() > 0)
    <!-- Courses Grid -->
    <div class="courses-grid">
      <div class="row g-4">
        @foreach($courses as $course)
        <div class="col-xl-4 col-lg-6 col-md-6">
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

                @if($course->teacher)
                <div class="meta-item">
                  <i class="bi bi-person-badge me-1"></i>
                  {{ $course->teacher->name }}
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

              @if(!$course->isFull() && $course->status === 'active')
              <a href="{{ route('courses.enroll-choice', $course) }}" class="btn btn-primary flex-fill">
                <i class="bi bi-plus-circle me-1"></i>Enroll Now
              </a>
              @else
              <button class="btn btn-secondary flex-fill" disabled>
                @if($course->isFull())
                <i class="bi bi-exclamation-triangle me-1"></i>Full
                @else
                <i class="bi bi-pause-circle me-1"></i>Unavailable
                @endif
              </button>
              @endif

              @if(auth()->check() && auth()->user()->hasStaffAccess())
              <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle ms-2" type="button" data-bs-toggle="dropdown">
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
    </div>

    <!-- Pagination -->
    <div class="pagination-wrapper mt-5 d-flex justify-content-center">
      {{ $courses->links() }}
    </div>
    @else
    <!-- Empty State -->
    <div class="empty-state">
      <div class="empty-state-content">
        <div class="empty-icon">
          <i class="bi bi-music-note-list"></i>
        </div>
        <h3 class="mt-4 mb-3">No courses available</h3>
        <p class="text-muted mb-4">There are currently no courses to display. Check back soon for new offerings!</p>
        @if(auth()->check() && auth()->user()->hasStaffAccess())
        <a href="{{ route('courses.create') }}" class="btn btn-primary btn-lg">
          <i class="bi bi-plus-circle me-2"></i>Create First Course
        </a>
        @endif
      </div>
    </div>
    @endif
  </div>
</div>

<style>
  :root {
    --course-card-radius: 20px;
    --course-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    --course-hover-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    --warning-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    --danger-gradient: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
  }

  .courses-page-wrapper {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
  }

  .courses-header {
    background: white;
    padding: 2rem;
    border-radius: var(--course-card-radius);
    box-shadow: var(--course-shadow);
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
  }

  .courses-header::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 200px;
    height: 200px;
    background: var(--primary-gradient);
    opacity: 0.1;
    border-radius: 50%;
    transform: translate(50%, -50%);
  }

  .courses-header h1 {
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .courses-grid {
    position: relative;
  }

  .course-card {
    background: white;
    border-radius: var(--course-card-radius);
    box-shadow: var(--course-shadow);
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    display: flex;
    flex-direction: column;
    border: 1px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
  }

  .course-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: var(--course-hover-shadow);
  }

  .course-image-container {
    position: relative;
    height: 220px;
    overflow: hidden;
    background: var(--primary-gradient);
  }

  .course-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
  }

  .course-card:hover .course-image {
    transform: scale(1.1);
  }

  .course-image-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    background: var(--primary-gradient);
    color: white;
    font-size: 4rem;
    opacity: 0.8;
  }

  .course-status-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    padding: 8px 16px;
    border-radius: 25px;
    font-size: 0.85rem;
    font-weight: 600;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
  }

  .course-status-badge.available {
    background: var(--success-gradient);
  }

  .course-status-badge.almost-full {
    background: var(--warning-gradient);
  }

  .course-status-badge.full {
    background: var(--danger-gradient);
  }

  .course-card-body {
    padding: 1.75rem;
    flex: 1;
    display: flex;
    flex-direction: column;
  }

  .course-title {
    font-size: 1.35rem;
    font-weight: 700;
    margin-bottom: 1rem;
    color: #2d3748;
    line-height: 1.3;
  }

  .course-description {
    color: #718096;
    font-size: 1rem;
    line-height: 1.6;
    margin-bottom: 1.5rem;
    flex: 1;
  }

  .course-meta {
    margin-bottom: 1.5rem;
  }

  .meta-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.75rem;
    font-size: 0.95rem;
    color: #4a5568;
    font-weight: 500;
  }

  .meta-item i {
    color: #667eea;
    font-size: 1.1rem;
    width: 20px;
  }

  .course-progress {
    margin-bottom: 1rem;
  }

  .course-progress .progress {
    height: 8px;
    border-radius: 10px;
    background-color: #edf2f7;
    overflow: hidden;
  }

  .course-progress .progress-bar {
    background: var(--primary-gradient);
    border-radius: 10px;
    transition: width 0.6s ease;
  }

  .course-card-footer {
    padding: 1.5rem 1.75rem;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    display: flex;
    gap: 0.75rem;
    align-items: center;
  }

  .course-card-footer .btn {
    border-radius: 12px;
    font-weight: 600;
    padding: 0.75rem 1.25rem;
    transition: all 0.3s ease;
    border: 2px solid transparent;
  }

  .course-card-footer .btn-primary {
    background: var(--primary-gradient);
    border: none;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
  }

  .course-card-footer .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
  }

  .course-card-footer .btn-outline-primary {
    border-color: #667eea;
    color: #667eea;
  }

  .course-card-footer .btn-outline-primary:hover {
    background: var(--primary-gradient);
    border-color: transparent;
    transform: translateY(-2px);
  }

  .empty-state {
    text-align: center;
    padding: 5rem 2rem;
    background: white;
    border-radius: var(--course-card-radius);
    box-shadow: var(--course-shadow);
    margin: 2rem 0;
  }

  .empty-state-content {
    max-width: 400px;
    margin: 0 auto;
  }

  .empty-icon {
    width: 120px;
    height: 120px;
    margin: 0 auto 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--primary-gradient);
    border-radius: 50%;
    color: white;
    font-size: 3rem;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
  }

  .empty-state h3 {
    color: #2d3748;
    font-weight: 700;
  }

  .pagination-wrapper .pagination {
    background: white;
    border-radius: 15px;
    padding: 1rem;
    box-shadow: var(--course-shadow);
    border: none;
  }

  .pagination-wrapper .page-link {
    border: none;
    border-radius: 10px;
    margin: 0 0.25rem;
    color: #667eea;
    font-weight: 600;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
  }

  .pagination-wrapper .page-link:hover {
    background: var(--primary-gradient);
    color: white;
    transform: translateY(-2px);
  }

  .pagination-wrapper .page-item.active .page-link {
    background: var(--primary-gradient);
    border: none;
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
  }

  .alert {
    border: none;
    border-radius: 15px;
    padding: 1.25rem 1.5rem;
    font-weight: 500;
    box-shadow: var(--course-shadow);
  }

  .alert-success {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
  }

  .alert-danger {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    color: #721c24;
  }

  /* Responsive Design */
  @media (max-width: 768px) {
    .courses-header {
      padding: 1.5rem;
      text-align: center;
    }

    .courses-header .row {
      flex-direction: column;
    }

    .courses-header .col-lg-4 {
      margin-top: 1rem;
    }

    .course-card-body {
      padding: 1.25rem;
    }

    .course-card-footer {
      flex-direction: column;
      gap: 0.5rem;
    }

    .course-card-footer .btn {
      width: 100%;
    }
  }

  /* Animation */
  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .course-card {
    animation: fadeInUp 0.6s ease forwards;
  }

  .course-card:nth-child(2) {
    animation-delay: 0.1s;
  }

  .course-card:nth-child(3) {
    animation-delay: 0.2s;
  }

  .course-card:nth-child(4) {
    animation-delay: 0.3s;
  }

  .course-card:nth-child(5) {
    animation-delay: 0.4s;
  }

  .course-card:nth-child(6) {
    animation-delay: 0.5s;
  }
</style>

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