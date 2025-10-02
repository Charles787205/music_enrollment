@extends('layouts.app')

@section('title', 'Enroll in ' . $course->title)

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card shadow-lg">
        <div class="card-header bg-primary text-white text-center">
          <h3 class="mb-0">
            <i class="bi bi-mortarboard me-2"></i>
            Enroll in Course
          </h3>
        </div>

        <div class="card-body p-5">
          <!-- Course Info -->
          <div class="course-preview mb-4">
            <div class="row">
              @if($course->image)
              <div class="col-md-4">
                <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}"
                  class="img-fluid rounded shadow-sm">
              </div>
              <div class="col-md-8">
                @else
                <div class="col-md-12">
                  @endif
                  <h4 class="course-title text-primary">{{ $course->title }}</h4>
                  <p class="course-description text-muted">
                    {{ Str::limit($course->description, 150) }}
                  </p>

                  <div class="course-details">
                    @if($course->teacher)
                    <div class="detail-item">
                      <i class="bi bi-person-badge text-primary me-2"></i>
                      <strong>Teacher:</strong> {{ $course->teacher->name }}
                    </div>
                    @endif

                    @if($course->price > 0)
                    <div class="detail-item">
                      <i class="bi bi-currency-dollar text-success me-2"></i>
                      <strong>Price:</strong> ${{ number_format($course->price, 2) }}
                    </div>
                    @else
                    <div class="detail-item">
                      <i class="bi bi-gift text-success me-2"></i>
                      <strong>Price:</strong> Free
                    </div>
                    @endif

                    <div class="detail-item">
                      <i class="bi bi-people text-info me-2"></i>
                      <strong>Enrollment:</strong> {{ $course->current_enrolled }}/{{ $course->max_students }} students
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <hr class="my-4">

            <!-- Enrollment Choice -->
            <div class="text-center">
              <h5 class="mb-4">Are you a student at our music school?</h5>

              <div class="row g-3">
                <div class="col-md-6">
                  <div class="choice-card h-100">
                    <div class="card border-success">
                      <div class="card-body text-center p-4">
                        <i class="bi bi-check-circle-fill text-success display-4 mb-3"></i>
                        <h5 class="card-title text-success">Yes, I'm a Student</h5>
                        <p class="card-text">
                          Sign in to your student account to enroll in this course.
                        </p>
                        <a href="{{ route('login', ['redirect' => route('courses.show', $course)]) }}"
                          class="btn btn-success btn-lg">
                          <i class="bi bi-box-arrow-in-right me-2"></i>
                          Sign In to Enroll
                        </a>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="choice-card h-100">
                    <div class="card border-primary">
                      <div class="card-body text-center p-4">
                        <i class="bi bi-person-plus-fill text-primary display-4 mb-3"></i>
                        <h5 class="card-title text-primary">No, I'm New</h5>
                        <p class="card-text">
                          Register as a new student and we'll get you enrolled!
                        </p>
                        <a href="{{ route('guest.enroll.course', $course) }}" class="btn btn-primary btn-lg">
                          <i class="bi bi-plus-circle me-2"></i>
                          Register & Enroll
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Additional Information -->
            <div class="mt-5 text-center">
              <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Note:</strong> All course enrollments require approval from our staff.
                You'll receive confirmation once your enrollment is reviewed.
              </div>

              <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left me-2"></i>
                View Course Details
              </a>

              <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-grid me-2"></i>
                Browse Other Courses
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <style>
    .course-preview {
      background: #f8f9fa;
      border-radius: 10px;
      padding: 20px;
    }

    .course-title {
      font-weight: 600;
      margin-bottom: 10px;
    }

    .course-description {
      font-size: 1.1rem;
      line-height: 1.6;
      margin-bottom: 15px;
    }

    .detail-item {
      margin-bottom: 8px;
      display: flex;
      align-items: center;
    }

    .choice-card .card {
      transition: all 0.3s ease;
      height: 100%;
    }

    .choice-card .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .choice-card .btn {
      min-width: 180px;
    }

    .choice-card i.display-4 {
      opacity: 0.8;
    }
  </style>
  @endsection