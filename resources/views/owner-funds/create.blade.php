@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Add Owner Fund</h3>
        <p class="text-muted mb-0">
            Record money received from an owner
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
    <div class="card-header bg-white py-3">
        <h5 class="fw-bold mb-1">Fund Details</h5>

        <small class="text-muted">
            Enter owner, amount and receiving details
        </small>
    </div>

    <div class="card-body">
        <form
            action="{{ route('owner-funds.store') }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        Fund Date *
                    </label>

                    <input
                        type="date"
                        name="fund_date"
                        value="{{ old('fund_date', date('Y-m-d')) }}"
                        class="form-control @error('fund_date') is-invalid @enderror"
                        required
                    >

                    @error('fund_date')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        Owner Name *
                    </label>

                    <input
                        type="text"
                        name="owner_name"
                        value="{{ old('owner_name') }}"
                        class="form-control @error('owner_name') is-invalid @enderror"
                        placeholder="Enter owner name"
                        required
                    >

                    @error('owner_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        Amount *
                    </label>

                    <input
                        type="number"
                        name="amount"
                        value="{{ old('amount') }}"
                        step="0.01"
                        min="1"
                        class="form-control @error('amount') is-invalid @enderror"
                        placeholder="0.00"
                        required
                    >

                    @error('amount')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        Received In *
                    </label>

                    <select
                        name="received_in"
                        class="form-select @error('received_in') is-invalid @enderror"
                        required
                    >
                        <option value="cash" @selected(old('received_in') === 'cash')>
                            Cash on Hand
                        </option>

                        <option value="bank" @selected(old('received_in') === 'bank')>
                            Bank Account
                        </option>
                    </select>

                    @error('received_in')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        Purpose *
                    </label>

                    <input
                        type="text"
                        name="purpose"
                        value="{{ old('purpose') }}"
                        class="form-control @error('purpose') is-invalid @enderror"
                        placeholder="Example: Raw material purchase"
                        required
                    >

                    @error('purpose')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        Reference Number
                    </label>

                    <input
                        type="text"
                        name="reference_number"
                        value="{{ old('reference_number') }}"
                        class="form-control"
                        placeholder="Cheque, bank or receipt reference"
                    >
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">
                        Description
                    </label>

                    <textarea
                        name="description"
                        rows="4"
                        class="form-control"
                        placeholder="Enter additional details"
                    >{{ old('description') }}</textarea>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">
                        Attachment
                    </label>

                    <input
                        type="file"
                        name="attachment"
                        class="form-control"
                        accept=".jpg,.jpeg,.png,.webp,.pdf"
                    >

                    <small class="text-muted">
                        JPG, PNG, WEBP or PDF. Maximum 10 MB.
                    </small>
                </div>

            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    Save Owner Fund
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
