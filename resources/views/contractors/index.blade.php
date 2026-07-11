@extends('layouts.app')

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h3 class="fw-bold mb-0">
            Contractors
        </h3>

        <small class="text-muted">
            Manage factory contract workers
        </small>
    </div>

    <a href="{{ route('contractors.create') }}"
       class="btn btn-dark">

        <i class="bi bi-plus-lg me-1"></i>
        Add Contractor
    </a>
</div>

{{-- Success Message --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert">
        </button>
    </div>
@endif

{{-- Error Message --}}
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert">
        </button>
    </div>
@endif

{{-- Validation Errors --}}
@if($errors->any())
    <div class="alert alert-danger">

        <strong>
            Please fix the following errors:
        </strong>

        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>

    </div>
@endif

{{-- Search --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">

        <div class="row align-items-end">

            <div class="col-md-8">

                <label class="form-label">
                    Search Contractor
                </label>

                <div class="search-wrapper">

                    <i class="bi bi-search search-icon"></i>

                    <input type="text"
                           id="contractorSearch"
                           class="form-control contractor-search"
                           placeholder="Search name, phone, CNIC, department, machine or status..."
                           autocomplete="off">

                    <button type="button"
                            id="clearContractorSearch"
                            class="clear-search"
                            title="Clear search">

                        <i class="bi bi-x-lg"></i>
                    </button>

                </div>

            </div>

            <div class="col-md-4 mt-3 mt-md-0">

                <div class="result-count">

                    Showing

                    <strong id="visibleContractorCount">
                        {{ $contractors->count() }}
                    </strong>

                    contractor(s)

                </div>

            </div>

        </div>

    </div>
</div>

{{-- Contractors Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle contractor-table">

                <thead>
                    <tr>
                        <th width="90">Profile</th>
                        <th>Contractor Details</th>
                        <th>Department</th>
                        <th>Machine</th>
                        <th>Status</th>
                        <th width="180">Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($contractors as $contractor)

                        @php
                            $searchText = strtolower(
                                implode(' ', [
                                    $contractor->name ?? '',
                                    $contractor->cnic ?? '',
                                    $contractor->phone ?? '',
                                    $contractor->address ?? '',
                                    $contractor->status ?? '',
                                    $contractor->department?->name ?? '',
                                    $contractor->machine?->name ?? '',
                                ])
                            );

                            $photoUrl = null;

                            if ($contractor->photo) {
                                $cleanPhotoPath = ltrim(
                                    str_replace(
                                        'public/',
                                        '',
                                        $contractor->photo
                                    ),
                                    '/'
                                );

                                $photoUrl = asset(
                                    'storage/' . $cleanPhotoPath
                                );
                            }
                        @endphp

                        <tr class="contractor-row"
                            data-search="{{ $searchText }}">

                            {{-- Profile --}}
                            <td>

                                @if($photoUrl)

                                    <img src="{{ $photoUrl }}"
                                         width="55"
                                         height="55"
                                         class="contractor-profile"
                                         alt="{{ $contractor->name }}"
                                         onerror="
                                            this.style.display='none';
                                            this.nextElementSibling.style.display='flex';
                                         ">

                                    <div class="contractor-avatar"
                                         style="display:none;">

                                        {{ strtoupper(
                                            substr(
                                                $contractor->name,
                                                0,
                                                1
                                            )
                                        ) }}

                                    </div>

                                @else

                                    <div class="contractor-avatar">

                                        {{ strtoupper(
                                            substr(
                                                $contractor->name,
                                                0,
                                                1
                                            )
                                        ) }}

                                    </div>

                                @endif

                            </td>

                            {{-- Contractor Details --}}
                            <td>

                                <strong class="contractor-name">
                                    {{ $contractor->name }}
                                </strong>

                                <br>

                                <small class="text-muted">
                                    <i class="bi bi-person-vcard me-1"></i>

                                    {{ $contractor->cnic ?? 'No CNIC' }}
                                </small>

                                <br>

                                <small class="text-muted">
                                    <i class="bi bi-telephone me-1"></i>

                                    {{ $contractor->phone ?? 'No phone' }}
                                </small>

                                @if($contractor->address)
                                    <br>

                                    <small class="text-muted">
                                        <i class="bi bi-geo-alt me-1"></i>

                                        {{ \Illuminate\Support\Str::limit(
                                            $contractor->address,
                                            45
                                        ) }}
                                    </small>
                                @endif

                            </td>

                            {{-- Department --}}
                            <td>

                                @if($contractor->department)

                                    <span class="department-name">
                                        {{ $contractor->department->name }}
                                    </span>

                                @else

                                    <span class="text-muted">
                                        Not assigned
                                    </span>

                                @endif

                            </td>

                            {{-- Machine --}}
                            <td>

                                @if($contractor->machine)

                                    <span class="machine-name">
                                        {{ $contractor->machine->name }}
                                    </span>

                                @else

                                    <span class="text-muted">
                                        No machine
                                    </span>

                                @endif

                            </td>

                            {{-- Status --}}
                            <td>

                                @if($contractor->status === 'active')

                                    <span class="badge bg-success-subtle text-success status-badge">
                                        <span class="status-dot active-dot"></span>
                                        Active
                                    </span>

                                @else

                                    <span class="badge bg-secondary-subtle text-secondary status-badge">
                                        <span class="status-dot inactive-dot"></span>
                                        Inactive
                                    </span>

                                @endif

                            </td>

                            {{-- Actions --}}
                            <td>

                                <div class="d-flex flex-wrap gap-1">

                                    <a href="{{ route(
                                            'contractors.edit',
                                            $contractor->id
                                        ) }}"
                                       class="btn btn-primary btn-sm">

                                        <i class="bi bi-pencil-square me-1"></i>
                                        Edit
                                    </a>

                                    <form method="POST"
                                          action="{{ route(
                                            'contractors.destroy',
                                            $contractor->id
                                          ) }}"
                                          class="d-inline delete-contractor-form">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-danger btn-sm">

                                            <i class="bi bi-trash me-1"></i>
                                            Delete
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr id="emptyContractorRow">

                            <td colspan="6"
                                class="text-center text-muted py-5">

                                <i class="bi bi-people fs-2 d-block mb-2"></i>

                                No contractor found

                            </td>

                        </tr>

                    @endforelse

                    {{-- Search No Result --}}
                    <tr id="noContractorResult"
                        style="display:none;">

                        <td colspan="6"
                            class="text-center text-muted py-5">

                            <i class="bi bi-search fs-2 d-block mb-2"></i>

                            No matching contractor found

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

        {{-- Pagination --}}
        @if($contractors->hasPages())
            <div class="d-flex justify-content-end mt-3">
                {{ $contractors->links() }}
            </div>
        @endif

    </div>
</div>

<style>
.contractor-table {
    min-width: 950px;
}

.contractor-table thead th {
    padding: 14px 12px;
    color: #111827;
    background: #f8fafc;
    border-bottom: 1px solid #d9dee7;
    font-size: 14px;
    font-weight: 700;
    white-space: nowrap;
}

.contractor-table tbody td {
    padding: 14px 12px;
    border-bottom: 1px solid #e5e7eb;
}

.contractor-row {
    transition: background 0.2s ease;
}

.contractor-row:hover {
    background: #fafcff;
}

.contractor-profile {
    width: 55px;
    height: 55px;
    border: 2px solid #e5e7eb;
    border-radius: 50%;
    object-fit: cover;
    background: #ffffff;
}

.contractor-avatar {
    width: 55px;
    height: 55px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #e5e7eb;
    border-radius: 50%;
    color: #ffffff;
    background: #0d6efd;
    font-size: 20px;
    font-weight: 800;
}

.contractor-name {
    display: inline-block;
    margin-bottom: 3px;
    color: #111827;
    font-size: 15px;
}

.department-name,
.machine-name {
    color: #374151;
    font-size: 14px;
    font-weight: 600;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 10px;
    border-radius: 20px;
}

.status-dot {
    width: 7px;
    height: 7px;
    display: inline-block;
    border-radius: 50%;
}

.active-dot {
    background: #198754;
}

.inactive-dot {
    background: #6c757d;
}

.search-wrapper {
    position: relative;
}

.contractor-search {
    height: 44px;
    padding-left: 42px;
    padding-right: 42px;
}

.search-icon {
    position: absolute;
    top: 50%;
    left: 15px;
    z-index: 2;
    color: #87909e;
    transform: translateY(-50%);
    pointer-events: none;
}

.clear-search {
    position: absolute;
    top: 50%;
    right: 8px;
    width: 30px;
    height: 30px;
    display: none;
    align-items: center;
    justify-content: center;
    border: 0;
    border-radius: 50%;
    color: #87909e;
    background: transparent;
    transform: translateY(-50%);
    transition: 0.2s ease;
}

.clear-search:hover {
    color: #dc3545;
    background: #eef2f7;
}

.result-count {
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 5px;
    color: #6b7280;
}

.result-count strong {
    color: #111827;
}

@media (max-width: 767px) {
    .result-count {
        justify-content: flex-start;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const searchInput =
        document.getElementById('contractorSearch');

    const clearButton =
        document.getElementById('clearContractorSearch');

    const contractorRows =
        document.querySelectorAll('.contractor-row');

    const countElement =
        document.getElementById(
            'visibleContractorCount'
        );

    const noResultRow =
        document.getElementById(
            'noContractorResult'
        );

    function filterContractors() {

        const searchValue = (
            searchInput?.value || ''
        )
            .toLowerCase()
            .trim();

        let visibleCount = 0;

        contractorRows.forEach(function (row) {

            const searchableText = (
                row.dataset.search || ''
            ).toLowerCase();

            const matched =
                searchableText.includes(searchValue);

            row.style.display =
                matched ? '' : 'none';

            if (matched) {
                visibleCount++;
            }

        });

        if (countElement) {
            countElement.textContent =
                visibleCount;
        }

        if (noResultRow) {
            noResultRow.style.display =
                visibleCount === 0 &&
                contractorRows.length > 0
                    ? ''
                    : 'none';
        }

        if (clearButton) {
            clearButton.style.display =
                searchValue.length > 0
                    ? 'flex'
                    : 'none';
        }
    }

    if (searchInput) {
        searchInput.addEventListener(
            'input',
            filterContractors
        );
    }

    if (clearButton && searchInput) {

        clearButton.addEventListener(
            'click',
            function () {

                searchInput.value = '';
                filterContractors();
                searchInput.focus();

            }
        );

    }

    document
        .querySelectorAll(
            '.delete-contractor-form'
        )
        .forEach(function (form) {

            form.addEventListener(
                'submit',
                function (event) {

                    const confirmed = confirm(
                        'Are you sure you want to delete this contractor?'
                    );

                    if (!confirmed) {
                        event.preventDefault();
                    }

                }
            );

        });

    filterContractors();

});
</script>

@endsection
