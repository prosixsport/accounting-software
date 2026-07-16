@extends('layouts.app')

@section('content')

@php
    $existingPictures = is_array($employee->pictures)
        ? $employee->pictures
        : (json_decode($employee->pictures ?? '[]', true) ?? []);

    $existingCnicPictures = is_array($employee->cnic_pictures)
        ? $employee->cnic_pictures
        : (json_decode($employee->cnic_pictures ?? '[]', true) ?? []);

    $existingDocuments = is_array($employee->other_documents)
        ? $employee->other_documents
        : (json_decode($employee->other_documents ?? '[]', true) ?? []);
@endphp

<div class="employee-edit-page">

    {{-- PAGE HEADER --}}
    <div class="employee-edit-header">

        <div>
            <h3 class="employee-edit-title">
                Edit Employee / Worker
            </h3>

            <p class="employee-edit-subtitle">
                Update employee information, salary, pictures and documents
            </p>
        </div>

        <div class="employee-header-actions">

            <a href="{{ route('employees.show', $employee->id) }}"
               class="employee-outline-btn">

                <i class="bi bi-eye"></i>
                View Employee

            </a>

            <a href="{{ route('employees.index') }}"
               class="employee-back-btn">

                <i class="bi bi-arrow-left"></i>
                Back to Employees

            </a>

        </div>

    </div>

    {{-- VALIDATION ERRORS --}}
    @if($errors->any())

        <div class="alert alert-danger employee-error-box">

            <div class="fw-bold mb-2">
                Please fix the following errors:
            </div>

            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>

    @endif

    <form action="{{ route('employees.update', $employee->id) }}"
          method="POST"
          enctype="multipart/form-data"
          id="employeeEditForm">

        @csrf
        @method('PUT')

        <div class="row g-4">

            {{-- LEFT SIDE --}}
            <div class="col-xl-4 col-lg-5">

                <div class="employee-upload-card">

                    {{-- EMPLOYEE PICTURES --}}
                    <div class="upload-section">

                        <div class="upload-section-header">

                            <div>
                                <h5>
                                    Employee Pictures
                                </h5>

                                <p>
                                    Existing and newly selected pictures
                                </p>
                            </div>

                            <div class="upload-header-icon">
                                <i class="bi bi-person-square"></i>
                            </div>

                        </div>

                        {{-- Existing Pictures --}}
                        @if(count($existingPictures) > 0)

                            <div class="existing-files-heading">
                                Existing Pictures

                                <span>
                                    {{ count($existingPictures) }}
                                </span>
                            </div>

                            <div class="preview-grid existing-preview-grid">

                                @foreach($existingPictures as $index => $picture)

                                    <div class="preview-image-card">

                                        <img src="{{ asset('storage/' . $picture) }}"
                                             alt="Employee Picture">

                                        <div class="preview-image-overlay">

                                            <a href="{{ asset('storage/' . $picture) }}"
                                               target="_blank"
                                               class="preview-action-btn"
                                               title="View picture">

                                                <i class="bi bi-eye"></i>
                                            </a>

                                            <a href="{{ asset('storage/' . $picture) }}"
                                               download="{{ basename($picture) }}"
                                               class="preview-action-btn"
                                               title="Download picture">

                                                <i class="bi bi-download"></i>
                                            </a>

                                        </div>

                                        <div class="preview-image-label">
                                            {{ $index === 0 ? 'Profile Picture' : 'Picture ' . ($index + 1) }}
                                        </div>

                                    </div>

                                @endforeach

                            </div>

                        @else

                            <div class="empty-preview-box">
                                <i class="bi bi-image"></i>
                                No employee pictures uploaded
                            </div>

                        @endif

                        <label for="pictures"
                               class="employee-upload-box mt-3">

                            <div class="employee-upload-icon">
                                <i class="bi bi-camera"></i>
                            </div>

                           <strong>
    Replace Employee Pictures
</strong>

<span>
    Selecting new pictures will remove old pictures
</span>

                            <span class="employee-upload-button">
                                Choose Pictures
                            </span>

                        </label>

                        <input type="file"
                               name="pictures[]"
                               id="pictures"
                               class="d-none"
                               multiple
                               accept="image/*">

                        <div id="picturesSelectedHeading"
                             class="existing-files-heading mt-3 d-none">

                            Newly Selected Pictures

                            <span id="picturesSelectedCount">
                                0
                            </span>

                        </div>

                        <div id="picturesPreview"
                             class="preview-grid mt-2"></div>

                        @error('pictures')
                            <div class="text-danger small mt-2">
                                {{ $message }}
                            </div>
                        @enderror

                        @error('pictures.*')
                            <div class="text-danger small mt-2">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    {{-- CNIC PICTURES --}}
                    <div class="upload-section">

                        <div class="upload-section-header">

                            <div>
                                <h5>
                                    CNIC Pictures
                                </h5>

                                <p>
                                    Front and back side of employee CNIC
                                </p>
                            </div>

                            <div class="upload-header-icon">
                                <i class="bi bi-person-vcard"></i>
                            </div>

                        </div>

                        {{-- Existing CNIC --}}
                        @if(count($existingCnicPictures) > 0)

                            <div class="existing-files-heading">
                                Existing CNIC Pictures

                                <span>
                                    {{ count($existingCnicPictures) }}
                                </span>
                            </div>

                            <div class="preview-grid existing-preview-grid">

                                @foreach($existingCnicPictures as $index => $cnicPicture)

                                    <div class="preview-image-card">

                                        <img src="{{ asset('storage/' . $cnicPicture) }}"
                                             alt="CNIC Picture">

                                        <div class="preview-image-overlay">

                                            <a href="{{ asset('storage/' . $cnicPicture) }}"
                                               target="_blank"
                                               class="preview-action-btn">

                                                <i class="bi bi-eye"></i>
                                            </a>

                                            <a href="{{ asset('storage/' . $cnicPicture) }}"
                                               download="{{ basename($cnicPicture) }}"
                                               class="preview-action-btn">

                                                <i class="bi bi-download"></i>
                                            </a>

                                        </div>

                                        <div class="preview-image-label">

                                            @if($index === 0)
                                                CNIC Front
                                            @elseif($index === 1)
                                                CNIC Back
                                            @else
                                                CNIC {{ $index + 1 }}
                                            @endif

                                        </div>

                                    </div>

                                @endforeach

                            </div>

                        @else

                            <div class="empty-preview-box">
                                <i class="bi bi-card-image"></i>
                                No CNIC pictures uploaded
                            </div>

                        @endif

                        <label for="cnic_pictures"
                               class="employee-upload-box mt-3">

                            <div class="employee-upload-icon">
                                <i class="bi bi-card-image"></i>
                            </div>

                            <strong>
    Replace CNIC Pictures
</strong>

<span>
    New CNIC images will replace existing CNIC images
</span>

                            <span class="employee-upload-button">
                                Choose CNIC Pictures
                            </span>

                        </label>

                        <input type="file"
                               name="cnic_pictures[]"
                               id="cnic_pictures"
                               class="d-none"
                               multiple
                               accept="image/*">

                        <div id="cnicSelectedHeading"
                             class="existing-files-heading mt-3 d-none">

                            Newly Selected CNIC

                            <span id="cnicSelectedCount">
                                0
                            </span>

                        </div>

                        <div id="cnicPreview"
                             class="preview-grid mt-2"></div>

                        @error('cnic_pictures')
                            <div class="text-danger small mt-2">
                                {{ $message }}
                            </div>
                        @enderror

                        @error('cnic_pictures.*')
                            <div class="text-danger small mt-2">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    {{-- OTHER DOCUMENTS --}}
                    <div class="upload-section border-0">

                        <div class="upload-section-header">

                            <div>
                                <h5>
                                    Other Documents
                                </h5>

                                <p>
                                    Employee additional files
                                </p>
                            </div>

                            <div class="upload-header-icon">
                                <i class="bi bi-folder2-open"></i>
                            </div>

                        </div>

                        {{-- Existing Documents --}}
                        @if(count($existingDocuments) > 0)

                            <div class="existing-files-heading">
                                Existing Documents

                                <span>
                                    {{ count($existingDocuments) }}
                                </span>
                            </div>

                            <div class="documents-preview-list">

                                @foreach($existingDocuments as $index => $document)

                                    @php
                                        $extension = strtolower(
                                            pathinfo(
                                                $document,
                                                PATHINFO_EXTENSION
                                            )
                                        );

                                        $isImage = in_array($extension, [
                                            'jpg',
                                            'jpeg',
                                            'png',
                                            'webp',
                                            'gif',
                                        ]);
                                    @endphp

                                    <div class="document-preview-row">

                                        <div class="document-preview-icon">

                                            @if($isImage)
                                                <i class="bi bi-file-earmark-image"></i>
                                            @elseif($extension === 'pdf')
                                                <i class="bi bi-file-earmark-pdf"></i>
                                            @elseif(in_array($extension, ['doc', 'docx']))
                                                <i class="bi bi-file-earmark-word"></i>
                                            @else
                                                <i class="bi bi-file-earmark"></i>
                                            @endif

                                        </div>

                                        <div class="document-preview-info">

                                            <strong title="{{ basename($document) }}">
                                                {{ basename($document) }}
                                            </strong>

                                            <small>
                                                Existing Document {{ $index + 1 }}
                                            </small>

                                        </div>

                                        <div class="document-row-actions">

                                            <a href="{{ asset('storage/' . $document) }}"
                                               target="_blank"
                                               class="small-document-action">

                                                <i class="bi bi-eye"></i>
                                            </a>

                                            <a href="{{ asset('storage/' . $document) }}"
                                               download="{{ basename($document) }}"
                                               class="small-document-action">

                                                <i class="bi bi-download"></i>
                                            </a>

                                        </div>

                                    </div>

                                @endforeach

                            </div>

                        @else

                            <div class="empty-preview-box">
                                <i class="bi bi-file-earmark"></i>
                                No other documents uploaded
                            </div>

                        @endif

                        <label for="other_documents"
                               class="employee-upload-box mt-3">

                            <div class="employee-upload-icon">
                                <i class="bi bi-file-earmark-arrow-up"></i>
                            </div>

                           <strong>
    Replace Documents
</strong>

<span>
    New documents will replace existing documents
</span>

                            <span class="employee-upload-button">
                                Choose Documents
                            </span>

                        </label>

                        <input type="file"
                               name="other_documents[]"
                               id="other_documents"
                               class="d-none"
                               multiple
                               accept="image/*,.pdf,.doc,.docx">

                        <div id="documentsSelectedHeading"
                             class="existing-files-heading mt-3 d-none">

                            Newly Selected Documents

                            <span id="documentsSelectedCount">
                                0
                            </span>

                        </div>

                        <div id="documentsPreview"
                             class="documents-preview-list mt-2"></div>

                        @error('other_documents')
                            <div class="text-danger small mt-2">
                                {{ $message }}
                            </div>
                        @enderror

                        @error('other_documents.*')
                            <div class="text-danger small mt-2">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

            </div>

            {{-- RIGHT SIDE --}}
            <div class="col-xl-8 col-lg-7">

                <div class="employee-form-card">

                    <div class="employee-form-card-header">

                        <div>
                            <h4>
                                Personal Information
                            </h4>

                            <p>
                                Update complete employee details
                            </p>
                        </div>

                        <div class="form-header-icon">
                            <i class="bi bi-pencil-square"></i>
                        </div>

                    </div>

                    <div class="employee-form-card-body">

                        <div class="row g-3">

                            {{-- Employee Name --}}
                            <div class="col-md-6">

                                <div class="employee-field">

                                    <label class="form-label">
                                        Employee Name
                                        <span>*</span>
                                    </label>

                                    <div class="input-icon-wrapper">

                                        <i class="bi bi-person"></i>

                                        <input type="text"
                                               name="name"
                                               value="{{ old('name', $employee->name) }}"
                                               class="form-control employee-input @error('name') is-invalid @enderror"
                                               placeholder="Enter employee name"
                                               required>

                                    </div>

                                    @error('name')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                            </div>

                            {{-- Father Name --}}
                            <div class="col-md-6">

                                <div class="employee-field">

                                    <label class="form-label">
                                        Father Name
                                    </label>

                                    <div class="input-icon-wrapper">

                                        <i class="bi bi-person-badge"></i>

                                        <input type="text"
                                               name="father_name"
                                               value="{{ old('father_name', $employee->father_name) }}"
                                               class="form-control employee-input @error('father_name') is-invalid @enderror"
                                               placeholder="Enter father name">

                                    </div>

                                    @error('father_name')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                            </div>

                            {{-- Phone --}}
                            <div class="col-md-6">

                                <div class="employee-field">

                                    <label class="form-label">
                                        Phone
                                    </label>

                                    <div class="input-icon-wrapper">

                                        <i class="bi bi-telephone"></i>

                                        <input type="text"
                                               name="phone"
                                               value="{{ old('phone', $employee->phone) }}"
                                               class="form-control employee-input phone-input @error('phone') is-invalid @enderror"
                                               placeholder="0300-1234567"
                                               maxlength="12">

                                    </div>

                                    <small class="employee-field-help">
                                        Example: 0300-1234567
                                    </small>

                                    @error('phone')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                            </div>

                            {{-- CNIC --}}
                            <div class="col-md-6">

                                <div class="employee-field">

                                    <label class="form-label">
                                        CNIC
                                    </label>

                                    <div class="input-icon-wrapper">

                                        <i class="bi bi-credit-card-2-front"></i>

                                        <input type="text"
                                               name="cnic"
                                               value="{{ old('cnic', $employee->cnic) }}"
                                               class="form-control employee-input cnic-input @error('cnic') is-invalid @enderror"
                                               placeholder="35101-1234567-1"
                                               maxlength="15">

                                    </div>

                                    <small class="employee-field-help">
                                        Example: 35101-1234567-1
                                    </small>

                                    @error('cnic')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                            </div>

                            {{-- Email --}}
                            <div class="col-md-6">

                                <div class="employee-field">

                                    <label class="form-label">
                                        Email
                                    </label>

                                    <div class="input-icon-wrapper">

                                        <i class="bi bi-envelope"></i>

                                        <input type="email"
                                               name="email"
                                               value="{{ old('email', $employee->email) }}"
                                               class="form-control employee-input @error('email') is-invalid @enderror"
                                               placeholder="employee@email.com">

                                    </div>

                                    @error('email')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                            </div>

                            {{-- Department --}}
                            <div class="col-md-6">

                                <div class="employee-field">

                                    <label class="form-label">
                                        Department
                                    </label>

                                    <div class="input-icon-wrapper">

                                        <i class="bi bi-building"></i>

                                        <input type="text"
                                               name="department"
                                               value="{{ old('department', $employee->department) }}"
                                               class="form-control employee-input @error('department') is-invalid @enderror"
                                               placeholder="Stitching / Cutting / Packing">

                                    </div>

                                    @error('department')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                            </div>

                            {{-- Designation --}}
                            <div class="col-md-6">

                                <div class="employee-field">

                                    <label class="form-label">
                                        Designation
                                    </label>

                                    <div class="input-icon-wrapper">

                                        <i class="bi bi-person-workspace"></i>

                                        <input type="text"
                                               name="designation"
                                               value="{{ old('designation', $employee->designation) }}"
                                               class="form-control employee-input @error('designation') is-invalid @enderror"
                                               placeholder="Worker / Supervisor / Manager">

                                    </div>

                                    @error('designation')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                            </div>

                            {{-- Basic Salary --}}
                            <div class="col-md-6">

                                <div class="employee-field">

                                    <label class="form-label">
                                        Basic Salary
                                        <span>*</span>
                                    </label>

                                    <div class="input-icon-wrapper">

                                        <i class="bi bi-cash-stack"></i>

                                        <input type="number"
                                               step="0.01"
                                               min="0"
                                               name="basic_salary"
                                               value="{{ old('basic_salary', $employee->basic_salary) }}"
                                               class="form-control employee-input @error('basic_salary') is-invalid @enderror"
                                               placeholder="0.00"
                                               required>

                                    </div>

                                    @error('basic_salary')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                            </div>

                            {{-- Joining Date --}}
                            <div class="col-md-6">

                                <div class="employee-field">

                                    <label class="form-label">
                                        Joining Date
                                    </label>

                                    <div class="input-icon-wrapper">

                                        <i class="bi bi-calendar3"></i>

                                        <input type="date"
                                               name="joining_date"
                                               value="{{ old(
                                                   'joining_date',
                                                   $employee->joining_date
                                                       ? \Carbon\Carbon::parse($employee->joining_date)->format('Y-m-d')
                                                       : ''
                                               ) }}"
                                               class="form-control employee-input @error('joining_date') is-invalid @enderror">

                                    </div>

                                    @error('joining_date')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                            </div>

                            {{-- Status --}}
                            <div class="col-md-6">

                                <div class="employee-field">

                                    <label class="form-label">
                                        Status
                                    </label>

                                    <div class="input-icon-wrapper">

                                        <i class="bi bi-toggle-on"></i>

                                        <select name="status"
                                                class="form-select employee-input @error('status') is-invalid @enderror">

                                            <option value="inactive"
                                                {{ old('status', $employee->status) === 'inactive' ? 'selected' : '' }}>
                                                Inactive
                                            </option>

                                            <option value="active"
                                                {{ old('status', $employee->status) === 'active' ? 'selected' : '' }}>
                                                Active
                                            </option>

                                        </select>

                                    </div>

                                    @error('status')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                            </div>

                            {{-- Address --}}
                            <div class="col-12">

                                <div class="employee-field">

                                    <label class="form-label">
                                        Address
                                    </label>

                                    <div class="textarea-icon-wrapper">

                                        <i class="bi bi-geo-alt"></i>

                                        <textarea name="address"
                                                  rows="4"
                                                  class="form-control employee-textarea @error('address') is-invalid @enderror"
                                                  placeholder="Enter complete employee address">{{ old('address', $employee->address) }}</textarea>

                                    </div>

                                    @error('address')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- FORM FOOTER --}}
                    <div class="employee-form-footer">

                        <a href="{{ route('employees.index') }}"
                           class="employee-cancel-btn">

                            <i class="bi bi-x-lg"></i>
                            Cancel

                        </a>

                        <button type="submit"
                                class="employee-save-btn"
                                id="updateEmployeeButton">

                            <i class="bi bi-check2-circle"></i>
                            Update Employee

                        </button>

                    </div>

                </div>

            </div>

        </div>

    </form>

</div>

<style>
:root {
    --employee-black: #111111;
    --employee-dark: #242424;
    --employee-white: #ffffff;
    --employee-border: #e4e6eb;
    --employee-muted: #747b86;
    --employee-light: #f8f9fa;
}

.employee-edit-page {
    color: var(--employee-black);
}

.employee-edit-header {
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
}

.employee-edit-title {
    margin: 0;
    color: var(--employee-black);
    font-size: 28px;
    font-weight: 900;
}

.employee-edit-subtitle {
    margin: 4px 0 0;
    color: var(--employee-muted);
    font-size: 13px;
}

.employee-header-actions {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

.employee-back-btn,
.employee-outline-btn {
    min-height: 40px;
    padding: 9px 15px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    border: 1px solid var(--employee-black);
    border-radius: 8px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 800;
    transition: 0.2s ease;
}

.employee-back-btn {
    color: var(--employee-white);
    background: var(--employee-black);
}

.employee-outline-btn {
    color: var(--employee-black);
    background: var(--employee-white);
}

.employee-back-btn:hover,
.employee-outline-btn:hover {
    color: var(--employee-white);
    background: #333333;
}

.employee-upload-card,
.employee-form-card {
    overflow: hidden;
    border: 1px solid var(--employee-border);
    border-radius: 14px;
    background: var(--employee-white);
    box-shadow: 0 6px 22px rgba(17, 24, 39, 0.06);
}

.upload-section {
    padding: 22px;
    border-bottom: 1px solid var(--employee-border);
}

.upload-section-header {
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.upload-section-header h5 {
    margin: 0;
    color: var(--employee-black);
    font-size: 15px;
    font-weight: 900;
}

.upload-section-header p {
    margin: 3px 0 0;
    color: var(--employee-muted);
    font-size: 11px;
}

.upload-header-icon {
    flex: 0 0 39px;
    width: 39px;
    height: 39px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    color: var(--employee-white);
    background: var(--employee-black);
    font-size: 18px;
}

.existing-files-heading {
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    color: var(--employee-black);
    font-size: 11px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}

.existing-files-heading span {
    min-width: 25px;
    height: 25px;
    padding: 0 7px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50px;
    color: var(--employee-white);
    background: var(--employee-black);
    font-size: 10px;
}

.employee-upload-box {
    min-height: 160px;
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 7px;
    border: 1.5px dashed #bfc3c9;
    border-radius: 12px;
    color: var(--employee-black);
    background: var(--employee-light);
    text-align: center;
    cursor: pointer;
    transition: 0.2s ease;
}

.employee-upload-box:hover {
    border-color: var(--employee-black);
    background: var(--employee-white);
}

.employee-upload-icon {
    width: 48px;
    height: 48px;
    margin-bottom: 3px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    color: var(--employee-white);
    background: var(--employee-black);
    font-size: 20px;
}

.employee-upload-box strong {
    font-size: 13px;
    font-weight: 900;
}

.employee-upload-box > span:not(.employee-upload-button) {
    color: var(--employee-muted);
    font-size: 10px;
}

.employee-upload-button {
    margin-top: 7px;
    padding: 8px 13px;
    border-radius: 7px;
    color: var(--employee-white);
    background: var(--employee-black);
    font-size: 10px;
    font-weight: 800;
}

.preview-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
}

.preview-image-card {
    position: relative;
    height: 130px;
    overflow: hidden;
    border: 1px solid var(--employee-border);
    border-radius: 10px;
    background: #eeeeee;
}

.preview-image-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.preview-image-overlay {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: rgba(0, 0, 0, 0.62);
    opacity: 0;
    transition: opacity 0.2s ease;
}

.preview-image-card:hover .preview-image-overlay {
    opacity: 1;
}

.preview-action-btn {
    width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--employee-white);
    border-radius: 50%;
    color: var(--employee-black);
    background: var(--employee-white);
    text-decoration: none;
}

.preview-action-btn:hover {
    color: var(--employee-white);
    background: var(--employee-black);
}

.preview-image-label {
    position: absolute;
    right: 7px;
    bottom: 7px;
    left: 7px;
    padding: 5px 7px;
    overflow: hidden;
    border-radius: 6px;
    color: var(--employee-white);
    background: rgba(0, 0, 0, 0.75);
    font-size: 9px;
    font-weight: 700;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.empty-preview-box {
    min-height: 90px;
    padding: 15px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 7px;
    border: 1px dashed #bec2c8;
    border-radius: 10px;
    color: var(--employee-muted);
    background: var(--employee-light);
    font-size: 11px;
}

.empty-preview-box i {
    color: var(--employee-black);
    font-size: 25px;
}

.documents-preview-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.document-preview-row {
    padding: 9px;
    display: flex;
    align-items: center;
    gap: 10px;
    border: 1px solid var(--employee-border);
    border-radius: 9px;
    background: var(--employee-light);
}

.document-preview-icon {
    flex: 0 0 40px;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 7px;
    color: var(--employee-white);
    background: var(--employee-black);
    font-size: 18px;
}

.document-preview-info {
    min-width: 0;
    flex: 1;
}

.document-preview-info strong {
    display: block;
    overflow: hidden;
    color: var(--employee-black);
    font-size: 11px;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.document-preview-info small {
    color: var(--employee-muted);
    font-size: 9px;
}

.document-row-actions {
    display: flex;
    gap: 5px;
}

.small-document-action {
    width: 30px;
    height: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--employee-black);
    border-radius: 7px;
    color: var(--employee-white);
    background: var(--employee-black);
    text-decoration: none;
}

.small-document-action:hover {
    color: var(--employee-black);
    background: var(--employee-white);
}

.employee-form-card-header {
    min-height: 84px;
    padding: 20px 22px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid var(--employee-border);
}

.employee-form-card-header h4 {
    margin: 0;
    color: var(--employee-black);
    font-size: 19px;
    font-weight: 900;
}

.employee-form-card-header p {
    margin: 3px 0 0;
    color: var(--employee-muted);
    font-size: 12px;
}

.form-header-icon {
    width: 43px;
    height: 43px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 11px;
    color: var(--employee-white);
    background: var(--employee-black);
    font-size: 20px;
}

.employee-form-card-body {
    padding: 22px;
}

.employee-field {
    height: 100%;
}

.employee-field .form-label {
    margin-bottom: 7px;
    color: var(--employee-black);
    font-size: 11px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.35px;
}

.employee-field .form-label span {
    color: #dc3545;
}

.input-icon-wrapper,
.textarea-icon-wrapper {
    position: relative;
}

.input-icon-wrapper > i {
    position: absolute;
    top: 50%;
    left: 14px;
    z-index: 2;
    color: var(--employee-black);
    font-size: 15px;
    transform: translateY(-50%);
    pointer-events: none;
}

.textarea-icon-wrapper > i {
    position: absolute;
    top: 15px;
    left: 14px;
    z-index: 2;
    color: var(--employee-black);
    font-size: 15px;
    pointer-events: none;
}

.employee-input {
    min-height: 47px;
    padding-left: 42px;
    border: 1px solid var(--employee-border);
    border-radius: 9px;
    color: var(--employee-black);
    background: var(--employee-light);
    font-size: 13px;
    font-weight: 700;
    box-shadow: none !important;
}

.employee-input:focus {
    border-color: var(--employee-black);
    background: var(--employee-white);
}

.employee-textarea {
    padding: 13px 14px 13px 42px;
    border: 1px solid var(--employee-border);
    border-radius: 9px;
    color: var(--employee-black);
    background: var(--employee-light);
    font-size: 13px;
    font-weight: 700;
    resize: vertical;
    box-shadow: none !important;
}

.employee-textarea:focus {
    border-color: var(--employee-black);
    background: var(--employee-white);
}

.employee-field-help {
    display: block;
    margin-top: 6px;
    color: var(--employee-muted);
    font-size: 10px;
}

.employee-form-footer {
    padding: 18px 22px;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 9px;
    border-top: 1px solid var(--employee-border);
    background: var(--employee-light);
}

.employee-cancel-btn,
.employee-save-btn {
    min-height: 42px;
    padding: 10px 16px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    border: 1px solid var(--employee-black);
    border-radius: 8px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 900;
    cursor: pointer;
    transition: 0.2s ease;
}

.employee-cancel-btn {
    color: var(--employee-black);
    background: var(--employee-white);
}

.employee-save-btn {
    color: var(--employee-white);
    background: var(--employee-black);
}

.employee-cancel-btn:hover {
    color: var(--employee-white);
    background: var(--employee-black);
}

.employee-save-btn:hover {
    color: var(--employee-black);
    background: var(--employee-white);
}

.employee-save-btn:disabled {
    cursor: not-allowed;
    opacity: 0.7;
}

@media (max-width: 991px) {
    .employee-edit-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .employee-header-actions {
        width: 100%;
    }

    .employee-back-btn,
    .employee-outline-btn {
        flex: 1;
    }
}

@media (max-width: 575px) {
    .employee-edit-title {
        font-size: 23px;
    }

    .employee-header-actions {
        display: grid;
        grid-template-columns: 1fr;
    }

    .employee-form-card-body,
    .upload-section {
        padding: 17px;
    }

    .employee-form-footer {
        display: grid;
        grid-template-columns: 1fr 1fr;
    }

    .employee-cancel-btn,
    .employee-save-btn {
        width: 100%;
    }

    .preview-image-card {
        height: 110px;
    }
}

/* =========================================================
   DESKTOP EQUAL-HEIGHT LAYOUT
   Both panels remain the same height. Large preview content
   scrolls inside the left panel instead of increasing page height.
   ========================================================= */
@media (min-width: 992px) {
    .employee-edit-page form > .row {
        align-items: stretch;
    }

    .employee-edit-page form > .row > [class*="col-"] {
        display: flex;
        min-width: 0;
    }

    .employee-upload-card,
    .employee-form-card {
        width: 100%;
        height: clamp(700px, calc(100vh - 185px), 820px);
        min-height: 0;
    }

    /* LEFT PANEL */
    .employee-upload-card {
        display: grid;
        grid-template-rows: repeat(3, minmax(0, 1fr));
    }

    .employee-upload-card .upload-section {
        min-height: 0;
        padding: 16px 18px;
        overflow-x: hidden;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #b9bdc4 transparent;
    }

    .employee-upload-card .upload-section::-webkit-scrollbar,
    .employee-form-card-body::-webkit-scrollbar {
        width: 6px;
    }

    .employee-upload-card .upload-section::-webkit-scrollbar-thumb,
    .employee-form-card-body::-webkit-scrollbar-thumb {
        border-radius: 50px;
        background: #b9bdc4;
    }

    .employee-upload-card .upload-section::-webkit-scrollbar-track,
    .employee-form-card-body::-webkit-scrollbar-track {
        background: transparent;
    }

    .upload-section-header {
        margin-bottom: 10px;
    }

    .upload-section-header h5 {
        font-size: 14px;
    }

    .upload-section-header p {
        font-size: 10px;
    }

    .upload-header-icon {
        flex-basis: 34px;
        width: 34px;
        height: 34px;
        border-radius: 9px;
        font-size: 15px;
    }

    .existing-files-heading {
        margin-bottom: 7px;
        font-size: 10px;
    }

    .existing-files-heading span {
        min-width: 22px;
        height: 22px;
        font-size: 9px;
    }

    .existing-preview-grid,
    #picturesPreview,
    #cnicPreview {
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 7px;
    }

    .preview-image-card {
        height: 82px;
        border-radius: 8px;
    }

    .preview-image-label {
        right: 4px;
        bottom: 4px;
        left: 4px;
        padding: 3px 5px;
        font-size: 7px;
    }

    .preview-action-btn {
        width: 28px;
        height: 28px;
        font-size: 12px;
    }

    .empty-preview-box {
        min-height: 58px;
        padding: 9px;
        gap: 3px;
        font-size: 9px;
    }

    .empty-preview-box i {
        font-size: 19px;
    }

    .employee-upload-box {
        min-height: 72px;
        margin-top: 9px !important;
        padding: 10px 12px;
        display: grid;
        grid-template-columns: 38px minmax(0, 1fr) auto;
        grid-template-areas:
            "icon title button"
            "icon note button";
        column-gap: 10px;
        row-gap: 1px;
        align-items: center;
        text-align: left;
    }

    .employee-upload-icon {
        grid-area: icon;
        width: 38px;
        height: 38px;
        margin: 0;
        font-size: 16px;
    }

    .employee-upload-box strong {
        grid-area: title;
        align-self: end;
        font-size: 11px;
        line-height: 1.2;
    }

    .employee-upload-box > span:not(.employee-upload-button) {
        grid-area: note;
        align-self: start;
        overflow: hidden;
        font-size: 8px;
        line-height: 1.25;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .employee-upload-button {
        grid-area: button;
        margin: 0;
        padding: 7px 9px;
        font-size: 8px;
        white-space: nowrap;
    }

    .documents-preview-list {
        gap: 6px;
    }

    .document-preview-row {
        padding: 6px;
        gap: 7px;
    }

    .document-preview-icon {
        flex-basis: 32px;
        width: 32px;
        height: 32px;
        font-size: 14px;
    }

    .document-preview-info strong {
        font-size: 9px;
    }

    .document-preview-info small {
        font-size: 8px;
    }

    .small-document-action {
        width: 25px;
        height: 25px;
        border-radius: 6px;
        font-size: 11px;
    }

    /* RIGHT PANEL */
    .employee-form-card {
        display: flex;
        flex-direction: column;
    }

    .employee-form-card-header {
        flex: 0 0 auto;
        min-height: 74px;
        padding: 16px 20px;
    }

    .employee-form-card-body {
        min-height: 0;
        flex: 1 1 auto;
        padding: 18px 20px;
        overflow-x: hidden;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #b9bdc4 transparent;
    }

    .employee-form-card-body .row {
        --bs-gutter-y: 0.75rem;
    }

    .employee-field .form-label {
        margin-bottom: 5px;
        font-size: 10px;
    }

    .employee-input {
        min-height: 43px;
        font-size: 12px;
    }

    .employee-textarea {
        min-height: 86px;
        max-height: 120px;
        font-size: 12px;
    }

    .employee-field-help {
        margin-top: 4px;
        font-size: 8px;
    }

    .employee-form-footer {
        flex: 0 0 auto;
        padding: 13px 18px;
    }
}

/* Medium laptop screens */
@media (min-width: 992px) and (max-height: 820px) {
    .employee-upload-card,
    .employee-form-card {
        height: calc(100vh - 165px);
        min-height: 620px;
    }

    .employee-form-card-header {
        min-height: 66px;
        padding-top: 13px;
        padding-bottom: 13px;
    }

    .employee-form-card-body {
        padding-top: 14px;
        padding-bottom: 14px;
    }

    .employee-input {
        min-height: 40px;
    }

    .employee-textarea {
        min-height: 72px;
    }
}

</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const phoneInput = document.querySelector('.phone-input');
    const cnicInput = document.querySelector('.cnic-input');

    const picturesInput = document.getElementById('pictures');
    const cnicPicturesInput = document.getElementById('cnic_pictures');
    const documentsInput = document.getElementById('other_documents');

    const picturesPreview = document.getElementById('picturesPreview');
    const cnicPreview = document.getElementById('cnicPreview');
    const documentsPreview = document.getElementById('documentsPreview');

    const picturesSelectedHeading = document.getElementById(
        'picturesSelectedHeading'
    );

    const cnicSelectedHeading = document.getElementById(
        'cnicSelectedHeading'
    );

    const documentsSelectedHeading = document.getElementById(
        'documentsSelectedHeading'
    );

    const picturesSelectedCount = document.getElementById(
        'picturesSelectedCount'
    );

    const cnicSelectedCount = document.getElementById(
        'cnicSelectedCount'
    );

    const documentsSelectedCount = document.getElementById(
        'documentsSelectedCount'
    );

    const employeeForm = document.getElementById('employeeEditForm');
    const updateButton = document.getElementById('updateEmployeeButton');

    /*
    |--------------------------------------------------------------------------
    | Phone Mask
    |--------------------------------------------------------------------------
    */

    if (phoneInput) {
        phoneInput.addEventListener('input', function () {
            let value = this.value.replace(/\D/g, '');

            if (value.length > 11) {
                value = value.substring(0, 11);
            }

            if (value.length > 4) {
                value =
                    value.substring(0, 4)
                    + '-'
                    + value.substring(4);
            }

            this.value = value;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | CNIC Mask
    |--------------------------------------------------------------------------
    */

    if (cnicInput) {
        cnicInput.addEventListener('input', function () {
            let value = this.value.replace(/\D/g, '');

            if (value.length > 13) {
                value = value.substring(0, 13);
            }

            if (value.length > 12) {
                value =
                    value.substring(0, 5)
                    + '-'
                    + value.substring(5, 12)
                    + '-'
                    + value.substring(12);
            } else if (value.length > 5) {
                value =
                    value.substring(0, 5)
                    + '-'
                    + value.substring(5);
            }

            this.value = value;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | New Image Preview
    |--------------------------------------------------------------------------
    */

    function showImagePreviews(
        input,
        container,
        heading,
        countElement,
        previewType
    ) {
        container.innerHTML = '';

        const files = Array.from(input.files || []);

        countElement.textContent = files.length;

        if (files.length > 0) {
            heading.classList.remove('d-none');
        } else {
            heading.classList.add('d-none');
        }

        files.forEach(function (file, index) {
            if (!file.type.startsWith('image/')) {
                return;
            }

            const previewUrl = URL.createObjectURL(file);

            const card = document.createElement('div');
            card.className = 'preview-image-card';

            const image = document.createElement('img');
            image.src = previewUrl;
            image.alt = file.name;

            image.onload = function () {
                URL.revokeObjectURL(previewUrl);
            };

            const label = document.createElement('div');
            label.className = 'preview-image-label';

            if (previewType === 'employee' && index === 0) {
                label.textContent = 'New Profile: ' + file.name;
            } else if (previewType === 'cnic' && index === 0) {
                label.textContent = 'New CNIC Front: ' + file.name;
            } else if (previewType === 'cnic' && index === 1) {
                label.textContent = 'New CNIC Back: ' + file.name;
            } else {
                label.textContent = file.name;
            }

            card.appendChild(image);
            card.appendChild(label);

            container.appendChild(card);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | New Documents Preview
    |--------------------------------------------------------------------------
    */

    function showDocumentPreviews(
        input,
        container,
        heading,
        countElement
    ) {
        container.innerHTML = '';

        const files = Array.from(input.files || []);

        countElement.textContent = files.length;

        if (files.length > 0) {
            heading.classList.remove('d-none');
        } else {
            heading.classList.add('d-none');
        }

        files.forEach(function (file) {
            const row = document.createElement('div');
            row.className = 'document-preview-row';

            const icon = document.createElement('div');
            icon.className = 'document-preview-icon';

            const extension = file.name
                .split('.')
                .pop()
                .toLowerCase();

            if (file.type.startsWith('image/')) {
                icon.innerHTML =
                    '<i class="bi bi-file-earmark-image"></i>';
            } else if (extension === 'pdf') {
                icon.innerHTML =
                    '<i class="bi bi-file-earmark-pdf"></i>';
            } else if (
                extension === 'doc'
                || extension === 'docx'
            ) {
                icon.innerHTML =
                    '<i class="bi bi-file-earmark-word"></i>';
            } else {
                icon.innerHTML =
                    '<i class="bi bi-file-earmark"></i>';
            }

            const info = document.createElement('div');
            info.className = 'document-preview-info';

            const name = document.createElement('strong');
            name.textContent = file.name;

            const size = document.createElement('small');
            size.textContent = formatFileSize(file.size);

            info.appendChild(name);
            info.appendChild(size);

            row.appendChild(icon);
            row.appendChild(info);

            container.appendChild(row);
        });
    }

    function formatFileSize(bytes) {
        if (!bytes) {
            return '0 KB';
        }

        const units = ['Bytes', 'KB', 'MB', 'GB'];

        const index = Math.floor(
            Math.log(bytes) / Math.log(1024)
        );

        const size = bytes / Math.pow(1024, index);

        return size.toFixed(index === 0 ? 0 : 1)
            + ' '
            + units[index];
    }

    if (picturesInput) {
        picturesInput.addEventListener('change', function () {
            showImagePreviews(
                this,
                picturesPreview,
                picturesSelectedHeading,
                picturesSelectedCount,
                'employee'
            );
        });
    }

    if (cnicPicturesInput) {
        cnicPicturesInput.addEventListener('change', function () {
            showImagePreviews(
                this,
                cnicPreview,
                cnicSelectedHeading,
                cnicSelectedCount,
                'cnic'
            );
        });
    }

    if (documentsInput) {
        documentsInput.addEventListener('change', function () {
            showDocumentPreviews(
                this,
                documentsPreview,
                documentsSelectedHeading,
                documentsSelectedCount
            );
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Prevent Double Submit
    |--------------------------------------------------------------------------
    */

    if (employeeForm) {
        employeeForm.addEventListener('submit', function () {
            updateButton.disabled = true;

            updateButton.innerHTML = `
                <span class="spinner-border spinner-border-sm"></span>
                Updating Employee...
            `;
        });
    }
});
</script>

@endsection
