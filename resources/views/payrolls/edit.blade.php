@extends('layouts.app')

@section('content')

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h4 class="mb-0 fw-bold">Edit Payroll</h4>
    </div>

    <div class="card-body">

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="border rounded p-3">
                    <small class="text-muted">Employee</small>
                    <h6 class="mb-0">{{ $payroll->employee->name ?? '-' }}</h6>
                </div>
            </div>

            <div class="col-md-3">
                <div class="border rounded p-3">
                    <small class="text-muted">Month</small>
                    <h6 class="mb-0">{{ $payroll->month }}</h6>
                </div>
            </div>

            <div class="col-md-3">
                <div class="border rounded p-3">
                    <small class="text-muted">Present Days</small>
                    <h6 class="mb-0">{{ $payroll->present_days }}</h6>
                </div>
            </div>

            <div class="col-md-3">
                <div class="border rounded p-3">
                    <small class="text-muted">Absent Days</small>
                    <h6 class="mb-0">{{ $payroll->absent_days }}</h6>
                </div>
            </div>
        </div>

        <form action="{{ route('payrolls.update', $payroll->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">Overtime Amount</label>
                    <input type="number"
                           step="0.01"
                           name="overtime_amount"
                           value="{{ $payroll->overtime_amount }}"
                           class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Bonus</label>
                    <input type="number"
                           step="0.01"
                           name="bonus"
                           value="{{ $payroll->bonus }}"
                           class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Advance Amount</label>
                    <input type="number"
                           step="0.01"
                           name="advance_amount"
                           value="{{ $payroll->advance_amount }}"
                           class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Deduction Amount</label>
                    <input type="number"
                           step="0.01"
                           name="deduction_amount"
                           value="{{ $payroll->deduction_amount }}"
                           class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Payment Status</label>
                    <select name="payment_status" class="form-select">
                        <option value="pending" {{ $payroll->payment_status == 'pending' ? 'selected' : '' }}>
                            Pending
                        </option>
                        <option value="paid" {{ $payroll->payment_status == 'paid' ? 'selected' : '' }}>
                            Paid
                        </option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Payment Date</label>
                    <input type="date"
                           name="payment_date"
                           value="{{ $payroll->payment_date }}"
                           class="form-control">
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Remarks</label>
                    <textarea name="remarks"
                              rows="3"
                              class="form-control">{{ $payroll->remarks }}</textarea>
                </div>

            </div>

            <div class="alert alert-light border">
                <strong>Gross Salary:</strong>
                Rs {{ number_format($payroll->gross_salary, 2) }}
                <br>
                <strong>Net Salary:</strong>
                Rs {{ number_format($payroll->net_salary, 2) }}
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary">
                    Update Payroll
                </button>

                <a href="{{ route('payrolls.index') }}" class="btn btn-secondary">
                    Cancel
                </a>
            </div>

        </form>

    </div>
</div>

@endsection
