@extends('layouts.auth')

@section('title', 'Staff Registration')

@section('content')
<div class="auth-gradient d-flex align-items-center justify-content-center">
  <div class="wave-bg"></div>

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="auth-card slide-in p-4">
          <!-- Logo/Icon -->
          <div class="auth-icon staff-icon">
            <i class="bi bi-shield-check"></i>
          </div>

          <!-- Welcome Text -->
          <div class="text-center mb-4">
            <h2 class="fw-bold mb-2" style="color: #2d3748;">Staff Registration</h2>
            <p class="text-muted">Join our music school team</p>
            @if(!App\Models\User::hasAdmins())
            <div class="alert alert-info">
              <i class="bi bi-info-circle me-2"></i>
              <strong>First Admin Setup:</strong> No administrators found. You will be automatically approved as the
              first admin.
            </div>
            @endif
          </div>

          <!-- Registration Form -->
          <form method="POST" action="{{ route('staff.register.store') }}">
            @csrf

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

            <!-- Phone Field -->
            <div class="floating-label">
              <input id="phone" type="tel" class="form-control glass-input @error('phone') is-invalid @enderror"
                name="phone" value="{{ old('phone') }}" autocomplete="tel" placeholder=" ">
              <label for="phone">Phone Number (Optional)</label>
              @error('phone')
              <div class="invalid-feedback d-block">
                <strong>{{ $message }}</strong>
              </div>
              @enderror
            </div>

            <!-- User Type Field -->
            <div class="floating-label">
              <select id="user_type" class="form-control glass-input @error('user_type') is-invalid @enderror"
                name="user_type" required>
                <option value="">Select Role</option>
                <option value="admin" {{ old('user_type') === 'admin' ? 'selected' : '' }}>Administrator</option>
                <option value="employee" {{ old('user_type') === 'employee' ? 'selected' : '' }}>Employee</option>
              </select>
              <label for="user_type">Role</label>
              @error('user_type')
              <div class="invalid-feedback d-block">
                <strong>{{ $message }}</strong>
              </div>
              @enderror
            </div>

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

            <!-- Confirm Password Field -->
            <div class="floating-label">
              <input id="password_confirmation" type="password" class="form-control glass-input"
                name="password_confirmation" required autocomplete="new-password" placeholder=" ">
              <label for="password_confirmation">Confirm Password</label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn auth-btn w-100 mt-4">
              <span class="btn-text">Register</span>
              <div class="btn-ripple"></div>
            </button>
          </form>

          <!-- Additional Links -->
          <div class="text-center mt-4">
            <div class="auth-divider">
              <span>Already have an account?</span>
            </div>
            <a href="{{ route('login') }}" class="btn btn-link text-decoration-none">
              <i class="bi bi-arrow-left me-1"></i> Back to Sign In
            </a>
          </div>

          <!-- Student Information -->
          <div class="text-center mt-3">
            <small class="text-muted">
              Students can enroll directly without creating an account
            </small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .staff-icon {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
  }

  .auth-card {
    backdrop-filter: blur(20px);
    background: rgba(255, 255, 255, 0.95);
    border: 1px solid rgba(255, 255, 255, 0.2);
  }

  .floating-label select {
    padding-top: 1.5rem;
  }

  .floating-label select+label {
    transform: translateY(-0.5rem) scale(0.85);
    color: #667eea;
  }

  .alert-info {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    border: 1px solid #2196f3;
    color: #0d47a1;
    border-radius: 12px;
    font-size: 0.9rem;
  }
</style>
@endsection