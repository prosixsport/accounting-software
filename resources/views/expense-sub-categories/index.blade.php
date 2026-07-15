@extends('layouts.app')

@section('content')

<div class="expense-subcategory-page">

    {{-- PAGE HEADER --}}
    <div class="subcategory-page-header">

        <div>
            <h3>Expense Sub Categories</h3>

            <p>
                Manage sub categories under main expense categories
            </p>
        </div>

        <div class="header-actions">

            <a href="{{ route('expense-categories.index') }}"
               class="outline-header-button">

                <i class="bi bi-folder"></i>
                Main Categories

            </a>

            <a href="{{ route('expenses.index') }}"
               class="dark-header-button">

                <i class="bi bi-arrow-left"></i>
                Back to Expenses

            </a>

        </div>

    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))

        <div class="alert alert-success page-alert">

            <i class="bi bi-check-circle-fill me-2"></i>

            {{ session('success') }}

        </div>

    @endif

    {{-- VALIDATION ERRORS --}}
    @if($errors->any())

        <div class="alert alert-danger page-alert">

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
    {{-- <div class="subcategory-summary-grid">

        <div class="subcategory-summary-card">

            <div class="summary-icon">
                <i class="bi bi-diagram-3-fill"></i>
            </div>

            <div>
                <span>Total Sub Categories</span>

                <strong>
                    {{ $subCategories->count() }}
                </strong>
            </div>

        </div>

        <div class="subcategory-summary-card">

            <div class="summary-icon active-summary-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>

            <div>
                <span>Active</span>

                <strong>
                    {{ $subCategories->where('status', true)->count() }}
                </strong>
            </div>

        </div>

        <div class="subcategory-summary-card">

            <div class="summary-icon inactive-summary-icon">
                <i class="bi bi-x-circle-fill"></i>
            </div>

            <div>
                <span>Inactive</span>

                <strong>
                    {{ $subCategories->where('status', false)->count() }}
                </strong>
            </div>

        </div>

        <div class="subcategory-summary-card">

            <div class="summary-icon expense-summary-icon">
                <i class="bi bi-receipt"></i>
            </div>

            <div>
                <span>Total Expense Records</span>

                <strong>
                    {{ $subCategories->sum('expenses_count') }}
                </strong>
            </div>

        </div>

    </div> --}}

    {{-- ADD SUB CATEGORY --}}
    <div class="subcategory-form-card">

        <div class="card-section-header">

            <div>
                <h4>Add New Sub Category</h4>

                <p>
                    Select a main category and create its sub category
                </p>
            </div>

            <div class="section-header-icon">
                <i class="bi bi-folder-plus"></i>
            </div>

        </div>

        <form action="{{ route('expense-sub-categories.store') }}"
              method="POST"
              class="subcategory-create-form">

            @csrf

            <div class="row g-3 align-items-end">

                <div class="col-lg-5">

                    <label class="field-label">
                        Main Category
                        <span>*</span>
                    </label>

                    <div class="input-icon-wrapper">

                        <i class="bi bi-folder-fill"></i>

                        <select name="expense_category_id"
                                class="form-select styled-input @error('expense_category_id') is-invalid @enderror"
                                required>

                            <option value="">
                                Select Category
                            </option>

                            @foreach($categories as $category)

                                <option value="{{ $category->id }}"
                                    {{ old('expense_category_id', request('expense_category_id')) == $category->id ? 'selected' : '' }}>

                                    {{ $category->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    @error('expense_category_id')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <div class="col-lg-4">

                    <label class="field-label">
                        Sub Category Name
                        <span>*</span>
                    </label>

                    <div class="input-icon-wrapper">

                        <i class="bi bi-diagram-2-fill"></i>

                        <input type="text"
                               name="name"
                               value="{{ old('name') }}"
                               class="form-control styled-input @error('name') is-invalid @enderror"
                               placeholder="Example: Tea / Lunch / Stitching Worker"
                               required>

                    </div>

                    @error('name')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <div class="col-lg-2">

                    <label class="field-label">
                        Status
                    </label>

                    <div class="input-icon-wrapper">

                        <i class="bi bi-toggle-on"></i>

                        <select name="status"
                                class="form-select styled-input @error('status') is-invalid @enderror">

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

                </div>

                <div class="col-lg-1">

                    <button type="submit"
                            class="add-subcategory-button"
                            title="Add Sub Category">

                        <i class="bi bi-plus-lg"></i>

                    </button>

                </div>

            </div>

        </form>

    </div>

    {{-- FILTER --}}
    <div class="subcategory-filter-card">

        <div class="row g-3 align-items-end">

            <div class="col-md-5">

                <label class="field-label">
                    Search Sub Category
                </label>

                <div class="input-icon-wrapper">

                    <i class="bi bi-search"></i>

                    <input type="text"
                           id="subcategorySearch"
                           class="form-control styled-input"
                           placeholder="Search category or sub category...">

                </div>

            </div>

            <div class="col-md-5">

                <label class="field-label">
                    Main Category
                </label>

                <div class="input-icon-wrapper">

                    <i class="bi bi-folder"></i>

                    <select id="mainCategoryFilter"
                            class="form-select styled-input">

                        <option value="">
                            All Main Categories
                        </option>

                        @foreach($categories as $category)

                            <option value="{{ strtolower(trim($category->name)) }}"
                                {{ request('expense_category_id') == $category->id ? 'selected' : '' }}>

                                {{ $category->name }}

                            </option>

                        @endforeach

                    </select>

                </div>

            </div>

            <div class="col-md-2">

                <button type="button"
                        id="resetSubcategoryFilters"
                        class="reset-filter-button">

                    <i class="bi bi-arrow-counterclockwise"></i>
                    Reset

                </button>

            </div>

        </div>

        <div class="filter-result">

            Showing

            <strong id="visibleSubcategoryCount">
                {{ $subCategories->count() }}
            </strong>

            of

            <strong>
                {{ $subCategories->count() }}
            </strong>

            sub categories

        </div>

    </div>

    {{-- SUB CATEGORY LIST --}}
    <div class="subcategory-list-card">

        <div class="card-section-header">

            <div>
                <h4>Sub Category List</h4>

                <p>
                    Edit category assignments, names and statuses
                </p>
            </div>

            <div class="section-header-icon">
                <i class="bi bi-list-ul"></i>
            </div>

        </div>

        <div class="table-responsive">

            <table class="table subcategory-table align-middle mb-0">

                <thead>

                    <tr>
                        <th>Main Category</th>
                        <th>Sub Category</th>
                        <th>Expenses</th>
                        <th>Status</th>
                        <th class="text-center">
                            Actions
                        </th>
                    </tr>

                </thead>

                <tbody id="subcategoryTableBody">

                    @forelse($subCategories as $subCategory)

                        @php
                            $updateFormId =
                                'updateSubCategoryForm'
                                . $subCategory->id;

                            $mainCategoryName = strtolower(
                                trim(
                                    $subCategory->category?->name
                                    ?? ''
                                )
                            );

                            $subCategoryName = strtolower(
                                trim($subCategory->name)
                            );
                        @endphp

                        <tr class="subcategory-row"
                            data-category="{{ $mainCategoryName }}"
                            data-subcategory="{{ $subCategoryName }}">

                            {{-- MAIN CATEGORY --}}
                            <td>

                                <div class="main-category-cell">

                                    <div class="category-icon-box">
                                        <i class="bi bi-folder-fill"></i>
                                    </div>

                                    <div class="category-select-area">

                                        <label>
                                            Main Category
                                        </label>

                                        <select name="expense_category_id"
                                                form="{{ $updateFormId }}"
                                                class="form-select table-input"
                                                required>

                                            @foreach($categories as $category)

                                                <option value="{{ $category->id }}"
                                                    {{ $subCategory->expense_category_id == $category->id ? 'selected' : '' }}>

                                                    {{ $category->name }}

                                                </option>

                                            @endforeach

                                        </select>

                                    </div>

                                </div>

                            </td>

                            {{-- SUB CATEGORY --}}
                            <td>

                                <div class="subcategory-name-area">

                                    <label>
                                        Sub Category Name
                                    </label>

                                    <input type="text"
                                           name="name"
                                           form="{{ $updateFormId }}"
                                           value="{{ $subCategory->name }}"
                                           class="form-control table-input"
                                           required>

                                </div>

                            </td>

                            {{-- EXPENSE COUNT --}}
                            <td>

                                <div class="expense-count-box">

                                    <div class="expense-count-icon">
                                        <i class="bi bi-receipt"></i>
                                    </div>

                                    <div>
                                        <strong>
                                            {{ $subCategory->expenses_count ?? 0 }}
                                        </strong>

                                        <span>
                                            Expense Records
                                        </span>
                                    </div>

                                </div>

                            </td>

                            {{-- STATUS --}}
                            <td>

                                <select name="status"
                                        form="{{ $updateFormId }}"
                                        class="form-select table-status-select">

                                    <option value="1"
                                        {{ $subCategory->status ? 'selected' : '' }}>

                                        Active

                                    </option>

                                    <option value="0"
                                        {{ !$subCategory->status ? 'selected' : '' }}>

                                        Inactive

                                    </option>

                                </select>

                            </td>

                            {{-- ACTIONS --}}
                            <td>

                                <div class="subcategory-actions">

                                    {{-- UPDATE FORM --}}
                                    <form id="{{ $updateFormId }}"
                                          action="{{ route('expense-sub-categories.update', $subCategory->id) }}"
                                          method="POST">

                                        @csrf
                                        @method('PUT')

                                    </form>

                                    {{-- UPDATE --}}
                                    <button type="submit"
                                            form="{{ $updateFormId }}"
                                            class="subcategory-action-button update-action"
                                            title="Update Sub Category">

                                        <i class="bi bi-check2-circle"></i>

                                    </button>

                                    {{-- VIEW EXPENSES --}}
                                    <a href="{{ route('expenses.index', [
                                        'expense_category_id' => $subCategory->expense_category_id,
                                        'expense_sub_category_id' => $subCategory->id,
                                    ]) }}"
                                       class="subcategory-action-button view-action"
                                       title="View Expenses">

                                        <i class="bi bi-eye"></i>

                                    </a>

                                    {{-- DELETE --}}
                                    <form action="{{ route('expense-sub-categories.destroy', $subCategory->id) }}"
                                          method="POST"
                                          class="delete-subcategory-form">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="subcategory-action-button delete-action"
                                                title="Delete Sub Category"
                                                onclick="return confirm('Delete {{ addslashes($subCategory->name) }} sub category?')">

                                            <i class="bi bi-trash3"></i>

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5">

                                <div class="subcategory-empty-state">

                                    <div class="empty-state-icon">
                                        <i class="bi bi-folder2-open"></i>
                                    </div>

                                    <h5>
                                        No Sub Categories Found
                                    </h5>

                                    <p>
                                        Add your first sub category using the form above.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                    <tr id="noSubcategoryFilterResult"
                        style="display: none;">

                        <td colspan="5">

                            <div class="subcategory-empty-state">

                                <div class="empty-state-icon">
                                    <i class="bi bi-search"></i>
                                </div>

                                <h5>
                                    No Matching Records
                                </h5>

                                <p>
                                    Change the search text or category filter.
                                </p>

                            </div>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>

<style>
:root {
    --sub-black: #111111;
    --sub-white: #ffffff;
    --sub-border: #e4e6eb;
    --sub-light: #f7f8fa;
    --sub-muted: #727a86;
    --sub-green: #198754;
    --sub-red: #dc3545;
    --sub-blue: #0d6efd;
    --sub-purple: #6f42c1;
}

.expense-subcategory-page {
    color: var(--sub-black);
}

/* Header */

.subcategory-page-header {
    margin-bottom: 22px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}

.subcategory-page-header h3 {
    margin: 0;
    font-size: 29px;
    font-weight: 900;
}

.subcategory-page-header p {
    margin: 4px 0 0;
    color: var(--sub-muted);
    font-size: 13px;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.outline-header-button,
.dark-header-button {
    min-height: 42px;
    padding: 9px 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    border: 1px solid var(--sub-black);
    border-radius: 8px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 900;
}

.outline-header-button {
    color: var(--sub-black);
    background: var(--sub-white);
}

.dark-header-button {
    color: var(--sub-white);
    background: var(--sub-black);
}

.page-alert {
    border-radius: 10px;
}

/* Summary */

.subcategory-summary-grid {
    margin-bottom: 18px;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
}

.subcategory-summary-card {
    min-height: 92px;
    padding: 15px;
    display: flex;
    align-items: center;
    gap: 12px;
    border: 1px solid var(--sub-border);
    border-radius: 12px;
    background: var(--sub-white);
    box-shadow: 0 5px 16px rgba(17, 24, 39, 0.05);
}

.summary-icon {
    flex: 0 0 44px;
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    color: var(--sub-white);
    background: var(--sub-black);
    font-size: 19px;
}

.active-summary-icon {
    background: var(--sub-green);
}

.inactive-summary-icon {
    background: var(--sub-red);
}

.expense-summary-icon {
    background: var(--sub-purple);
}

.subcategory-summary-card span {
    display: block;
    color: var(--sub-muted);
    font-size: 11px;
}

.subcategory-summary-card strong {
    display: block;
    margin-top: 2px;
    font-size: 22px;
    font-weight: 900;
}

/* Cards */

.subcategory-form-card,
.subcategory-filter-card,
.subcategory-list-card {
    overflow: hidden;
    border: 1px solid var(--sub-border);
    border-radius: 13px;
    background: var(--sub-white);
    box-shadow: 0 6px 20px rgba(17, 24, 39, 0.05);
}

.subcategory-form-card,
.subcategory-filter-card {
    margin-bottom: 18px;
}

.card-section-header {
    min-height: 76px;
    padding: 17px 19px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid var(--sub-border);
}

.card-section-header h4 {
    margin: 0;
    font-size: 18px;
    font-weight: 900;
}

.card-section-header p {
    margin: 3px 0 0;
    color: var(--sub-muted);
    font-size: 12px;
}

.section-header-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    color: var(--sub-white);
    background: var(--sub-black);
    font-size: 18px;
}

.subcategory-create-form,
.subcategory-filter-card {
    padding: 18px;
}

/* Inputs */

.field-label {
    margin-bottom: 7px;
    color: var(--sub-black);
    font-size: 11px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.35px;
}

.field-label span {
    color: var(--sub-red);
}

.input-icon-wrapper {
    position: relative;
}

.input-icon-wrapper > i {
    position: absolute;
    top: 50%;
    left: 13px;
    z-index: 2;
    color: var(--sub-black);
    font-size: 14px;
    transform: translateY(-50%);
    pointer-events: none;
}

.styled-input {
    min-height: 46px;
    padding-left: 40px;
    border: 1px solid var(--sub-border);
    border-radius: 8px;
    color: var(--sub-black);
    background: var(--sub-light);
    font-size: 13px;
    font-weight: 700;
    box-shadow: none !important;
}

.styled-input:focus {
    border-color: var(--sub-black);
    background: var(--sub-white);
}

.add-subcategory-button {
    width: 100%;
    min-height: 46px;
    border: 1px solid var(--sub-black);
    border-radius: 8px;
    color: var(--sub-white);
    background: var(--sub-black);
    font-size: 17px;
}

.reset-filter-button {
    width: 100%;
    min-height: 46px;
    padding: 9px 13px;
    border: 1px solid var(--sub-black);
    border-radius: 8px;
    color: var(--sub-black);
    background: var(--sub-white);
    font-size: 12px;
    font-weight: 900;
}

.reset-filter-button:hover {
    color: var(--sub-white);
    background: var(--sub-black);
}

.filter-result {
    margin-top: 14px;
    padding-top: 13px;
    border-top: 1px solid var(--sub-border);
    color: var(--sub-muted);
    font-size: 12px;
}

.filter-result strong {
    color: var(--sub-black);
}

/* Table */

.subcategory-table {
    min-width: 1050px;
}

.subcategory-table thead th {
    padding: 13px 16px;
    border-bottom: 1px solid var(--sub-border);
    color: #5d6470;
    background: var(--sub-light);
    font-size: 11px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    white-space: nowrap;
}

.subcategory-table tbody td {
    padding: 14px 16px;
    border-bottom: 1px solid #edf0f2;
    font-size: 13px;
}

.subcategory-table tbody tr:hover {
    background: #fbfbfc;
}

.main-category-cell {
    min-width: 290px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.category-icon-box {
    flex: 0 0 41px;
    width: 41px;
    height: 41px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    color: var(--sub-white);
    background: var(--sub-black);
    font-size: 17px;
}

.category-select-area,
.subcategory-name-area {
    min-width: 0;
    flex: 1;
}

.category-select-area label,
.subcategory-name-area label {
    display: block;
    margin-bottom: 4px;
    color: var(--sub-muted);
    font-size: 9px;
    font-weight: 900;
    text-transform: uppercase;
}

.table-input,
.table-status-select {
    min-height: 41px;
    border: 1px solid var(--sub-border);
    border-radius: 8px;
    color: var(--sub-black);
    background: var(--sub-light);
    font-size: 12px;
    font-weight: 700;
    box-shadow: none !important;
}

.table-input:focus,
.table-status-select:focus {
    border-color: var(--sub-black);
    background: var(--sub-white);
}

.subcategory-name-area {
    min-width: 260px;
}

.table-status-select {
    min-width: 120px;
}

.expense-count-box {
    min-width: 145px;
    display: flex;
    align-items: center;
    gap: 9px;
}

.expense-count-icon {
    width: 37px;
    height: 37px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    color: var(--sub-white);
    background: var(--sub-purple);
}

.expense-count-box strong,
.expense-count-box span {
    display: block;
}

.expense-count-box strong {
    font-size: 17px;
    font-weight: 900;
}

.expense-count-box span {
    color: var(--sub-muted);
    font-size: 9px;
}

/* Actions */

.subcategory-actions {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.subcategory-action-button {
    width: 36px;
    height: 36px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--sub-black);
    border-radius: 7px;
    color: var(--sub-black);
    background: var(--sub-white);
    text-decoration: none;
    font-size: 14px;
    cursor: pointer;
    transition: 0.17s ease;
}

.update-action {
    border-color: var(--sub-green);
    color: var(--sub-green);
}

.update-action:hover {
    color: var(--sub-white);
    background: var(--sub-green);
}

.view-action {
    border-color: var(--sub-blue);
    color: var(--sub-blue);
}

.view-action:hover {
    color: var(--sub-white);
    background: var(--sub-blue);
}

.delete-action {
    border-color: var(--sub-red);
    color: var(--sub-red);
}

.delete-action:hover {
    color: var(--sub-white);
    background: var(--sub-red);
}

/* Empty state */

.subcategory-empty-state {
    min-height: 230px;
    padding: 35px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: var(--sub-muted);
    text-align: center;
}

.empty-state-icon {
    width: 59px;
    height: 59px;
    margin-bottom: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    color: var(--sub-white);
    background: var(--sub-black);
    font-size: 25px;
}

.subcategory-empty-state h5 {
    margin: 0;
    color: var(--sub-black);
    font-size: 17px;
    font-weight: 900;
}

.subcategory-empty-state p {
    margin: 5px 0 0;
    font-size: 11px;
}

/* Responsive */

@media(max-width: 991px) {
    .subcategory-page-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .header-actions {
        width: 100%;
    }

    .outline-header-button,
    .dark-header-button {
        flex: 1;
    }

    .subcategory-summary-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media(max-width: 575px) {
    .subcategory-page-header h3 {
        font-size: 24px;
    }

    .header-actions {
        display: grid;
        grid-template-columns: 1fr;
    }

    .subcategory-summary-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById(
        'subcategorySearch'
    );

    const categoryFilter = document.getElementById(
        'mainCategoryFilter'
    );

    const resetButton = document.getElementById(
        'resetSubcategoryFilters'
    );

    const rows = document.querySelectorAll(
        '.subcategory-row'
    );

    const visibleCount = document.getElementById(
        'visibleSubcategoryCount'
    );

    const noResult = document.getElementById(
        'noSubcategoryFilterResult'
    );

    function normalize(value) {
        return String(value || '')
            .trim()
            .toLowerCase();
    }

    function filterRows() {
        const search = normalize(searchInput.value);
        const category = normalize(categoryFilter.value);

        let count = 0;

        rows.forEach(function (row) {
            const rowCategory = normalize(
                row.dataset.category
            );

            const rowSubcategory = normalize(
                row.dataset.subcategory
            );

            const matchesSearch =
                search === ''
                || rowCategory.includes(search)
                || rowSubcategory.includes(search);

            const matchesCategory =
                category === ''
                || rowCategory === category;

            const show =
                matchesSearch
                && matchesCategory;

            row.style.display = show ? '' : 'none';

            if (show) {
                count++;
            }
        });

        visibleCount.textContent = count;

        if (noResult) {
            noResult.style.display =
                rows.length > 0 && count === 0
                    ? ''
                    : 'none';
        }
    }

    function resetFilters() {
        searchInput.value = '';
        categoryFilter.value = '';

        filterRows();
    }

    searchInput.addEventListener(
        'input',
        filterRows
    );

    categoryFilter.addEventListener(
        'change',
        filterRows
    );

    resetButton.addEventListener(
        'click',
        resetFilters
    );

    filterRows();
});
</script>

@endsection
