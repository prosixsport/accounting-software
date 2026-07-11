@extends('layouts.app')

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h2 class="fw-bold mb-1">
            Add Contract Item
        </h2>

        <p class="text-muted mb-0">
            Create item, select machine and define its fixed rate.
        </p>
    </div>

    <a href="{{ route('contractor-items.index') }}"
       class="btn btn-secondary">

        <i class="bi bi-arrow-left me-1"></i>
        Back
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger">

        <strong>Please fix the following errors:</strong>

        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>

    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body">

        <form action="{{ route('contractor-items.store') }}"
              method="POST"
              enctype="multipart/form-data">

            @csrf

            <div class="row">

                <div class="col-lg-6 mb-3">

                    <label class="form-label">
                        Item Name *
                    </label>

                    <input type="text"
                           name="name"
                           value="{{ old('name') }}"
                           class="form-control @error('name') is-invalid @enderror"
                           required>

                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <div class="col-lg-6 mb-3">

                    <label class="form-label">
                        Thumbnail
                    </label>

                    <input type="file"
                           name="thumbnail"
                           id="thumbnailInput"
                           class="form-control @error('thumbnail') is-invalid @enderror"
                           accept="image/*">

                    <small class="text-muted">
                        JPG, PNG or WEBP. Maximum 5MB.
                    </small>

                    @error('thumbnail')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                    <div id="thumbnailPreview"
                         class="mt-2"></div>

                </div>

                <div class="col-lg-4 col-md-6 mb-3">

                    <label class="form-label">
                        Unit *
                    </label>

                    <select name="unit"
                            class="form-select"
                            required>

                        @php
                            $units = [
                                'Piece',
                                'Pair',
                                'Set',
                                'Kg',
                                'Meter',
                                'Roll',
                                'Dozen',
                            ];
                        @endphp

                        @foreach($units as $unit)

                            <option value="{{ $unit }}"
                                {{ old('unit', 'Piece') === $unit
                                    ? 'selected'
                                    : '' }}>

                                {{ $unit }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-lg-4 col-md-6 mb-3">

                    <label class="form-label">
                        Rate / Price *
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            Rs
                        </span>

                        <input type="number"
                               name="rate"
                               value="{{ old('rate') }}"
                               step="0.01"
                               min="0"
                               class="form-control @error('rate') is-invalid @enderror"
                               required>

                    </div>

                    @error('rate')
                        <small class="text-danger">
                            {{ $message }}
                        </small>
                    @enderror

                </div>

                <div class="col-lg-4 col-md-6 mb-3">

                    <label class="form-label">
                        Machine *
                    </label>

                    <select name="contractor_machine_id"
                            class="form-select @error('contractor_machine_id') is-invalid @enderror"
                            required>

                        <option value="">
                            Select Machine
                        </option>

                        @foreach($machines as $machine)

                            <option value="{{ $machine->id }}"
                                {{ old('contractor_machine_id') == $machine->id
                                    ? 'selected'
                                    : '' }}>

                                {{ $machine->name }}

                                @if($machine->department)
                                    — {{ $machine->department->name }}
                                @endif

                            </option>

                        @endforeach

                    </select>

                    <small class="text-muted">
                        Machines added in Contractor section automatically appear here.
                    </small>

                    @error('contractor_machine_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <div class="col-lg-4 col-md-6 mb-3">

                    <label class="form-label">
                        Status *
                    </label>

                    <select name="status"
                            class="form-select"
                            required>

                        <option value="active"
                            {{ old('status', 'active') === 'active'
                                ? 'selected'
                                : '' }}>
                            Active
                        </option>

                        <option value="inactive"
                            {{ old('status') === 'inactive'
                                ? 'selected'
                                : '' }}>
                            Inactive
                        </option>

                    </select>

                </div>

                <div class="col-12 mb-3">

                    <label class="form-label">
                        Description
                    </label>

                    <textarea name="description"
                              rows="4"
                              class="form-control"
                              placeholder="Enter item or work description">{{ old('description') }}</textarea>

                </div>

            </div>

            <button type="submit"
                    class="btn btn-dark px-4">

                <i class="bi bi-check-circle me-1"></i>
                Save Item
            </button>

        </form>

    </div>
</div>

<style>
.thumbnail-preview-image {
    width: 100px;
    height: 100px;
    border: 1px solid #d9dee7;
    border-radius: 12px;
    object-fit: cover;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const input =
        document.getElementById('thumbnailInput');

    const preview =
        document.getElementById('thumbnailPreview');

    input?.addEventListener('change', function () {

        preview.innerHTML = '';

        const file = this.files[0];

        if (!file) {
            return;
        }

        const image = document.createElement('img');

        image.src = URL.createObjectURL(file);
        image.className = 'thumbnail-preview-image';

        image.onload = function () {
            URL.revokeObjectURL(image.src);
        };

        preview.appendChild(image);

    });

});
</script>

@endsection
