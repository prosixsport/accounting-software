@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Owner Fund Details</h3>
        <p class="text-muted mb-0">
            Complete fund transaction information
        </p>
    </div>

    <div class="d-flex gap-2">
        <a
            href="{{ route('owner-funds.edit', $ownerFund) }}"
            class="btn btn-warning"
        >
            Edit
        </a>

        <a
            href="{{ route('owner-funds.index') }}"
            class="btn btn-dark"
        >
            Back
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">

        <div class="row g-4">

            <div class="col-md-4">
                <small class="text-muted">Fund Date</small>

                <h6 class="fw-bold mt-1">
                    {{ $ownerFund->fund_date->format('d M Y') }}
                </h6>
            </div>

            <div class="col-md-4">
                <small class="text-muted">Owner Name</small>

                <h6 class="fw-bold mt-1">
                    {{ $ownerFund->owner_name }}
                </h6>
            </div>

            <div class="col-md-4">
                <small class="text-muted">Amount</small>

                <h5 class="fw-bold mt-1">
                    Rs. {{ number_format($ownerFund->amount, 2) }}
                </h5>
            </div>

            <div class="col-md-4">
                <small class="text-muted">Received In</small>

                <h6 class="fw-bold mt-1">
                    {{ ucfirst($ownerFund->received_in) }}
                </h6>
            </div>

            <div class="col-md-4">
                <small class="text-muted">Purpose</small>

                <h6 class="fw-bold mt-1">
                    {{ $ownerFund->purpose }}
                </h6>
            </div>

            <div class="col-md-4">
                <small class="text-muted">Reference Number</small>

                <h6 class="fw-bold mt-1">
                    {{ $ownerFund->reference_number ?: 'N/A' }}
                </h6>
            </div>

            <div class="col-md-12">
                <small class="text-muted">Description</small>

                <p class="mt-2 mb-0">
                    {{ $ownerFund->description ?: 'No description added.' }}
                </p>
            </div>

            @if ($ownerFund->attachment)
                <div class="col-md-12">
                    <small class="text-muted d-block mb-2">
                        Attachment
                    </small>

                    <a
                        href="{{ asset('storage/' . $ownerFund->attachment) }}"
                        target="_blank"
                        class="btn btn-outline-primary"
                    >
                        View Attachment
                    </a>
                </div>
            @endif

        </div>

    </div>
</div>

@endsection
