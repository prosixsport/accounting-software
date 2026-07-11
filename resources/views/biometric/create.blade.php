@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Register Fingerprint</h2>
        <p class="text-muted mb-0">Select worker and prepare fingerprint registration.</p>
    </div>

    <a href="{{ route('biometric.index') }}" class="btn btn-light">
        Back
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('biometric.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
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

                <div class="col-md-3 mb-3">
                    <label class="form-label">Finger *</label>
                    <select name="finger_name" class="form-select" required>
                        <option value="Right Thumb">Right Thumb</option>
                        <option value="Left Thumb">Left Thumb</option>
                        <option value="Right Index">Right Index</option>
                        <option value="Left Index">Left Index</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Device Name</label>
                    <input type="text" name="device_name" class="form-control" value="DigitalPersona">
                </div>
            </div>

            <div class="alert alert-info">
                <strong>Note:</strong> This will create a biometric registration record.
                Actual fingerprint capture will connect later through DigitalPersona SDK / local service.
            </div>

            <button class="btn btn-dark">
                <i class="bi bi-check-circle"></i> Save Registration
            </button>
        </form>
    </div>
</div>

@endsection
