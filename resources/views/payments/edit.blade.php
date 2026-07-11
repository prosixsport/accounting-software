@extends('layouts.app')

@section('content')

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h4 class="mb-0 fw-bold">Edit Payment</h4>
    </div>

    <div class="card-body">

        <form action="{{ route('payments.update', $payment->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">Customer *</label>
                    <select name="customer_id" class="form-select" required>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ $payment->customer_id == $customer->id ? 'selected' : '' }}>
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
                            <option value="{{ $invoice->id }}" {{ $payment->invoice_id == $invoice->id ? 'selected' : '' }}>
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
                           value="{{ $payment->payment_date }}"
                           class="form-control"
                           required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Amount *</label>
                    <input type="number"
                           step="0.01"
                           name="amount"
                           value="{{ $payment->amount }}"
                           class="form-control"
                           required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Payment Method *</label>
                    <select name="payment_method" class="form-select" required>
                        <option value="cash" {{ $payment->payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="bank" {{ $payment->payment_method == 'bank' ? 'selected' : '' }}>Bank</option>
                        <option value="cheque" {{ $payment->payment_method == 'cheque' ? 'selected' : '' }}>Cheque</option>
                        <option value="online" {{ $payment->payment_method == 'online' ? 'selected' : '' }}>Online</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Reference No</label>
                    <input type="text"
                           name="reference_no"
                           value="{{ $payment->reference_no }}"
                           class="form-control">
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes"
                              rows="3"
                              class="form-control">{{ $payment->notes }}</textarea>
                </div>

            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary">
                    Update Payment
                </button>

                <a href="{{ route('payments.index') }}" class="btn btn-secondary">
                    Cancel
                </a>
            </div>

        </form>

    </div>
</div>

@endsection
