@extends('layouts.app')

@section('title', 'Borrow Details')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Borrow Request Details</h1>
                <a href="{{ route('instrument-borrows.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Back to My Borrows
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
                    <div class="row">
                        <div class="col-md-4 text-center mb-4 mb-md-0">
                            @if($instrumentBorrow->instrument->image)
                            <img src="{{ asset('storage/' . $instrumentBorrow->instrument->image) }}"
                                alt="{{ $instrumentBorrow->instrument->name }}" class="img-fluid rounded shadow-sm mb-3"
                                style="max-height: 300px; object-fit: cover;">
                            @else
                            <div class="bg-secondary rounded d-flex align-items-center justify-content-center mb-3 mx-auto"
                                style="width: 200px; height: 200px;">
                                <i class="bi bi-music-note text-white" style="font-size: 4rem;"></i>
                            </div>
                            @endif

                            @php
                            $statusConfig = [
                            'pending' => ['class' => 'warning', 'icon' => 'clock', 'text' => 'Pending Approval'],
                            'borrowed' => ['class' => 'success', 'icon' => 'check-circle', 'text' => 'Borrowed'],
                            'returned' => ['class' => 'info', 'icon' => 'arrow-return-left', 'text' => 'Returned'],
                            'overdue' => ['class' => 'danger', 'icon' => 'exclamation-triangle', 'text' => 'Overdue']
                            ];
                            $status = $statusConfig[$instrumentBorrow->status] ?? $statusConfig['pending'];
                            @endphp
                            <div class="badge bg-{{ $status['class'] }} fs-6 px-3 py-2">
                                <i class="bi bi-{{ $status['icon'] }} me-1"></i>{{ $status['text'] }}
                            </div>

                            @if($instrumentBorrow->status === 'borrowed' && $instrumentBorrow->isOverdue())
                            <div class="badge bg-danger fs-6 px-3 py-2 ms-2">
                                <i class="bi bi-exclamation-triangle me-1"></i>Overdue
                            </div>
                            @endif
                        </div>

                        <div class="col-md-8">
                            <h2 class="h4 mb-3">{{ $instrumentBorrow->instrument->name }}</h2>

                            <div class="row mb-3">
                                <div class="col-sm-6">
                                    <strong>Type:</strong>
                                    <p class="text-muted mb-2">{{ $instrumentBorrow->instrument->type }}</p>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Brand:</strong>
                                    <p class="text-muted mb-2">{{ $instrumentBorrow->instrument->brand ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-6">
                                    <strong>Condition:</strong>
                                    <p class="text-muted mb-2">{{ ucfirst($instrumentBorrow->instrument->condition) }}
                                    </p>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Purchase Price:</strong>
                                    <p class="text-muted mb-2">
                                        ${{ number_format($instrumentBorrow->instrument->purchase_price, 2) }}</p>
                                </div>
                            </div>

                            @if($instrumentBorrow->instrument->description)
                            <div class="mb-3">
                                <strong>Description:</strong>
                                <p class="text-muted">{{ $instrumentBorrow->instrument->description }}</p>
                            </div>
                            @endif

                            <div class="row mb-3">
                                <div class="col-sm-6">
                                    <strong>Request Date:</strong>
                                    <p class="text-muted mb-2">
                                        {{ $instrumentBorrow->created_at->format('F j, Y \a\t g:i A') }}
                                        <br><small>({{ $instrumentBorrow->created_at->diffForHumans() }})</small>
                                    </p>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Due Date:</strong>
                                    <p class="text-muted mb-2">
                                        @if($instrumentBorrow->due_date)
                                        {{ $instrumentBorrow->due_date->format('F j, Y') }}
                                        <br><small>({{ $instrumentBorrow->due_date->diffForHumans() }})</small>
                                        @else
                                        <span class="text-muted">Not set</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            @if($instrumentBorrow->borrowed_at)
                            <div class="row mb-3">
                                <div class="col-sm-6">
                                    <strong>Borrowed Date:</strong>
                                    <p class="text-muted mb-2">
                                        {{ $instrumentBorrow->borrowed_at->format('F j, Y \a\t g:i A') }}
                                        <br><small>({{ $instrumentBorrow->borrowed_at->diffForHumans() }})</small>
                                    </p>
                                </div>
                                @if($instrumentBorrow->returned_at)
                                <div class="col-sm-6">
                                    <strong>Returned Date:</strong>
                                    <p class="text-muted mb-2">
                                        {{ $instrumentBorrow->returned_at->format('F j, Y \a\t g:i A') }}
                                        <br><small>({{ $instrumentBorrow->returned_at->diffForHumans() }})</small>
                                    </p>
                                </div>
                                @endif
                            </div>
                            @endif

                            @if($instrumentBorrow->notes)
                            <div class="mb-3">
                                <strong>Notes:</strong>
                                <div class="bg-light rounded p-3 mt-1">
                                    {{ $instrumentBorrow->notes }}
                                </div>
                            </div>
                            @endif

                            <div class="d-flex gap-2 mt-4">
                                @if($instrumentBorrow->status === 'pending')
                                <a href="{{ route('instrument-borrows.edit', $instrumentBorrow) }}"
                                    class="btn btn-primary">
                                    <i class="bi bi-pencil me-1"></i>Edit Request
                                </a>
                                <form method="POST"
                                    action="{{ route('instrument-borrows.destroy', $instrumentBorrow) }}"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('Are you sure you want to cancel this borrow request?')">
                                        <i class="bi bi-trash me-1"></i>Cancel Request
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($instrumentBorrow->status === 'pending')
            <div class="alert alert-info mt-4">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Pending Approval:</strong> Your request is waiting for staff approval. You will be notified once
                it's processed.
            </div>
            @elseif($instrumentBorrow->status === 'borrowed')
            <div class="alert alert-success mt-4">
                <i class="bi bi-check-circle me-2"></i>
                <strong>Instrument Borrowed:</strong> Please take good care of the instrument and return it by the due
                date.
                @if($instrumentBorrow->isOverdue())
                <br><span class="text-danger">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    <strong>This instrument is overdue. Please return it as soon as possible to avoid additional
                        fees.</strong>
                </span>
                @endif
            </div>
            @elseif($instrumentBorrow->status === 'returned')
            <div class="alert alert-primary mt-4">
                <i class="bi bi-arrow-return-left me-2"></i>
                <strong>Instrument Returned:</strong> Thank you for returning the instrument in good condition.
            </div>
            @elseif($instrumentBorrow->status === 'overdue')
            <div class="alert alert-danger mt-4">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Overdue:</strong> This instrument is past its due date. Please contact staff to resolve this
                issue.
            </div>
            @endif
        </div>
    </div>
</div>
@endsection