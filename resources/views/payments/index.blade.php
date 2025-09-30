@extends('layouts.app')

@section('title', 'Payment Collection')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-credit-card"></i> Payment Collection</h1>
      </div>

      <!-- Payment Statistics -->
      <div class="row mb-4">
        <div class="col-md-3">
          <div class="card bg-warning text-white">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <div>
                  <h6 class="card-title">Pending Payments</h6>
                  <h3 class="mb-0">{{ $stats['pending_payments'] }}</h3>
                </div>
                <div class="align-self-center">
                  <i class="bi bi-clock fs-1"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card bg-info text-white">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <div>
                  <h6 class="card-title">Partial Payments</h6>
                  <h3 class="mb-0">{{ $stats['partial_payments'] }}</h3>
                </div>
                <div class="align-self-center">
                  <i class="bi bi-pie-chart fs-1"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card bg-danger text-white">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <div>
                  <h6 class="card-title">Overdue</h6>
                  <h3 class="mb-0">{{ $stats['overdue_payments'] }}</h3>
                </div>
                <div class="align-self-center">
                  <i class="bi bi-exclamation-triangle fs-1"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card bg-success text-white">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <div>
                  <h6 class="card-title">Outstanding</h6>
                  <h3 class="mb-0">${{ number_format($stats['total_outstanding'], 2) }}</h3>
                </div>
                <div class="align-self-center">
                  <i class="bi bi-currency-dollar fs-1"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Payments Table -->
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Outstanding Payments</h5>
        </div>
        <div class="card-body">
          @if($enrollments->count() > 0)
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Student</th>
                  <th>Course</th>
                  <th>Teacher</th>
                  <th>Total Fee</th>
                  <th>Amount Paid</th>
                  <th>Balance</th>
                  <th>Due Date</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($enrollments as $enrollment)
                <tr class="{{ $enrollment->isPaymentOverdue() ? 'table-danger' : '' }}">
                  <td>
                    <div class="d-flex align-items-center">
                      <div>
                        <strong>{{ $enrollment->user->name }}</strong><br>
                        <small class="text-muted">{{ $enrollment->user->email }}</small>
                      </div>
                    </div>
                  </td>
                  <td>{{ $enrollment->course->title }}</td>
                  <td>{{ $enrollment->teacher->name ?? 'Not assigned' }}</td>
                  <td>${{ number_format($enrollment->total_fee, 2) }}</td>
                  <td>${{ number_format($enrollment->amount_paid, 2) }}</td>
                  <td>
                    <strong class="text-danger">
                      ${{ number_format($enrollment->getRemainingBalance(), 2) }}
                    </strong>
                  </td>
                  <td>
                    @if($enrollment->payment_due_date)
                    {{ $enrollment->payment_due_date->format('M d, Y') }}
                    @if($enrollment->isPaymentOverdue())
                    <br><small class="text-danger">OVERDUE</small>
                    @endif
                    @else
                    <span class="text-muted">Not set</span>
                    @endif
                  </td>
                  <td>
                    <span class="badge bg-{{ $enrollment->payment_status === 'pending' ? 'warning' : 'info' }}">
                      {{ ucfirst($enrollment->payment_status) }}
                    </span>
                  </td>
                  <td>
                    <a href="{{ route('employee.payments.show', $enrollment) }}" class="btn btn-sm btn-primary">
                      <i class="bi bi-credit-card"></i> Collect
                    </a>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="d-flex justify-content-center">
            {{ $enrollments->links() }}
          </div>
          @else
          <div class="text-center py-4">
            <i class="bi bi-check-circle-fill text-success fs-1"></i>
            <h4 class="mt-3">All payments are up to date!</h4>
            <p class="text-muted">There are no outstanding payments to collect.</p>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection