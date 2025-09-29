@extends('layouts.auth')

@section('title', 'Create Account')

@section('content')
<div class="auth-gradient d-flex align-items-center justify-content-center">
  <div class="wave-bg"></div>

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-6">
        <div class="auth-card slide-in p-4">
          <!-- Logo/Icon -->
          <div class="auth-icon music-note">
            <i class="bi bi-person-plus"></i>
          </div>

          <!-- Welcome Text -->
          <div class="text-center mb-4">
            <h2 class="fw-bold mb-2" style="color: #2d3748;">Join Music School</h2>
            <p class="text-muted">Create your account to start your musical journey</p>
          </div>

          <!-- Registration Form -->
          <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="row">
              <div class="col-md-6">
                <!-- Name Field -->
                <div class="floating-label">
                  <input id="name" type="text" class="form-control glass-input @error('name') is-invalid @enderror"
                    name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder=" ">
                  <label for="name">Full Name</label>
                  @error('name')
                  <div class="invalid-feedback d-block">
                    <strong>{{ $message }}</strong>
                  </div>
                  @enderror
                </div>
              </div>

              <div class="col-md-6">
                <!-- Email Field -->
                <div class="floating-label">
                  <input id="email" type="email" class="form-control glass-input @error('email') is-invalid @enderror"
                    name="email" value="{{ old('email') }}" required autocomplete="email" placeholder=" ">
                  <label for="email">Email Address</label>
                  @error('email')
                  <div class="invalid-feedback d-block">
                    <strong>{{ $message }}</strong>
                  </div>
                  @enderror
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <!-- Password Field -->
                <div class="floating-label">
                  <input id="password" type="password"
                    class="form-control glass-input @error('password') is-invalid @enderror" name="password" required
                    autocomplete="new-password" placeholder=" ">
                  <label for="password">Password</label>
                  @error('password')
                  <div class="invalid-feedback d-block">
                    <strong>{{ $message }}</strong>
                  </div>
                  @enderror
                </div>
              </div>

              <div class="col-md-6">
                <!-- Confirm Password Field -->
                <div class="floating-label">
                  <input id="password-confirm" type="password" class="form-control glass-input"
                    name="password_confirmation" required autocomplete="new-password" placeholder=" ">
                  <label for="password-confirm">Confirm Password</label>
                </div>
              </div>
            </div>

            <!-- User Type Selection -->
            <div class="mb-4">
              <label class="form-label text-muted fw-semibold mb-3">Account Type</label>
              <div class="row g-3">
                <div class="col-md-4">
                  <div class="form-check h-100">
                    <input class="form-check-input" type="radio" name="user_type" id="student" value="student"
                      {{ old('user_type', 'student') === 'student' ? 'checked' : '' }}>
                    <label class="form-check-label w-100" for="student">
                      <i class="bi bi-person fs-4 d-block text-center mb-2"></i>
                      <strong>Student</strong>
                      <small class="d-block text-muted">Learn and enroll in courses</small>
                    </label>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-check h-100">
                    <input class="form-check-input" type="radio" name="user_type" id="employee" value="employee"
                      {{ old('user_type') === 'employee' ? 'checked' : '' }}>
                    <label class="form-check-label w-100" for="employee">
                      <i class="bi bi-briefcase fs-4 d-block text-center mb-2"></i>
                      <strong>Employee</strong>
                      <small class="d-block text-muted">Manage students and classes</small>
                    </label>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-check h-100">
                    <input class="form-check-input" type="radio" name="user_type" id="admin" value="admin"
                      {{ old('user_type') === 'admin' ? 'checked' : '' }}>
                    <label class="form-check-label w-100" for="admin">
                      <i class="bi bi-shield-check fs-4 d-block text-center mb-2"></i>
                      <strong>Admin</strong>
                      <small class="d-block text-muted">Full system access</small>
                    </label>
                  </div>
                </div>
              </div>
              @error('user_type')
              <div class="invalid-feedback d-block">
                <strong>{{ $message }}</strong>
              </div>
              @enderror
            </div>

            <!-- Register Button -->
            <div class="d-grid">
              <button type="submit" class="btn btn-gradient">
                <i class="bi bi-person-plus me-2"></i>
                Create Account
              </button>
            </div>
          </form>

          <!-- Login Link -->
          <div class="text-center mt-4 pt-3 border-top">
            <p class="text-muted mb-2">Already have an account?</p>
            <a href="{{ route('login') }}" class="btn btn-outline-secondary">
              <i class="bi bi-box-arrow-in-right me-2"></i>
              Sign In
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  /* Enhanced styles for registration form */
  .form-check {
    background: rgba(255, 255, 255, 0.7);
    border-radius: 12px;
    padding: 1rem;
    border: 1px solid rgba(102, 126, 234, 0.2);
    transition: all 0.3s ease;
    text-align: center;
    cursor: pointer;
  }

  .form-check:has(input:checked) {
    background: rgba(102, 126, 234, 0.15);
    border-color: #667eea;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
  }

  .form-check-input {
    margin: 0 0 0.5rem 0;
  }

  .form-check-input:checked {
    background-color: #667eea;
    border-color: #667eea;
  }

  .floating-label {
    margin-bottom: 1.25rem;
  }

  /* Better responsive design */
  @media (max-width: 768px) {
    .auth-card {
      margin: 1rem;
      padding: 1.5rem !important;
    }

    .row .col-md-6:first-child .floating-label {
      margin-bottom: 1rem;
    }
  }
</style>
@endsection