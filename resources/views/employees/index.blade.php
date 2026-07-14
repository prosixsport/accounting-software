@extends('layouts.app')

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold mb-0">Employees</h3>
        <small class="text-muted">Manage company employees</small>
    </div>

    <a href="{{ route('employees.create') }}" class="btn btn-primary">
        + Add Employee
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

{{-- Department quick buttons --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">

        <div class="d-flex flex-wrap align-items-center gap-2">

            <span class="fw-semibold me-2">
                Departments:
            </span>

            <button type="button"
                    class="btn btn-sm btn-primary department-btn active"
                    data-department="">
                All
            </button>

            @foreach($departments as $department)
                <button type="button"
                        class="btn btn-sm btn-outline-primary department-btn"
                        data-department="{{ strtolower(trim($department)) }}">
                    {{ $department }}
                </button>
            @endforeach

        </div>

    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">

        <div class="row g-3 align-items-end">

            <div class="col-lg-4 col-md-6">
                <label class="form-label fw-semibold">
                    Search Employee
                </label>

                <input type="text"
                       id="employeeSearch"
                       class="form-control"
                       placeholder="Search by name, department or designation">
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label fw-semibold">
                    Select Department
                </label>

                <select id="departmentFilter" class="form-select">
                    <option value="">All Departments</option>

                    @foreach($departments as $department)
                        <option value="{{ strtolower(trim($department)) }}">
                            {{ $department }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label fw-semibold">
                    Select Designation
                </label>

                <select id="designationFilter" class="form-select">
                    <option value="">All Designations</option>

                    @foreach($designations as $designation)
                        <option value="{{ strtolower(trim($designation)) }}">
                            {{ $designation }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-2 col-md-6">
                <button type="button"
                        id="resetFilters"
                        class="btn btn-outline-secondary w-100">
                    Reset Filters
                </button>
            </div>

        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">

            <small class="text-muted">
                Showing
                <strong id="visibleEmployeeCount">
                    {{ $employees->count() }}
                </strong>
                of
                <strong>
                    {{ $employees->count() }}
                </strong>
                employees
            </small>

            <small id="selectedFilterText"
                   class="text-primary fw-semibold">
                All Departments
            </small>

        </div>

    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th width="90">Profile</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Designation</th>
                        <th>Salary</th>
                        <th>Status</th>
                        <th width="250">Action</th>
                    </tr>
                </thead>

                <tbody id="employeeTableBody">

                    @forelse($employees as $employee)

                        @php
                            $profile = $employee->pictures[0] ?? null;

                            $employeeName = strtolower(trim($employee->name ?? ''));
                            $employeeDepartment = strtolower(trim($employee->department ?? ''));
                            $employeeDesignation = strtolower(trim($employee->designation ?? ''));
                        @endphp

                        <tr class="employee-row"
                            data-name="{{ $employeeName }}"
                            data-department="{{ $employeeDepartment }}"
                            data-designation="{{ $employeeDesignation }}">

                            <td>
                                @if($profile)

                                    <a href="{{ asset('storage/' . $profile) }}"
                                       target="_blank"
                                       title="Open profile picture">

                                        <img src="{{ asset('storage/' . $profile) }}"
                                             alt="{{ $employee->name }}"
                                             width="52"
                                             height="52"
                                             class="rounded-circle border shadow-sm employee-avatar">
                                    </a>

                                @else

                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($employee->name) }}&background=0D6EFD&color=fff&size=80"
                                         alt="{{ $employee->name }}"
                                         width="52"
                                         height="52"
                                         class="rounded-circle border shadow-sm employee-avatar">

                                @endif
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $employee->name }}
                                </div>

                                <small class="text-muted">
                                    {{ $employee->employee_code ?? '-' }}
                                </small>
                            </td>

                            <td>
                                @if($employee->department)
                                    <span class="department-badge">
                                        {{ $employee->department }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td>
                                {{ $employee->designation ?? '-' }}
                            </td>

                            <td class="fw-semibold">
                                Rs {{ number_format($employee->basic_salary, 2) }}
                            </td>

                            <td>
                                @if($employee->status === 'active')
                                    <span class="badge bg-success">
                                        Active
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        Inactive
                                    </span>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex flex-wrap gap-1">

                                    <a href="{{ route('employees.show', $employee->id) }}"
                                       class="btn btn-info btn-sm text-white">
                                        View
                                    </a>

                                    <a href="{{ route('employees.edit', $employee->id) }}"
                                       class="btn btn-warning btn-sm">
                                        Edit
                                    </a>

                                    <form action="{{ route('employees.destroy', $employee->id) }}"
                                          method="POST"
                                          class="d-inline">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this employee?')">
                                            Delete
                                        </button>

                                    </form>

                                </div>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7"
                                class="text-center py-5 text-muted">
                                No Employees Found
                            </td>
                        </tr>

                    @endforelse

                    {{-- Filter empty result --}}
                    <tr id="noFilterResult" style="display: none;">
                        <td colspan="7" class="text-center py-5">
                            <div class="filter-empty-icon">
                                🔍
                            </div>

                            <h5 class="fw-bold mb-1">
                                No Matching Employees
                            </h5>

                            <p class="text-muted mb-0">
                                Change department, designation or search text.
                            </p>
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

    </div>
</div>

<style>
.employee-avatar {
    object-fit: cover;
    transition: transform 0.2s ease;
}

.employee-avatar:hover {
    transform: scale(1.08);
}

.department-badge {
    display: inline-flex;
    align-items: center;
    padding: 5px 10px;
    border-radius: 50px;
    background: rgba(13, 110, 253, 0.1);
    color: #0d6efd;
    font-size: 12px;
    font-weight: 700;
}

.department-btn {
    border-radius: 50px;
    padding-left: 15px;
    padding-right: 15px;
}

.filter-empty-icon {
    margin-bottom: 10px;
    font-size: 40px;
}

.table > :not(caption) > * > * {
    padding-top: 12px;
    padding-bottom: 12px;
}

@media (max-width: 767px) {
    .department-btn {
        font-size: 12px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('employeeSearch');
    const departmentFilter = document.getElementById('departmentFilter');
    const designationFilter = document.getElementById('designationFilter');
    const resetButton = document.getElementById('resetFilters');

    const departmentButtons = document.querySelectorAll('.department-btn');
    const employeeRows = document.querySelectorAll('.employee-row');

    const visibleEmployeeCount = document.getElementById(
        'visibleEmployeeCount'
    );

    const noFilterResult = document.getElementById('noFilterResult');
    const selectedFilterText = document.getElementById(
        'selectedFilterText'
    );

    function normalize(value) {
        return String(value || '')
            .trim()
            .toLowerCase();
    }

    function updateDepartmentButtons(selectedDepartment) {
        departmentButtons.forEach(function (button) {
            const buttonDepartment = normalize(
                button.dataset.department
            );

            button.classList.remove(
                'btn-primary',
                'active'
            );

            button.classList.add(
                'btn-outline-primary'
            );

            if (buttonDepartment === selectedDepartment) {
                button.classList.remove(
                    'btn-outline-primary'
                );

                button.classList.add(
                    'btn-primary',
                    'active'
                );
            }
        });
    }

    function updateSelectedFilterText() {
        const departmentText =
            departmentFilter.options[
                departmentFilter.selectedIndex
            ]?.text || 'All Departments';

        const designationText =
            designationFilter.options[
                designationFilter.selectedIndex
            ]?.text || 'All Designations';

        if (
            departmentFilter.value === '' &&
            designationFilter.value === ''
        ) {
            selectedFilterText.textContent = 'All Employees';
            return;
        }

        selectedFilterText.textContent =
            departmentText + ' / ' + designationText;
    }

    function filterEmployees() {
        const searchValue = normalize(searchInput.value);
        const selectedDepartment = normalize(
            departmentFilter.value
        );

        const selectedDesignation = normalize(
            designationFilter.value
        );

        let visibleCount = 0;

        employeeRows.forEach(function (row) {
            const name = normalize(row.dataset.name);
            const department = normalize(
                row.dataset.department
            );

            const designation = normalize(
                row.dataset.designation
            );

            const completeText = [
                name,
                department,
                designation
            ].join(' ');

            const matchesSearch =
                searchValue === '' ||
                completeText.includes(searchValue);

            const matchesDepartment =
                selectedDepartment === '' ||
                department === selectedDepartment;

            const matchesDesignation =
                selectedDesignation === '' ||
                designation === selectedDesignation;

            const shouldShow =
                matchesSearch &&
                matchesDepartment &&
                matchesDesignation;

            row.style.display = shouldShow ? '' : 'none';

            if (shouldShow) {
                visibleCount++;
            }
        });

        visibleEmployeeCount.textContent = visibleCount;

        noFilterResult.style.display =
            visibleCount === 0 ? '' : 'none';

        updateDepartmentButtons(selectedDepartment);
        updateSelectedFilterText();
    }

    searchInput.addEventListener('input', filterEmployees);

    departmentFilter.addEventListener(
        'change',
        filterEmployees
    );

    designationFilter.addEventListener(
        'change',
        filterEmployees
    );

    departmentButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const selectedDepartment = normalize(
                this.dataset.department
            );

            departmentFilter.value = selectedDepartment;

            filterEmployees();
        });
    });

    resetButton.addEventListener('click', function () {
        searchInput.value = '';
        departmentFilter.value = '';
        designationFilter.value = '';

        filterEmployees();
    });

    filterEmployees();
});
</script>

@endsection
