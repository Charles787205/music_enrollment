@extends('layouts.app')

@section('title', 'Create New Course')

@section('content')
<div class="create-course-page">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="course-form-card">
        <div class="form-header">
          <h2><i class="bi bi-plus-circle me-2"></i>Create New Course</h2>
          <p class="text-muted mb-0">Add a new course to the music enrollment system</p>
        </div>

        @if ($errors->any())
        <div class="alert alert-danger">
          <h6><i class="bi bi-exclamation-triangle me-2"></i>Please fix the following errors:</h6>
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif

        <form action="{{ route('courses.store') }}" method="POST" enctype="multipart/form-data">
          @csrf

          <div class="row g-3">
            <!-- Title -->
            <div class="col-md-8">
              <label for="title" class="form-label">Course Title <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title"
                value="{{ old('title') }}" placeholder="Enter course title">
              @error('title')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Max Students -->
            <div class="col-md-4">
              <label for="max_students" class="form-label">Max Students <span class="text-danger">*</span></label>
              <input type="number" class="form-control @error('max_students') is-invalid @enderror" id="max_students"
                name="max_students" value="{{ old('max_students', 50) }}" min="1" max="200" placeholder="50">
              @error('max_students')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Description -->
            <div class="col-12">
              <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
              <textarea class="form-control @error('description') is-invalid @enderror" id="description"
                name="description" rows="4"
                placeholder="Enter course description...">{{ old('description') }}</textarea>
              @error('description')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Course Image -->
            <div class="col-12">
              <label for="image" class="form-label">Course Image</label>
              <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image"
                accept="image/jpeg,image/png,image/jpg,image/gif">
              <div class="form-text">
                <i class="bi bi-info-circle me-1"></i>
                Upload an image for the course (JPEG, PNG, JPG, GIF - Max 2MB)
              </div>
              @error('image')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror

              <!-- Image Preview -->
              <div id="imagePreview" class="image-preview mt-3" style="display: none;">
                <img id="previewImg" src="" alt="Course Image Preview">
              </div>
            </div>

            <!-- Price and Instructor Row -->
            <div class="col-md-6">
              <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price"
                  value="{{ old('price', '0.00') }}" min="0" step="0.01" placeholder="0.00">
                @error('price')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="form-text">Enter 0 for free courses</div>
            </div>

            <div class="col-md-6">
              <label for="instructor" class="form-label">Instructor</label>
              <input type="text" class="form-control @error('instructor') is-invalid @enderror" id="instructor"
                name="instructor" value="{{ old('instructor') }}" placeholder="Instructor name (optional)">
              @error('instructor')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Date Range -->
            <div class="col-md-6">
              <label for="start_date" class="form-label">Start Date</label>
              <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date"
                name="start_date" value="{{ old('start_date') }}" min="{{ date('Y-m-d') }}">
              @error('start_date')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label for="end_date" class="form-label">End Date</label>
              <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date"
                name="end_date" value="{{ old('end_date') }}">
              @error('end_date')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-check-circle me-2"></i>Create Course
            </button>
            <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">
              <i class="bi bi-arrow-left me-2"></i>Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
  .course-form-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    overflow: hidden;
  }

  .form-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem 2rem 1.5rem;
  }

  .form-header h2 {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
  }

  .course-form-card form {
    padding: 2rem;
  }

  .form-label {
    font-weight: 600;
    color: #4a5568;
    margin-bottom: 0.5rem;
  }

  .form-control {
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    transition: all 0.2s ease;
  }

  .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
  }

  .form-control.is-invalid {
    border-color: #e53e3e;
  }

  .invalid-feedback {
    font-size: 0.875rem;
    margin-top: 0.25rem;
  }

  .input-group-text {
    background: #f7fafc;
    border: 2px solid #e2e8f0;
    border-right: none;
    font-weight: 600;
    color: #4a5568;
  }

  .form-text {
    font-size: 0.875rem;
    color: #718096;
    margin-top: 0.25rem;
  }

  .image-preview {
    max-width: 300px;
    border-radius: 8px;
    overflow: hidden;
    border: 2px solid #e2e8f0;
  }

  .image-preview img {
    width: 100%;
    height: auto;
    display: block;
  }

  .form-actions {
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e2e8f0;
    display: flex;
    gap: 1rem;
  }

  .btn {
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.2s ease;
  }

  .btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
  }

  .btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
  }

  .btn-outline-secondary {
    border: 2px solid #e2e8f0;
    color: #4a5568;
  }

  .btn-outline-secondary:hover {
    background: #f7fafc;
    border-color: #cbd5e0;
    color: #2d3748;
  }

  @media (max-width: 768px) {
    .form-actions {
      flex-direction: column;
    }

    .form-header {
      padding: 1.5rem;
    }

    .course-form-card form {
      padding: 1.5rem;
    }
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Image preview functionality
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');

    imageInput.addEventListener('change', function (e) {
      const file = e.target.files[0];

      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          previewImg.src = e.target.result;
          imagePreview.style.display = 'block';
        };
        reader.readAsDataURL(file);
      } else {
        imagePreview.style.display = 'none';
      }
    });

    // Date validation
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');

    startDate.addEventListener('change', function () {
      endDate.min = this.value;
    });
  });
</script>
@endsection