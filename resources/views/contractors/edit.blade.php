@extends('layouts.app')

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold mb-1">Edit Contractor</h2>

        <p class="text-muted mb-0">
            Update contractor information, department, machine and documents.
        </p>
    </div>

    <a href="{{ route('contractors.index') }}"
       class="btn btn-light border">

        <i class="bi bi-arrow-left me-1"></i>
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

<form action="{{ route('contractors.update', $contractor->id) }}"
      method="POST"
      enctype="multipart/form-data">

    @csrf
    @method('PUT')

    {{-- Basic Information --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">
                Basic Information
            </h5>
        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-lg-4 col-md-6 mb-3">
                    <label class="form-label">
                        Contractor Name *
                    </label>

                    <input type="text"
                           name="name"
                           value="{{ old('name', $contractor->name) }}"
                           class="form-control @error('name') is-invalid @enderror"
                           required>

                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-lg-4 col-md-6 mb-3">
                    <label class="form-label">
                        CNIC *
                    </label>

                    <input type="text"
                           name="cnic"
                           id="cnic"
                           value="{{ old('cnic', $contractor->cnic) }}"
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
                           value="{{ old('phone', $contractor->phone) }}"
                           class="form-control @error('phone') is-invalid @enderror"
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

                    @if($contractor->photo)
                        <div class="current-file-preview mb-3">

                            <img src="{{ asset('storage/' . ltrim($contractor->photo, '/')) }}"
                                 class="contractor-photo-preview"
                                 alt="{{ $contractor->name }}">

                            <div>
                                <strong>Current Photo</strong>

                                <small class="d-block text-muted">
                                    Choose a new image to replace it.
                                </small>
                            </div>

                        </div>
                    @endif

                    <input type="file"
                           name="photo"
                           id="photoInput"
                           class="form-control @error('photo') is-invalid @enderror"
                           accept="image/*">

                    <small class="text-muted">
                        JPG, PNG or WEBP. Maximum 10MB.
                    </small>

                    @error('photo')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                    <div id="newPhotoPreview"
                         class="mt-3"></div>
                </div>

                <div class="col-lg-4 col-md-6 mb-3">
                    <label class="form-label">
                        Status *
                    </label>

                    <select name="status"
                            class="form-select"
                            required>

                        <option value="active"
                            {{ old('status', $contractor->status) === 'active' ? 'selected' : '' }}>
                            Active
                        </option>

                        <option value="inactive"
                            {{ old('status', $contractor->status) === 'inactive' ? 'selected' : '' }}>
                            Inactive
                        </option>

                    </select>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">
                        Address
                    </label>

                    <textarea name="address"
                              class="form-control"
                              rows="3">{{ old('address', $contractor->address) }}</textarea>
                </div>

                <div class="col-md-12">
                    <label class="form-label">
                        Notes
                    </label>

                    <textarea name="notes"
                              class="form-control"
                              rows="3">{{ old('notes', $contractor->notes) }}</textarea>
                </div>

            </div>

        </div>
    </div>

    {{-- Department and Machine --}}
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
                                    {{ old(
                                        'contractor_department_id',
                                        $contractor->contractor_department_id
                                    ) == $department->id ? 'selected' : '' }}>

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
                                Loading machines...
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

    {{-- Documents --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">
                Contractor Documents
            </h5>
        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-lg-6 mb-4">

                    <label class="form-label">
                        CNIC Front
                    </label>

                    @if($contractor->cnic_front)
                        <div class="document-preview mb-3">

                            <img src="{{ asset('storage/' . ltrim($contractor->cnic_front, '/')) }}"
                                 alt="CNIC Front">

                            <div>
                                <strong>Current CNIC Front</strong>

                                <a href="{{ asset('storage/' . ltrim($contractor->cnic_front, '/')) }}"
                                   target="_blank"
                                   class="d-block small">
                                    View Image
                                </a>
                            </div>

                        </div>
                    @endif

                    <input type="file"
                           name="cnic_front"
                           class="form-control @error('cnic_front') is-invalid @enderror"
                           accept="image/*">

                    <small class="text-muted">
                        Leave empty to keep the current image.
                    </small>

                    @error('cnic_front')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <div class="col-lg-6 mb-4">

                    <label class="form-label">
                        CNIC Back
                    </label>

                    @if($contractor->cnic_back)
                        <div class="document-preview mb-3">

                            <img src="{{ asset('storage/' . ltrim($contractor->cnic_back, '/')) }}"
                                 alt="CNIC Back">

                            <div>
                                <strong>Current CNIC Back</strong>

                                <a href="{{ asset('storage/' . ltrim($contractor->cnic_back, '/')) }}"
                                   target="_blank"
                                   class="d-block small">
                                    View Image
                                </a>
                            </div>

                        </div>
                    @endif

                    <input type="file"
                           name="cnic_back"
                           class="form-control @error('cnic_back') is-invalid @enderror"
                           accept="image/*">

                    <small class="text-muted">
                        Leave empty to keep the current image.
                    </small>

                    @error('cnic_back')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <div class="col-md-12 mb-4">

                    <label class="form-label">
                        Add More Documents
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

                @if($contractor->documents->count() > 0)

                    <div class="col-md-12">

                        <label class="form-label fw-bold">
                            Existing Documents
                        </label>

                        <div class="row">

                            @foreach($contractor->documents as $document)

                                @php
                                    $documentUrl = asset(
                                        'storage/' . ltrim(
                                            $document->file_path,
                                            '/'
                                        )
                                    );

                                    $isImage = str_starts_with(
                                        $document->mime_type ?? '',
                                        'image/'
                                    );
                                @endphp

                                <div class="col-lg-4 col-md-6 mb-3">

                                    <div class="existing-document">

                                        <div class="document-icon">

                                            @if($isImage)
                                                <i class="bi bi-file-earmark-image"></i>
                                            @elseif(
                                                $document->mime_type ===
                                                'application/pdf'
                                            )
                                                <i class="bi bi-file-earmark-pdf"></i>
                                            @else
                                                <i class="bi bi-file-earmark-text"></i>
                                            @endif

                                        </div>

                                        <div class="document-info">

                                            <strong title="{{ $document->file_name }}">
                                                {{ \Illuminate\Support\Str::limit(
                                                    $document->file_name,
                                                    28
                                                ) }}
                                            </strong>

                                            <small class="d-block text-muted">
                                                {{ $document->file_size
                                                    ? number_format(
                                                        $document->file_size / 1024,
                                                        1
                                                    ) . ' KB'
                                                    : 'File' }}
                                            </small>

                                        </div>

                                        <a href="{{ $documentUrl }}"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-primary">
                                            View
                                        </a>

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    </div>

                @endif

            </div>

        </div>
    </div>

    <div class="d-flex flex-wrap gap-2">

        <button type="submit"
                class="btn btn-dark px-4">

            <i class="bi bi-check-lg me-1"></i>
            Update Contractor
        </button>

        <a href="{{ route('contractors.index') }}"
           class="btn btn-light border px-4">
            Cancel
        </a>

    </div>

</form>

<style>
.current-file-preview,
.document-preview {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    border: 1px solid #e2e7ee;
    border-radius: 12px;
    background: #f8fafc;
}

.contractor-photo-preview {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #e2e7ee;
    background: #ffffff;
}

.document-preview img {
    width: 90px;
    height: 65px;
    object-fit: cover;
    border: 1px solid #dde3eb;
    border-radius: 8px;
    background: #ffffff;
}

.existing-document {
    min-height: 75px;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    border: 1px solid #e2e7ee;
    border-radius: 12px;
    background: #ffffff;
}

.document-icon {
    width: 42px;
    height: 42px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    color: #0d6efd;
    background: #eaf2ff;
    font-size: 20px;
}

.document-info {
    min-width: 0;
    flex: 1;
}

.document-info strong {
    display: block;
    overflow: hidden;
    color: #111827;
    font-size: 13px;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.selected-file-item {
    padding: 9px 12px;
    margin-bottom: 7px;
    border: 1px solid #e2e7ee;
    border-radius: 8px;
    background: #f8fafc;
    font-size: 13px;
}

.new-photo-image {
    width: 90px;
    height: 90px;
    object-fit: cover;
    border: 2px solid #e2e7ee;
    border-radius: 50%;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute('content');

    const departmentSelect =
        document.getElementById('departmentSelect');

    const machineSelect =
        document.getElementById('machineSelect');

    const selectedMachineId =
        @json(old(
            'contractor_machine_id',
            $contractor->contractor_machine_id
        ));

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

        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    async function parseResponse(response) {

        const result = await response.json();

        if (!response.ok) {

            let message =
                result.message || 'Something went wrong.';

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
        machineId = null
    ) {

        if (!departmentId) {

            machineSelect.innerHTML = `
                <option value="">
                    Select Department First
                </option>
            `;

            return;
        }

        machineSelect.innerHTML = `
            <option value="">
                Loading...
            </option>
        `;

        try {

            const response = await fetch(
                `{{ route('contractor-machines.index') }}?department_id=${departmentId}`,
                {
                    headers: {
                        'Accept': 'application/json'
                    }
                }
            );

            const result =
                await parseResponse(response);

            machineSelect.innerHTML = `
                <option value="">
                    Select Machine
                </option>
            `;

            result.machines.forEach(function (machine) {

                const option =
                    document.createElement('option');

                option.value = machine.id;
                option.textContent = machine.name;

                if (
                    machineId &&
                    String(machineId) ===
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
            selectedMachineId
        );
    }

    document
        .getElementById('addDepartmentBtn')
        .addEventListener('click', async function () {

            const input =
                document.getElementById(
                    'newDepartmentName'
                );

            const name = input.value.trim();

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

                const result =
                    await parseResponse(response);

                const option =
                    document.createElement('option');

                option.value =
                    result.department.id;

                option.textContent =
                    result.department.name;

                option.selected = true;

                departmentSelect.appendChild(option);

                input.value = '';

                await loadMachines(
                    result.department.id
                );

                showAlert(result.message);

            } catch (error) {
                showAlert(error.message, 'danger');
            }
        });

    document
        .getElementById('editDepartmentBtn')
        .addEventListener('click', async function () {

            const departmentId =
                departmentSelect.value;

            const option =
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
                option.textContent.trim()
            );

            if (!newName || !newName.trim()) {
                return;
            }

            try {

                const response = await fetch(
                    `{{ url('/contractor-departments') }}/${departmentId}`,
                    {
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
                    }
                );

                const result =
                    await parseResponse(response);

                option.textContent =
                    result.department.name;

                showAlert(result.message);

            } catch (error) {
                showAlert(error.message, 'danger');
            }
        });

    document
        .getElementById('deleteDepartmentBtn')
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

                const response = await fetch(
                    `{{ url('/contractor-departments') }}/${departmentId}`,
                    {
                        method: 'DELETE',
                        headers: {
                            'Accept':
                                'application/json',
                            'X-CSRF-TOKEN':
                                csrfToken
                        }
                    }
                );

                const result =
                    await parseResponse(response);

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

    document
        .getElementById('addMachineBtn')
        .addEventListener('click', async function () {

            const departmentId =
                departmentSelect.value;

            const input =
                document.getElementById(
                    'newMachineName'
                );

            const name = input.value.trim();

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

                const result =
                    await parseResponse(response);

                const option =
                    document.createElement('option');

                option.value = result.machine.id;
                option.textContent =
                    result.machine.name;

                option.selected = true;

                machineSelect.appendChild(option);

                input.value = '';

                showAlert(result.message);

            } catch (error) {
                showAlert(error.message, 'danger');
            }
        });

    document
        .getElementById('editMachineBtn')
        .addEventListener('click', async function () {

            const machineId =
                machineSelect.value;

            const option =
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
                option.textContent.trim()
            );

            if (!newName || !newName.trim()) {
                return;
            }

            try {

                const response = await fetch(
                    `{{ url('/contractor-machines') }}/${machineId}`,
                    {
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
                    }
                );

                const result =
                    await parseResponse(response);

                option.textContent =
                    result.machine.name;

                showAlert(result.message);

            } catch (error) {
                showAlert(error.message, 'danger');
            }
        });

    document
        .getElementById('deleteMachineBtn')
        .addEventListener('click', async function () {

            const machineId =
                machineSelect.value;

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

                const response = await fetch(
                    `{{ url('/contractor-machines') }}/${machineId}`,
                    {
                        method: 'DELETE',
                        headers: {
                            'Accept':
                                'application/json',
                            'X-CSRF-TOKEN':
                                csrfToken
                        }
                    }
                );

                const result =
                    await parseResponse(response);

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

    const cnicInput =
        document.getElementById('cnic');

    cnicInput.addEventListener('input', function () {

        let value =
            this.value.replace(/\D/g, '');

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

    document
        .getElementById('photoInput')
        .addEventListener('change', function () {

            const preview =
                document.getElementById(
                    'newPhotoPreview'
                );

            preview.innerHTML = '';

            const file = this.files[0];

            if (!file) {
                return;
            }

            const image =
                document.createElement('img');

            image.src =
                URL.createObjectURL(file);

            image.className =
                'new-photo-image';

            image.onload = function () {
                URL.revokeObjectURL(image.src);
            };

            preview.appendChild(image);
        });

    document
        .getElementById('otherDocuments')
        .addEventListener('change', function () {

            const container =
                document.getElementById(
                    'selectedDocuments'
                );

            container.innerHTML = '';

            Array.from(this.files).forEach(
                function (file, index) {

                    const item =
                        document.createElement('div');

                    item.className =
                        'selected-file-item';

                    item.textContent =
                        `${index + 1}. ${file.name}`;

                    container.appendChild(item);
                }
            );
        });

});
</script>
@endpush

@endsection
