@extends('layouts.app')

@section('content')

<div class="expense-category-page">

    {{-- PAGE HEADER --}}
    <div class="category-page-header">

        <div>
            <h3>Expense Categories</h3>

            <p>
                Manage main expense categories used for factory expenses
            </p>
        </div>

        <a href="{{ route('expenses.index') }}"
           class="back-expenses-button">

            <i class="bi bi-arrow-left"></i>
            Back to Expenses

        </a>

    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))

        <div class="alert alert-success category-alert">

            <i class="bi bi-check-circle-fill me-2"></i>

            {{ session('success') }}

        </div>

    @endif

    {{-- VALIDATION ERRORS --}}
    @if($errors->any())

        <div class="alert alert-danger category-alert">

            <div class="fw-bold mb-2">

                <i class="bi bi-exclamation-circle-fill me-2"></i>
                Please fix these errors:

            </div>

            <ul class="mb-0">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif

    {{-- SUMMARY CARDS --}}
    {{-- <div class="category-summary-grid">

        <div class="category-summary-card">

            <div class="summary-card-icon">
                <i class="bi bi-folder-fill"></i>
            </div>

            <div>
                <span>Total Categories</span>

                <strong>
                    {{ $categories->count() }}
                </strong>
            </div>

        </div>

        <div class="category-summary-card">

            <div class="summary-card-icon active-summary-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>

            <div>
                <span>Active Categories</span>

                <strong>
                    {{ $categories->where('status', true)->count() }}
                </strong>
            </div>

        </div>

        <div class="category-summary-card">

            <div class="summary-card-icon inactive-summary-icon">
                <i class="bi bi-x-circle-fill"></i>
            </div>

            <div>
                <span>Inactive Categories</span>

                <strong>
                    {{ $categories->where('status', false)->count() }}
                </strong>
            </div>

        </div>

    </div> --}}

    {{-- ADD CATEGORY --}}
    <div class="category-form-card">

        <div class="category-card-header">

            <div>
                <h4>Add New Category</h4>

                <p>
                    Create a main category for factory expenses
                </p>
            </div>

            <div class="header-card-icon">
                <i class="bi bi-folder-plus"></i>
            </div>

        </div>

        <form action="{{ route('expense-categories.store') }}"
              method="POST"
              class="category-create-form">

            @csrf

            <div class="row g-3 align-items-end">

                <div class="col-lg-7">

                    <label class="category-label">
                        Category Name
                        <span>*</span>
                    </label>

                    <div class="category-input-wrapper">

                        <i class="bi bi-folder"></i>

                        <input type="text"
                               name="name"
                               value="{{ old('name') }}"
                               class="form-control category-input @error('name') is-invalid @enderror"
                               placeholder="Example: Worker Salary"
                               required>

                    </div>

                    @error('name')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <div class="col-lg-3">

                    <label class="category-label">
                        Status
                    </label>

                    <div class="category-input-wrapper">

                        <i class="bi bi-toggle-on"></i>

                        <select name="status"
                                class="form-select category-input @error('status') is-invalid @enderror">

                            <option value="1"
                                {{ old('status', '1') === '1' ? 'selected' : '' }}>

                                Active

                            </option>

                            <option value="0"
                                {{ old('status') === '0' ? 'selected' : '' }}>

                                Inactive

                            </option>

                        </select>

                    </div>

                    @error('status')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <div class="col-lg-2">

                    <button type="submit"
                            class="add-category-button">

                        <i class="bi bi-plus-circle"></i>
                        Add Category

                    </button>

                </div>

            </div>

        </form>

    </div>

    {{-- CATEGORY LIST --}}
    <div class="category-list-card">

        <div class="category-card-header">

            <div>
                <h4>Category List</h4>

                <p>
                    Edit category names, status and manage records
                </p>
            </div>

            <div class="header-card-icon">
                <i class="bi bi-list-ul"></i>
            </div>

        </div>

        <div class="table-responsive">

            <table class="table category-table align-middle mb-0">

                <thead>

                    <tr>
                        <th>Category</th>
                        <th>Sub Categories</th>
                        <th>Expenses</th>
                        <th>Status</th>
                        <th class="text-center">
                            Actions
                        </th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($categories as $category)

                        <tr>

                            <td>

                                <form action="{{ route('expense-categories.update', $category->id) }}"
                                      method="POST"
                                      class="category-update-form">

                                    @csrf
                                    @method('PUT')

                                    <div class="category-name-cell">

                                        <div class="category-row-icon">

                                            <i class="bi bi-folder-fill"></i>

                                        </div>

                                        <div class="category-edit-field">

                                            <label>
                                                Category Name
                                            </label>

                                            <input type="text"
                                                   name="name"
                                                   value="{{ old('name_' . $category->id, $category->name) }}"
                                                   class="form-control row-category-input"
                                                   required>

                                        </div>

                                    </div>

                            </td>

                            <td>

                                <div class="count-info">

                                    <strong>
                                        {{ $category->sub_categories_count ?? $category->subCategories->count() }}
                                    </strong>

                                    <span>
                                        Sub Categories
                                    </span>

                                </div>

                            </td>

                            <td>

                                <div class="count-info">

                                    <strong>
                                        {{ $category->expenses_count ?? $category->expenses->count() }}
                                    </strong>

                                    <span>
                                        Expense Records
                                    </span>

                                </div>

                            </td>

                            <td>

                                <select name="status"
                                        class="form-select row-status-select">

                                    <option value="1"
                                        {{ $category->status ? 'selected' : '' }}>

                                        Active

                                    </option>

                                    <option value="0"
                                        {{ !$category->status ? 'selected' : '' }}>

                                        Inactive

                                    </option>

                                </select>

                            </td>

                            <td>

                                <div class="category-actions">

                                    {{-- UPDATE --}}
                                    <button type="submit"
                                            class="category-action-button update-category-button"
                                            title="Update Category">

                                        <i class="bi bi-check2-circle"></i>

                                    </button>

                                    </form>

                                    {{-- MANAGE SUB CATEGORIES --}}
                                    <a href="{{ route('expense-sub-categories.index', [
                                        'expense_category_id' => $category->id
                                    ]) }}"
                                       class="category-action-button subcategory-action-button"
                                       title="Manage Sub Categories">

                                        <i class="bi bi-diagram-3"></i>

                                    </a>

                                    {{-- VIEW EXPENSES --}}
                                    <a href="{{ route('expenses.index', [
                                        'expense_category_id' => $category->id
                                    ]) }}"
                                       class="category-action-button view-category-button"
                                       title="View Expenses">

                                        <i class="bi bi-eye"></i>

                                    </a>

                                    {{-- DELETE --}}
                                    <form action="{{ route('expense-categories.destroy', $category->id) }}"
                                          method="POST"
                                          class="category-delete-form">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="category-action-button delete-category-button"
                                                title="Delete Category"
                                                onclick="return confirm('Delete {{ addslashes($category->name) }} category? Its related records may also be affected.')">

                                            <i class="bi bi-trash3"></i>

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5">

                                <div class="category-empty-state">

                                    <div class="empty-category-icon">
                                        <i class="bi bi-folder-x"></i>
                                    </div>

                                    <h5>
                                        No Categories Found
                                    </h5>

                                    <p>
                                        Add your first expense category using the form above.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

<style>
:root {
    --category-black: #111111;
    --category-white: #ffffff;
    --category-border: #e4e6eb;
    --category-light: #f7f8fa;
    --category-muted: #727a86;
    --category-green: #198754;
    --category-red: #dc3545;
    --category-blue: #0d6efd;
}

.expense-category-page {
    color: var(--category-black);
}

/* Header */

.category-page-header {
    margin-bottom: 22px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}

.category-page-header h3 {
    margin: 0;
    font-size: 29px;
    font-weight: 900;
}

.category-page-header p {
    margin: 4px 0 0;
    color: var(--category-muted);
    font-size: 13px;
}

.back-expenses-button {
    min-height: 42px;
    padding: 9px 15px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    border: 1px solid var(--category-black);
    border-radius: 8px;
    color: var(--category-white);
    background: var(--category-black);
    text-decoration: none;
    font-size: 13px;
    font-weight: 900;
    transition: 0.2s ease;
}

.back-expenses-button:hover {
    color: var(--category-black);
    background: var(--category-white);
}

.category-alert {
    border-radius: 10px;
}

/* Summary */

.category-summary-grid {
    margin-bottom: 18px;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}

.category-summary-card {
    min-height: 94px;
    padding: 16px;
    display: flex;
    align-items: center;
    gap: 13px;
    border: 1px solid var(--category-border);
    border-radius: 12px;
    background: var(--category-white);
    box-shadow: 0 5px 16px rgba(17, 24, 39, 0.05);
}

.summary-card-icon {
    flex: 0 0 45px;
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    color: var(--category-white);
    background: var(--category-black);
    font-size: 20px;
}

.active-summary-icon {
    background: var(--category-green);
}

.inactive-summary-icon {
    background: var(--category-red);
}

.category-summary-card span {
    display: block;
    margin-bottom: 2px;
    color: var(--category-muted);
    font-size: 11px;
    font-weight: 700;
}

.category-summary-card strong {
    display: block;
    font-size: 22px;
    font-weight: 900;
}

/* Cards */

.category-form-card,
.category-list-card {
    overflow: hidden;
    border: 1px solid var(--category-border);
    border-radius: 13px;
    background: var(--category-white);
    box-shadow: 0 6px 20px rgba(17, 24, 39, 0.05);
}

.category-form-card {
    margin-bottom: 18px;
}

.category-card-header {
    min-height: 76px;
    padding: 17px 19px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid var(--category-border);
}

.category-card-header h4 {
    margin: 0;
    font-size: 18px;
    font-weight: 900;
}

.category-card-header p {
    margin: 3px 0 0;
    color: var(--category-muted);
    font-size: 12px;
}

.header-card-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    color: var(--category-white);
    background: var(--category-black);
    font-size: 18px;
}

.category-create-form {
    padding: 18px;
}

/* Inputs */

.category-label {
    margin-bottom: 7px;
    color: var(--category-black);
    font-size: 11px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.35px;
}

.category-label span {
    color: var(--category-red);
}

.category-input-wrapper {
    position: relative;
}

.category-input-wrapper > i {
    position: absolute;
    top: 50%;
    left: 13px;
    z-index: 2;
    color: var(--category-black);
    font-size: 14px;
    transform: translateY(-50%);
    pointer-events: none;
}

.category-input {
    min-height: 46px;
    padding-left: 40px;
    border: 1px solid var(--category-border);
    border-radius: 8px;
    color: var(--category-black);
    background: var(--category-light);
    font-size: 13px;
    font-weight: 700;
    box-shadow: none !important;
}

.category-input:focus {
    border-color: var(--category-black);
    background: var(--category-white);
}

.add-category-button {
    width: 100%;
    min-height: 46px;
    padding: 9px 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    border: 1px solid var(--category-black);
    border-radius: 8px;
    color: var(--category-white);
    background: var(--category-black);
    font-size: 13px;
    font-weight: 900;
    transition: 0.2s ease;
}

.add-category-button:hover {
    color: var(--category-black);
    background: var(--category-white);
}

/* Table */

.category-table thead th {
    padding: 13px 16px;
    border-bottom: 1px solid var(--category-border);
    color: #5d6470;
    background: var(--category-light);
    font-size: 11px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    white-space: nowrap;
}

.category-table tbody td {
    padding: 14px 16px;
    border-bottom: 1px solid #edf0f2;
    font-size: 13px;
    vertical-align: middle;
}

.category-table tbody tr:hover {
    background: #fbfbfc;
}

.category-name-cell {
    min-width: 300px;
    display: flex;
    align-items: center;
    gap: 11px;
}

.category-row-icon {
    flex: 0 0 42px;
    width: 42px;
    height: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    color: var(--category-white);
    background: var(--category-black);
    font-size: 18px;
}

.category-edit-field {
    min-width: 0;
    flex: 1;
}

.category-edit-field label {
    display: block;
    margin-bottom: 4px;
    color: var(--category-muted);
    font-size: 9px;
    font-weight: 900;
    text-transform: uppercase;
}

.row-category-input,
.row-status-select {
    min-height: 41px;
    border: 1px solid var(--category-border);
    border-radius: 8px;
    color: var(--category-black);
    background: var(--category-light);
    font-size: 12px;
    font-weight: 700;
    box-shadow: none !important;
}

.row-category-input:focus,
.row-status-select:focus {
    border-color: var(--category-black);
    background: var(--category-white);
}

.row-status-select {
    min-width: 125px;
}

.count-info strong,
.count-info span {
    display: block;
}

.count-info strong {
    font-size: 18px;
    font-weight: 900;
}

.count-info span {
    margin-top: 2px;
    color: var(--category-muted);
    font-size: 10px;
}

/* Actions */

.category-actions {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.category-action-button {
    width: 36px;
    height: 36px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--category-black);
    border-radius: 7px;
    color: var(--category-black);
    background: var(--category-white);
    text-decoration: none;
    font-size: 14px;
    cursor: pointer;
    transition: 0.17s ease;
}

.category-action-button:hover {
    color: var(--category-white);
    background: var(--category-black);
    transform: translateY(-1px);
}

.update-category-button {
    border-color: var(--category-green);
    color: var(--category-green);
}

.update-category-button:hover {
    color: var(--category-white);
    background: var(--category-green);
}

.subcategory-action-button {
    border-color: #6f42c1;
    color: #6f42c1;
}

.subcategory-action-button:hover {
    color: var(--category-white);
    background: #6f42c1;
}

.view-category-button {
    border-color: var(--category-blue);
    color: var(--category-blue);
}

.view-category-button:hover {
    color: var(--category-white);
    background: var(--category-blue);
}

.delete-category-button {
    border-color: var(--category-red);
    color: var(--category-red);
}

.delete-category-button:hover {
    color: var(--category-white);
    background: var(--category-red);
}

/* Empty */

.category-empty-state {
    min-height: 240px;
    padding: 35px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: var(--category-muted);
    text-align: center;
}

.empty-category-icon {
    width: 60px;
    height: 60px;
    margin-bottom: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    color: var(--category-white);
    background: var(--category-black);
    font-size: 25px;
}

.category-empty-state h5 {
    margin: 0;
    color: var(--category-black);
    font-size: 17px;
    font-weight: 900;
}

.category-empty-state p {
    margin: 5px 0 0;
    font-size: 11px;
}

/* Responsive */

@media(max-width: 991px) {
    .category-page-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .back-expenses-button {
        width: 100%;
    }

    .category-summary-grid {
        grid-template-columns: 1fr;
    }

    .category-table {
        min-width: 950px;
    }
}

@media(max-width: 575px) {
    .category-page-header h3 {
        font-size: 24px;
    }

    .category-create-form {
        padding: 15px;
    }
}
</style>

@endsection
