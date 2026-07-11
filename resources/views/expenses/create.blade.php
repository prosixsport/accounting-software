@extends('layouts.app')

@section('content')

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h4 class="mb-0 fw-bold">Add Expense</h4>
    </div>

    <div class="card-body">
        <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">Expense Date *</label>
                    <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Category *</label>
                    <select name="expense_category_id" id="expense_category_id" class="form-select" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Sub Category</label>
                    <select name="expense_sub_category_id" id="expense_sub_category_id" class="form-select">
                        <option value="">Select Sub Category</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Expense Account</label>
                    <select name="account_id" class="form-select">
                        <option value="">Select Account</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}">
                                {{ $account->code }} - {{ $account->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Amount *</label>
                    <input type="number" step="0.01" name="amount" class="form-control" placeholder="0.00" required>
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
                    <label class="form-label">Paid By</label>
                    <input type="text" name="paid_by" class="form-control" placeholder="Person name">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Vendor / Person</label>
                    <input type="text" name="vendor_name" class="form-control" placeholder="Vendor or person name">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Receipt / Bill</label>
                    <input type="file" name="receipt" class="form-control" accept="image/*,.pdf">
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="3" class="form-control" placeholder="Expense details"></textarea>
                </div>

            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary">Save Expense</button>
                <a href="{{ route('expenses.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
const categories = @json($categories);

document.addEventListener('DOMContentLoaded', function () {
    const categorySelect = document.getElementById('expense_category_id');
    const subCategorySelect = document.getElementById('expense_sub_category_id');

    categorySelect.addEventListener('change', function () {
        const categoryId = this.value;

        subCategorySelect.innerHTML = '<option value="">Select Sub Category</option>';

        const category = categories.find(item => item.id == categoryId);

        if (category && category.sub_categories) {
            category.sub_categories.forEach(function (subCategory) {
                const option = document.createElement('option');
                option.value = subCategory.id;
                option.textContent = subCategory.name;
                subCategorySelect.appendChild(option);
            });
        }
    });
});
</script>

@endsection
