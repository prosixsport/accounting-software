@extends('layouts.app')

@section('content')

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h4 class="mb-0 fw-bold">Generate Payroll</h4>
    </div>

    <div class="card-body">

        <form action="{{ route('payrolls.store') }}" method="POST">
            @csrf

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">Employee *</label>
                    <select name="employee_id" class="form-select" required>
                        <option value="">Select Employee</option>

                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">
                                {{ $employee->employee_code }} - {{ $employee->name }}
                                | Rs {{ number_format($employee->basic_salary, 2) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Month *</label>
                    <input type="month"
                           name="month"
                           value="{{ date('Y-m') }}"
                           class="form-control"
                           required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Overtime Amount</label>
                    <input type="number"
                           step="0.01"
                           name="overtime_amount"
                           value="0"
                           class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Bonus</label>
                    <input type="number"
                           step="0.01"
                           name="bonus"
                           value="0"
                           class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Advance Amount</label>
                    <input type="number"
                           step="0.01"
                           name="advance_amount"
                           value="0"
                           class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Deduction Amount</label>
                    <input type="number"
                           step="0.01"
                           name="deduction_amount"
                           value="0"
                           class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Payment Status</label>
                    <select name="payment_status" class="form-select">
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Payment Date</label>
                    <input type="date"
                           name="payment_date"
                           class="form-control">
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Remarks</label>
                    <textarea name="remarks"
                              rows="3"
                              class="form-control"
                              placeholder="Optional remarks"></textarea>
                </div>

            </div>

            <div class="alert alert-info">
                Payroll attendance se automatic calculate hogi:
                Present Days, Absent Days, Basic Salary, Gross Salary aur Net Salary.
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary">
                    Generate Payroll
                </button>

                <a href="{{ route('payrolls.index') }}" class="btn btn-secondary">
                    Cancel
                </a>
            </div>

        </form>

    </div>
</div>

@endsection
