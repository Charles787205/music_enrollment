@extends('layouts.app')

@section('title', 'Change Password')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header {{ session('password_changed') ? 'bg-success text-white' : 'bg-warning text-dark' }}">
          <h4 class="mb-0">
            <i class="fas fa-{{ session('password_changed') ? 'check' : 'key' }} me-2"></i>
            {{ session('password_changed') ? 'Password Changed Successfully' : 'Change Password Required' }}
          </h4>
        </div>
        <div class="card-body">
          @if(auth()->user()->password_change_required)
          <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Security Notice:</strong>
            You must change your temporary password before continuing to use your account.
          </div>
          @endif

          @if (session('warning'))
          <div class="alert alert-warning">
            {{ session('warning') }}
          </div>
          @endif

          @if (session('success'))
          <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
          </div>

          @if(session('password_changed'))
          <div class="d-grid gap-2 mb-4">
            @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-lg">
              <i class="fas fa-arrow-right me-2"></i>
              Continue to Admin Dashboard
            </a>
            @elseif(auth()->user()->isEmployee())
            <a href="{{ route('employee.dashboard') }}" class="btn btn-primary btn-lg">
              <i class="fas fa-arrow-right me-2"></i>
              Continue to Employee Dashboard
            </a>
            @else
            <a href="{{ route('courses.index') }}" class="btn btn-primary btn-lg">
              <i class="fas fa-arrow-right me-2"></i>
              Continue to Courses
            </a>
            @endif
          </div>
          @endif
          @endif

          @if ($errors->any())
          <div class="alert alert-danger">
            @if($errors->has('general'))
            <div class="mb-2">{{ $errors->first('general') }}</div>
            @endif
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
          @endif

          @if(!session('password_changed'))
          <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <div class="mb-3">
              <label for="current_password" class="form-label">
                <i class="fas fa-lock me-1"></i>
                Current Password <span class="text-danger">*</span>
              </label>
              <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                id="current_password" name="current_password" required autocomplete="current-password">
              @error('current_password')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div class="form-text">
                Enter your current/temporary password
              </div>
            </div>

            <div class="mb-3">
              <label for="password" class="form-label">
                <i class="fas fa-key me-1"></i>
                New Password <span class="text-danger">*</span>
              </label>
              <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                name="password" required autocomplete="new-password">
              @error('password')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div class="form-text">
                Password must be at least 8 characters long
              </div>
            </div>

            <div class="mb-3">
              <label for="password_confirmation" class="form-label">
                <i class="fas fa-key me-1"></i>
                Confirm New Password <span class="text-danger">*</span>
              </label>
              <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                required autocomplete="new-password">
              <div class="form-text">
                Re-enter your new password to confirm
              </div>
            </div>

            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>
                Change Password
              </button>

              @if(!auth()->user()->password_change_required)
              <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Cancel
              </a>
              @endif
            </div>
          </form>
          @endif

          @if(auth()->user()->password_change_required)
          <div class="mt-4 p-3 bg-light rounded">
            <h6><i class="fas fa-info-circle me-2"></i>Password Requirements:</h6>
            <ul class="mb-0 small">
              <li>At least 8 characters long</li>
              <li>Mix of uppercase and lowercase letters (recommended)</li>
              <li>Include numbers and special characters (recommended)</li>
              <li>Avoid using personal information</li>
            </ul>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');

    function checkPasswordMatch() {
      if (confirmInput.value && passwordInput.value !== confirmInput.value) {
        confirmInput.setCustomValidity('Passwords do not match');
      } else {
        confirmInput.setCustomValidity('');
      }
    }

    passwordInput.addEventListener('input', checkPasswordMatch);
    confirmInput.addEventListener('input', checkPasswordMatch);
  });
</script>
@endsection