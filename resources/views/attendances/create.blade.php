@extends('layouts.app')

@section('content')

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h4 class="mb-0 fw-bold">Add Attendance</h4>
    </div>

    <div class="card-body">
        <form action="{{ route('attendances.store') }}" method="POST">
            @csrf

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">Employee *</label>
                    <select name="employee_id" class="form-select" required>
                        <option value="">Select Employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">
                                {{ $employee->employee_code }} - {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Attendance Date *</label>
                    <input type="date"
                           name="attendance_date"
                           value="{{ date('Y-m-d') }}"
                           class="form-control"
                           required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-select" required>
                        <option value="present">Present</option>
                        <option value="absent">Absent</option>
                        <option value="leave">Leave</option>
                        <option value="half_day">Half Day</option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Overtime Hours</label>
                    <input type="number"
                           step="0.01"
                           name="overtime_hours"
                           value="0"
                           class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Check In</label>
                    <input type="time" name="check_in" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Check Out</label>
                    <input type="time" name="check_out" class="form-control">
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Remarks</label>
                    <textarea name="remarks"
                              rows="3"
                              class="form-control"
                              placeholder="Optional remarks"></textarea>
                </div>

            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary">Save Attendance</button>

                <a href="{{ route('attendances.index') }}" class="btn btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
