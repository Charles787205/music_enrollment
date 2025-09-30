@extends('layouts.app')

@section('title', 'Enrollment Successful')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header bg-success text-white text-center">
          <i class="fas fa-check-circle fa-3x mb-3"></i>
          <h3 class="mb-0">Enrollment Successful!</h3>
          <p class="mb-0 mt-2">Welcome to our music school family!</p>
        </div>
        <div class="card-body">
          @if(session('enrollment'))
          @php $enrollment = session('enrollment'); @endphp

          <!-- New User Account Information -->
          @if(session('isNewUser') && session('tempPassword'))
          <div class="alert alert-info">
            <h5><i class="fas fa-key me-2"></i>Your Account Information</h5>
            <hr>
            <p class="mb-2">
              <strong>Great news!</strong> We've created a temporary account for you.
              You can use these credentials to log in and track your enrollment:
            </p>
            <div class="row">
              <div class="col-md-6">
                <p class="mb-1"><strong>Email:</strong> {{ session('user')->email }}</p>
                <p class="mb-0"><strong>Temporary Password:</strong>
                  <code class="bg-light text-dark px-2 py-1 rounded">{{ session('tempPassword') }}</code>
                </p>
              </div>
              <div class="col-md-6">
                <div class="text-end">
                  <a href="{{ route('login') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-sign-in-alt me-1"></i>
                    Log In Now
                  </a>
                </div>
              </div>
            </div>
            <div class="alert alert-warning mt-3 mb-0">
              <i class="fas fa-shield-alt me-2"></i>
              <small>
                <strong>Security Note:</strong> Please log in and change your password as soon as possible.
                You can do this from your account settings after logging in.
              </small>
            </div>
          </div>
          @endif

          <!-- Enrollment Details -->
          <div class="alert alert-success">
            <h5><i class="fas fa-info-circle me-2"></i>Enrollment Details</h5>
            <hr>
            <div class="row">
              <div class="col-md-6">
                <strong>Student:</strong> {{ $enrollment->user->name }}<br>
                <strong>Email:</strong> {{ $enrollment->user->email }}<br>
                <strong>Phone:</strong> {{ $enrollment->user->phone }}
              </div>
              <div class="col-md-6">
                <strong>Course:</strong> {{ $enrollment->course->title }}<br>
                <strong>Instructor:</strong> {{ $enrollment->course->instructor ?? 'TBA' }}<br>
                <strong>Fee:</strong> ${{ number_format($enrollment->course->price, 2) }}
              </div>
            </div>
            @if($enrollment->teacher)
            <div class="mt-3 p-3 bg-light rounded">
              <h6><i class="fas fa-user-tie me-2"></i>Your Assigned Teacher</h6>
              <strong>{{ $enrollment->teacher->name }}</strong><br>
              <small class="text-muted">
                You'll receive contact details and lesson scheduling information soon.
              </small>
            </div>
            @endif
          </div>

          <!-- Payment Information -->
          <div class="card mb-4">
            <div class="card-header bg-info text-white">
              <h5 class="mb-0">
                <i class="fas fa-credit-card me-2"></i>
                Payment Information
              </h5>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-8">
                  <p class="mb-2">
                    <strong>Total Course Fee:</strong>
                    <span class="h5 text-primary">${{ number_format($enrollment->total_fee, 2) }}</span>
                  </p>
                  <p class="mb-2">
                    <strong>Payment Status:</strong>
                    <span class="badge bg-warning">{{ ucfirst($enrollment->payment_status) }}</span>
                  </p>
                  @if($enrollment->payment_due_date)
                  <p class="mb-0">
                    <strong>Payment Due:</strong>
                    <span class="text-danger">{{ $enrollment->payment_due_date->format('F j, Y') }}</span>
                  </p>
                  @endif
                </div>
                <div class="col-md-4 text-end">
                  <i class="fas fa-money-bill-wave fa-3x text-success"></i>
                </div>
              </div>

              <div class="alert alert-warning mt-3">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Payment Required:</strong>
                Our staff will contact you soon to arrange payment collection.
                Please have payment ready by the due date to secure your spot.
              </div>
            </div>
          </div>
          @endif

          <!-- Next Steps -->
          <div class="card mb-4">
            <div class="card-header bg-primary text-white">
              <h5 class="mb-0">
                <i class="fas fa-list-ol me-2"></i>
                What Happens Next?
              </h5>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="d-flex mb-3">
                    <div class="flex-shrink-0">
                      <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 30px; height: 30px;">
                        <small>1</small>
                      </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <h6 class="mb-1">Email Confirmation</h6>
                      <small class="text-muted">You'll receive a confirmation email with enrollment details.</small>
                    </div>
                  </div>

                  <div class="d-flex mb-3">
                    <div class="flex-shrink-0">
                      <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 30px; height: 30px;">
                        <small>2</small>
                      </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <h6 class="mb-1">Payment Collection</h6>
                      <small class="text-muted">Our staff will contact you to arrange payment collection.</small>
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="d-flex mb-3">
                    <div class="flex-shrink-0">
                      <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 30px; height: 30px;">
                        <small>3</small>
                      </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <h6 class="mb-1">Teacher Assignment</h6>
                      <small class="text-muted">You'll be matched with the perfect teacher for your needs.</small>
                    </div>
                  </div>

                  <div class="d-flex mb-3">
                    <div class="flex-shrink-0">
                      <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 30px; height: 30px;">
                        <small>4</small>
                      </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <h6 class="mb-1">Start Learning!</h6>
                      <small class="text-muted">Begin your musical journey with scheduled lessons.</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Account Management -->
          <div class="card mb-4">
            <div class="card-header bg-light">
              <h5 class="mb-0">
                <i class="fas fa-user-cog me-2"></i>
                @if(session('isNewUser'))
                Manage Your New Account
                @else
                Account Access
                @endif
              </h5>
            </div>
            <div class="card-body">
              @if(session('isNewUser'))
              <p>
                Your account has been created! Use the temporary password above to log in and:
              </p>
              <ul>
                <li>Change your password to something memorable</li>
                <li>Complete your profile information</li>
                <li>Track your enrollment progress</li>
                <li>Communicate with your teacher</li>
              </ul>
              <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                <a href="{{ route('login') }}" class="btn btn-primary">
                  <i class="fas fa-sign-in-alt me-2"></i>
                  Log In to Your Account
                </a>
              </div>
              @else
              <p>
                You can log in to your existing account to track your new enrollment and manage your courses.
              </p>
              <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                <a href="{{ route('login') }}" class="btn btn-primary">
                  <i class="fas fa-sign-in-alt me-2"></i>
                  Log In to Your Account
                </a>
              </div>
              @endif
            </div>
          </div>

          <!-- Contact Information -->
          <div class="card">
            <div class="card-header bg-light">
              <h5 class="mb-0">
                <i class="fas fa-phone me-2"></i>
                Need Help?
              </h5>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <h6>Contact Us</h6>
                  <p class="mb-1">
                    <i class="fas fa-phone me-2"></i>
                    Phone: (555) 123-4567
                  </p>
                  <p class="mb-1">
                    <i class="fas fa-envelope me-2"></i>
                    Email: info@musicschool.com
                  </p>
                  <p class="mb-0">
                    <i class="fas fa-clock me-2"></i>
                    Hours: Mon-Fri 9AM-6PM, Sat 9AM-2PM
                  </p>
                </div>
                <div class="col-md-6">
                  <h6>Visit Us</h6>
                  <p class="mb-0">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    123 Music Lane<br>
                    Harmony City, HC 12345
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
            <a href="{{ route('instruments.index') }}" class="btn btn-outline-primary">
              <i class="fas fa-eye me-2"></i>
              Browse More Courses
            </a>
            <a href="{{ route('guest.enroll') }}" class="btn btn-outline-secondary">
              <i class="fas fa-plus me-2"></i>
              Enroll in Another Course
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .card-header.bg-success {
    border: none;
  }

  .step-number {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background-color: #007bff;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-right: 15px;
  }

  .alert-success {
    border-left: 4px solid #28a745;
  }

  .alert-warning {
    border-left: 4px solid #ffc107;
  }

  .fa-check-circle {
    animation: bounceIn 1s ease-in-out;
  }

  @keyframes bounceIn {
    0% {
      transform: scale(0.3);
      opacity: 0;
    }

    50% {
      transform: scale(1.05);
    }

    70% {
      transform: scale(0.9);
    }

    100% {
      transform: scale(1);
      opacity: 1;
    }
  }
</style>
@endsection