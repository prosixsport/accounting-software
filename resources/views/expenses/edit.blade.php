@extends('layouts.app')

@section('content')

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h4 class="mb-0 fw-bold">Edit Expense</h4>
    </div>

    <div class="card-body">
        <form action="{{ route('expenses.update', $expense->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">Expense Date *</label>
                    <input type="date" name="expense_date" value="{{ $expense->expense_date }}" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Category *</label>
                    <select name="expense_category_id" id="expense_category_id" class="form-select" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $expense->expense_category_id == $category->id ? 'selected' : '' }}>
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
                            <option value="{{ $account->id }}" {{ $expense->account_id == $account->id ? 'selected' : '' }}>
                                {{ $account->code }} - {{ $account->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Amount *</label>
                    <input type="number" step="0.01" name="amount" value="{{ $expense->amount }}" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Payment Method *</label>
                    <select name="payment_method" class="form-select" required>
                        <option value="cash" {{ $expense->payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="bank" {{ $expense->payment_method == 'bank' ? 'selected' : '' }}>Bank</option>
                        <option value="cheque" {{ $expense->payment_method == 'cheque' ? 'selected' : '' }}>Cheque</option>
                        <option value="online" {{ $expense->payment_method == 'online' ? 'selected' : '' }}>Online</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Paid By</label>
                    <input type="text" name="paid_by" value="{{ $expense->paid_by }}" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Vendor / Person</label>
                    <input type="text" name="vendor_name" value="{{ $expense->vendor_name }}" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Receipt / Bill</label>
                    <input type="file" name="receipt" class="form-control" accept="image/*,.pdf">

                    @if($expense->receipt)
                        <a href="{{ asset('storage/'.$expense->receipt) }}" target="_blank" class="btn btn-sm btn-outline-dark mt-2">
                            View Current Receipt
                        </a>
                    @endif
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="3" class="form-control">{{ $expense->description }}</textarea>
                </div>

            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary">Update Expense</button>
                <a href="{{ route('expenses.index', [
                    'expense_category_id' => $expense->expense_category_id,
                    'expense_sub_category_id' => $expense->expense_sub_category_id
                ]) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
const categories = @json($categories);
let selectedSubCategoryId = "{{ $expense->expense_sub_category_id }}";

document.addEventListener('DOMContentLoaded', function () {
    const categorySelect = document.getElementById('expense_category_id');
    const subCategorySelect = document.getElementById('expense_sub_category_id');

    function loadSubCategories() {
        const categoryId = categorySelect.value;

        subCategorySelect.innerHTML = '<option value="">Select Sub Category</option>';

        const category = categories.find(item => item.id == categoryId);

        if (category && category.sub_categories) {
            category.sub_categories.forEach(function (subCategory) {
                const option = document.createElement('option');
                option.value = subCategory.id;
                option.textContent = subCategory.name;

                if (selectedSubCategoryId == subCategory.id) {
                    option.selected = true;
                }

                subCategorySelect.appendChild(option);
            });
        }
    }

    loadSubCategories();

    categorySelect.addEventListener('change', function () {
        selectedSubCategoryId = "";
        loadSubCategories();
    });
});
</script>

@endsection
