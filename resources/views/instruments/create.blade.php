@extends('layouts.app')

@section('title', 'Add New Instrument')

@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">Add New Instrument</h4>
          <a href="{{ route('instruments.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Instruments
          </a>
        </div>

        <div class="card-body">
          @if($errors->any())
          <div class="alert alert-danger">
            <strong>Please correct the following errors:</strong>
            <ul class="mb-0">
              @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
          @endif

          <form method="POST" action="{{ route('instruments.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="row">
              <div class="col-md-12 mb-3">
                <label for="name" class="form-label">Instrument Name *</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                  value="{{ old('name') }}" required>
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="category" class="form-label">Category *</label>
                <select class="form-select @error('category') is-invalid @enderror" id="category" name="category"
                  required>
                  <option value="">Choose category...</option>
                  <option value="string" {{ old('category') == 'string' ? 'selected' : '' }}>String Instruments</option>
                  <option value="wind" {{ old('category') == 'wind' ? 'selected' : '' }}>Wind Instruments</option>
                  <option value="brass" {{ old('category') == 'brass' ? 'selected' : '' }}>Brass</option>
                  <option value="percussion" {{ old('category') == 'percussion' ? 'selected' : '' }}>Percussion</option>
                  <option value="keyboard" {{ old('category') == 'keyboard' ? 'selected' : '' }}>Keyboard</option>
                </select>
                @error('category')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="col-md-6 mb-3">
                <label for="difficulty_level" class="form-label">Difficulty Level *</label>
                <select class="form-select @error('difficulty_level') is-invalid @enderror" id="difficulty_level"
                  name="difficulty_level" required>
                  <option value="">Choose difficulty...</option>
                  <option value="beginner" {{ old('difficulty_level') == 'beginner' ? 'selected' : '' }}>Beginner
                  </option>
                  <option value="intermediate" {{ old('difficulty_level') == 'intermediate' ? 'selected' : '' }}>
                    Intermediate</option>
                  <option value="advanced" {{ old('difficulty_level') == 'advanced' ? 'selected' : '' }}>Advanced
                  </option>
                </select>
                @error('difficulty_level')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="rental_fee" class="form-label">Monthly Rental Fee</label>
                <div class="input-group">
                  <span class="input-group-text">$</span>
                  <input type="number" class="form-control @error('rental_fee') is-invalid @enderror" id="rental_fee"
                    name="rental_fee" value="{{ old('rental_fee') }}" step="0.01" min="0" placeholder="0.00">
                </div>
                @error('rental_fee')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Leave empty if not for rent</div>
              </div>
            </div>

            <div class="mb-3">
              <label for="description" class="form-label">Description</label>
              <textarea class="form-control @error('description') is-invalid @enderror" id="description"
                name="description" rows="4"
                placeholder="Describe the instrument's features, specifications, or any additional notes...">{{ old('description') }}</textarea>
              @error('description')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="is_available" name="is_available" value="1"
                  {{ old('is_available', '1') ? 'checked' : '' }}>
                <label class="form-check-label" for="is_available">
                  Available for borrowing
                </label>
              </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
              <a href="{{ route('instruments.index') }}" class="btn btn-outline-secondary me-md-2">
                <i class="bi bi-x-circle"></i> Cancel
              </a>
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Create Instrument
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('styles')
<style>
  .form-label {
    font-weight: 500;
  }

  .card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
  }
</style>
@endsection