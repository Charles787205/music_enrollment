@extends('layouts.app')

@section('title', 'My Borrowed Instruments')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">My Borrowed Instruments</h1>
                <a href="{{ route('instrument-borrows.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Request Instrument
                </a>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <div class="card">
                <div class="card-body">
                    @if($borrows->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Instrument</th>
                                    <th>Brand</th>
                                    <th>Status</th>
                                    <th>Request Date</th>
                                    <th>Due Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($borrows as $borrow)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($borrow->instrument->image)
                                            <img src="{{ asset('storage/' . $borrow->instrument->image) }}"
                                                alt="{{ $borrow->instrument->name }}" class="rounded me-2"
                                                style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                            <div class="bg-secondary rounded me-2 d-flex align-items-center justify-content-center"
                                                style="width: 40px; height: 40px;">
                                                <i class="bi bi-music-note text-white"></i>
                                            </div>
                                            @endif
                                            <div>
                                                <strong>{{ $borrow->instrument->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $borrow->instrument->type }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $borrow->instrument->brand ?? 'N/A' }}</td>
                                    <td>
                                        @php
                                        $statusConfig = [
                                        'pending' => ['class' => 'warning', 'icon' => 'clock', 'text' => 'Pending'],
                                        'borrowed' => ['class' => 'success', 'icon' => 'check-circle', 'text' =>
                                        'Borrowed'],
                                        'returned' => ['class' => 'info', 'icon' => 'arrow-return-left', 'text' =>
                                        'Returned'],
                                        'overdue' => ['class' => 'danger', 'icon' => 'exclamation-triangle', 'text' =>
                                        'Overdue']
                                        ];
                                        $status = $statusConfig[$borrow->status] ?? $statusConfig['pending'];
                                        @endphp
                                        <span class="badge bg-{{ $status['class'] }}">
                                            <i class="bi bi-{{ $status['icon'] }} me-1"></i>{{ $status['text'] }}
                                        </span>
                                        @if($borrow->status === 'borrowed' && $borrow->isOverdue())
                                        <br>
                                        <small class="text-danger">
                                            <i class="bi bi-exclamation-triangle me-1"></i>Overdue
                                        </small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $borrow->created_at->format('M j, Y') }}
                                        <br>
                                        <small class="text-muted">{{ $borrow->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        @if($borrow->due_date)
                                        {{ $borrow->due_date->format('M j, Y') }}
                                        <br>
                                        <small class="text-muted">{{ $borrow->due_date->diffForHumans() }}</small>
                                        @else
                                        <span class="text-muted">Not set</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('instrument-borrows.show', $borrow) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            @if($borrow->status === 'pending')
                                            <a href="{{ route('instrument-borrows.edit', $borrow) }}"
                                                class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST"
                                                action="{{ route('instrument-borrows.destroy', $borrow) }}"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Cancel this borrow request?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $borrows->links() }}
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-music-note text-muted mb-3" style="font-size: 3rem;"></i>
                        <h5 class="text-muted">No Borrowed Instruments</h5>
                        <p class="text-muted mb-4">You haven't borrowed any instruments yet.</p>
                        <a href="{{ route('instrument-borrows.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>Request Your First Instrument
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection