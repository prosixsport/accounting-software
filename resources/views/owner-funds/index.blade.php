@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Owner Funds</h3>
        <p class="text-muted mb-0">
            Track money received from factory owners
        </p>
    </div>

    <a href="{{ route('owner-funds.create') }}" class="btn btn-primary">
        Add Owner Fund
    </a>
</div>

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="row g-3 mb-4">

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <small class="text-muted">Total Owner Funds</small>

                <h4 class="fw-bold mt-2 mb-0">
                    Rs. {{ number_format($totalFunds, 2) }}
                </h4>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <small class="text-muted">Cash Received</small>

                <h4 class="fw-bold mt-2 mb-0">
                    Rs. {{ number_format($cashFunds, 2) }}
                </h4>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <small class="text-muted">Bank Received</small>

                <h4 class="fw-bold mt-2 mb-0">
                    Rs. {{ number_format($bankFunds, 2) }}
                </h4>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <small class="text-muted">Last Week Funds</small>

                <h4 class="fw-bold mt-2 mb-0">
                    Rs. {{ number_format($lastWeekFunds, 2) }}
                </h4>
            </div>
        </div>
    </div>

</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">

        <form method="GET" action="{{ route('owner-funds.index') }}">
            <div class="row g-3">

                <div class="col-lg-3">
                    <label class="form-label">Search</label>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        class="form-control"
                        placeholder="Owner, purpose or reference"
                    >
                </div>

                <div class="col-lg-2">
                    <label class="form-label">From Date</label>

                    <input
                        type="date"
                        name="from_date"
                        value="{{ request('from_date') }}"
                        class="form-control"
                    >
                </div>

                <div class="col-lg-2">
                    <label class="form-label">To Date</label>

                    <input
                        type="date"
                        name="to_date"
                        value="{{ request('to_date') }}"
                        class="form-control"
                    >
                </div>

                <div class="col-lg-2">
                    <label class="form-label">Received In</label>

                    <select name="received_in" class="form-select">
                        <option value="">All</option>

                        <option
                            value="cash"
                            @selected(request('received_in') === 'cash')
                        >
                            Cash
                        </option>

                        <option
                            value="bank"
                            @selected(request('received_in') === 'bank')
                        >
                            Bank
                        </option>
                    </select>
                </div>

                <div class="col-lg-3 d-flex align-items-end gap-2">
                    <button class="btn btn-dark">
                        Filter
                    </button>

                    <a
                        href="{{ route('owner-funds.index') }}"
                        class="btn btn-light border"
                    >
                        Reset
                    </a>
                </div>

            </div>
        </form>

    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="fw-bold mb-0">Fund History</h5>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Owner</th>
                        <th>Purpose</th>
                        <th>Received In</th>
                        <th>Reference</th>
                        <th class="text-end">Amount</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($ownerFunds as $fund)

                        <tr>
                            <td>
                                {{ $fund->fund_date->format('d M Y') }}
                            </td>

                            <td class="fw-semibold">
                                {{ $fund->owner_name }}
                            </td>

                            <td>
                                {{ $fund->purpose }}
                            </td>

                            <td>
                                @if ($fund->received_in === 'cash')
                                    <span class="badge bg-success">
                                        Cash
                                    </span>
                                @else
                                    <span class="badge bg-primary">
                                        Bank
                                    </span>
                                @endif
                            </td>

                            <td>
                                {{ $fund->reference_number ?: 'N/A' }}
                            </td>

                            <td class="text-end fw-bold">
                                Rs. {{ number_format($fund->amount, 2) }}
                            </td>

                            <td class="text-end">

                                <a
                                    href="{{ route('owner-funds.show', $fund) }}"
                                    class="btn btn-sm btn-info"
                                >
                                    View
                                </a>

                                <a
                                    href="{{ route('owner-funds.edit', $fund) }}"
                                    class="btn btn-sm btn-warning"
                                >
                                    Edit
                                </a>

                                <form
                                    action="{{ route('owner-funds.destroy', $fund) }}"
                                    method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Delete this owner fund record?')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-sm btn-danger">
                                        Delete
                                    </button>
                                </form>

                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <h6 class="fw-bold mb-1">
                                    No owner fund records found
                                </h6>

                                <p class="text-muted mb-0">
                                    Add the first owner fund transaction.
                                </p>
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>
    </div>

    @if ($ownerFunds->hasPages())
        <div class="card-footer bg-white">
            {{ $ownerFunds->links() }}
        </div>
    @endif
</div>

@endsection
