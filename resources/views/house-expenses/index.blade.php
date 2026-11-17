@extends('layouts.app')

@section('content')
@php
    $money = static fn ($paise) => number_format(intdiv((int) $paise, 100))
        . '.' . str_pad((string) ((int) $paise % 100), 2, '0', STR_PAD_LEFT);
@endphp

<div class="mb-4">
    <h1 class="h3 mb-1">House Expense</h1>
    <p class="text-muted mb-0">Record household spending one item at a time.</p>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body d-flex flex-wrap align-items-end justify-content-between gap-3">
        <div>
            <div class="text-muted small">Total for {{ \Illuminate\Support\Carbon::parse($date)->format('d M Y') }}</div>
            <div class="h2 mb-0 text-success">Rs {{ $money($totalPaise) }}</div>
        </div>
        <form method="GET" action="{{ route('house-expenses.index') }}" class="d-flex align-items-end gap-2">
            <div>
                <label class="form-label" for="house-filter-date">View date</label>
                <input class="form-control" id="house-filter-date" name="date" type="date" value="{{ $date }}" required>
            </div>
            <button class="btn btn-outline-secondary">View</button>
        </form>
    </div>
</div>

@if(session('house_expense_saved'))
    <div class="alert alert-success" role="status">{{ session('house_expense_saved') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="card shadow-sm mb-3">
    <div class="card-body d-flex flex-wrap gap-4">
        <form method="POST" action="{{ route('house-expenses.categories.store') }}" class="d-flex flex-wrap align-items-end gap-2">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">
            <div>
                <label class="form-label" for="house-new-category">New category</label>
                <input class="form-control" id="house-new-category" name="name" maxlength="40" value="{{ old('name') }}" required>
            </div>
            <button class="btn btn-outline-success">Add Category</button>
        </form>
        <form method="GET" action="{{ route('house-expenses.print') }}" target="_blank" class="d-flex flex-wrap align-items-end gap-2">
            <div>
                <label class="form-label" for="house-print-from">Print from</label>
                <input class="form-control" id="house-print-from" name="from" type="date" value="{{ $date }}" required>
            </div>
            <div>
                <label class="form-label" for="house-print-to">Print through</label>
                <input class="form-control" id="house-print-to" name="to" type="date" value="{{ $date }}" required>
            </div>
            <button class="btn btn-outline-primary">Open Print Report</button>
        </form>
    </div>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <h2 class="h5">Add expense</h2>
        <form method="POST" action="{{ route('house-expenses.store', ['date' => $date]) }}" class="row g-3 align-items-end">
            @csrf
            <div class="col-sm-6 col-xl-2">
                <label class="form-label" for="house-date">Expense date</label>
                <input class="form-control" id="house-date" name="expense_date" type="date" value="{{ old('expense_date', $date) }}" required>
            </div>
            <div class="col-sm-6 col-xl-3">
                <label class="form-label" for="house-category">Category</label>
                <select class="form-select" id="house-category" name="category" required>
                    <option value="">Select category</option>
                    @foreach($categories as $category)
                        <option @selected(old('category', session('house_category_selected')) === $category)>{{ $category }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6 col-xl-3">
                <label class="form-label" for="house-taken-by">Money taken by</label>
                <input class="form-control" id="house-taken-by" name="money_taken_by" maxlength="100" value="{{ old('money_taken_by') }}" placeholder="e.g. Ali" required>
                <small class="text-muted">Who took the money?</small>
            </div>
            <div class="col-sm-6 col-xl-3">
                <label class="form-label" for="house-received-from">Money received from</label>
                <input class="form-control" id="house-received-from" name="money_received_from" maxlength="100" value="{{ old('money_received_from') }}" placeholder="e.g. Bilal or House Cash" required>
                <small class="text-muted">Who gave the money, or which fund was used?</small>
            </div>
            <div class="col-sm-6 col-xl-3">
                <label class="form-label" for="house-location">Shop / place</label>
                <input class="form-control" id="house-location" name="location" maxlength="150" value="{{ old('location') }}" placeholder="e.g. Local market" required>
            </div>
            <div class="col-sm-6 col-xl-2">
                <label class="form-label" for="house-amount">Amount (Rs)</label>
                <input class="form-control" id="house-amount" name="amount" type="number" min="0.01" max="999999999.99" step="0.01" value="{{ old('amount') }}" required>
            </div>
            <div class="col-12">
                <label class="form-label" for="house-description">Purpose / description</label>
                <textarea class="form-control" id="house-description" name="description" maxlength="150" rows="2" placeholder="e.g. Milk and bread for home" required>{{ old('description') }}</textarea>
            </div>
            <div class="col-12"><button class="btn btn-success">Add Expense</button></div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th scope="col">Category</th><th scope="col">Money taken by</th><th scope="col">Money received from</th><th scope="col">Purpose / description</th><th scope="col">Shop / place</th><th scope="col" class="text-end">Amount</th></tr></thead>
            <tbody>
                @forelse($entries as $entry)
                    <tr><td>{{ $entry->category }}</td><td>{{ $entry->money_taken_by ?? '—' }}</td><td>{{ $entry->money_received_from ?? '—' }}</td><td>{{ $entry->description }}</td><td>{{ $entry->location ?: '—' }}</td><td class="text-end text-nowrap">Rs {{ $money($entry->amount_paise) }}</td></tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No expenses recorded for this date.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
