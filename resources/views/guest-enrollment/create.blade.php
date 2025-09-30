@extends('layouts.app')

@section('title', 'Enroll in a Course')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header bg-primary text-white">
          <h4 class="mb-0">
            <i class="fas fa-music me-2"></i>
            Enroll in a Music Course
          </h4>
          <p class="mb-0 mt-2 text-light">
            Start your musical journey with us! Fill out the form below to enroll.
          </p>
        </div>
        <div class="card-body">
          @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
          @endif

          <form method="POST" action="{{ route('guest.enroll.store') }}">
            @csrf

            <!-- Course Selection -->
            <div class="row mb-3">
              <div class="col-md-12">
                <label for="course_id" class="form-label">
                  <i class="fas fa-graduation-cap me-1"></i>
                  Select Course <span class="text-danger">*</span>
                </label>
                <select class="form-control @error('course_id') is-invalid @enderror" id="course_id" name="course_id"
                  required>
                  <option value="">Choose a course...</option>
                  @foreach($courses as $course)
                  <option value="{{ $course->id }}" data-fee="{{ $course->price }}"
                    {{ old('course_id', $selectedCourse?->id) == $course->id ? 'selected' : '' }}>
                    {{ $course->title }}
                    ({{ $course->instructor ?? 'TBA' }})
                    - ${{ number_format($course->price, 2) }}
                  </option>
                  @endforeach
                </select>
                @error('course_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">
                  Choose the course and instrument you'd like to learn.
                </div>
              </div>
            </div>

            <!-- Course Fee Display -->
            <div id="course-fee-display" class="alert alert-info" style="display: none;">
              <i class="fas fa-dollar-sign me-1"></i>
              <strong>Course Fee: $<span id="fee-amount">0.00</span></strong>
              <small class="d-block mt-1">Payment can be arranged after enrollment confirmation.</small>
            </div>

            <!-- Personal Information -->
            <div class="card mb-4">
              <div class="card-header bg-light">
                <h5 class="mb-0">
                  <i class="fas fa-user me-1"></i>
                  Personal Information
                </h5>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="first_name" class="form-label">
                      First Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name"
                      name="first_name" value="{{ old('first_name') }}" required>
                    @error('first_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="last_name" class="form-label">
                      Last Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name"
                      name="last_name" value="{{ old('last_name') }}" required>
                    @error('last_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">
                      Email Address <span class="text-danger">*</span>
                    </label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                      name="email" value="{{ old('email') }}" required>
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                      We'll use this to send enrollment confirmation and account details.
                    </div>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">
                      Phone Number <span class="text-danger">*</span>
                    </label>
                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone"
                      value="{{ old('phone') }}" required>
                    @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <div class="mb-3">
                  <label for="date_of_birth" class="form-label">
                    Date of Birth <span class="text-danger">*</span>
                  </label>
                  <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror"
                    id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
                  @error('date_of_birth')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label for="address" class="form-label">
                    Address
                  </label>
                  <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address"
                    rows="2">{{ old('address') }}</textarea>
                  @error('address')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <!-- Emergency Contact -->
            <div class="card mb-4">
              <div class="card-header bg-light">
                <h5 class="mb-0">
                  <i class="fas fa-phone-alt me-1"></i>
                  Emergency Contact
                </h5>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="emergency_contact_name" class="form-label">
                      Contact Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control @error('emergency_contact_name') is-invalid @enderror"
                      id="emergency_contact_name" name="emergency_contact_name"
                      value="{{ old('emergency_contact_name') }}" required>
                    @error('emergency_contact_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="emergency_contact_phone" class="form-label">
                      Contact Phone <span class="text-danger">*</span>
                    </label>
                    <input type="tel" class="form-control @error('emergency_contact_phone') is-invalid @enderror"
                      id="emergency_contact_phone" name="emergency_contact_phone"
                      value="{{ old('emergency_contact_phone') }}" required>
                    @error('emergency_contact_phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <div class="mb-3">
                  <label for="emergency_contact_relationship" class="form-label">
                    Relationship to Student <span class="text-danger">*</span>
                  </label>
                  <select class="form-control @error('emergency_contact_relationship') is-invalid @enderror"
                    id="emergency_contact_relationship" name="emergency_contact_relationship" required>
                    <option value="">Select relationship...</option>
                    <option value="parent" {{ old('emergency_contact_relationship') == 'parent' ? 'selected' : '' }}>
                      Parent</option>
                    <option value="guardian"
                      {{ old('emergency_contact_relationship') == 'guardian' ? 'selected' : '' }}>Guardian</option>
                    <option value="spouse" {{ old('emergency_contact_relationship') == 'spouse' ? 'selected' : '' }}>
                      Spouse</option>
                    <option value="sibling" {{ old('emergency_contact_relationship') == 'sibling' ? 'selected' : '' }}>
                      Sibling</option>
                    <option value="friend" {{ old('emergency_contact_relationship') == 'friend' ? 'selected' : '' }}>
                      Friend</option>
                    <option value="other" {{ old('emergency_contact_relationship') == 'other' ? 'selected' : '' }}>Other
                    </option>
                  </select>
                  @error('emergency_contact_relationship')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <!-- Previous Experience -->
            <div class="card mb-4">
              <div class="card-header bg-light">
                <h5 class="mb-0">
                  <i class="fas fa-history me-1"></i>
                  Musical Experience (Optional)
                </h5>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <label for="previous_experience" class="form-label">
                    Previous Musical Experience
                  </label>
                  <textarea class="form-control @error('previous_experience') is-invalid @enderror"
                    id="previous_experience" name="previous_experience" rows="3"
                    placeholder="Tell us about any previous musical training or experience...">{{ old('previous_experience') }}</textarea>
                  @error('previous_experience')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <label for="goals" class="form-label">
                    Musical Goals
                  </label>
                  <textarea class="form-control @error('goals') is-invalid @enderror" id="goals" name="goals" rows="2"
                    placeholder="What would you like to achieve through music lessons?">{{ old('goals') }}</textarea>
                  @error('goals')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <!-- Terms and Conditions -->
            <div class="card mb-4">
              <div class="card-body">
                <div class="form-check">
                  <input class="form-check-input @error('terms_accepted') is-invalid @enderror" type="checkbox"
                    value="1" id="terms_accepted" name="terms_accepted" {{ old('terms_accepted') ? 'checked' : '' }}
                    required>
                  <label class="form-check-label" for="terms_accepted">
                    I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms and
                      Conditions</a>
                    and understand that enrollment is subject to course availability and confirmation.
                    <span class="text-danger">*</span>
                  </label>
                  @error('terms_accepted')
                  <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-paper-plane me-2"></i>
                Submit Enrollment Application
              </button>
              <a href="{{ route('instruments.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Back to Courses
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Terms and Conditions Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <h6>Enrollment Terms:</h6>
        <ul>
          <li>Enrollment is subject to course availability and instructor confirmation</li>
          <li>Course fees must be paid before lessons begin</li>
          <li>Students must provide accurate contact and emergency information</li>
          <li>Regular attendance is expected for optimal learning progress</li>
          <li>48-hour notice is required for lesson cancellations</li>
        </ul>

        <h6>Payment Terms:</h6>
        <ul>
          <li>Course fees are due before the start of lessons</li>
          <li>Payment plans may be available upon request</li>
          <li>Refunds are subject to our refund policy</li>
          <li>Late payment fees may apply</li>
        </ul>

        <h6>Privacy:</h6>
        <ul>
          <li>Personal information will be kept confidential</li>
          <li>Information is used only for enrollment and educational purposes</li>
          <li>Emergency contacts will only be used in case of emergencies</li>
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const courseSelect = document.getElementById('course_id');
    const feeDisplay = document.getElementById('course-fee-display');
    const feeAmount = document.getElementById('fee-amount');

    courseSelect.addEventListener('change', function () {
      const selectedOption = this.options[this.selectedIndex];
      const fee = selectedOption.getAttribute('data-fee');

      if (fee) {
        feeAmount.textContent = parseFloat(fee).toFixed(2);
        feeDisplay.style.display = 'block';
      } else {
        feeDisplay.style.display = 'none';
      }
    });

    // Trigger change event if a course is pre-selected
    if (courseSelect.value) {
      courseSelect.dispatchEvent(new Event('change'));
    }
  });
</script>
@endsection