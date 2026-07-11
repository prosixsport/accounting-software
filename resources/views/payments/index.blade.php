@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Payments Received</h3>
        <small class="text-muted">Manage customer invoice payments</small>
    </div>

    <a href="{{ route('payments.create') }}" class="btn btn-primary">
        + Receive Payment
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
                    <th>Payment No</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Invoice</th>
                    <th>Method</th>
                    <th>Amount</th>
                    <th width="180">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td>{{ $payment->payment_no }}</td>
                        <td>{{ date('d M Y', strtotime($payment->payment_date)) }}</td>
                        <td>{{ $payment->customer->customer_name ?? '-' }}</td>
                        <td>{{ $payment->invoice->invoice_no ?? '-' }}</td>
                        <td>{{ ucfirst($payment->payment_method) }}</td>
                        <td><strong>Rs {{ number_format($payment->amount, 2) }}</strong></td>

                        <td>
                            <a href="{{ route('payments.edit', $payment->id) }}" class="btn btn-warning btn-sm">
                                Edit
                            </a>

                            <form action="{{ route('payments.destroy', $payment->id) }}"
                                  method="POST"
                                  class="d-inline">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-danger btn-sm"
                                        onclick="return confirm('Delete payment?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            No payments found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</div>

@endsection
