@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-person-gear"></i> Edit Teacher</h1>
            <a href="{{ route('admin.teachers') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Teachers
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.teachers.update', $teacher) }}">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $teacher->name) }}" required>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $teacher->email) }}">
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Optional - leave blank if teacher doesn't have email</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $teacher->phone) }}">
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="specialization" class="form-label">Specialization</label>
                                <input type="text" class="form-control @error('specialization') is-invalid @enderror" 
                                       id="specialization" name="specialization" value="{{ old('specialization', $teacher->specialization) }}"
                                       placeholder="e.g., Piano, Guitar, Violin">
                                @error('specialization')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="bio" class="form-label">Biography</label>
                        <textarea class="form-control @error('bio') is-invalid @enderror" 
                                  id="bio" name="bio" rows="4" 
                                  placeholder="Tell us about the teacher's background, experience, and teaching philosophy...">{{ old('bio', $teacher->bio) }}</textarea>
                        @error('bio')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                   value="1" {{ old('is_active', $teacher->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active Teacher
                            </label>
                            <div class="form-text">Uncheck if this teacher is temporarily inactive</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.teachers') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update Teacher
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Course Assignments -->
        @if($teacher->courses && $teacher->courses->count() > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-book"></i> Assigned Courses</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($teacher->courses as $course)
                    <div class="col-md-6 mb-3">
                        <div class="card border">
                            <div class="card-body">
                                <h6 class="card-title">{{ $course->title }}</h6>
                                <p class="card-text text-muted mb-1">
                                    <i class="bi bi-cash"></i> ${{ number_format($course->fee, 2) }}
                                </p>
                                <p class="card-text text-muted mb-0">
                                    <i class="bi bi-people"></i> {{ $course->courseEnrollments->count() }} students
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection