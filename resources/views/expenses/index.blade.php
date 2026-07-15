@extends('layouts.app')

@section('content')

@php
    function expenseIcon($text)
    {
        $name = strtolower((string) $text);

        if (str_contains($name, 'gas')) return 'bi-fire';
        if (str_contains($name, 'water')) return 'bi-droplet-fill';
        if (str_contains($name, 'electric')) return 'bi-lightning-charge-fill';
        if (str_contains($name, 'salary')) return 'bi-cash-stack';
        if (str_contains($name, 'worker')) return 'bi-people-fill';
        if (str_contains($name, 'contract')) return 'bi-person-workspace';
        if (str_contains($name, 'food')) return 'bi-cup-hot-fill';
        if (str_contains($name, 'tea')) return 'bi-cup-hot-fill';
        if (str_contains($name, 'fuel')) return 'bi-fuel-pump-fill';
        if (str_contains($name, 'petrol')) return 'bi-fuel-pump-fill';
        if (str_contains($name, 'diesel')) return 'bi-fuel-pump-fill';
        if (str_contains($name, 'transport')) return 'bi-truck';
        if (str_contains($name, 'rent')) return 'bi-house-door-fill';
        if (str_contains($name, 'internet')) return 'bi-wifi';
        if (str_contains($name, 'machine')) return 'bi-gear-fill';
        if (str_contains($name, 'repair')) return 'bi-tools';
        if (str_contains($name, 'office')) return 'bi-building-fill';
        if (str_contains($name, 'packing')) return 'bi-box-seam-fill';
        if (str_contains($name, 'cutting')) return 'bi-scissors';
        if (str_contains($name, 'stitch')) return 'bi-scissors';

        return 'bi-wallet2';
    }

    $canShowExpenses =
        !$selectedCategory
        || !$selectedCategoryHasSubCategories
        || $selectedSubCategory;
@endphp

<div class="expenses-page">

    <div class="expense-header">

        <div>
            <h3>Factory Expenses</h3>
            <p>Track daily, weekly, monthly and yearly factory expenses</p>
        </div>

        <a href="{{ route('expenses.create') }}"
           class="add-expense-button">

            <i class="bi bi-plus-lg"></i>
            Add Expense

        </a>

    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Period Filter --}}
    <div class="period-filter-card">

        <div class="period-tabs">

            @foreach([
                'day' => 'Daily',
                'week' => 'Weekly',
                'month' => 'Monthly',
                'year' => 'Yearly',
                'all' => 'All Time',
            ] as $value => $label)

                <a href="{{ route('expenses.index', array_filter([
                    'expense_category_id' => $selectedCategory,
                    'expense_sub_category_id' => $selectedSubCategory,
                    'period' => $value,
                    'date' => $selectedDate,
                    'month' => $selectedMonth,
                    'year' => $selectedYear,
                ])) }}"
                   class="period-tab {{ $period === $value ? 'active' : '' }}">

                    {{ $label }}

                </a>

            @endforeach

        </div>

        <form method="GET"
              action="{{ route('expenses.index') }}"
              class="period-form">

            <input type="hidden"
                   name="expense_category_id"
                   value="{{ $selectedCategory }}">

            <input type="hidden"
                   name="expense_sub_category_id"
                   value="{{ $selectedSubCategory }}">

            <input type="hidden"
                   name="period"
                   value="{{ $period }}">

            @if(in_array($period, ['day', 'week']))

                <input type="date"
                       name="date"
                       value="{{ $selectedDate }}"
                       class="form-control">

            @elseif($period === 'month')

                <input type="month"
                       name="month"
                       value="{{ $selectedMonth }}"
                       class="form-control">

            @elseif($period === 'year')

                <select name="year"
                        class="form-select">

                    @for($yearOption = now()->year - 10; $yearOption <= now()->year + 1; $yearOption++)

                        <option value="{{ $yearOption }}"
                            {{ $selectedYear === $yearOption ? 'selected' : '' }}>

                            {{ $yearOption }}

                        </option>

                    @endfor

                </select>

            @endif

            @if($period !== 'all')
                <button type="submit">
                    <i class="bi bi-funnel"></i>
                    Apply
                </button>
            @endif

        </form>

    </div>

    {{-- Summary --}}
    {{-- <div class="expense-summary-grid">

        <div class="expense-summary-card main-total">
            <i class="bi bi-wallet2"></i>

            <div>
                <span>{{ $periodLabel }} Total</span>
                <strong>Rs {{ number_format($totalExpense, 2) }}</strong>
            </div>
        </div>

        <div class="expense-summary-card">
            <i class="bi bi-receipt"></i>

            <div>
                <span>Expense Entries</span>
                <strong>{{ $expenseCount }}</strong>
            </div>
        </div>

        <div class="expense-summary-card">
            <i class="bi bi-cash"></i>

            <div>
                <span>Cash</span>
                <strong>Rs {{ number_format($cashTotal, 2) }}</strong>
            </div>
        </div>

        <div class="expense-summary-card">
            <i class="bi bi-bank"></i>

            <div>
                <span>Bank / Online</span>
                <strong>Rs {{ number_format($bankTotal, 2) }}</strong>
            </div>
        </div>

        <div class="expense-summary-card">
            <i class="bi bi-calculator"></i>

            <div>
                <span>Average Expense</span>
                <strong>Rs {{ number_format($averageExpense, 2) }}</strong>
            </div>
        </div>

    </div> --}}

    {{-- Categories --}}
    <div class="category-scroll">

        <a href="{{ route('expenses.index', [
            'period' => $period,
            'date' => $selectedDate,
            'month' => $selectedMonth,
            'year' => $selectedYear,
        ]) }}"
           class="category-card {{ !$selectedCategory ? 'active' : '' }}">

            <div class="category-icon">
                <i class="bi bi-grid-fill"></i>
            </div>

            <strong>All Expenses</strong>
            <small>{{ $expenseCount }} records</small>

        </a>

        @foreach($categories as $category)

            <a href="{{ route('expenses.index', [
                'expense_category_id' => $category->id,
                'period' => $period,
                'date' => $selectedDate,
                'month' => $selectedMonth,
                'year' => $selectedYear,
            ]) }}"
               class="category-card {{ (int) $selectedCategory === $category->id ? 'active' : '' }}">

                <div class="category-icon">
                    <i class="bi {{ expenseIcon($category->name) }}"></i>
                </div>

                <strong title="{{ $category->name }}">
                    {{ $category->name }}
                </strong>

                <small>
                    {{ $category->expenses_count }} expenses
                </small>

            </a>

        @endforeach

    </div>

    {{-- Subcategories --}}
    @if($selectedCategory && $selectedCategoryHasSubCategories)

        <div class="subcategory-card">

            <div class="subcategory-header">

                <div>
                    <strong>Sub Categories</strong>
                    <span>Select one to view expenses</span>
                </div>

                <a href="{{ route('expense-sub-categories.index') }}">
                    <i class="bi bi-gear"></i>
                    Manage
                </a>

            </div>

            <div class="subcategory-list">

                @foreach($subCategories as $subCategory)

                    <a href="{{ route('expenses.index', [
                        'expense_category_id' => $selectedCategory,
                        'expense_sub_category_id' => $subCategory->id,
                        'period' => $period,
                        'date' => $selectedDate,
                        'month' => $selectedMonth,
                        'year' => $selectedYear,
                    ]) }}"
                       class="{{ (int) $selectedSubCategory === $subCategory->id ? 'active' : '' }}">

                        <i class="bi {{ expenseIcon($subCategory->name) }}"></i>

                        {{ $subCategory->name }}

                        <span>{{ $subCategory->expenses_count }}</span>

                    </a>

                @endforeach

            </div>

        </div>

    @elseif($selectedCategory)

        <div class="direct-category-message">

            <i class="bi bi-check-circle-fill"></i>

            <div>
                <strong>Direct Category</strong>
                <span>
                    This category has no subcategories. Its expenses are shown directly below.
                </span>
            </div>

        </div>

    @endif

    {{-- Expense Table --}}
    @if($canShowExpenses)

        <div class="expense-table-card">

            <div class="expense-table-header">

                <div>
                    <h5>Expense Records</h5>
                    <p>{{ $periodLabel }}</p>
                </div>

                <strong>
                    Rs {{ number_format($totalExpense, 2) }}
                </strong>

            </div>

            <div class="table-responsive">

                <table class="table align-middle mb-0">

                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Expense</th>
                            <th>Category</th>
                            <th>Payment</th>
                            <th>Vendor</th>
                            <th>Amount</th>
                            <th>Receipt</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($expenses as $expense)

                            <tr>
                                <td>
                                    <strong>
                                        {{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}
                                    </strong>

                                    <small>
                                        {{ \Carbon\Carbon::parse($expense->expense_date)->format('l') }}
                                    </small>
                                </td>

                                <td>
                                    <strong>{{ $expense->expense_no }}</strong>

                                    <small>
                                        {{ \Illuminate\Support\Str::limit($expense->description, 45) ?: 'No description' }}
                                    </small>
                                </td>

                                <td>
                                    <span class="category-badge">
                                        {{ $expense->category?->name ?? $expense->category ?? '-' }}
                                    </span>

                                    @if($expense->subCategory)
                                        <small>
                                            {{ $expense->subCategory->name }}
                                        </small>
                                    @endif
                                </td>

                                <td>
                                    <span class="payment-badge">
                                        <i class="bi bi-credit-card"></i>
                                        {{ ucfirst($expense->payment_method) }}
                                    </span>
                                </td>

                                <td>
                                    {{ $expense->vendor_name ?: '-' }}
                                </td>

                                <td>
                                    <strong class="expense-amount">
                                        Rs {{ number_format($expense->amount, 2) }}
                                    </strong>
                                </td>

                                <td>
                                    @if($expense->receipt)

                                        @php
                                            $extension = strtolower(
                                                pathinfo(
                                                    $expense->receipt,
                                                    PATHINFO_EXTENSION
                                                )
                                            );
                                        @endphp

                                        <a href="{{ asset('storage/' . $expense->receipt) }}"
                                           target="_blank"
                                           class="receipt-button">

                                            @if(in_array($extension, [
                                                'jpg',
                                                'jpeg',
                                                'png',
                                                'webp',
                                            ]))
                                                <img src="{{ asset('storage/' . $expense->receipt) }}"
                                                     alt="Receipt">
                                            @else
                                                <i class="bi bi-file-earmark-pdf"></i>
                                            @endif

                                        </a>

                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                <td>
                               <div class="expense-actions">

    {{-- View Expense Slip --}}
    <a href="{{ route('expenses.show', $expense->id) }}"
       class="view-expense-action"
       title="View Expense Slip">

        <i class="bi bi-eye"></i>

    </a>

    {{-- Edit Expense --}}
    <a href="{{ route('expenses.edit', $expense->id) }}"
       class="edit-expense-action"
       title="Edit Expense">

        <i class="bi bi-pencil-square"></i>

    </a>

    {{-- Delete Expense --}}
    <form action="{{ route('expenses.destroy', $expense->id) }}"
          method="POST">

        @csrf
        @method('DELETE')

        <button type="submit"
                class="delete-expense-action"
                onclick="return confirm('Delete this expense?')"
                title="Delete Expense">

            <i class="bi bi-trash3"></i>

        </button>

    </form>

</div>
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="8">

                                    <div class="expense-empty-state">
                                        <i class="bi bi-folder2-open"></i>

                                        <strong>No expenses found</strong>

                                        <span>
                                            No expense entries exist for the selected period.
                                        </span>
                                    </div>

                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    @else

        <div class="expense-empty-state select-subcategory">
            <i class="bi bi-hand-index-thumb"></i>

            <strong>Select a subcategory</strong>

            <span>
                This category contains subcategories. Select one above to view its expenses.
            </span>
        </div>

    @endif

</div>

<style>
:root {
    --expense-black: #111111;
    --expense-white: #ffffff;
    --expense-border: #e4e6eb;
    --expense-light: #f7f8fa;
    --expense-muted: #737b88;
}

.expense-header,
.period-filter-card,
.expense-table-header,
.subcategory-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.expense-header {
    margin-bottom: 20px;
}

.expense-header h3 {
    margin: 0;
    font-size: 28px;
    font-weight: 900;
}

.expense-header p,
.expense-table-header p {
    margin: 3px 0 0;
    color: var(--expense-muted);
    font-size: 11px;
}

.add-expense-button {
    padding: 10px 15px;
    border-radius: 8px;
    color: white;
    background: #111;
    text-decoration: none;
    font-weight: 800;
}

.period-filter-card {
    margin-bottom: 16px;
    padding: 12px;
    gap: 15px;
    border: 1px solid var(--expense-border);
    border-radius: 12px;
    background: white;
}

.period-tabs,
.period-form {
    display: flex;
    align-items: center;
    gap: 7px;
}

.period-tab {
    padding: 8px 12px;
    border: 1px solid var(--expense-border);
    border-radius: 7px;
    color: #555;
    background: var(--expense-light);
    text-decoration: none;
    font-size: 10px;
    font-weight: 800;
}

.period-tab.active {
    border-color: #111;
    color: white;
    background: #111;
}

.period-form .form-control,
.period-form .form-select {
    min-width: 145px;
    font-size: 11px;
}

.period-form button {
    padding: 8px 12px;
    border: 0;
    border-radius: 7px;
    color: white;
    background: #111;
    font-size: 10px;
    font-weight: 800;
}

.expense-summary-grid {
    margin-bottom: 17px;
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 10px;
}

.expense-summary-card {
    min-height: 80px;
    padding: 13px;
    display: flex;
    align-items: center;
    gap: 10px;
    border: 1px solid var(--expense-border);
    border-radius: 11px;
    background: white;
}

.expense-summary-card > i {
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    color: white;
    background: #111;
}

.expense-summary-card span,
.expense-summary-card strong {
    display: block;
}

.expense-summary-card span {
    color: var(--expense-muted);
    font-size: 9px;
}

.expense-summary-card strong {
    margin-top: 2px;
    font-size: 14px;
}

.main-total {
    color: white;
    background: #111;
}

.main-total > i {
    color: #111;
    background: white;
}

.main-total span {
    color: #ccc;
}

.category-scroll {
    margin-bottom: 16px;
    display: flex;
    gap: 10px;
    overflow-x: auto;
    padding-bottom: 5px;
}

.category-card {
    min-width: 135px;
    width: 135px;
    height: 120px;
    padding: 12px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--expense-border);
    border-radius: 13px;
    color: #111;
    background: white;
    text-decoration: none;
    text-align: center;
}

.category-card.active {
    border-color: #111;
    color: white;
    background: #111;
}

.category-icon {
    width: 39px;
    height: 39px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    background: var(--expense-light);
    font-size: 17px;
}

.category-card.active .category-icon {
    color: #111;
    background: white;
}

.category-card strong {
    max-width: 100%;
    overflow: hidden;
    font-size: 10px;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.category-card small {
    margin-top: 4px;
    color: var(--expense-muted);
    font-size: 8px;
}

.category-card.active small {
    color: #ccc;
}

.subcategory-card,
.direct-category-message,
.expense-table-card,
.expense-empty-state {
    border: 1px solid var(--expense-border);
    border-radius: 13px;
    background: white;
}

.subcategory-card {
    margin-bottom: 16px;
    padding: 15px;
}

.subcategory-header {
    margin-bottom: 12px;
}

.subcategory-header span {
    display: block;
    color: var(--expense-muted);
    font-size: 9px;
}

.subcategory-list {
    display: flex;
    flex-wrap: wrap;
    gap: 7px;
}

.subcategory-list a {
    padding: 8px 11px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border: 1px solid var(--expense-border);
    border-radius: 50px;
    color: #111;
    background: var(--expense-light);
    text-decoration: none;
    font-size: 10px;
    font-weight: 800;
}

.subcategory-list a.active {
    color: white;
    background: #111;
}

.subcategory-list span {
    min-width: 20px;
    height: 20px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    color: white;
    background: #555;
    font-size: 8px;
}

.direct-category-message {
    margin-bottom: 16px;
    padding: 13px;
    display: flex;
    gap: 10px;
    color: #146c43;
    background: #f2fff8;
}

.direct-category-message span {
    display: block;
    font-size: 9px;
}

.expense-table-card {
    overflow: hidden;
}

.expense-table-header {
    padding: 15px 18px;
    border-bottom: 1px solid var(--expense-border);
}

.expense-table-header h5 {
    margin: 0;
    font-weight: 900;
}

.expense-table-header > strong {
    font-size: 18px;
}

.expense-table-card th {
    padding: 11px 14px;
    background: var(--expense-light);
    font-size: 9px;
    text-transform: uppercase;
}

.expense-table-card td {
    padding: 12px 14px;
    font-size: 10px;
}

.expense-table-card td small {
    display: block;
    margin-top: 3px;
    color: var(--expense-muted);
}

.category-badge,
.payment-badge {
    padding: 5px 8px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    border-radius: 50px;
    font-size: 9px;
    font-weight: 800;
}

.category-badge {
    color: white;
    background: #111;
}

.payment-badge {
    border: 1px solid var(--expense-border);
    background: var(--expense-light);
}

.expense-amount {
    color: #dc3545;
}

.receipt-button img,
.receipt-button {
    width: 38px;
    height: 38px;
}

.receipt-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    border: 1px solid var(--expense-border);
    border-radius: 7px;
    color: #dc3545;
    text-decoration: none;
}

.receipt-button img {
    object-fit: cover;
}

.expense-actions {
    display: flex;
    gap: 5px;
}

.expense-actions a,
.expense-actions button {
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #111;
    border-radius: 7px;
    color: #111;
    background: white;
}

.expense-actions button {
    border-color: #dc3545;
    color: #dc3545;
}

.expense-empty-state {
    padding: 45px;
    display: flex;
    flex-direction: column;
    align-items: center;
    color: var(--expense-muted);
    text-align: center;
}

.expense-empty-state > i {
    margin-bottom: 8px;
    color: #111;
    font-size: 30px;
}

.expense-empty-state span {
    margin-top: 4px;
    font-size: 10px;
}

.select-subcategory {
    margin-top: 16px;
}

@media(max-width: 1199px) {
    .expense-summary-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media(max-width: 767px) {
    .expense-header,
    .period-filter-card {
        align-items: stretch;
        flex-direction: column;
    }

    .period-tabs {
        overflow-x: auto;
    }

    .period-form {
        width: 100%;
    }

    .period-form .form-control,
    .period-form .form-select,
    .period-form button {
        flex: 1;
    }

    .expense-summary-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .expense-table-card table {
        min-width: 950px;
    }
}
</style>

@endsection
