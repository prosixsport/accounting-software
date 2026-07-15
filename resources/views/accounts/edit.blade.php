@extends('layouts.app')

@section('content')

<div class="card shadow-sm border-0">

    <div class="card-header bg-white">
        <h4 class="mb-0">Edit Account</h4>
    </div>

    <div class="card-body">

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('accounts.update', $account->id) }}">
            @csrf
            @method('PUT')

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">Account Code *</label>

                    <input
                        type="text"
                        name="code"
                        value="{{ old('code', $account->code) }}"
                        class="form-control @error('code') is-invalid @enderror"
                        required
                    >

                    @error('code')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Account Name *</label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $account->name) }}"
                        class="form-control @error('name') is-invalid @enderror"
                        required
                    >

                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Account Type *</label>

                    <select
                        name="type"
                        class="form-select @error('type') is-invalid @enderror"
                        required
                    >
                        <option value="">Select Account Type</option>

                        <option
                            value="asset"
                            {{ old('type', $account->type) == 'asset' ? 'selected' : '' }}
                        >
                            Asset
                        </option>

                        <option
                            value="liability"
                            {{ old('type', $account->type) == 'liability' ? 'selected' : '' }}
                        >
                            Liability
                        </option>

                        <option
                            value="equity"
                            {{ old('type', $account->type) == 'equity' ? 'selected' : '' }}
                        >
                            Equity
                        </option>

                        <option
                            value="income"
                            {{ old('type', $account->type) == 'income' ? 'selected' : '' }}
                        >
                            Income
                        </option>

                        <option
                            value="expense"
                            {{ old('type', $account->type) == 'expense' ? 'selected' : '' }}
                        >
                            Expense
                        </option>
                    </select>

                    @error('type')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Opening Balance</label>

                    <input
                        type="number"
                        step="0.01"
                        name="opening_balance"
                        value="{{ old('opening_balance', $account->opening_balance) }}"
                        class="form-control @error('opening_balance') is-invalid @enderror"
                    >

                    @error('opening_balance')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-12 mb-3">
                    <div class="form-check">

                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            id="is_active"
                            class="form-check-input"
                            {{ old('is_active', $account->is_active) ? 'checked' : '' }}
                        >

                        <label class="form-check-label" for="is_active">
                            Active Account
                        </label>

                    </div>
                </div>

            </div>

            <div class="d-flex gap-2">

                <button type="submit" class="btn btn-primary">
                    Update Account
                </button>

                <a href="{{ route('accounts.index') }}" class="btn btn-secondary">
                    Cancel
                </a>

            </div>

        </form>

    </div>
</div>

@endsection
