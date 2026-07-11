@extends('layouts.app')

@section('content')

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h4 class="mb-0 fw-bold">Add Customer</h4>
    </div>

    <div class="card-body">

        <form action="{{ route('customers.store') }}" method="POST">
            @csrf

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">Customer Name *</label>
                    <input type="text" name="customer_name" class="form-control" placeholder="Enter customer name" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Company Name</label>
                    <input type="text" name="company_name" class="form-control" placeholder="Enter company name">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control phone-input" placeholder="0300-1234567" maxlength="12">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="customer@email.com">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Opening Balance</label>
                    <input type="number" step="0.01" name="opening_balance" value="0" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" rows="3" class="form-control" placeholder="Customer address"></textarea>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="3" class="form-control" placeholder="Optional notes"></textarea>
                </div>

            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary">Save Customer</button>
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a>
            </div>

        </form>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const phoneInput = document.querySelector('.phone-input');

    if (phoneInput) {
        phoneInput.addEventListener('input', function () {
            let value = this.value.replace(/\D/g, '');

            if (value.length > 11) {
                value = value.substring(0, 11);
            }

            if (value.length > 4) {
                value = value.substring(0, 4) + '-' + value.substring(4);
            }

            this.value = value;
        });
    }
});
</script>

@endsection
