@extends('layouts.app')

@section('content')

<div class="card shadow-sm border-0">
    <div class="card-header">
        <h4 class="mb-0">Add Account</h4>
    </div>

    <div class="card-body">

<form method="POST" action="{{ route('accounts.update',$account->id) }}">
    @method('PUT')
                @csrf

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label>Account Code</label>
                   <input type="text"
       name="code"
       value="{{ $account->code }}"
       class="form-control"
       required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Account Name</label>
                  <input type="text"
       name="code"
       value="{{ $account->code }}"
       class="form-control"
       required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Account Type</label>

                    <select name="type" class="form-select" required>
                        <option value="asset">Asset</option>
                        <option value="liability">Liability</option>
                        <option value="equity">Equity</option>
                        <option value="income">Income</option>
                        <option value="expense">Expense</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Opening Balance</label>
                    <input type="number"
                           step="0.01"
                           name="opening_balance"
                           value="0"
                           class="form-control">
                </div>

                <div class="col-md-12 mb-3">
                    <div class="form-check">
                        <input type="checkbox"
                               name="is_active"
                               checked
                               class="form-check-input">

                        <label class="form-check-label">
                            Active Account
                        </label>
                    </div>
                </div>

            </div>

            <button class="btn btn-primary">
                Save Account
            </button>

        </form>

    </div>
</div>

@endsection
