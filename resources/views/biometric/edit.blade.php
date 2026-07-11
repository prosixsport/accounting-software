@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Edit Fingerprint Registration</h2>
        <p class="text-muted mb-0">Update biometric registration details.</p>
    </div>

    <a href="{{ route('biometric.index') }}" class="btn btn-light">
        Back
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form action="{{ route('biometric.update', $biometric->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Employee / Worker *</label>
                    <select name="employee_id" class="form-select" required>
                        <option value="">Select Employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}"
                                {{ $biometric->employee_id == $employee->id ? 'selected' : '' }}>
                                {{ $employee->employee_name ?? $employee->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Finger *</label>
                    <select name="finger_name" class="form-select" required>
                        @foreach(['Right Thumb', 'Left Thumb', 'Right Index', 'Left Index'] as $finger)
                            <option value="{{ $finger }}" {{ $biometric->finger_name == $finger ? 'selected' : '' }}>
                                {{ $finger }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label">Device Name</label>
                    <input type="text"
                           name="device_name"
                           class="form-control"
                           value="{{ old('device_name', $biometric->device_name ?? 'DigitalPersona') }}">
                </div>
            </div>

            <div class="alert alert-warning">
                Current Status:
                @if($biometric->template_data)
                    <strong class="text-success">Fingerprint saved</strong>
                @else
                    <strong class="text-danger">Fingerprint capture pending</strong>
                @endif
            </div>

            <button class="btn btn-dark">
                <i class="bi bi-check-circle"></i> Update Registration
            </button>
        </form>
    </div>
</div>

@endsection
