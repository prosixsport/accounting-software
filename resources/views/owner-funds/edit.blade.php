@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Edit Owner Fund</h3>
        <p class="text-muted mb-0">
            Update owner fund details
        </p>
    </div>

    <a href="{{ route('owner-funds.index') }}" class="btn btn-dark">
        Back to Owner Funds
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body">

        <form
            action="{{ route('owner-funds.update', $ownerFund) }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf
            @method('PUT')

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">Fund Date *</label>

                    <input
                        type="date"
                        name="fund_date"
                        value="{{ old('fund_date', $ownerFund->fund_date->format('Y-m-d')) }}"
                        class="form-control"
                        required
                    >
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Owner Name *</label>

                    <input
                        type="text"
                        name="owner_name"
                        value="{{ old('owner_name', $ownerFund->owner_name) }}"
                        class="form-control"
                        required
                    >
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Amount *</label>

                    <input
                        type="number"
                        name="amount"
                        value="{{ old('amount', $ownerFund->amount) }}"
                        step="0.01"
                        min="1"
                        class="form-control"
                        required
                    >
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Received In *</label>

                    <select name="received_in" class="form-select" required>
                        <option
                            value="cash"
                            @selected(old('received_in', $ownerFund->received_in) === 'cash')
                        >
                            Cash on Hand
                        </option>

                        <option
                            value="bank"
                            @selected(old('received_in', $ownerFund->received_in) === 'bank')
                        >
                            Bank Account
                        </option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Purpose *</label>

                    <input
                        type="text"
                        name="purpose"
                        value="{{ old('purpose', $ownerFund->purpose) }}"
                        class="form-control"
                        required
                    >
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Reference Number</label>

                    <input
                        type="text"
                        name="reference_number"
                        value="{{ old('reference_number', $ownerFund->reference_number) }}"
                        class="form-control"
                    >
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Description</label>

                    <textarea
                        name="description"
                        rows="4"
                        class="form-control"
                    >{{ old('description', $ownerFund->description) }}</textarea>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Replace Attachment</label>

                    <input
                        type="file"
                        name="attachment"
                        class="form-control"
                        accept=".jpg,.jpeg,.png,.webp,.pdf"
                    >

                    @if ($ownerFund->attachment)
                        <div class="mt-2">
                            <a
                                href="{{ asset('storage/' . $ownerFund->attachment) }}"
                                target="_blank"
                            >
                                View Current Attachment
                            </a>
                        </div>
                    @endif
                </div>

                <div class="col-md-12 mb-3">
                    <div class="form-check">

                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            id="is_active"
                            class="form-check-input"
                            @checked(old('is_active', $ownerFund->is_active))
                        >

                        <label for="is_active" class="form-check-label">
                            Active Record
                        </label>

                    </div>
                </div>

            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    Update Owner Fund
                </button>

                <a
                    href="{{ route('owner-funds.index') }}"
                    class="btn btn-light border"
                >
                    Cancel
                </a>
            </div>

        </form>

    </div>
</div>

@endsection
