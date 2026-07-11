@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">{{ $customer->customer_name }}</h3>
        <small class="text-muted">Customer ledger statement</small>
    </div>

    <a href="{{ route('customer-ledgers.index') }}" class="btn btn-secondary">
        Back
    </a>
</div>

<div class="row g-4 mb-4">

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <p class="text-muted mb-1">Opening Balance</p>
                <h5 class="fw-bold mb-0">Rs {{ number_format($openingBalance, 2) }}</h5>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <p class="text-muted mb-1">Total Invoices</p>
                <h5 class="fw-bold mb-0">Rs {{ number_format($totalInvoices, 2) }}</h5>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <p class="text-muted mb-1">Total Payments</p>
                <h5 class="fw-bold mb-0">Rs {{ number_format($totalPayments, 2) }}</h5>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <p class="text-muted mb-1">Current Balance</p>
                <h5 class="fw-bold mb-0 text-danger">
                    Rs {{ number_format($balance, 2) }}
                </h5>
            </div>
        </div>
    </div>

</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-bold">
        Ledger Transactions
    </div>

    <div class="card-body">

        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Reference</th>
                    <th>Debit</th>
                    <th>Credit</th>
                    <th>Running Balance</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $runningBalance = 0;
                @endphp

                @forelse($transactions as $transaction)
                    @php
                        $runningBalance += $transaction['debit'];
                        $runningBalance -= $transaction['credit'];
                    @endphp

                    <tr>
                        <td>{{ date('d M Y', strtotime($transaction['date'])) }}</td>

                        <td>{{ $transaction['type'] }}</td>

                        <td>{{ $transaction['reference'] }}</td>

                        <td>
                            @if($transaction['debit'] > 0)
                                Rs {{ number_format($transaction['debit'], 2) }}
                            @else
                                -
                            @endif
                        </td>

                        <td>
                            @if($transaction['credit'] > 0)
                                Rs {{ number_format($transaction['credit'], 2) }}
                            @else
                                -
                            @endif
                        </td>

                        <td>
                            <strong>Rs {{ number_format($runningBalance, 2) }}</strong>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">
                            No ledger transactions found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</div>

@endsection
