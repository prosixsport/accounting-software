@extends('layouts.app')

@section('content')

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h4 class="mb-0 fw-bold">Receive Payment</h4>
    </div>

    <div class="card-body">

        <form action="{{ route('payments.store') }}" method="POST">
            @csrf

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">Customer *</label>
                    <select name="customer_id" class="form-select" required>
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">
                                {{ $customer->customer_code }} - {{ $customer->customer_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Invoice</label>
                    <select name="invoice_id" class="form-select">
                        <option value="">Select Invoice</option>
                        @foreach($invoices as $invoice)
                            <option value="{{ $invoice->id }}">
                                {{ $invoice->invoice_no }} -
                                {{ $invoice->customer->customer_name ?? '' }}
                                | Balance: Rs {{ number_format($invoice->balance_amount, 2) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Payment Date *</label>
                    <input type="date"
                           name="payment_date"
                           value="{{ date('Y-m-d') }}"
                           class="form-control"
                           required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Amount *</label>
                    <input type="number"
                           step="0.01"
                           name="amount"
                           class="form-control"
                           placeholder="0.00"
                           required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Payment Method *</label>
                    <select name="payment_method" class="form-select" required>
                        <option value="cash">Cash</option>
                        <option value="bank">Bank</option>
                        <option value="cheque">Cheque</option>
                        <option value="online">Online</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Reference No</label>
                    <input type="text"
                           name="reference_no"
                           class="form-control"
                           placeholder="Cheque / Bank / Transaction No">
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes"
                              rows="3"
                              class="form-control"
                              placeholder="Optional notes"></textarea>
                </div>

            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary">
                    Save Payment
                </button>

                <a href="{{ route('payments.index') }}" class="btn btn-secondary">
                    Cancel
                </a>
            </div>

        </form>

    </div>
</div>

@endsection
