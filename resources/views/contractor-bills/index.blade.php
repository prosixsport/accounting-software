@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h2 class="fw-bold mb-1">
            Contractor Bills
        </h2>

        <p class="text-muted mb-0">
            Manage contractor work bills and payments.
        </p>
    </div>

    <a href="{{ route('contractor-bills.create') }}"
       class="btn btn-dark">

        <i class="bi bi-plus-lg me-1"></i>
        Add Bill
    </a>

</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert">
        </button>
    </div>
@endif

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

        <div class="table-responsive">

            <table class="table table-hover align-middle bill-table">

                <thead>
                    <tr>
<th>Bill No</th>
                        <th>Contractor</th>
                        <th>Machine</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th width="310">Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($bills as $bill)

                        <tr>

                            <td class="fw-bold">
{{ $bill->bill_no }}
                            </td>

                            <td>
                                <div class="d-flex align-items-center">

                                    @if($bill->contractor?->photo)

                                        <img src="{{ asset(
                                                'storage/' .
                                                ltrim(
                                                    $bill->contractor->photo,
                                                    '/'
                                                )
                                            ) }}"
                                             class="contractor-photo me-3"
                                             alt="{{ $bill->contractor->name }}">

                                    @else

                                        <div class="contractor-avatar me-3">
                                            {{ strtoupper(
                                                substr(
                                                    $bill->contractor?->name ?? 'C',
                                                    0,
                                                    1
                                                )
                                            ) }}
                                        </div>

                                    @endif

                                    <div>
                                        <div class="fw-bold">
                                            {{ $bill->contractor?->name ?? 'N/A' }}
                                        </div>

                                        <small class="text-muted">
                                            {{ $bill->contractor?->phone ?? '-' }}
                                        </small>

                                        <br>

                                        <small class="text-muted">
                                            {{ $bill->contractor?->department?->name ?? '-' }}
                                        </small>
                                    </div>

                                </div>
                            </td>

                            <td>
                                {{ $bill->contractor?->machine?->name ?? '-' }}
                            </td>

                            <td>
                                {{ $bill->bill_date?->format('d M Y') ?? '-' }}
                            </td>

                            <td class="fw-bold">
                                Rs {{ number_format(
                                    $bill->grand_total,
                                    2
                                ) }}
                            </td>

                            <td class="text-success fw-semibold">
                                Rs {{ number_format(
                                    $bill->paid_amount,
                                    2
                                ) }}
                            </td>

                            <td class="text-danger fw-semibold">
                                Rs {{ number_format(
                                    $bill->balance,
                                    2
                                ) }}
                            </td>

                            <td>
                                @if($bill->status === 'Paid')

                                    <span class="badge bg-success">
                                        Paid
                                    </span>

                                @elseif($bill->status === 'Partial')

                                    <span class="badge bg-warning text-dark">
                                        Partial
                                    </span>

                                @else

                                    <span class="badge bg-danger">
                                        Pending
                                    </span>

                                @endif
                            </td>

                            <td>

                                <a href="{{ route(
                                        'contractor-bills.show',
                                        $bill->id
                                    ) }}"
                                   class="btn btn-info btn-sm mb-1">

                                    View Slip
                                </a>

                                @if((float) $bill->balance > 0)

                                    <button type="button"
                                            class="btn btn-warning btn-sm mb-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#advanceModal{{ $bill->id }}">

                                        Advance
                                    </button>

                                @else

                                    <button type="button"
                                            class="btn btn-success btn-sm mb-1"
                                            disabled>

                                        Paid
                                    </button>

                                @endif

                                <a href="{{ route(
                                        'contractor-bills.edit',
                                        $bill->id
                                    ) }}"
                                   class="btn btn-primary btn-sm mb-1">

                                    Edit
                                </a>

                                <form action="{{ route(
                                        'contractor-bills.destroy',
                                        $bill->id
                                    ) }}"
                                      method="POST"
                                      class="d-inline"
                                      onsubmit="return confirm('Delete this bill?')">

                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-danger btn-sm mb-1">
                                        Delete
                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="9"
                                class="text-center text-muted py-5">

                                No contractor bill found.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        @if($bills->hasPages())
            {{ $bills->links() }}
        @endif

    </div>
</div>

@foreach($bills as $bill)

    @if((float) $bill->balance > 0)

        <div class="modal fade"
             id="advanceModal{{ $bill->id }}"
             tabindex="-1">

            <div class="modal-dialog">

                <form method="POST"
                      action="{{ route(
                        'contractor-bills.advance.store'
                      ) }}"
                      class="modal-content">

                    @csrf

                    <input type="hidden"
                           name="contractor_bill_id"
                           value="{{ $bill->id }}">

                    <div class="modal-header">

                        <div>
                            <h5 class="modal-title">
                                Add Advance
                            </h5>

                            <small class="text-muted">
                                Bill No: {{ $bill->bill_no }}
                                —
                                {{ $bill->contractor?->name }}
                            </small>
                        </div>

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal">
                        </button>

                    </div>

                    <div class="modal-body">

                        <div class="alert alert-light border">
                            <div>
                                Total:
                                <strong>
                                    Rs {{ number_format(
                                        $bill->grand_total,
                                        2
                                    ) }}
                                </strong>
                            </div>

                            <div>
                                Paid:
                                <strong class="text-success">
                                    Rs {{ number_format(
                                        $bill->paid_amount,
                                        2
                                    ) }}
                                </strong>
                            </div>

                            <div>
                                Balance:
                                <strong class="text-danger">
                                    Rs {{ number_format(
                                        $bill->balance,
                                        2
                                    ) }}
                                </strong>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Advance Amount *
                            </label>

                            <input type="number"
                                   name="amount"
                                   step="0.01"
                                   min="0.01"
                                   max="{{ $bill->balance }}"
                                   class="form-control"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Date *
                            </label>

                            <input type="date"
                                   name="payment_date"
                                   value="{{ date('Y-m-d') }}"
                                   class="form-control"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Time
                            </label>

                            <input type="time"
                                   name="payment_time"
                                   value="{{ date('H:i') }}"
                                   class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Remarks
                            </label>

                            <textarea name="remarks"
                                      rows="3"
                                      class="form-control"></textarea>
                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">

                            Cancel
                        </button>

                        <button type="submit"
                                class="btn btn-primary">

                            Save Advance
                        </button>

                    </div>

                </form>

            </div>

        </div>

    @endif

@endforeach

<style>
.bill-table {
    min-width: 1200px;
}

.bill-table th,
.bill-table td {
    padding: 14px 12px;
}

.contractor-photo,
.contractor-avatar {
    width: 55px;
    height: 55px;
    flex-shrink: 0;
    border: 2px solid #e5e7eb;
    border-radius: 50%;
}

.contractor-photo {
    object-fit: cover;
}

.contractor-avatar {
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    background: #0d6efd;
    font-size: 20px;
    font-weight: 800;
}
</style>

@endsection
