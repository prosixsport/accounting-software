@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Customer Ledgers</h3>
        <small class="text-muted">Customer invoices, payments and balances</small>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">

        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Customer Code</th>
                    <th>Customer</th>
                    <th>Company</th>
                    <th>Opening Balance</th>
                    <th>Total Invoices</th>
                    <th>Total Payments</th>
                    <th>Balance</th>
                    <th width="120">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($customers as $customer)
                    @php
                        $totalInvoices = $customer->invoices->sum('total_amount');
                        $totalPayments = $customer->payments->sum('amount');
                        $balance = ($customer->opening_balance + $totalInvoices) - $totalPayments;
                    @endphp

                    <tr>
                        <td>{{ $customer->customer_code }}</td>

                        <td>
                            <strong>{{ $customer->customer_name }}</strong><br>
                            <small class="text-muted">{{ $customer->email }}</small>
                        </td>

                        <td>{{ $customer->company_name ?? '-' }}</td>

                        <td>Rs {{ number_format($customer->opening_balance, 2) }}</td>

                        <td>Rs {{ number_format($totalInvoices, 2) }}</td>

                        <td>Rs {{ number_format($totalPayments, 2) }}</td>

                        <td>
                            <strong>Rs {{ number_format($balance, 2) }}</strong>
                        </td>

                        <td>
                            <a href="{{ route('customer-ledgers.show', $customer->id) }}"
                               class="btn btn-primary btn-sm">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            No customers found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</div>

@endsection
