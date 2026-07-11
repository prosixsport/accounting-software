@extends('layouts.app')

@section('content')

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h4 class="fw-bold mb-0">Create Invoice</h4>
    </div>

    <div class="card-body">

        <form action="{{ route('invoices.store') }}" method="POST">
            @csrf

            <div class="row mb-3">
                <div class="col-md-4 mb-3">
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

                <div class="col-md-4 mb-3">
                    <label class="form-label">Invoice Date *</label>
                    <input type="date" name="invoice_date" value="{{ date('Y-m-d') }}" class="form-control" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Due Date</label>
                    <input type="date" name="due_date" class="form-control">
                </div>
            </div>

            <h5 class="fw-bold mb-3">Invoice Items</h5>

            <div class="table-responsive">
                <table class="table table-bordered align-middle" id="itemsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Item Name</th>
                            <th>Description</th>
                            <th width="120">Qty</th>
                            <th width="140">Rate</th>
                            <th width="140">Amount</th>
                            <th width="80">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>
                                <input type="text" name="item_name[]" class="form-control" required>
                            </td>
                            <td>
                                <input type="text" name="description[]" class="form-control">
                            </td>
                            <td>
                                <input type="number" step="0.01" name="qty[]" value="1" class="form-control qty" required>
                            </td>
                            <td>
                                <input type="number" step="0.01" name="rate[]" value="0" class="form-control rate" required>
                            </td>
                            <td>
                                <input type="number" step="0.01" class="form-control amount" value="0" readonly>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-row">X</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <button type="button" class="btn btn-outline-primary btn-sm mb-4" id="addRow">
                + Add Item
            </button>

            <div class="row justify-content-end">
                <div class="col-md-4">

                    <div class="mb-3">
                        <label class="form-label">Subtotal</label>
                        <input type="number" id="subtotal" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Discount</label>
                        <input type="number" step="0.01" name="discount" id="discount" value="0" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tax</label>
                        <input type="number" step="0.01" name="tax" id="tax" value="0" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Paid Amount</label>
                        <input type="number" step="0.01" name="paid_amount" id="paid_amount" value="0" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Grand Total</label>
                        <input type="number" id="grand_total" class="form-control fw-bold" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Balance</label>
                        <input type="number" id="balance" class="form-control fw-bold" readonly>
                    </div>

                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" rows="3" class="form-control"></textarea>
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary">Save Invoice</button>
                <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Cancel</a>
            </div>

        </form>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tableBody = document.querySelector('#itemsTable tbody');
    const addRowBtn = document.getElementById('addRow');

    function calculateTotals() {
        let subtotal = 0;

        document.querySelectorAll('#itemsTable tbody tr').forEach(row => {
            const qty = parseFloat(row.querySelector('.qty').value) || 0;
            const rate = parseFloat(row.querySelector('.rate').value) || 0;
            const amount = qty * rate;

            row.querySelector('.amount').value = amount.toFixed(2);
            subtotal += amount;
        });

        const discount = parseFloat(document.getElementById('discount').value) || 0;
        const tax = parseFloat(document.getElementById('tax').value) || 0;
        const paid = parseFloat(document.getElementById('paid_amount').value) || 0;

        const grandTotal = (subtotal - discount) + tax;
        const balance = grandTotal - paid;

        document.getElementById('subtotal').value = subtotal.toFixed(2);
        document.getElementById('grand_total').value = grandTotal.toFixed(2);
        document.getElementById('balance').value = balance.toFixed(2);
    }

    addRowBtn.addEventListener('click', function () {
        const row = `
            <tr>
                <td><input type="text" name="item_name[]" class="form-control" required></td>
                <td><input type="text" name="description[]" class="form-control"></td>
                <td><input type="number" step="0.01" name="qty[]" value="1" class="form-control qty" required></td>
                <td><input type="number" step="0.01" name="rate[]" value="0" class="form-control rate" required></td>
                <td><input type="number" step="0.01" class="form-control amount" value="0" readonly></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-row">X</button></td>
            </tr>
        `;
        tableBody.insertAdjacentHTML('beforeend', row);
    });

    document.addEventListener('input', function (e) {
        if (e.target.classList.contains('qty') ||
            e.target.classList.contains('rate') ||
            e.target.id === 'discount' ||
            e.target.id === 'tax' ||
            e.target.id === 'paid_amount') {
            calculateTotals();
        }
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row')) {
            if (tableBody.querySelectorAll('tr').length > 1) {
                e.target.closest('tr').remove();
                calculateTotals();
            }
        }
    });

    calculateTotals();
});
</script>

@endsection
