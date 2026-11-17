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
                        <option @selected(old('category') === $category)>{{ $category }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6 col-xl">
                <label class="form-label" for="house-description">Item / details</label>
                <input class="form-control" id="house-description" name="description" maxlength="150" value="{{ old('description') }}" placeholder="e.g. 2 litres of milk" required>
            </div>
            <div class="col-sm-6 col-xl-2">
                <label class="form-label" for="house-amount">Amount (Rs)</label>
                <input class="form-control" id="house-amount" name="amount" type="number" min="0.01" max="999999999.99" step="0.01" value="{{ old('amount') }}" required>
            </div>
            <div class="col-auto"><button class="btn btn-success">Add Expense</button></div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th scope="col">Category</th><th scope="col">Item / details</th><th scope="col" class="text-end">Amount</th></tr></thead>
            <tbody>
                @forelse($entries as $entry)
                    <tr><td>{{ $entry->category }}</td><td>{{ $entry->description }}</td><td class="text-end text-nowrap">Rs {{ $money($entry->amount_paise) }}</td></tr>
                @empty
                    <tr><td colspan="3" class="text-center text-muted py-4">No expenses recorded for this date.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
