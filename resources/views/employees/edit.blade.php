@extends('layouts.app')

@section('content')

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h4 class="mb-0 fw-bold">Edit Employee / Worker</h4>
    </div>

    <div class="card-body">

        <form action="{{ route('employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">Employee Name *</label>
                    <input type="text" name="name" value="{{ old('name', $employee->name) }}" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Father Name</label>
                    <input type="text" name="father_name" value="{{ old('father_name', $employee->father_name) }}" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $employee->phone) }}" class="form-control phone-input" placeholder="0300-1234567" maxlength="12">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">CNIC</label>
                    <input type="text" name="cnic" value="{{ old('cnic', $employee->cnic) }}" class="form-control cnic-input" placeholder="35101-1234567-1" maxlength="15">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $employee->email) }}" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Department</label>
                    <input type="text" name="department" value="{{ old('department', $employee->department) }}" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Designation</label>
                    <input type="text" name="designation" value="{{ old('designation', $employee->designation) }}" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Basic Salary *</label>
                    <input type="number" step="0.01" name="basic_salary" value="{{ old('basic_salary', $employee->basic_salary) }}" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Joining Date</label>
                    <input type="date" name="joining_date" value="{{ old('joining_date', optional($employee->joining_date)->format('Y-m-d')) }}" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="active" {{ old('status', $employee->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $employee->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" rows="3" class="form-control">{{ old('address', $employee->address) }}</textarea>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">Pictures</label>
                    <input type="file" name="pictures[]" class="form-control" multiple accept="image/*">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">CNIC Pictures</label>
                    <input type="file" name="cnic_pictures[]" class="form-control" multiple accept="image/*">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">Other Documents</label>
                    <input type="file" name="other_documents[]" class="form-control" multiple accept="image/*,.pdf,.doc,.docx">
                </div>

            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary">Update Employee</button>
                <a href="{{ route('employees.index') }}" class="btn btn-secondary">Cancel</a>
            </div>

        </form>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const phoneInput = document.querySelector('.phone-input');
    const cnicInput = document.querySelector('.cnic-input');

    if (phoneInput) {
        phoneInput.addEventListener('input', function () {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 11) value = value.substring(0, 11);
            if (value.length > 4) value = value.substring(0, 4) + '-' + value.substring(4);
            this.value = value;
        });
    }

    if (cnicInput) {
        cnicInput.addEventListener('input', function () {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 13) value = value.substring(0, 13);
            if (value.length > 5 && value.length <= 12) {
                value = value.substring(0, 5) + '-' + value.substring(5);
            } else if (value.length > 12) {
                value = value.substring(0, 5) + '-' + value.substring(5, 12) + '-' + value.substring(12);
            }
            this.value = value;
        });
    }
});
</script>

@endsection
