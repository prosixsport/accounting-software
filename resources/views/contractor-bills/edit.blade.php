@extends('layouts.app')

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h2 class="fw-bold mb-1">
            Edit Contractor Bill
        </h2>

        <p class="text-muted mb-0">
            Update order number, bill date and notes.
        </p>
    </div>

    <a href="{{ route('contractor-bills.index') }}"
       class="btn btn-light border">

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

<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-1">
                    Bill Information
                </h5>

                <small class="text-muted">
                    Order No:
                    {{ $contractorBill->order_no ?? '-' }}
                </small>
            </div>

            <span class="bill-status
                @if($contractorBill->status === 'Paid')
                    status-paid
                @elseif($contractorBill->status === 'Partial')
                    status-partial
                @else
                    status-pending
                @endif">

                {{ $contractorBill->status }}
            </span>
        </div>
    </div>

    <div class="card-body">

        <form action="{{ route(
                    'contractor-bills.update',
                    $contractorBill->id
                ) }}"
              method="POST">

            @csrf
            @method('PUT')

            <div class="row">

                <div class="col-lg-3 col-md-6 mb-3">
                    <label class="form-label">
                        Order No *
                    </label>

                    <input type="text"
                           name="order_no"
                           value="{{ old(
                                'order_no',
                                $contractorBill->order_no
                           ) }}"
                           class="form-control @error('order_no') is-invalid @enderror"
                           required>

                    @error('order_no')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <label class="form-label">
                        Contractor
                    </label>

                    <input type="text"
                           class="form-control"
                           value="{{ $contractorBill->contractor?->name ?? 'N/A' }}"
                           readonly>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <label class="form-label">
                        Machine
                    </label>

                    <input type="text"
                           class="form-control"
                           value="{{ $contractorBill->contractor?->machine?->name ?? 'No machine assigned' }}"
                           readonly>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <label class="form-label">
                        Bill Date *
                    </label>

                    <input type="date"
                           name="bill_date"
                           value="{{ old(
                                'bill_date',
                                optional(
                                    $contractorBill->bill_date
                                )->format('Y-m-d')
                           ) }}"
                           class="form-control @error('bill_date') is-invalid @enderror"
                           required>

                    @error('bill_date')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

            </div>

            <hr>

            <h5 class="fw-bold mb-3">
                Bill Items
            </h5>

            <div class="table-responsive mb-4">

                <table class="table table-bordered align-middle bill-items-table">

                    <thead>
                        <tr>
                            <th width="60">#</th>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Rate</th>
                            <th>Total</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($contractorBill->items as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td>
                                    <strong>
                                        {{ $item->item_name }}
                                    </strong>
                                </td>

                                <td>
                                    {{ number_format(
                                        $item->quantity,
                                        2
                                    ) }}
                                </td>

                                <td>
                                    Rs {{ number_format(
                                        $item->rate,
                                        2
                                    ) }}
                                </td>

                                <td class="fw-bold">
                                    Rs {{ number_format(
                                        $item->total,
                                        2
                                    ) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5"
                                    class="text-center text-muted py-4">

                                    No bill items found
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="row">

                <div class="col-lg-4 col-md-6 mb-3">
                    <label class="form-label">
                        Grand Total
                    </label>

                    <div class="input-group">
                        <span class="input-group-text">
                            Rs
                        </span>

                        <input type="text"
                               class="form-control fw-bold"
                               value="{{ number_format(
                                    $contractorBill->grand_total,
                                    2,
                                    '.',
                                    ''
                               ) }}"
                               readonly>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 mb-3">
                    <label class="form-label">
                        Paid Amount
                    </label>

                    <div class="input-group">
                        <span class="input-group-text">
                            Rs
                        </span>

                        <input type="text"
                               class="form-control text-success fw-bold"
                               value="{{ number_format(
                                    $contractorBill->paid_amount,
                                    2,
                                    '.',
                                    ''
                               ) }}"
                               readonly>
                    </div>

                    <small class="text-muted">
                        Paid amount changes through Advance button.
                    </small>
                </div>

                <div class="col-lg-4 col-md-6 mb-3">
                    <label class="form-label">
                        Remaining Balance
                    </label>

                    <div class="input-group">
                        <span class="input-group-text">
                            Rs
                        </span>

                        <input type="text"
                               class="form-control text-danger fw-bold"
                               value="{{ number_format(
                                    $contractorBill->balance,
                                    2,
                                    '.',
                                    ''
                               ) }}"
                               readonly>
                    </div>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">
                        Notes
                    </label>

                    <textarea name="notes"
                              rows="4"
                              class="form-control">{{ old(
                                'notes',
                                $contractorBill->notes
                              ) }}</textarea>
                </div>

            </div>

            <button type="submit"
                    class="btn btn-dark px-4">

                <i class="bi bi-check-circle me-1"></i>
                Update Bill
            </button>

        </form>

    </div>
</div>

<style>
.bill-items-table thead th {
    background: #f8fafc;
    font-weight: 700;
}

.bill-status {
    display: inline-flex;
    padding: 7px 13px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
}

.status-paid {
    color: #198754;
    background: #d1e7dd;
}

.status-partial {
    color: #856404;
    background: #fff3cd;
}

.status-pending {
    color: #b02a37;
    background: #f8d7da;
}
</style>

@endsection
