@extends('layouts.app')

@section('content')

<div class="employee-create-page">

    {{-- PAGE HEADER --}}
    <div class="employee-create-header">

        <div>
            <h3 class="employee-create-title">
                Add Employee / Worker
            </h3>

            <p class="employee-create-subtitle">
                Add personal information, salary and employee documents
            </p>
        </div>

        <a href="{{ route('employees.index') }}"
           class="employee-back-btn">

            <i class="bi bi-arrow-left"></i>
            Back to Employees

        </a>

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

    <form action="{{ route('employees.store') }}"
          method="POST"
          enctype="multipart/form-data"
          id="employeeCreateForm">

        @csrf

        <div class="row g-4">

            {{-- LEFT SIDE --}}
            <div class="col-xl-4 col-lg-5">

                <div class="employee-upload-card">

                    {{-- PROFILE PICTURE --}}
                    <div class="upload-section">

                        <div class="upload-section-header">

                            <div>
                                <h5>
                                    Employee Pictures
                                </h5>

                                <p>
                                    First picture will be used as profile picture
                                </p>
                            </div>

                            <div class="upload-header-icon">
                                <i class="bi bi-person-square"></i>
                            </div>

                        </div>

                        <label for="pictures"
                               class="employee-upload-box">

                            <div class="employee-upload-icon">
                                <i class="bi bi-camera"></i>
                            </div>

                            <strong>
                                Upload Employee Pictures
                            </strong>

                            <span>
                                Select one or multiple images
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

                        <div id="picturesPreview"
                             class="preview-grid mt-3"></div>

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
                                    Upload front and back side of CNIC
                                </p>
                            </div>

                            <div class="upload-header-icon">
                                <i class="bi bi-person-vcard"></i>
                            </div>

                        </div>

                        <label for="cnic_pictures"
                               class="employee-upload-box">

                            <div class="employee-upload-icon">
                                <i class="bi bi-card-image"></i>
                            </div>

                            <strong>
                                Upload CNIC Images
                            </strong>

                            <span>
                                Front and back pictures
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

                        <div id="cnicPreview"
                             class="preview-grid mt-3"></div>

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
                                    Upload additional employee files
                                </p>
                            </div>

                            <div class="upload-header-icon">
                                <i class="bi bi-folder2-open"></i>
                            </div>

                        </div>

                        <label for="other_documents"
                               class="employee-upload-box">

                            <div class="employee-upload-icon">
                                <i class="bi bi-file-earmark-arrow-up"></i>
                            </div>

                            <strong>
                                Upload Documents
                            </strong>

                            <span>
                                Images, PDF, DOC or DOCX
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

                        <div id="documentsPreview"
                             class="documents-preview-list mt-3"></div>

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
                                Enter complete employee details
                            </p>
                        </div>

                        <div class="form-header-icon">
                            <i class="bi bi-person-plus"></i>
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
                                               value="{{ old('name') }}"
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
                                               value="{{ old('father_name') }}"
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
                                               value="{{ old('phone') }}"
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
                                               value="{{ old('cnic') }}"
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
                                               value="{{ old('email') }}"
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
                                               value="{{ old('department') }}"
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
                                               value="{{ old('designation') }}"
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
                                               value="{{ old('basic_salary') }}"
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
                                               value="{{ old('joining_date', date('Y-m-d')) }}"
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
                                                {{ old('status', 'inactive') === 'inactive' ? 'selected' : '' }}>
                                                Inactive
                                            </option>

                                            <option value="active"
                                                {{ old('status') === 'active' ? 'selected' : '' }}>
                                                Active
                                            </option>

                                        </select>

                                    </div>

                                    <small class="employee-field-help">
                                        Employee will remain inactive until salary setup is completed
                                    </small>

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
                                                  placeholder="Enter complete employee address">{{ old('address') }}</textarea>

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
                                id="saveEmployeeButton">

                            <i class="bi bi-check2-circle"></i>
                            Save Employee

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
    --employee-page: #f5f6f8;
    --employee-border: #e4e6eb;
    --employee-muted: #747b86;
    --employee-light: #f8f9fa;
}

.employee-create-page {
    color: var(--employee-black);
}

.employee-create-header {
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
}

.employee-create-title {
    margin: 0;
    color: var(--employee-black);
    font-size: 28px;
    font-weight: 900;
}

.employee-create-subtitle {
    margin: 4px 0 0;
    color: var(--employee-muted);
    font-size: 13px;
}

.employee-back-btn {
    min-height: 40px;
    padding: 9px 15px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    border: 1px solid var(--employee-black);
    border-radius: 8px;
    color: var(--employee-white);
    background: var(--employee-black);
    text-decoration: none;
    font-size: 13px;
    font-weight: 800;
    transition: 0.2s ease;
}

.employee-back-btn:hover {
    color: var(--employee-black);
    background: var(--employee-white);
}

.employee-error-box {
    border-radius: 12px;
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

.employee-upload-box {
    min-height: 195px;
    padding: 22px;
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
    width: 52px;
    height: 52px;
    margin-bottom: 3px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    color: var(--employee-white);
    background: var(--employee-black);
    font-size: 22px;
}

.employee-upload-box strong {
    font-size: 14px;
    font-weight: 900;
}

.employee-upload-box span {
    color: var(--employee-muted);
    font-size: 11px;
}

.employee-upload-button {
    margin-top: 7px;
    padding: 8px 13px;
    border-radius: 7px;
    color: var(--employee-white) !important;
    background: var(--employee-black);
    font-size: 11px !important;
    font-weight: 800;
}

.preview-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
}

.preview-image-card {
    position: relative;
    height: 125px;
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

.preview-image-label {
    position: absolute;
    right: 7px;
    bottom: 7px;
    left: 7px;
    padding: 5px 7px;
    overflow: hidden;
    border-radius: 6px;
    color: var(--employee-white);
    background: rgba(0, 0, 0, 0.72);
    font-size: 9px;
    font-weight: 700;
    text-overflow: ellipsis;
    white-space: nowrap;
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
    flex: 0 0 38px;
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 7px;
    color: var(--employee-white);
    background: var(--employee-black);
    font-size: 17px;
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
    .employee-create-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .employee-back-btn {
        width: 100%;
    }
}

@media (max-width: 575px) {
    .employee-create-title {
        font-size: 23px;
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

    .preview-grid {
        grid-template-columns: 1fr 1fr;
    }
}


/* =========================================================
   COMPACT EQUAL-HEIGHT DESKTOP LAYOUT
   Both panels stay the same height and the upload panel
   remains inside the right form card height.
========================================================= */

#employeeCreateForm > .row {
    align-items: stretch;
}

#employeeCreateForm > .row > [class*="col-"] {
    display: flex;
}

.employee-upload-card,
.employee-form-card {
    width: 100%;
    height: 100%;
}

.employee-upload-card {
    display: grid;
    grid-template-rows: repeat(3, minmax(0, 1fr));
}

.employee-form-card {
    display: flex;
    flex-direction: column;
}

.employee-form-card-body {
    flex: 1;
}

/* Compact left upload sections */
.upload-section {
    min-height: 0;
    padding: 16px 18px;
    display: flex;
    flex-direction: column;
}

.upload-section-header {
    margin-bottom: 10px;
}

.upload-section-header h5 {
    font-size: 14px;
}

.upload-section-header p {
    margin-top: 2px;
    font-size: 10px;
}

.upload-header-icon {
    flex-basis: 34px;
    width: 34px;
    height: 34px;
    border-radius: 9px;
    font-size: 16px;
}

.employee-upload-box {
    min-height: 96px;
    padding: 12px 14px;
    flex: 1;
    display: grid;
    grid-template-columns: 42px minmax(0, 1fr) auto;
    grid-template-rows: auto auto;
    column-gap: 11px;
    row-gap: 2px;
    align-content: center;
    justify-content: initial;
    text-align: left;
}

.employee-upload-icon {
    grid-column: 1;
    grid-row: 1 / span 2;
    width: 42px;
    height: 42px;
    margin: 0;
    font-size: 18px;
}

.employee-upload-box strong {
    grid-column: 2;
    grid-row: 1;
    align-self: end;
    font-size: 12px;
    line-height: 1.25;
}

.employee-upload-box > span:not(.employee-upload-button) {
    grid-column: 2;
    grid-row: 2;
    align-self: start;
    font-size: 9px;
}

.employee-upload-button {
    grid-column: 3;
    grid-row: 1 / span 2;
    align-self: center;
    margin: 0;
    padding: 7px 9px;
    white-space: nowrap;
    font-size: 9px !important;
}

/* Selected files remain inside the card instead of increasing its height */
.preview-grid,
.documents-preview-list {
    margin-top: 9px !important;
    max-height: 82px;
    overflow-y: auto;
    scrollbar-width: thin;
}

.preview-grid {
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 7px;
}

.preview-image-card {
    height: 72px;
    border-radius: 8px;
}

.preview-image-label {
    right: 4px;
    bottom: 4px;
    left: 4px;
    padding: 3px 4px;
    font-size: 7px;
}

.document-preview-row {
    padding: 6px 7px;
    gap: 7px;
}

.document-preview-icon {
    flex-basis: 30px;
    width: 30px;
    height: 30px;
    font-size: 14px;
}

/* Slightly tighter form so the complete page fits cleanly */
.employee-create-header {
    margin-bottom: 18px;
}

.employee-form-card-header {
    min-height: 72px;
    padding: 15px 20px;
}

.employee-form-card-body {
    padding: 18px 20px;
}

.employee-form-card-body .row {
    --bs-gutter-y: 0.78rem;
}

.employee-input {
    min-height: 44px;
}

.employee-textarea {
    min-height: 88px;
    max-height: 110px;
}

.employee-form-footer {
    padding: 13px 20px;
}

@media (min-width: 992px) {
    .employee-create-page {
        padding-bottom: 8px;
    }
}

@media (max-width: 1199px) and (min-width: 992px) {
    .employee-upload-box {
        grid-template-columns: 38px minmax(0, 1fr);
    }

    .employee-upload-button {
        grid-column: 2;
        grid-row: 3;
        justify-self: start;
        margin-top: 5px;
    }

    .employee-upload-icon {
        width: 38px;
        height: 38px;
    }
}

@media (max-width: 991px) {
    #employeeCreateForm > .row > [class*="col-"] {
        display: block;
    }

    .employee-upload-card {
        display: block;
    }

    .employee-upload-box {
        min-height: 110px;
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

    const employeeForm = document.getElementById('employeeCreateForm');
    const saveButton = document.getElementById('saveEmployeeButton');

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
    | Image Preview
    |--------------------------------------------------------------------------
    */

    function showImagePreviews(input, container) {
        container.innerHTML = '';

        const files = Array.from(input.files || []);

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

            if (container === picturesPreview && index === 0) {
                label.textContent = 'Profile: ' + file.name;
            } else if (container === cnicPreview && index === 0) {
                label.textContent = 'CNIC Front: ' + file.name;
            } else if (container === cnicPreview && index === 1) {
                label.textContent = 'CNIC Back: ' + file.name;
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
    | Other Documents Preview
    |--------------------------------------------------------------------------
    */

    function showDocumentPreviews(input, container) {
        container.innerHTML = '';

        const files = Array.from(input.files || []);

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
                icon.innerHTML = '<i class="bi bi-file-earmark-image"></i>';
            } else if (extension === 'pdf') {
                icon.innerHTML = '<i class="bi bi-file-earmark-pdf"></i>';
            } else if (
                extension === 'doc'
                || extension === 'docx'
            ) {
                icon.innerHTML = '<i class="bi bi-file-earmark-word"></i>';
            } else {
                icon.innerHTML = '<i class="bi bi-file-earmark"></i>';
            }

            const info = document.createElement('div');
            info.className = 'document-preview-info';

            const fileName = document.createElement('strong');
            fileName.textContent = file.name;

            const fileSize = document.createElement('small');
            fileSize.textContent = formatFileSize(file.size);

            info.appendChild(fileName);
            info.appendChild(fileSize);

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
        const unitIndex = Math.floor(
            Math.log(bytes) / Math.log(1024)
        );

        const value = bytes / Math.pow(1024, unitIndex);

        return value.toFixed(unitIndex === 0 ? 0 : 1)
            + ' '
            + units[unitIndex];
    }

    if (picturesInput) {
        picturesInput.addEventListener('change', function () {
            showImagePreviews(this, picturesPreview);
        });
    }

    if (cnicPicturesInput) {
        cnicPicturesInput.addEventListener('change', function () {
            showImagePreviews(this, cnicPreview);
        });
    }

    if (documentsInput) {
        documentsInput.addEventListener('change', function () {
            showDocumentPreviews(this, documentsPreview);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Prevent Double Submit
    |--------------------------------------------------------------------------
    */

    if (employeeForm) {
        employeeForm.addEventListener('submit', function () {
            saveButton.disabled = true;

            saveButton.innerHTML = `
                <span class="spinner-border spinner-border-sm"></span>
                Saving Employee...
            `;
        });
    }
});
</script>

@endsection
