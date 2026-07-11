@extends('layouts.app')

@section('content')

@php
    $contractor = $contractorBill->contractor;

    $photoUrl = null;

    if ($contractor?->photo) {
        $cleanPhotoPath = ltrim(
            str_replace(
                'public/',
                '',
                $contractor->photo
            ),
            '/'
        );

        $photoUrl = asset(
            'storage/' . $cleanPhotoPath
        );
    }
@endphp

<div class="d-flex justify-content-between align-items-center mb-4 no-print">

    <h3 class="fw-bold mb-0">
        Contractor Bill Slip
    </h3>

    <div>

        <button type="button"
                onclick="window.print()"
                class="btn btn-primary">

            Print Slip
        </button>

        <a href="{{ route('contractor-bills.index') }}"
           class="btn btn-secondary">

            Back
        </a>

    </div>

</div>

<div class="bill-slip">

    <div class="slip-header">

        <div>
            <h2 class="fw-bold mb-0">
                Accounts System
            </h2>

            <div class="text-muted">
                Contractor Work Bill
            </div>

            <div class="mt-2">
                <strong>Bill No:</strong>
                {{ $contractorBill->bill_no }}
            </div>

            <div>
                <strong>Bill Date:</strong>
                {{ $contractorBill->bill_date?->format('d M Y') ?? '-' }}
            </div>
        </div>

        <div class="slip-photo">

            @if($photoUrl)

                <img src="{{ $photoUrl }}"
                     alt="{{ $contractor?->name ?? 'Contractor' }}"
                     onerror="
                        this.style.display='none';
                        this.nextElementSibling.style.display='flex';
                     ">

                <div class="photo-fallback"
                     style="display:none;">

                    {{ strtoupper(
                        substr(
                            $contractor?->name ?? 'C',
                            0,
                            1
                        )
                    ) }}

                </div>

            @else

                <div class="photo-fallback">

                    {{ strtoupper(
                        substr(
                            $contractor?->name ?? 'C',
                            0,
                            1
                        )
                    ) }}

                </div>

            @endif

        </div>

    </div>

    <hr>

    <div class="row mb-3">

        <div class="col-6">

            <p>
                <strong>Contractor:</strong>
                {{ $contractor?->name ?? '-' }}
            </p>

            <p>
                <strong>Phone:</strong>
                {{ $contractor?->phone ?? '-' }}
            </p>

            <p>
                <strong>Address:</strong>
                {{ $contractor?->address ?? '-' }}
            </p>

        </div>

        <div class="col-6">

            <p>
                <strong>Department:</strong>
                {{ $contractor?->department?->name ?? '-' }}
            </p>

            <p>
                <strong>Machine:</strong>
                {{ $contractor?->machine?->name ?? '-' }}
            </p>

            <p>
                <strong>Status:</strong>
                {{ ucfirst($contractorBill->status ?? 'Pending') }}
            </p>

        </div>

    </div>

    <h5 class="fw-bold mt-4">
        Work Details
    </h5>

    <table class="table table-bordered">

        <thead>
            <tr>
                <th>#</th>
                <th>Order No</th>
                <th>Item</th>
                <th>Quantity</th>
                <th>Rate</th>
                <th>Total</th>
            </tr>
        </thead>

        <tbody>

            @forelse($contractorBill->items as $item)

                <tr>
                    <td>
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $item->order_no ?? '-' }}
                    </td>

                    <td>
                        {{ $item->item_name }}
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

                    <td>
                        Rs {{ number_format(
                            $item->total,
                            2
                        ) }}
                    </td>
                </tr>

            @empty

                <tr>
                    <td colspan="6"
                        class="text-center text-muted py-3">

                        No work items found

                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>

    <table class="table table-bordered summary-table">

        <tr>
            <th>Grand Total</th>

            <td>
                Rs {{ number_format(
                    $contractorBill->grand_total,
                    2
                ) }}
            </td>
        </tr>

        <tr>
            <th>Paid Amount</th>

            <td class="text-success fw-bold">
                Rs {{ number_format(
                    $contractorBill->paid_amount,
                    2
                ) }}
            </td>
        </tr>

        <tr class="balance-row">
            <th>Remaining Balance</th>

            <td>
                Rs {{ number_format(
                    $contractorBill->balance,
                    2
                ) }}
            </td>
        </tr>

        <tr>
            <th>Status</th>

            <td>
                {{ $contractorBill->status }}
            </td>
        </tr>

    </table>

    <h5 class="fw-bold mt-4">
        Advance / Payment Details
    </h5>

    <table class="table table-sm table-bordered">

        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Time</th>
                <th>Amount</th>
                <th>Remarks</th>
                <th class="no-print">Action</th>
            </tr>
        </thead>

        <tbody>

            @forelse($contractorBill->payments as $payment)

                <tr>
                    <td>
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $payment->payment_date?->format(
                            'd M Y'
                        ) ?? '-' }}
                    </td>

                    <td>
                        {{ $payment->payment_time ?? '-' }}
                    </td>

                    <td>
                        Rs {{ number_format(
                            $payment->amount,
                            2
                        ) }}
                    </td>

                    <td>
                        {{ $payment->remarks ?? '-' }}
                    </td>

                    <td class="no-print">

                        <form method="POST"
                              action="{{ route(
                                'contractor-bill-payments.destroy',
                                $payment->id
                              ) }}"
                              onsubmit="return confirm('Delete this payment?')">

                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="btn btn-danger btn-sm">

                                Delete
                            </button>

                        </form>

                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="6"
                        class="text-center text-muted">

                        No payment found

                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>

    @if($contractorBill->notes)

        <div class="mt-4 notes-box">

            <strong>Notes:</strong>

            <p class="mb-0 mt-1">
                {{ $contractorBill->notes }}
            </p>

        </div>

    @endif

    <div class="row signature-section">

        <div class="col-6 text-center">
            <hr>
            <strong>Contractor Signature</strong>
        </div>

        <div class="col-6 text-center">
            <hr>
            <strong>Authorized Signature</strong>
        </div>

    </div>

</div>

<style>
.bill-slip {
    max-width: 900px;
    margin: 0 auto;
    padding: 30px;
    border: 2px solid #111;
    background: #ffffff;
    box-shadow: 0 4px 16px rgba(0,0,0,.08);
}

.slip-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
}

.slip-photo {
    width: 120px;
    height: 145px;
    flex-shrink: 0;
    padding: 4px;
    border: 2px solid #111;
    background: #ffffff;
}

.slip-photo img {
    width: 100%;
    height: 100%;
    display: block;
    object-fit: cover;
}

.photo-fallback {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    background: #0d6efd;
    font-size: 44px;
    font-weight: 800;
}

.summary-table {
    max-width: 470px;
    margin-left: auto;
}

.summary-table th {
    width: 55%;
    background: #f8f9fa;
}

.balance-row th,
.balance-row td {
    background: #f8d7da !important;
    font-size: 17px;
    font-weight: 800;
}

.notes-box {
    padding: 14px;
    border: 1px solid #dee2e6;
    background: #f8f9fa;
}

.signature-section {
    margin-top: 70px;
}

@media print {
    @page {
        size: A4;
        margin: 10mm;
    }

    .no-print,
    aside,
    nav,
    .mobile-header,
    .sidebar-overlay {
        display: none !important;
    }

    body {
        background: #ffffff !important;
    }

    .app-layout {
        display: block !important;
    }

    .main-content,
    main {
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .bill-slip {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 20px !important;
        border: 2px solid #000 !important;
        box-shadow: none !important;
    }

    .table {
        page-break-inside: avoid;
    }
}
</style>

@endsection
