@extends('layouts.app')

@section('title', $instrument->name)

@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">{{ $instrument->name }}</h4>
          <div>
            <a href="{{ route('instruments.index') }}" class="btn btn-outline-secondary">
              <i class="bi bi-arrow-left"></i> Back to Instruments
            </a>
            @auth
            @if(auth()->user()->isAdmin())
            <a href="{{ route('instruments.edit', $instrument) }}" class="btn btn-warning">
              <i class="bi bi-pencil"></i> Edit
            </a>
            @endif
            @endauth
          </div>
        </div>

        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <h5>Instrument Details</h5>
              <table class="table table-borderless">
                <tr>
                  <th width="30%">Name:</th>
                  <td>{{ $instrument->name }}</td>
                </tr>
                <tr>
                  <th>Category:</th>
                  <td>{{ ucfirst($instrument->category) }} Instruments</td>
                </tr>
                <tr>
                  <th>Difficulty:</th>
                  <td>
                    <span
                      class="badge bg-{{ $instrument->difficulty_level == 'beginner' ? 'success' : ($instrument->difficulty_level == 'intermediate' ? 'warning' : 'danger') }}">
                      {{ ucfirst($instrument->difficulty_level) }}
                    </span>
                  </td>
                </tr>
                <tr>
                  <th>Availability:</th>
                  <td>
                    <span class="badge bg-{{ $instrument->is_available ? 'success' : 'danger' }}">
                      {{ $instrument->is_available ? 'Available' : 'Unavailable' }}
                    </span>
                  </td>
                </tr>
                @if($instrument->rental_fee)
                <tr>
                  <th>Rental Fee:</th>
                  <td class="text-success">
                    <strong>${{ number_format($instrument->rental_fee, 2) }}/month</strong>
                  </td>
                </tr>
                @endif
                <tr>
                  <th>Current Borrows:</th>
                  <td>{{ $borrowCount }}</td>
                </tr>
              </table>
            </div>

            <div class="col-md-6">
              @if($instrument->description)
              <h5>Description</h5>
              <p>{{ $instrument->description }}</p>
              @endif

              <div class="mt-4">
                @auth
                @if($instrument->is_available && !auth()->user()->isBorrowingInstrument($instrument->id))
                <a href="{{ route('instrument-borrows.create', ['instrument' => $instrument->id]) }}"
                  class="btn btn-success">
                  <i class="bi bi-plus-circle"></i> Request to Borrow
                </a>
                @elseif(auth()->user()->isBorrowingInstrument($instrument->id))
                <div class="alert alert-info">
                  <i class="bi bi-info-circle"></i>
                  You have already requested or are currently borrowing this instrument.
                </div>
                @else
                <div class="alert alert-warning">
                  <i class="bi bi-exclamation-triangle"></i>
                  This instrument is currently unavailable for borrowing.
                </div>
                @endif
                @else
                <div class="alert alert-primary">
                  <i class="bi bi-info-circle"></i>
                  <a href="{{ route('login') }}">Login</a> to request borrowing this instrument.
                </div>
                @endauth
              </div>
            </div>
          </div>
        </div>
      </div>

      @if($borrowCount > 0)
      <div class="card mt-4">
        <div class="card-header">
          <h5 class="mb-0">Current Borrowing Activity</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4">
              <div class="text-center">
                <h6 class="text-muted">Active Requests</h6>
                <h4 class="text-primary">{{ $borrowCount }}</h4>
              </div>
            </div>
            <div class="col-md-8">
              <p class="text-muted mb-0">
                This instrument currently has {{ $borrowCount }} active borrow
                {{ $borrowCount == 1 ? 'request' : 'requests' }}
                (including pending requests and active borrowings).
              </p>
            </div>
          </div>
        </div>
      </div>
      @endif
    </div>
  </div>
</div>
@endsection

@section('styles')
<style>
  .card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
  }

  .table th {
    font-weight: 600;
    color: #6c757d;
  }

  .badge {
    font-size: 0.875rem;
  }
</style>
@endsection