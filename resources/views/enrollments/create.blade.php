@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <h4><i class="bi bi-plus-circle"></i> Enroll in Instruments</h4>
      </div>
      <div class="card-body">
        @if($availableInstruments->count() > 0)
        <form method="POST" action="{{ route('enrollments.store') }}">
          @csrf

          <div class="mb-3">
            <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
            <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date"
              name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
            @error('start_date')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-4">
            <label class="form-label">Select Instruments <span class="text-danger">*</span></label>
            <div class="text-muted mb-3">Choose one or more instruments you'd like to learn:</div>

            @php
            $groupedInstruments = $availableInstruments->groupBy('category');
            @endphp

            @foreach($groupedInstruments as $category => $instruments)
            <div class="card mb-3">
              <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-music-note"></i> {{ ucfirst($category) }} Instruments</h6>
              </div>
              <div class="card-body">
                <div class="row">
                  @foreach($instruments as $instrument)
                  <div class="col-md-6 mb-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" value="{{ $instrument->id }}"
                        id="instrument_{{ $instrument->id }}" name="instrument_ids[]"
                        {{ in_array($instrument->id, old('instrument_ids', [])) ? 'checked' : '' }}>
                      <label class="form-check-label" for="instrument_{{ $instrument->id }}">
                        <div>
                          <strong>{{ $instrument->name }}</strong>
                          <span class="badge bg-secondary ms-1">{{ ucfirst($instrument->difficulty_level) }}</span>
                        </div>
                        @if($instrument->description)
                        <small class="text-muted d-block">
                          {{ Str::limit($instrument->description, 80) }}
                        </small>
                        @endif
                        @if($instrument->rental_fee)
                        <small class="text-success d-block">
                          <strong>Rental: ${{ number_format($instrument->rental_fee, 2) }}/month</strong>
                        </small>
                        @endif
                      </label>
                    </div>
                  </div>
                  @endforeach
                </div>
              </div>
            </div>
            @endforeach

            @error('instrument_ids')
            <div class="text-danger">{{ $message }}</div>
            @enderror
          </div>

          <div class="alert alert-info">
            <i class="bi bi-info-circle"></i>
            <strong>Please note:</strong> Your enrollment will be pending approval.
            You'll receive confirmation once your enrollment has been reviewed by our staff.
          </div>

          <div class="d-flex justify-content-between">
            <a href="{{ route('instruments.index') }}" class="btn btn-secondary">
              <i class="bi bi-arrow-left"></i> Back to Instruments
            </a>
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-check-circle"></i> Submit Enrollment
            </button>
          </div>
        </form>
        @else
        <div class="text-center py-4">
          <i class="bi bi-exclamation-triangle display-1 text-warning"></i>
          <h4 class="mt-3">No Available Instruments</h4>
          <p class="text-muted">
            Either all instruments are currently unavailable, or you're already enrolled in all available instruments.
          </p>
          <div class="mt-3">
            <a href="{{ route('instruments.index') }}" class="btn btn-primary">
              <i class="bi bi-arrow-left"></i> View All Instruments
            </a>
            <a href="{{ route('enrollments.index') }}" class="btn btn-outline-secondary">
              <i class="bi bi-list"></i> My Enrollments
            </a>
          </div>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Add some interactivity to show total estimated cost
  document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = document.querySelectorAll('input[name="instrument_ids[]"]');
    const form = document.querySelector('form');

    if (form) {
      // Create a div to show estimated total
      const totalDiv = document.createElement('div');
      totalDiv.className = 'alert alert-light mt-3';
      totalDiv.innerHTML = '<strong>Estimated Monthly Cost: $<span id="total-cost">0.00</span></strong>';

      const submitButton = form.querySelector('button[type="submit"]');
      submitButton.parentNode.insertBefore(totalDiv, submitButton.parentNode.firstChild);

      function updateTotal() {
        let total = 0;
        checkboxes.forEach(checkbox => {
          if (checkbox.checked) {
            const label = checkbox.parentNode.querySelector('label');
            const feeText = label.querySelector('.text-success');
            if (feeText) {
              const fee = parseFloat(feeText.textContent.match(/\$(\d+\.?\d*)/)?.[1] || 0);
              total += fee;
            }
          }
        });
        document.getElementById('total-cost').textContent = total.toFixed(2);
      }

      checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateTotal);
      });

      updateTotal(); // Initial calculation
    }
  });
</script>
@endpush