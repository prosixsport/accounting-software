@extends('layouts.app')

@section('content')

@php
    $pictures = is_array($employee->pictures)
        ? $employee->pictures
        : (json_decode($employee->pictures ?? '[]', true) ?? []);

    $cnicPictures = is_array($employee->cnic_pictures)
        ? $employee->cnic_pictures
        : (json_decode($employee->cnic_pictures ?? '[]', true) ?? []);

    $otherDocuments = is_array($employee->other_documents)
        ? $employee->other_documents
        : (json_decode($employee->other_documents ?? '[]', true) ?? []);

    $profilePicture = $pictures[0] ?? null;
@endphp

<div class="employee-page">

    {{-- PAGE HEADER --}}
    <div class="employee-page-header">

        <div>
            <h3 class="employee-page-title">
                Employee Details
            </h3>

            <p class="employee-page-subtitle">
                View employee information, CNIC and documents
            </p>
        </div>

        <div class="employee-header-actions">

            <a href="{{ route('employees.index') }}"
               class="page-action-btn back-action-btn">
                <i class="bi bi-arrow-left"></i>
                Back
            </a>

            <a href="{{ route('employees.edit', $employee->id) }}"
               class="page-action-btn edit-action-btn">
                <i class="bi bi-pencil-square"></i>
                Edit Employee
            </a>

            <form action="{{ route('employees.destroy', $employee->id) }}"
                  method="POST">

                @csrf
                @method('DELETE')

                <button type="submit"
                        class="page-action-btn delete-action-btn"
                        onclick="return confirm('Are you sure you want to delete this employee?')">

                    <i class="bi bi-trash3"></i>
                    Delete Employee
                </button>

            </form>

        </div>

    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row g-4">

        {{-- LEFT SIDE --}}
        <div class="col-xl-4 col-lg-5">

            <div class="employee-side-card">

                {{-- PROFILE TOP SECTION --}}
                <div class="employee-profile-top">

                    <div class="profile-layout">

                        {{-- Profile Image --}}
                        <div class="profile-left">

                            <div class="profile-picture-box">

                                @if($profilePicture)

                                    <img src="{{ asset('storage/' . $profilePicture) }}"
                                         alt="{{ $employee->name }}"
                                         class="employee-profile-image">

                                @else

                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($employee->name) }}&background=111111&color=ffffff&size=200"
                                         alt="{{ $employee->name }}"
                                         class="employee-profile-image">

                                @endif

                            </div>

                            @if($profilePicture)

                                <div class="profile-actions">

                                    <a href="{{ asset('storage/' . $profilePicture) }}"
                                       target="_blank"
                                       class="profile-action-icon"
                                       title="View profile picture">

                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <a href="{{ asset('storage/' . $profilePicture) }}"
                                       download="{{ basename($profilePicture) }}"
                                       class="profile-action-icon"
                                       title="Download profile picture">

                                        <i class="bi bi-download"></i>
                                    </a>

                                </div>

                            @endif

                        </div>

                        {{-- Profile Details --}}
                        <div class="profile-right">

                            <span class="employee-code-label">
                                {{ $employee->employee_code ?? 'Employee' }}
                            </span>

                            <h4 class="employee-profile-name">
                                {{ $employee->name }}
                            </h4>

                            <div class="profile-detail-line">
                                <i class="bi bi-building"></i>

                                <span>
                                    {{ $employee->department ?? 'No Department' }}
                                </span>
                            </div>

                            <div class="profile-detail-line">
                                <i class="bi bi-person-badge"></i>

                                <span>
                                    {{ $employee->designation ?? 'No Designation' }}
                                </span>
                            </div>

                            <div class="mt-3">

                                @if($employee->status === 'active')

                                    <span class="employee-status active-status">
                                        <span></span>
                                        Active
                                    </span>

                                @else

                                    <span class="employee-status inactive-status">
                                        <span></span>
                                        Inactive
                                    </span>

                                @endif

                            </div>

                        </div>

                    </div>

                </div>

                {{-- CNIC SECTION --}}
                <div class="side-section">

                    <div class="side-section-header">

                        <div>
                            <h5 class="side-section-title">
                                CNIC Pictures
                            </h5>

                            <small class="side-section-description">
                                Front and back side
                            </small>
                        </div>

                        <span class="black-count-badge">
                            {{ count($cnicPictures) }}
                        </span>

                    </div>

                    @if(count($cnicPictures) > 0)

                        <div class="row g-3 mt-1">

                            @foreach($cnicPictures as $index => $cnicPicture)

                                <div class="col-6">

                                    <div class="cnic-card">

                                        <div class="cnic-image-wrapper">

                                            <img src="{{ asset('storage/' . $cnicPicture) }}"
                                                 alt="CNIC Picture"
                                                 class="cnic-image">

                                            <div class="cnic-actions">

                                                <a href="{{ asset('storage/' . $cnicPicture) }}"
                                                   target="_blank"
                                                   class="document-action-icon"
                                                   title="View CNIC">

                                                    <i class="bi bi-eye"></i>
                                                </a>

                                                <a href="{{ asset('storage/' . $cnicPicture) }}"
                                                   download="{{ basename($cnicPicture) }}"
                                                   class="document-action-icon"
                                                   title="Download CNIC">

                                                    <i class="bi bi-download"></i>
                                                </a>

                                            </div>

                                        </div>

                                        <div class="cnic-title">

                                            @if($index === 0)
                                                CNIC Front
                                            @elseif($index === 1)
                                                CNIC Back
                                            @else
                                                CNIC {{ $index + 1 }}
                                            @endif

                                        </div>

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    @else

                        <div class="empty-document-box">

                            <i class="bi bi-card-image"></i>

                            <span>
                                No CNIC pictures uploaded
                            </span>

                        </div>

                    @endif

                </div>

                {{-- OTHER DOCUMENTS --}}
                <div class="side-section border-0">

                    <div class="side-section-header">

                        <div>
                            <h5 class="side-section-title">
                                Other Documents
                            </h5>

                            <small class="side-section-description">
                                Employee additional files
                            </small>
                        </div>

                        <span class="black-count-badge">
                            {{ count($otherDocuments) }}
                        </span>

                    </div>

                    @if(count($otherDocuments) > 0)

                        <div class="documents-list">

                            @foreach($otherDocuments as $index => $document)

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

                                <div class="document-row">

                                    <div class="document-preview">

                                        @if($isImage)

                                            <img src="{{ asset('storage/' . $document) }}"
                                                 alt="Document"
                                                 class="document-preview-image">

                                        @else

                                            <div class="document-type-icon">

                                                @if($extension === 'pdf')

                                                    <i class="bi bi-file-earmark-pdf"></i>

                                                @elseif(in_array($extension, ['doc', 'docx']))

                                                    <i class="bi bi-file-earmark-word"></i>

                                                @else

                                                    <i class="bi bi-file-earmark"></i>

                                                @endif

                                            </div>

                                        @endif

                                    </div>

                                    <div class="document-info">

                                        <strong>
                                            Document {{ $index + 1 }}
                                        </strong>

                                        <small title="{{ basename($document) }}">
                                            {{ basename($document) }}
                                        </small>

                                    </div>

                                    <div class="document-row-actions">

                                        <a href="{{ asset('storage/' . $document) }}"
                                           target="_blank"
                                           class="small-black-action"
                                           title="View document">

                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <a href="{{ asset('storage/' . $document) }}"
                                           download="{{ basename($document) }}"
                                           class="small-black-action"
                                           title="Download document">

                                            <i class="bi bi-download"></i>
                                        </a>

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    @else

                        <div class="empty-document-box">

                            <i class="bi bi-file-earmark"></i>

                            <span>
                                No other documents uploaded
                            </span>

                        </div>

                    @endif

                </div>

                {{-- EXTRA EMPLOYEE PICTURES --}}
                @if(count($pictures) > 1)

                    <div class="side-section border-0">

                        <div class="side-section-header">

                            <div>
                                <h5 class="side-section-title">
                                    Other Pictures
                                </h5>

                                <small class="side-section-description">
                                    Additional employee pictures
                                </small>
                            </div>

                            <span class="black-count-badge">
                                {{ count($pictures) - 1 }}
                            </span>

                        </div>

                        <div class="row g-3 mt-1">

                            @foreach(array_slice($pictures, 1) as $index => $picture)

                                <div class="col-6">

                                    <div class="cnic-card">

                                        <div class="cnic-image-wrapper">

                                            <img src="{{ asset('storage/' . $picture) }}"
                                                 alt="Employee Picture"
                                                 class="cnic-image">

                                            <div class="cnic-actions">

                                                <a href="{{ asset('storage/' . $picture) }}"
                                                   target="_blank"
                                                   class="document-action-icon">

                                                    <i class="bi bi-eye"></i>
                                                </a>

                                                <a href="{{ asset('storage/' . $picture) }}"
                                                   download="{{ basename($picture) }}"
                                                   class="document-action-icon">

                                                    <i class="bi bi-download"></i>
                                                </a>

                                            </div>

                                        </div>

                                        <div class="cnic-title">
                                            Picture {{ $index + 2 }}
                                        </div>

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    </div>

                @endif

            </div>

        </div>

        {{-- RIGHT SIDE INFORMATION --}}
        <div class="col-xl-8 col-lg-7">

            <div class="employee-info-card">

                <div class="employee-info-header">

                    <div>
                        <h4>Personal Information</h4>

                        <p>
                            Complete employee information
                        </p>
                    </div>

                    <div class="header-black-icon">
                        <i class="bi bi-person-vcard"></i>
                    </div>

                </div>

                <div class="employee-info-body">

                    <div class="row g-3">

                        <div class="col-md-6">

                            <div class="employee-info-box">

                                <span>
                                    Employee Code
                                </span>

                                <strong>
                                    {{ $employee->employee_code ?? '-' }}
                                </strong>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="employee-info-box">

                                <span>
                                    Employee Name
                                </span>

                                <strong>
                                    {{ $employee->name }}
                                </strong>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="employee-info-box">

                                <span>
                                    Father Name
                                </span>

                                <strong>
                                    {{ $employee->father_name ?? '-' }}
                                </strong>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="employee-info-box">

                                <span>
                                    Phone
                                </span>

                                <strong>
                                    {{ $employee->phone ?? '-' }}
                                </strong>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="employee-info-box">

                                <span>
                                    CNIC
                                </span>

                                <strong>
                                    {{ $employee->cnic ?? '-' }}
                                </strong>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="employee-info-box">

                                <span>
                                    Email
                                </span>

                                <strong>
                                    {{ $employee->email ?? '-' }}
                                </strong>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="employee-info-box">

                                <span>
                                    Department
                                </span>

                                <strong>
                                    {{ $employee->department ?? '-' }}
                                </strong>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="employee-info-box">

                                <span>
                                    Designation
                                </span>

                                <strong>
                                    {{ $employee->designation ?? '-' }}
                                </strong>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="employee-info-box">

                                <span>
                                    Basic Salary
                                </span>

                                <strong>
                                    Rs {{ number_format($employee->basic_salary ?? 0, 2) }}
                                </strong>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="employee-info-box">

                                <span>
                                    Joining Date
                                </span>

                                <strong>
                                    {{ $employee->joining_date
                                        ? \Carbon\Carbon::parse($employee->joining_date)->format('d M Y')
                                        : '-' }}
                                </strong>

                            </div>

                        </div>

                        <div class="col-12">

                            <div class="employee-info-box address-info-box">

                                <span>
                                    Address
                                </span>

                                <strong>
                                    {{ $employee->address ?? '-' }}
                                </strong>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

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

.employee-page {
    color: var(--employee-black);
}

.employee-page-header {
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
}

.employee-page-title {
    margin: 0;
    color: var(--employee-black);
    font-size: 28px;
    font-weight: 900;
}

.employee-page-subtitle {
    margin: 3px 0 0;
    color: var(--employee-muted);
    font-size: 13px;
}

.employee-header-actions {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

.page-action-btn {
    min-height: 39px;
    padding: 9px 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    border: 1px solid var(--employee-black);
    border-radius: 8px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 800;
    cursor: pointer;
}

.back-action-btn {
    color: var(--employee-white);
    background: var(--employee-black);
}

.edit-action-btn {
    color: var(--employee-black);
    background: var(--employee-white);
}

.delete-action-btn {
    color: var(--employee-white);
    background: var(--employee-black);
}

.page-action-btn:hover {
    color: var(--employee-white);
    background: #333333;
}

.employee-side-card,
.employee-info-card {
    overflow: hidden;
    border: 1px solid var(--employee-border);
    border-radius: 14px;
    background: var(--employee-white);
    box-shadow: 0 6px 22px rgba(17, 24, 39, 0.06);
}

.employee-profile-top {
    padding: 26px 22px;
    border-bottom: 1px solid var(--employee-border);
}

.profile-layout {
    display: flex;
    align-items: center;
    gap: 22px;
}

.profile-left {
    flex: 0 0 150px;
    width: 150px;
    text-align: center;
}

.profile-picture-box {
    width: 145px;
    height: 180px;
    overflow: hidden;
    border: 2px solid #111;
    border-radius: 8px;   /* ya 0px agar bilkul square chahiye */
    background: #eee;
    box-shadow: 0 6px 18px rgba(0,0,0,.15);
}

.employee-profile-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.profile-actions {
    margin-top: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
}

.profile-action-icon {
    width: 37px;
    height: 37px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--employee-black);
    border-radius: 50%;
    color: var(--employee-white);
    background: var(--employee-black);
    text-decoration: none;
    font-size: 15px;
    transition: 0.2s ease;
}

.profile-action-icon:hover {
    color: var(--employee-black);
    background: var(--employee-white);
    transform: translateY(-2px);
}

.profile-right {
    min-width: 0;
    flex: 1;
    text-align: left;
}

.employee-code-label {
    display: inline-flex;
    margin-bottom: 8px;
    padding: 5px 9px;
    border-radius: 6px;
    color: var(--employee-white);
    background: var(--employee-black);
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.employee-profile-name {
    margin: 0 0 12px;
    color: var(--employee-black);
    font-size: 24px;
    font-weight: 900;
    line-height: 1.2;
}

.profile-detail-line {
    margin-top: 7px;
    display: flex;
    align-items: center;
    gap: 8px;
    color: #50555e;
    font-size: 13px;
    font-weight: 700;
}

.profile-detail-line i {
    width: 17px;
    color: var(--employee-black);
    font-size: 15px;
    text-align: center;
}

.employee-status {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 6px 11px;
    border: 1px solid var(--employee-black);
    border-radius: 50px;
    color: var(--employee-black);
    background: var(--employee-white);
    font-size: 12px;
    font-weight: 800;
}

.employee-status span {
    width: 7px;
    height: 7px;
    border-radius: 50%;
}

.active-status span {
    background: #198754;
}

.inactive-status span {
    background: #dc3545;
}

.side-section {
    padding: 22px;
    border-bottom: 1px solid var(--employee-border);
}

.side-section-header {
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.side-section-title {
    margin: 0;
    color: var(--employee-black);
    font-size: 15px;
    font-weight: 900;
}

.side-section-description {
    color: var(--employee-muted);
    font-size: 11px;
}

.black-count-badge {
    min-width: 27px;
    height: 27px;
    padding: 0 7px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50px;
    color: var(--employee-white);
    background: var(--employee-black);
    font-size: 11px;
    font-weight: 800;
}

.cnic-card {
    overflow: hidden;
    border: 1px solid var(--employee-border);
    border-radius: 10px;
    background: var(--employee-white);
}

.cnic-image-wrapper {
    position: relative;
    height: 125px;
    overflow: hidden;
    background: #eeeeee;
}

.cnic-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.cnic-actions {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    background: rgba(0, 0, 0, 0.62);
    opacity: 0;
    transition: opacity 0.2s ease;
}

.cnic-image-wrapper:hover .cnic-actions {
    opacity: 1;
}

.document-action-icon {
    width: 36px;
    height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--employee-white);
    border-radius: 50%;
    color: var(--employee-black);
    background: var(--employee-white);
    text-decoration: none;
}

.document-action-icon:hover {
    color: var(--employee-white);
    background: var(--employee-black);
}

.cnic-title {
    padding: 9px;
    color: var(--employee-black);
    text-align: center;
    font-size: 12px;
    font-weight: 800;
}

.documents-list {
    display: flex;
    flex-direction: column;
    gap: 9px;
}

.document-row {
    padding: 9px;
    display: flex;
    align-items: center;
    gap: 10px;
    border: 1px solid var(--employee-border);
    border-radius: 10px;
    background: var(--employee-light);
}

.document-preview {
    flex: 0 0 48px;
    width: 48px;
    height: 48px;
}

.document-preview-image {
    width: 48px;
    height: 48px;
    object-fit: cover;
    border-radius: 7px;
}

.document-type-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 7px;
    color: var(--employee-white);
    background: var(--employee-black);
    font-size: 21px;
}

.document-info {
    min-width: 0;
    flex: 1;
}

.document-info strong {
    display: block;
    color: var(--employee-black);
    font-size: 12px;
}

.document-info small {
    display: block;
    overflow: hidden;
    color: var(--employee-muted);
    font-size: 10px;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.document-row-actions {
    display: flex;
    gap: 5px;
}

.small-black-action {
    width: 31px;
    height: 31px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--employee-black);
    border-radius: 7px;
    color: var(--employee-white);
    background: var(--employee-black);
    text-decoration: none;
}

.small-black-action:hover {
    color: var(--employee-black);
    background: var(--employee-white);
}

.empty-document-box {
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
    font-size: 12px;
}

.empty-document-box i {
    color: var(--employee-black);
    font-size: 25px;
}

.employee-info-card {
    min-height: 100%;
}

.employee-info-header {
    min-height: 84px;
    padding: 20px 22px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid var(--employee-border);
}

.employee-info-header h4 {
    margin: 0;
    color: var(--employee-black);
    font-size: 19px;
    font-weight: 900;
}

.employee-info-header p {
    margin: 3px 0 0;
    color: var(--employee-muted);
    font-size: 12px;
}

.header-black-icon {
    width: 42px;
    height: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 11px;
    color: var(--employee-white);
    background: var(--employee-black);
    font-size: 20px;
}

.employee-info-body {
    padding: 22px;
}

.employee-info-box {
    min-height: 82px;
    padding: 15px 16px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    border: 1px solid var(--employee-border);
    border-radius: 10px;
    background: var(--employee-light);
    transition: 0.2s ease;
}

.employee-info-box:hover {
    border-color: #bbbbbb;
    background: var(--employee-white);
    transform: translateY(-1px);
}

.employee-info-box span {
    margin-bottom: 5px;
    color: var(--employee-muted);
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.45px;
}

.employee-info-box strong {
    color: var(--employee-black);
    font-size: 14px;
    font-weight: 900;
    word-break: break-word;
}

.address-info-box {
    min-height: 78px;
}

@media (max-width: 1199px) {
    .profile-layout {
        align-items: flex-start;
    }

    .profile-left {
        flex-basis: 125px;
        width: 125px;
    }

    .profile-picture-box {
        width: 120px;
        height: 120px;
    }

    .employee-profile-name {
        font-size: 20px;
    }
}

@media (max-width: 991px) {
    .employee-page-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .employee-header-actions {
        width: 100%;
    }
}

@media (max-width: 575px) {
    .employee-page-title {
        font-size: 23px;
    }

    .employee-header-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
    }

    .employee-header-actions form {
        grid-column: 1 / -1;
    }

    .employee-header-actions form button {
        width: 100%;
    }

    .profile-layout {
        align-items: center;
        flex-direction: column;
    }

    .profile-left {
        width: 100%;
        flex-basis: auto;
    }

    .profile-right {
        width: 100%;
        text-align: center;
    }

    .profile-detail-line {
        justify-content: center;
    }

    .employee-profile-name {
        font-size: 22px;
    }

    .cnic-image-wrapper {
        height: 105px;
    }

    .document-row-actions {
        flex-direction: column;
    }
}
</style>

@endsection
