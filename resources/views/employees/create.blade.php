@extends('layouts.app')

@section('content')

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h4 class="mb-0 fw-bold">Add Employee / Worker</h4>
    </div>

    <div class="card-body">

        <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">Employee Name *</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter employee name" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Father Name</label>
                    <input type="text" name="father_name" class="form-control" placeholder="Enter father name">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control phone-input" placeholder="0300-1234567" maxlength="12">
                    <small class="text-muted">Example: 0300-1234567</small>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">CNIC</label>
                    <input type="text" name="cnic" class="form-control cnic-input" placeholder="35101-1234567-1" maxlength="15">
                    <small class="text-muted">Example: 35101-1234567-1</small>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="employee@email.com">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Department</label>
                    <input type="text" name="department" class="form-control" placeholder="Stitching / Cutting / Packing">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Designation</label>
                    <input type="text" name="designation" class="form-control" placeholder="Worker / Supervisor / Manager">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Basic Salary *</label>
                    <input type="number" step="0.01" name="basic_salary" class="form-control" placeholder="0.00" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Joining Date</label>
                    <input type="date" name="joining_date" class="form-control">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" rows="3" class="form-control" placeholder="Enter full address"></textarea>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">Pictures</label>
                    <input type="file" name="pictures[]" class="form-control" multiple accept="image/*">
                    <small class="text-muted">Upload multiple employee pictures</small>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">CNIC Pictures</label>
                    <input type="file" name="cnic_pictures[]" class="form-control" multiple accept="image/*">
                    <small class="text-muted">Upload CNIC front/back pictures</small>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">Other Documents</label>
                    <input type="file" name="other_documents[]" class="form-control" multiple accept="image/*,.pdf,.doc,.docx">
                    <small class="text-muted">Upload other documents</small>
                </div>

            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary">
                    Save Employee
                </button>

                <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                    Cancel
                </a>
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

            if (value.length > 11) {
                value = value.substring(0, 11);
            }

            if (value.length > 4) {
                value = value.substring(0, 4) + '-' + value.substring(4);
            }

            this.value = value;
        });
    }

    if (cnicInput) {
        cnicInput.addEventListener('input', function () {
            let value = this.value.replace(/\D/g, '');

            if (value.length > 13) {
                value = value.substring(0, 13);
            }

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
