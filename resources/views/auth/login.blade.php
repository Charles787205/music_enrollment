@extends('layouts.auth')

@section('title', 'Sign In')

@section('content')
<div class="auth-gradient d-flex align-items-center justify-content-center">
  <div class="wave-bg"></div>

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-5 col-lg-4">
        <div class="auth-card slide-in p-4">
          <!-- Logo/Icon -->
          <div class="auth-icon music-note">
            <i class="bi bi-music-note-beamed"></i>
          </div>

          <!-- Welcome Text -->
          <div class="text-center mb-4">
            <h2 class="fw-bold mb-2" style="color: #2d3748;">Welcome Back</h2>
            <p class="text-muted">Sign in to your Muzic's Kool account</p>
          </div>

          <!-- Login Form -->
          <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Field -->
            <div class="floating-label">
              <input id="email" type="email" class="form-control glass-input @error('email') is-invalid @enderror"
                name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder=" ">
              <label for="email">Email Address</label>
              @error('email')
              <div class="invalid-feedback d-block">
                <strong>{{ $message }}</strong>
              </div>
              @enderror
            </div>

            <!-- Password Field -->
            <div class="floating-label">
              <input id="password" type="password"
                class="form-control glass-input @error('password') is-invalid @enderror" name="password" required
                autocomplete="current-password" placeholder=" ">
              <label for="password">Password</label>
              @error('password')
              <div class="invalid-feedback d-block">
                <strong>{{ $message }}</strong>
              </div>
              @enderror
            </div>

            <!-- Remember Me -->
            <div class="remember-me">
              <input class="form-check-input" type="checkbox" name="remember" id="remember"
                {{ old('remember') ? 'checked' : '' }}>
              <label class="form-check-label text-muted" for="remember">
                Remember me
              </label>
            </div>

            <!-- Login Button -->
            <div class="d-grid">
              <button type="submit" class="btn btn-gradient">
                <i class="bi bi-box-arrow-in-right me-2"></i>
                Sign In
              </button>
            </div>
          </form>

          <!-- Register Link -->
          <div class="text-center mt-4 pt-3 border-top">
            <p class="text-muted mb-3">Welcome to Muzic's Kool!</p>

            <!-- Quick Action Buttons -->
            <div class="row g-2 mb-3">
              <div class="col-12">
                <a href="{{ route('courses.index') }}" class="btn btn-success w-100">
                  <i class="bi bi-music-note me-2"></i>
                  Browse Courses & Enroll
                </a>
              </div>
            </div>

            <!-- Help Text -->
            <div class="small text-muted">
              <p class="mb-2">
                <i class="bi bi-info-circle me-1"></i>
                Students can <strong>enroll directly</strong> without creating an account
              </p>
              <hr class="my-3">
              <p class="mb-0 text-center">
                <i class="bi bi-shield-check me-1"></i>
                Staff Member? <a href="{{ route('staff.register') }}"
                  class="text-primary text-decoration-none fw-bold">Staff Registration</a>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  /* Additional inline styles for better control */
  .floating-label input:focus+label {
    color: #667eea !important;
  }

  .btn-outline-secondary {
    border-color: #667eea;
    color: #667eea;
    transition: all 0.3s ease;
  }

  .btn-outline-secondary:hover {
    background: #667eea;
    border-color: #667eea;
    color: white;
    transform: translateY(-1px);
  }

  .btn-success {
    background: linear-gradient(135deg, #28a745, #20c997);
    border: none;
    transition: all 0.3s ease;
    position: relative;
    z-index: 10;
  }

  .btn-success:hover {
    background: linear-gradient(135deg, #218838, #1ca085);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
  }

  .btn-outline-primary {
    border-color: #667eea;
    color: #667eea;
    transition: all 0.3s ease;
    position: relative;
    z-index: 10;
  }

  .btn-outline-primary:hover {
    background: #667eea;
    border-color: #667eea;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
  }

  /* Small animation for buttons */
  .btn {
    transition: all 0.3s ease;
    position: relative;
    z-index: 10;
  }

  /* Ensure auth-card has proper z-index */
  .auth-card {
    position: relative;
    z-index: 5;
  }

  /* Ensure links are clickable */
  a {
    position: relative;
    z-index: 10;
  }

  /* Info text styling */
  .text-muted p {
    font-size: 0.875rem;
    line-height: 1.4;
  }
</style>
@endsection