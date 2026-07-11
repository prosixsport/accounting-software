@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-1">Add Contractor</h2>
        <p class="text-muted mb-0">
            Add contractor information, department, machine and documents.
        </p>
    </div>

    <a href="{{ route('contractors.index') }}"
       class="btn btn-light border">
        Back
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <strong>Please fix the following errors:</strong>

        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div id="ajaxAlert"></div>

<form action="{{ route('contractors.store') }}"
      method="POST"
      enctype="multipart/form-data">

    @csrf

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">Basic Information</h5>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-3">
                    <label class="form-label">
                        Contractor Name *
                    </label>

                    <input type="text"
                           name="name"
                           value="{{ old('name') }}"
                           class="form-control @error('name') is-invalid @enderror"
                           required>

                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-lg-4 col-md-6 mb-3">
                    <label class="form-label">CNIC *</label>

                    <input type="text"
                           name="cnic"
                           id="cnic"
                           value="{{ old('cnic') }}"
                           class="form-control @error('cnic') is-invalid @enderror"
                           placeholder="35202-1234567-1"
                           maxlength="15"
                           required>

                    @error('cnic')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-lg-4 col-md-6 mb-3">
                    <label class="form-label">
                        Phone Number *
                    </label>

                    <input type="text"
                           name="phone"
                           value="{{ old('phone') }}"
                           class="form-control @error('phone') is-invalid @enderror"
                           placeholder="03001234567"
                           required>

                    @error('phone')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-lg-4 col-md-6 mb-3">
                    <label class="form-label">
                        Contractor Photo
                    </label>

                    <input type="file"
                           name="photo"
                           class="form-control"
                           accept="image/*">

                    <small class="text-muted">
                        JPG, PNG or WEBP. Maximum 3MB.
                    </small>
                </div>

                <div class="col-lg-4 col-md-6 mb-3">
                    <label class="form-label">Status *</label>

                    <select name="status"
                            class="form-select"
                            required>
                        <option value="active"
                            {{ old('status', 'active') === 'active' ? 'selected' : '' }}>
                            Active
                        </option>

                        <option value="inactive"
                            {{ old('status') === 'inactive' ? 'selected' : '' }}>
                            Inactive
                        </option>
                    </select>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Address</label>

                    <textarea name="address"
                              class="form-control"
                              rows="3">{{ old('address') }}</textarea>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Notes</label>

                    <textarea name="notes"
                              class="form-control"
                              rows="3">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">
                Department and Machine
            </h5>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <label class="form-label">
                        Department *
                    </label>

                    <div class="input-group">
                        <select name="contractor_department_id"
                                id="departmentSelect"
                                class="form-select"
                                required>
                            <option value="">
                                Select Department
                            </option>

                            @foreach($departments as $department)
                                <option value="{{ $department->id }}"
                                    {{ old('contractor_department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>

                        <button type="button"
                                class="btn btn-outline-primary"
                                id="editDepartmentBtn">
                            Edit
                        </button>

                        <button type="button"
                                class="btn btn-outline-danger"
                                id="deleteDepartmentBtn">
                            Delete
                        </button>
                    </div>

                    <div class="mt-3 p-3 bg-light rounded">
                        <label class="form-label fw-semibold">
                            Add New Department
                        </label>

                        <div class="input-group">
                            <input type="text"
                                   id="newDepartmentName"
                                   class="form-control"
                                   placeholder="Enter department name">

                            <button type="button"
                                    class="btn btn-dark"
                                    id="addDepartmentBtn">
                                Add
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <label class="form-label">
                        Machine
                    </label>

                    <div class="input-group">
                        <select name="contractor_machine_id"
                                id="machineSelect"
                                class="form-select">
                            <option value="">
                                Select Department First
                            </option>
                        </select>

                        <button type="button"
                                class="btn btn-outline-primary"
                                id="editMachineBtn">
                            Edit
                        </button>

                        <button type="button"
                                class="btn btn-outline-danger"
                                id="deleteMachineBtn">
                            Delete
                        </button>
                    </div>

                    <div class="mt-3 p-3 bg-light rounded">
                        <label class="form-label fw-semibold">
                            Add New Machine
                        </label>

                        <div class="input-group">
                            <input type="text"
                                   id="newMachineName"
                                   class="form-control"
                                   placeholder="Enter machine name">

                            <button type="button"
                                    class="btn btn-dark"
                                    id="addMachineBtn">
                                Add
                            </button>
                        </div>

                        <small class="text-muted">
                            Select a department before adding a machine.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">Documents</h5>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-lg-6 mb-3">
                    <label class="form-label">
                        CNIC Front *
                    </label>

                    <input type="file"
                           name="cnic_front"
                           class="form-control"
                           accept="image/*"
                           required>
                </div>

                <div class="col-lg-6 mb-3">
                    <label class="form-label">
                        CNIC Back *
                    </label>

                    <input type="file"
                           name="cnic_back"
                           class="form-control"
                           accept="image/*"
                           required>
                </div>

                <div class="col-md-12">
                    <label class="form-label">
                        Other Documents
                    </label>

                    <input type="file"
                           name="other_documents[]"
                           id="otherDocuments"
                           class="form-control"
                           accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx"
                           multiple>

                    <small class="text-muted">
                        You can select multiple images, PDF or Word files.
                    </small>

                    <div id="selectedDocuments"
                         class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <button type="submit"
            class="btn btn-dark px-4">
        Save Contractor
    </button>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector(
        'meta[name="csrf-token"]'
    ).getAttribute('content');

    const departmentSelect =
        document.getElementById('departmentSelect');

    const machineSelect =
        document.getElementById('machineSelect');

    const oldMachineId =
        @json(old('contractor_machine_id'));

    function showAlert(message, type = 'success') {
        const alertContainer =
            document.getElementById('ajaxAlert');

        alertContainer.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show">
                ${message}
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert">
                </button>
            </div>
        `;
    }

    async function parseResponse(response) {
        const result = await response.json();

        if (!response.ok) {
            let message = result.message || 'Something went wrong.';

            if (result.errors) {
                message = Object.values(result.errors)
                    .flat()
                    .join('<br>');
            }

            throw new Error(message);
        }

        return result;
    }

    async function loadMachines(
        departmentId,
        selectedMachineId = null
    ) {
        machineSelect.innerHTML = `
            <option value="">Loading...</option>
        `;

        if (!departmentId) {
            machineSelect.innerHTML = `
                <option value="">
                    Select Department First
                </option>
            `;

            return;
        }

        try {
            const response = await fetch(
                `{{ route('contractor-machines.index') }}?department_id=${departmentId}`,
                {
                    headers: {
                        'Accept': 'application/json'
                    }
                }
            );

            const result = await parseResponse(response);

            machineSelect.innerHTML = `
                <option value="">Select Machine</option>
            `;

            result.machines.forEach(machine => {
                const option =
                    document.createElement('option');

                option.value = machine.id;
                option.textContent = machine.name;

                if (
                    selectedMachineId &&
                    String(selectedMachineId) ===
                    String(machine.id)
                ) {
                    option.selected = true;
                }

                machineSelect.appendChild(option);
            });

            if (result.machines.length === 0) {
                machineSelect.innerHTML = `
                    <option value="">
                        No machine added
                    </option>
                `;
            }
        } catch (error) {
            showAlert(error.message, 'danger');
        }
    }

    departmentSelect.addEventListener(
        'change',
        function () {
            loadMachines(this.value);
        }
    );

    if (departmentSelect.value) {
        loadMachines(
            departmentSelect.value,
            oldMachineId
        );
    }

    document.getElementById('addDepartmentBtn')
        .addEventListener('click', async function () {
            const nameInput =
                document.getElementById('newDepartmentName');

            const name = nameInput.value.trim();

            if (!name) {
                showAlert(
                    'Enter department name.',
                    'warning'
                );

                return;
            }

            try {
                const response = await fetch(
                    `{{ route('contractor-departments.store') }}`,
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type':
                                'application/json',
                            'Accept':
                                'application/json',
                            'X-CSRF-TOKEN':
                                csrfToken
                        },
                        body: JSON.stringify({
                            name: name
                        })
                    }
                );

                const result = await parseResponse(response);

                const option =
                    document.createElement('option');

                option.value = result.department.id;
                option.textContent =
                    result.department.name;

                option.selected = true;

                departmentSelect.appendChild(option);

                nameInput.value = '';

                await loadMachines(
                    result.department.id
                );

                showAlert(result.message);
            } catch (error) {
                showAlert(error.message, 'danger');
            }
        });

    document.getElementById('editDepartmentBtn')
        .addEventListener('click', async function () {
            const departmentId =
                departmentSelect.value;

            const selectedOption =
                departmentSelect.options[
                    departmentSelect.selectedIndex
                ];

            if (!departmentId) {
                showAlert(
                    'Select a department first.',
                    'warning'
                );

                return;
            }

            const newName = prompt(
                'Enter department name:',
                selectedOption.textContent.trim()
            );

            if (!newName || !newName.trim()) {
                return;
            }

            try {
                const url =
                    `{{ url('/contractor-departments') }}/${departmentId}`;

                const response = await fetch(url, {
                    method: 'PUT',
                    headers: {
                        'Content-Type':
                            'application/json',
                        'Accept':
                            'application/json',
                        'X-CSRF-TOKEN':
                            csrfToken
                    },
                    body: JSON.stringify({
                        name: newName.trim()
                    })
                });

                const result = await parseResponse(response);

                selectedOption.textContent =
                    result.department.name;

                showAlert(result.message);
            } catch (error) {
                showAlert(error.message, 'danger');
            }
        });

    document.getElementById('deleteDepartmentBtn')
        .addEventListener('click', async function () {
            const departmentId =
                departmentSelect.value;

            if (!departmentId) {
                showAlert(
                    'Select a department first.',
                    'warning'
                );

                return;
            }

            if (!confirm(
                'Delete this department and its machines?'
            )) {
                return;
            }

            try {
                const url =
                    `{{ url('/contractor-departments') }}/${departmentId}`;

                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'Accept':
                            'application/json',
                        'X-CSRF-TOKEN':
                            csrfToken
                    }
                });

                const result = await parseResponse(response);

                departmentSelect
                    .querySelector(
                        `option[value="${departmentId}"]`
                    )
                    .remove();

                departmentSelect.value = '';
                machineSelect.innerHTML = `
                    <option value="">
                        Select Department First
                    </option>
                `;

                showAlert(result.message);
            } catch (error) {
                showAlert(error.message, 'danger');
            }
        });

    document.getElementById('addMachineBtn')
        .addEventListener('click', async function () {
            const departmentId =
                departmentSelect.value;

            const nameInput =
                document.getElementById('newMachineName');

            const name = nameInput.value.trim();

            if (!departmentId) {
                showAlert(
                    'Select a department first.',
                    'warning'
                );

                return;
            }

            if (!name) {
                showAlert(
                    'Enter machine name.',
                    'warning'
                );

                return;
            }

            try {
                const response = await fetch(
                    `{{ route('contractor-machines.store') }}`,
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type':
                                'application/json',
                            'Accept':
                                'application/json',
                            'X-CSRF-TOKEN':
                                csrfToken
                        },
                        body: JSON.stringify({
                            contractor_department_id:
                                departmentId,
                            name: name
                        })
                    }
                );

                const result = await parseResponse(response);

                if (
                    machineSelect.options.length === 1 &&
                    !machineSelect.options[0].value
                ) {
                    machineSelect.innerHTML = `
                        <option value="">
                            Select Machine
                        </option>
                    `;
                }

                const option =
                    document.createElement('option');

                option.value = result.machine.id;
                option.textContent =
                    result.machine.name;

                option.selected = true;

                machineSelect.appendChild(option);

                nameInput.value = '';

                showAlert(result.message);
            } catch (error) {
                showAlert(error.message, 'danger');
            }
        });

    document.getElementById('editMachineBtn')
        .addEventListener('click', async function () {
            const machineId = machineSelect.value;

            const selectedOption =
                machineSelect.options[
                    machineSelect.selectedIndex
                ];

            if (!machineId) {
                showAlert(
                    'Select a machine first.',
                    'warning'
                );

                return;
            }

            const newName = prompt(
                'Enter machine name:',
                selectedOption.textContent.trim()
            );

            if (!newName || !newName.trim()) {
                return;
            }

            try {
                const url =
                    `{{ url('/contractor-machines') }}/${machineId}`;

                const response = await fetch(url, {
                    method: 'PUT',
                    headers: {
                        'Content-Type':
                            'application/json',
                        'Accept':
                            'application/json',
                        'X-CSRF-TOKEN':
                            csrfToken
                    },
                    body: JSON.stringify({
                        name: newName.trim()
                    })
                });

                const result = await parseResponse(response);

                selectedOption.textContent =
                    result.machine.name;

                showAlert(result.message);
            } catch (error) {
                showAlert(error.message, 'danger');
            }
        });

    document.getElementById('deleteMachineBtn')
        .addEventListener('click', async function () {
            const machineId = machineSelect.value;

            if (!machineId) {
                showAlert(
                    'Select a machine first.',
                    'warning'
                );

                return;
            }

            if (!confirm('Delete this machine?')) {
                return;
            }

            try {
                const url =
                    `{{ url('/contractor-machines') }}/${machineId}`;

                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'Accept':
                            'application/json',
                        'X-CSRF-TOKEN':
                            csrfToken
                    }
                });

                const result = await parseResponse(response);

                machineSelect
                    .querySelector(
                        `option[value="${machineId}"]`
                    )
                    .remove();

                machineSelect.value = '';

                showAlert(result.message);
            } catch (error) {
                showAlert(error.message, 'danger');
            }
        });

    const cnicInput = document.getElementById('cnic');

    cnicInput.addEventListener('input', function () {
        let value = this.value.replace(/\D/g, '');

        value = value.substring(0, 13);

        if (value.length > 5) {
            value =
                value.substring(0, 5) +
                '-' +
                value.substring(5);
        }

        if (value.length > 13) {
            value =
                value.substring(0, 13) +
                '-' +
                value.substring(13);
        }

        this.value = value;
    });

    document.getElementById('otherDocuments')
        .addEventListener('change', function () {
            const container =
                document.getElementById(
                    'selectedDocuments'
                );

            container.innerHTML = '';

            Array.from(this.files).forEach(
                (file, index) => {
                    const item =
                        document.createElement('div');

                    item.className =
                        'border rounded px-3 py-2 mb-2 bg-light';

                    item.textContent =
                        `${index + 1}. ${file.name}`;

                    container.appendChild(item);
                }
            );
        });
});
</script>
@endpush
