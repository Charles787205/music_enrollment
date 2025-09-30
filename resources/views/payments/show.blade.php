@extends('layouts.app')

@section('title', 'Collect Payment')

@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Collect Payment</h4>
          <a href="{{ route('employee.payments') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Payments
          </a>
        </div>

        <div class="card-body">
          <!-- Student and Course Information -->
          <div class="row mb-4">
            <div class="col-md-6">
              <h6 class="text-primary">Student Information</h6>
              <p class="mb-1"><strong>Name:</strong> {{ $enrollment->user->name }}</p>
              <p class="mb-1"><strong>Email:</strong> {{ $enrollment->user->email }}</p>
              <p class="mb-1"><strong>Phone:</strong> {{ $enrollment->user->phone }}</p>
            </div>
            <div class="col-md-6">
              <h6 class="text-primary">Course Information</h6>
              <p class="mb-1"><strong>Course:</strong> {{ $enrollment->course->title }}</p>
              <p class="mb-1"><strong>Teacher:</strong> {{ $enrollment->teacher->name ?? 'Not assigned' }}</p>
              <p class="mb-1"><strong>Status:</strong>
                <span class="badge bg-{{ $enrollment->status_badge_class }}">
                  {{ ucfirst($enrollment->status) }}
                </span>
              </p>
            </div>
          </div>

          <!-- Payment Summary -->
          <div class="card bg-light mb-4">
            <div class="card-body">
              <h6 class="card-title">Payment Summary</h6>
              <div class="row">
                <div class="col-md-3">
                  <p class="mb-1"><strong>Total Fee:</strong></p>
                  <h5 class="text-info">${{ number_format($enrollment->total_fee, 2) }}</h5>
                </div>
                <div class="col-md-3">
                  <p class="mb-1"><strong>Amount Paid:</strong></p>
                  <h5 class="text-success">${{ number_format($enrollment->amount_paid, 2) }}</h5>
                </div>
                <div class="col-md-3">
                  <p class="mb-1"><strong>Remaining Balance:</strong></p>
                  <h5 class="text-danger">${{ number_format($enrollment->getRemainingBalance(), 2) }}</h5>
                </div>
                <div class="col-md-3">
                  <p class="mb-1"><strong>Payment Status:</strong></p>
                  <span
                    class="badge bg-{{ $enrollment->payment_status === 'pending' ? 'warning' : ($enrollment->payment_status === 'partial' ? 'info' : 'success') }} fs-6">
                    {{ ucfirst($enrollment->payment_status) }}
                  </span>
                </div>
              </div>

              @if($enrollment->payment_due_date)
              <div class="mt-3">
                <p class="mb-1"><strong>Due Date:</strong> {{ $enrollment->payment_due_date->format('M d, Y') }}</p>
                @if($enrollment->isPaymentOverdue())
                <div class="alert alert-danger py-2">
                  <i class="bi bi-exclamation-triangle"></i> This payment is overdue!
                </div>
                @endif
              </div>
              @endif

              <!-- Payment Progress Bar -->
              <div class="mt-3">
                <label class="form-label">Payment Progress</label>
                <div class="progress">
                  <div class="progress-bar" role="progressbar" style="width: {{ $enrollment->getPaymentPercentage() }}%"
                    aria-valuenow="{{ $enrollment->getPaymentPercentage() }}" aria-valuemin="0" aria-valuemax="100">
                    {{ round($enrollment->getPaymentPercentage()) }}%
                  </div>
                </div>
              </div>
            </div>
          </div>

          @if($enrollment->getRemainingBalance() > 0)
          <!-- Payment Collection Form -->
          <form method="POST" action="{{ route('employee.payments.collect', $enrollment) }}">
            @csrf

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="amount" class="form-label">Payment Amount *</label>
                <div class="input-group">
                  <span class="input-group-text">$</span>
                  <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount"
                    name="amount" value="{{ old('amount', $enrollment->getRemainingBalance()) }}" step="0.01" min="0.01"
                    max="{{ $enrollment->getRemainingBalance() }}" required>
                </div>
                @error('amount')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">
                  Maximum: ${{ number_format($enrollment->getRemainingBalance(), 2) }}
                </div>
              </div>

              <div class="col-md-6 mb-3">
                <label for="payment_method" class="form-label">Payment Method *</label>
                <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method"
                  name="payment_method" required>
                  <option value="">Choose payment method...</option>
                  <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                  <option value="card" {{ old('payment_method') === 'card' ? 'selected' : '' }}>Credit/Debit Card
                  </option>
                  <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Bank
                    Transfer</option>
                  <option value="check" {{ old('payment_method') === 'check' ? 'selected' : '' }}>Check</option>
                </select>
                @error('payment_method')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <div class="mb-3">
              <label for="notes" class="form-label">Payment Notes</label>
              <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3"
                placeholder="Any additional notes about this payment...">{{ old('notes') }}</textarea>
              @error('notes')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
              <a href="{{ route('employee.payments') }}" class="btn btn-outline-secondary me-md-2">
                <i class="bi bi-x-circle"></i> Cancel
              </a>
              <button type="submit" class="btn btn-success">
                <i class="bi bi-credit-card"></i> Collect Payment
              </button>
            </div>
          </form>
          @else
          <div class="alert alert-success">
            <i class="bi bi-check-circle"></i>
            <strong>Payment Complete!</strong> This enrollment is fully paid.
          </div>
          @endif

          <!-- Payment History -->
          @if($enrollment->payment_collected_at)
          <div class="mt-4">
            <h6>Last Payment Details</h6>
            <div class="card border-success">
              <div class="card-body">
                <p class="mb-1"><strong>Collected by:</strong> {{ $enrollment->collector->name ?? 'Unknown' }}</p>
                <p class="mb-1"><strong>Collection Date:</strong>
                  {{ $enrollment->payment_collected_at->format('M d, Y H:i') }}</p>
                @if($enrollment->payment_notes)
                <p class="mb-0"><strong>Notes:</strong> {{ $enrollment->payment_notes }}</p>
                @endif
              </div>
            </div>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection