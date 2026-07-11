@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Invoices</h3>
        <small class="text-muted">Manage customer sales invoices</small>
    </div>

    <a href="{{ route('invoices.create') }}" class="btn btn-primary">
        + Create Invoice
    </a>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body">

        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Invoice No</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Paid</th>
                    <th>Balance</th>
                    <th>Status</th>
                    <th width="230">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($invoices as $invoice)
                    <tr>
                        <td>{{ $invoice->invoice_no }}</td>
                        <td>{{ $invoice->customer->customer_name ?? '-' }}</td>
                        <td>{{ date('d M Y', strtotime($invoice->invoice_date)) }}</td>
                        <td>Rs {{ number_format($invoice->total_amount, 2) }}</td>
                        <td>Rs {{ number_format($invoice->paid_amount, 2) }}</td>
                        <td>Rs {{ number_format($invoice->balance_amount, 2) }}</td>

                        <td>
                            @if($invoice->status == 'paid')
                                <span class="badge bg-success">Paid</span>
                            @elseif($invoice->status == 'partial')
                                <span class="badge bg-warning text-dark">Partial</span>
                            @elseif($invoice->status == 'cancelled')
                                <span class="badge bg-danger">Cancelled</span>
                            @else
                                <span class="badge bg-secondary">Unpaid</span>
                            @endif
                        </td>

                        <td>
                            <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-info btn-sm">
                                View
                            </a>

                            <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn btn-warning btn-sm">
                                Edit
                            </a>

                            <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-danger btn-sm" onclick="return confirm('Delete invoice?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            No invoices found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</div>

@endsection
