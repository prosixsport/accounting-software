@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Invoice {{ $invoice->invoice_no }}</h3>
        <small class="text-muted">Invoice details</small>
    </div>

    <div>
        <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn btn-warning">
            Edit
        </a>

        <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
            Back
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">

        <div class="row mb-4">
            <div class="col-md-6">
                <h5 class="fw-bold">Bill To</h5>
                <p class="mb-1">{{ $invoice->customer->customer_name ?? '-' }}</p>
                <p class="mb-1">{{ $invoice->customer->company_name ?? '' }}</p>
                <p class="mb-1">{{ $invoice->customer->phone ?? '' }}</p>
                <p class="mb-0">{{ $invoice->customer->address ?? '' }}</p>
            </div>

            <div class="col-md-6 text-md-end">
                <h5 class="fw-bold">Invoice Info</h5>
                <p class="mb-1"><strong>Invoice No:</strong> {{ $invoice->invoice_no }}</p>
                <p class="mb-1"><strong>Date:</strong> {{ date('d M Y', strtotime($invoice->invoice_date)) }}</p>
                <p class="mb-1"><strong>Due Date:</strong> {{ $invoice->due_date ? date('d M Y', strtotime($invoice->due_date)) : '-' }}</p>
                <p class="mb-0"><strong>Status:</strong> {{ ucfirst($invoice->status) }}</p>
            </div>
        </div>

        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Item</th>
                    <th>Description</th>
                    <th width="120">Qty</th>
                    <th width="140">Rate</th>
                    <th width="140">Amount</th>
                </tr>
            </thead>

            <tbody>
                @foreach($invoice->items as $item)
                    <tr>
                        <td>{{ $item->item_name }}</td>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->qty }}</td>
                        <td>Rs {{ number_format($item->rate, 2) }}</td>
                        <td>Rs {{ number_format($item->amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="row justify-content-end">
            <div class="col-md-4">
                <table class="table">
                    <tr>
                        <th>Subtotal</th>
                        <td>Rs {{ number_format($invoice->subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Discount</th>
                        <td>Rs {{ number_format($invoice->discount, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Tax</th>
                        <td>Rs {{ number_format($invoice->tax, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Total</th>
                        <td><strong>Rs {{ number_format($invoice->total_amount, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <th>Paid</th>
                        <td>Rs {{ number_format($invoice->paid_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Balance</th>
                        <td><strong>Rs {{ number_format($invoice->balance_amount, 2) }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>

        @if($invoice->notes)
            <div class="mt-3">
                <h6 class="fw-bold">Notes</h6>
                <p>{{ $invoice->notes }}</p>
            </div>
        @endif

    </div>
</div>

@endsection
