@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Verify Salary</h2>
        <p class="text-muted mb-0">Select worker, payroll and verify biometric status.</p>
    </div>

    <a href="{{ route('salary-verifications.index') }}" class="btn btn-light">
        Back
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('salary-verifications.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Employee / Worker *</label>
                    <select name="employee_id" class="form-select" required>
                        <option value="">Select Employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">
                                {{ $employee->employee_name ?? $employee->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Payroll / Salary Slip</label>
                    <select name="payroll_id" class="form-select">
                        <option value="">Select Payroll</option>
                        @foreach($payrolls as $payroll)
                            <option value="{{ $payroll->id }}">
                                Payroll #{{ $payroll->id }}
                                @if($payroll->employee)
                                    - {{ $payroll->employee->employee_name ?? $payroll->employee->name }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Device Name</label>
                    <input type="text" name="device_name" class="form-control" value="DigitalPersona">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Verification Status *</label>
                    <select name="verification_status" class="form-select" required>
                        <option value="pending">Pending</option>
                        <option value="verified">Verified</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>

                <div class="col-md-8 mb-3">
                    <label class="form-label">Remarks</label>
                    <input type="text" name="remarks" class="form-control" placeholder="Example: Finger matched and salary slip issued">
                </div>
            </div>

            <div class="alert alert-info">
                <strong>Note:</strong> DigitalPersona scanner direct integration SDK se hoga.
                Abhi ye screen manual verification record save kar rahi hai.
            </div>

            <button class="btn btn-dark">
                <i class="bi bi-check-circle"></i> Save Verification
            </button>
        </form>
    </div>
</div>

@endsection
