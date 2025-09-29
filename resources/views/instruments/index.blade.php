@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1><i class="bi bi-music-note-list"></i> Available Instruments</h1>
      @auth
      @if(auth()->user()->isAdmin())
      <a href="{{ route('instruments.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add Instrument
      </a>
      @endif
      @endauth
    </div>

    <!-- Filters -->
    <div class="card mb-4">
      <div class="card-body">
        <form method="GET" action="{{ route('instruments.index') }}" class="row g-3">
          <div class="col-md-3">
            <label for="search" class="form-label">Search</label>
            <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}"
              placeholder="Search instruments...">
          </div>
          <div class="col-md-3">
            <label for="category" class="form-label">Category</label>
            <select class="form-select" id="category" name="category">
              <option value="">All Categories</option>
              @foreach($categories as $category)
              <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                {{ ucfirst($category) }}
              </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label for="difficulty" class="form-label">Difficulty</label>
            <select class="form-select" id="difficulty" name="difficulty">
              <option value="">All Levels</option>
              @foreach($difficulties as $difficulty)
              <option value="{{ $difficulty }}" {{ request('difficulty') == $difficulty ? 'selected' : '' }}>
                {{ ucfirst($difficulty) }}
              </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">&nbsp;</label>
            <div>
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i> Filter
              </button>
              <a href="{{ route('instruments.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Clear
              </a>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Instruments Grid -->
    @if($instruments->count() > 0)
    <div class="row">
      @foreach($instruments as $instrument)
      <div class="col-lg-4 col-md-6 mb-4">
        <div class="card h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <h5 class="card-title">{{ $instrument->name }}</h5>
              <span class="badge bg-{{ $instrument->is_available ? 'success' : 'danger' }}">
                {{ $instrument->is_available ? 'Available' : 'Unavailable' }}
              </span>
            </div>

            <div class="mb-2">
              <span class="badge bg-info">{{ ucfirst($instrument->category) }}</span>
              <span class="badge bg-secondary">{{ ucfirst($instrument->difficulty_level) }}</span>
            </div>

            @if($instrument->description)
            <p class="card-text">{{ Str::limit($instrument->description, 100) }}</p>
            @endif

            @if($instrument->rental_fee)
            <p class="text-success mb-2">
              <strong>Rental Fee: ${{ number_format($instrument->rental_fee, 2) }}/month</strong>
            </p>
            @endif
          </div>

          <div class="card-footer bg-transparent">
            <div class="d-flex gap-2">
              <a href="{{ route('instruments.show', $instrument) }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-eye"></i> Details
              </a>

              @auth
              @if($instrument->is_available && !auth()->user()->isBorrowingInstrument($instrument->id))
              <a href="{{ route('instrument-borrows.create', ['instrument' => $instrument->id]) }}"
                class="btn btn-success btn-sm">
                <i class="bi bi-plus-circle"></i> Borrow
              </a>
              @elseif(auth()->user()->isBorrowingInstrument($instrument->id))
              <span class="btn btn-outline-success btn-sm disabled">
                <i class="bi bi-check-circle"></i> Borrowed
              </span>
              @endif

              @if(auth()->user()->isAdmin())
              <a href="{{ route('instruments.edit', $instrument) }}" class="btn btn-outline-warning btn-sm">
                <i class="bi bi-pencil"></i> Edit
              </a>
              @endif
              @else
              <a href="{{ route('demo') }}" class="btn btn-success btn-sm">
                <i class="bi bi-box-arrow-in-right"></i> Login to Enroll
              </a>
              @endauth
            </div>
          </div>
        </div>
      </div>
      @endforeach
    </div>
    @else
    <div class="text-center py-5">
      <i class="bi bi-music-note-beamed display-1 text-muted"></i>
      <h3 class="text-muted mt-3">No instruments found</h3>
      <p class="text-muted">Try adjusting your search criteria or check back later.</p>
    </div>
    @endif
  </div>
</div>
@endsection