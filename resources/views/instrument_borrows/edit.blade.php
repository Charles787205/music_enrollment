@extends('layouts.app')

@section('title', 'Edit Borrow Request')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Edit Borrow Request</h4>
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

                    <form method="POST" action="{{ route('instrument-borrows.update', $instrumentBorrow) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="instrument_id" class="form-label">Select Instrument *</label>
                            <select class="form-select @error('instrument_id') is-invalid @enderror" id="instrument_id"
                                name="instrument_id" required>
                                <option value="">Choose an instrument...</option>
                                @foreach($instruments as $instrument)
                                <option value="{{ $instrument->id }}"
                                    {{ old('instrument_id', $instrumentBorrow->instrument_id) == $instrument->id ? 'selected' : '' }}
                                    data-brand="{{ $instrument->brand }}" data-condition="{{ $instrument->condition }}"
                                    data-image="{{ $instrument->image ? asset('storage/' . $instrument->image) : '' }}">
                                    {{ $instrument->name }} - {{ $instrument->type }}
                                    @if($instrument->brand)
                                    ({{ $instrument->brand }})
                                    @endif
                                    @if(!$instrument->is_available && $instrument->id !=
                                    $instrumentBorrow->instrument_id)
                                    - Not Available
                                    @endif
                                </option>
                                @endforeach
                            </select>
                            @error('instrument_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Instrument Preview -->
                        <div id="instrumentPreview" class="mb-3" style="display: none;">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-3">
                                            <img id="previewImage" src="" alt="" class="img-fluid rounded"
                                                style="max-height: 100px; display: none;">
                                            <div id="previewPlaceholder"
                                                class="bg-secondary rounded d-flex align-items-center justify-content-center"
                                                style="height: 100px; display: none;">
                                                <i class="bi bi-music-note text-white" style="font-size: 2rem;"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <h6 class="mb-1" id="previewName"></h6>
                                            <p class="mb-1 text-muted">
                                                Brand: <span id="previewBrand"></span><br>
                                                Condition: <span id="previewCondition"></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes"
                                rows="3"
                                placeholder="Any additional information about your request...">{{ old('notes', $instrumentBorrow->notes) }}</textarea>
                            @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Optional: Include any specific requirements or notes for your request.
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Note:</strong> You can only edit pending borrow requests. Once approved, you'll need
                            to contact staff for any changes.
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('instrument-borrows.show', $instrumentBorrow) }}"
                                class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Back to Details
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Update Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const instrumentSelect = document.getElementById('instrument_id');
        const preview = document.getElementById('instrumentPreview');
        const previewImage = document.getElementById('previewImage');
        const previewPlaceholder = document.getElementById('previewPlaceholder');
        const previewName = document.getElementById('previewName');
        const previewBrand = document.getElementById('previewBrand');
        const previewCondition = document.getElementById('previewCondition');

        instrumentSelect.addEventListener('change', function () {
            if (this.value) {
                const selectedOption = this.options[this.selectedIndex];
                const name = selectedOption.text;
                const brand = selectedOption.getAttribute('data-brand') || 'N/A';
                const condition = selectedOption.getAttribute('data-condition') || 'N/A';
                const image = selectedOption.getAttribute('data-image');

                previewName.textContent = name;
                previewBrand.textContent = brand;
                previewCondition.textContent = condition;

                if (image) {
                    previewImage.src = image;
                    previewImage.style.display = 'block';
                    previewPlaceholder.style.display = 'none';
                } else {
                    previewImage.style.display = 'none';
                    previewPlaceholder.style.display = 'flex';
                }

                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        });

        // Trigger change event if there's already a selected value
        if (instrumentSelect.value) {
            instrumentSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection