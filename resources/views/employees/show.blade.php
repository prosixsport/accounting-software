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

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h3 class="fw-bold mb-0">Employee Details</h3>
        <small class="text-muted">
            View employee information, pictures and documents
        </small>
    </div>

    <div class="d-flex flex-wrap gap-2">

        <a href="{{ route('employees.index') }}"
           class="btn btn-secondary">
            Back
        </a>

        <a href="{{ route('employees.edit', $employee->id) }}"
           class="btn btn-warning">
            Edit Employee
        </a>

        <form action="{{ route('employees.destroy', $employee->id) }}"
              method="POST">

            @csrf
            @method('DELETE')

            <button type="submit"
                    class="btn btn-danger"
                    onclick="return confirm('Are you sure you want to delete this employee?')">
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

    {{-- Employee profile card --}}
    <div class="col-lg-4">

        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-4">

                @if($profilePicture)
                    <a href="{{ asset('storage/' . $profilePicture) }}"
                       target="_blank"
                       class="d-inline-block">

                        <img src="{{ asset('storage/' . $profilePicture) }}"
                             alt="{{ $employee->name }}"
                             class="employee-profile-image">
                    </a>
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($employee->name) }}&background=0D6EFD&color=fff&size=200"
                         alt="{{ $employee->name }}"
                         class="employee-profile-image">
                @endif

                <h4 class="fw-bold mt-3 mb-1">
                    {{ $employee->name }}
                </h4>

                <p class="text-muted mb-2">
                    {{ $employee->designation ?? 'No designation' }}
                </p>

                @if($employee->status === 'active')
                    <span class="badge bg-success px-3 py-2">
                        Active
                    </span>
                @else
                    <span class="badge bg-danger px-3 py-2">
                        Inactive
                    </span>
                @endif

                @if($profilePicture)
                    <div class="d-flex justify-content-center gap-2 mt-3">

                        <a href="{{ asset('storage/' . $profilePicture) }}"
                           target="_blank"
                           class="btn btn-outline-primary btn-sm">
                            Open Picture
                        </a>

                        <a href="{{ asset('storage/' . $profilePicture) }}"
                           download="{{ basename($profilePicture) }}"
                           class="btn btn-primary btn-sm">
                            Download
                        </a>

                    </div>
                @endif

            </div>
        </div>

    </div>

    {{-- Employee information --}}
    <div class="col-lg-8">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-header bg-white border-bottom py-3">
                <h5 class="fw-bold mb-0">
                    Personal Information
                </h5>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-6">
                        <div class="detail-box">
                            <span class="detail-label">Employee Code</span>
                            <strong>
                                {{ $employee->employee_code ?? '-' }}
                            </strong>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="detail-box">
                            <span class="detail-label">Employee Name</span>
                            <strong>
                                {{ $employee->name }}
                            </strong>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="detail-box">
                            <span class="detail-label">Father Name</span>
                            <strong>
                                {{ $employee->father_name ?? '-' }}
                            </strong>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="detail-box">
                            <span class="detail-label">Phone</span>
                            <strong>
                                {{ $employee->phone ?? '-' }}
                            </strong>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="detail-box">
                            <span class="detail-label">CNIC</span>
                            <strong>
                                {{ $employee->cnic ?? '-' }}
                            </strong>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="detail-box">
                            <span class="detail-label">Email</span>
                            <strong>
                                {{ $employee->email ?? '-' }}
                            </strong>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="detail-box">
                            <span class="detail-label">Department</span>
                            <strong>
                                {{ $employee->department ?? '-' }}
                            </strong>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="detail-box">
                            <span class="detail-label">Designation</span>
                            <strong>
                                {{ $employee->designation ?? '-' }}
                            </strong>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="detail-box">
                            <span class="detail-label">Basic Salary</span>
                            <strong class="text-success">
                                Rs {{ number_format($employee->basic_salary ?? 0, 2) }}
                            </strong>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="detail-box">
                            <span class="detail-label">Joining Date</span>
                            <strong>
                                {{ $employee->joining_date
                                    ? \Carbon\Carbon::parse($employee->joining_date)->format('d M Y')
                                    : '-' }}
                            </strong>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="detail-box">
                            <span class="detail-label">Address</span>
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

{{-- Employee pictures --}}
<div class="card border-0 shadow-sm mt-4">

    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">
                Employee Pictures
            </h5>

            <span class="badge bg-primary">
                {{ count($pictures) }} Files
            </span>
        </div>
    </div>

    <div class="card-body">

        @if(count($pictures) > 0)

            <div class="row g-3">

                @foreach($pictures as $index => $picture)

                    <div class="col-xl-3 col-lg-4 col-md-6">

                        <div class="document-card">

                            <a href="{{ asset('storage/' . $picture) }}"
                               target="_blank">

                                <img src="{{ asset('storage/' . $picture) }}"
                                     alt="Employee Picture {{ $index + 1 }}"
                                     class="document-image">
                            </a>

                            <div class="p-3">

                                <h6 class="text-truncate mb-3"
                                    title="{{ basename($picture) }}">
                                    Picture {{ $index + 1 }}
                                </h6>

                                <div class="d-flex gap-2">

                                    <a href="{{ asset('storage/' . $picture) }}"
                                       target="_blank"
                                       class="btn btn-outline-primary btn-sm flex-fill">
                                        Open
                                    </a>

                                    <a href="{{ asset('storage/' . $picture) }}"
                                       download="{{ basename($picture) }}"
                                       class="btn btn-primary btn-sm flex-fill">
                                        Download
                                    </a>

                                </div>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

        @else

            <div class="empty-state">
                <div class="empty-icon">🖼️</div>
                <h6>No employee pictures uploaded</h6>
                <p class="text-muted mb-0">
                    Employee pictures will appear here.
                </p>
            </div>

        @endif

    </div>
</div>

{{-- CNIC pictures --}}
<div class="card border-0 shadow-sm mt-4">

    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">
                CNIC Pictures
            </h5>

            <span class="badge bg-info text-dark">
                {{ count($cnicPictures) }} Files
            </span>
        </div>
    </div>

    <div class="card-body">

        @if(count($cnicPictures) > 0)

            <div class="row g-3">

                @foreach($cnicPictures as $index => $cnicPicture)

                    <div class="col-xl-3 col-lg-4 col-md-6">

                        <div class="document-card">

                            <a href="{{ asset('storage/' . $cnicPicture) }}"
                               target="_blank">

                                <img src="{{ asset('storage/' . $cnicPicture) }}"
                                     alt="CNIC Picture {{ $index + 1 }}"
                                     class="document-image">
                            </a>

                            <div class="p-3">

                                <h6 class="text-truncate mb-1">
                                    {{ $index === 0 ? 'CNIC Front' : ($index === 1 ? 'CNIC Back' : 'CNIC Picture ' . ($index + 1)) }}
                                </h6>

                                <small class="text-muted d-block text-truncate mb-3"
                                       title="{{ basename($cnicPicture) }}">
                                    {{ basename($cnicPicture) }}
                                </small>

                                <div class="d-flex gap-2">

                                    <a href="{{ asset('storage/' . $cnicPicture) }}"
                                       target="_blank"
                                       class="btn btn-outline-primary btn-sm flex-fill">
                                        Open
                                    </a>

                                    <a href="{{ asset('storage/' . $cnicPicture) }}"
                                       download="{{ basename($cnicPicture) }}"
                                       class="btn btn-primary btn-sm flex-fill">
                                        Download
                                    </a>

                                </div>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

        @else

            <div class="empty-state">
                <div class="empty-icon">🪪</div>
                <h6>No CNIC pictures uploaded</h6>
                <p class="text-muted mb-0">
                    CNIC front and back pictures will appear here.
                </p>
            </div>

        @endif

    </div>
</div>

{{-- Other documents --}}
<div class="card border-0 shadow-sm mt-4 mb-4">

    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">
                Other Documents
            </h5>

            <span class="badge bg-dark">
                {{ count($otherDocuments) }} Files
            </span>
        </div>
    </div>

    <div class="card-body">

        @if(count($otherDocuments) > 0)

            <div class="row g-3">

                @foreach($otherDocuments as $index => $document)

                    @php
                        $extension = strtolower(pathinfo($document, PATHINFO_EXTENSION));

                        $isImage = in_array($extension, [
                            'jpg',
                            'jpeg',
                            'png',
                            'webp',
                            'gif',
                        ]);
                    @endphp

                    <div class="col-xl-3 col-lg-4 col-md-6">

                        <div class="document-card h-100">

                            @if($isImage)

                                <a href="{{ asset('storage/' . $document) }}"
                                   target="_blank">

                                    <img src="{{ asset('storage/' . $document) }}"
                                         alt="Document {{ $index + 1 }}"
                                         class="document-image">
                                </a>

                            @else

                                <a href="{{ asset('storage/' . $document) }}"
                                   target="_blank"
                                   class="document-placeholder">

                                    @if($extension === 'pdf')
                                        <span class="file-extension pdf-file">
                                            PDF
                                        </span>
                                    @elseif(in_array($extension, ['doc', 'docx']))
                                        <span class="file-extension word-file">
                                            WORD
                                        </span>
                                    @else
                                        <span class="file-extension">
                                            {{ strtoupper($extension) }}
                                        </span>
                                    @endif

                                </a>

                            @endif

                            <div class="p-3">

                                <h6 class="mb-1">
                                    Document {{ $index + 1 }}
                                </h6>

                                <small class="text-muted d-block text-truncate mb-3"
                                       title="{{ basename($document) }}">
                                    {{ basename($document) }}
                                </small>

                                <div class="d-flex gap-2">

                                    <a href="{{ asset('storage/' . $document) }}"
                                       target="_blank"
                                       class="btn btn-outline-primary btn-sm flex-fill">
                                        Open
                                    </a>

                                    <a href="{{ asset('storage/' . $document) }}"
                                       download="{{ basename($document) }}"
                                       class="btn btn-primary btn-sm flex-fill">
                                        Download
                                    </a>

                                </div>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

        @else

            <div class="empty-state">
                <div class="empty-icon">📄</div>
                <h6>No other documents uploaded</h6>
                <p class="text-muted mb-0">
                    PDF, Word and other documents will appear here.
                </p>
            </div>

        @endif

    </div>
</div>

<style>
.employee-profile-image {
    width: 155px;
    height: 155px;
    object-fit: cover;
    border-radius: 50%;
    border: 5px solid #ffffff;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.14);
}

.detail-box {
    height: 100%;
    min-height: 76px;
    padding: 14px 16px;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    background: #f8f9fa;
}

.detail-label {
    display: block;
    margin-bottom: 5px;
    color: #6c757d;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}

.document-card {
    overflow: hidden;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    background: #ffffff;
    transition: all 0.2s ease;
}

.document-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 22px rgba(0, 0, 0, 0.10);
}

.document-image {
    display: block;
    width: 100%;
    height: 210px;
    object-fit: cover;
    background: #f1f3f5;
}

.document-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 210px;
    background: #f8f9fa;
    text-decoration: none;
}

.file-extension {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 92px;
    height: 92px;
    border-radius: 18px;
    background: #6c757d;
    color: #ffffff;
    font-size: 18px;
    font-weight: 800;
}

.pdf-file {
    background: #dc3545;
}

.word-file {
    background: #0d6efd;
}

.empty-state {
    padding: 45px 20px;
    text-align: center;
    border: 2px dashed #dee2e6;
    border-radius: 12px;
    background: #f8f9fa;
}

.empty-icon {
    margin-bottom: 12px;
    font-size: 45px;
}

@media (max-width: 767px) {
    .employee-profile-image {
        width: 125px;
        height: 125px;
    }

    .document-image,
    .document-placeholder {
        height: 230px;
    }
}
</style>

@endsection
