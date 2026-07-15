@extends('layouts.app')

@section('content')

<div class="employees-page">

    {{-- PAGE HEADER --}}
    <div class="employees-page-header">

        <div>
            <h3 class="employees-page-title">
                Employees
            </h3>

            <p class="employees-page-subtitle">
                Manage employees, documents and monthly attendance
            </p>
        </div>

        <a href="{{ route('employees.create') }}"
           class="add-employee-btn">

            <i class="bi bi-person-plus"></i>
            Add Employee

        </a>

    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))

        <div class="alert alert-success employee-alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
        </div>

    @endif

    {{-- ERROR MESSAGE --}}
    @if($errors->any())

        <div class="alert alert-danger employee-alert">

            <i class="bi bi-exclamation-circle-fill me-2"></i>

            {{ $errors->first() }}

        </div>

    @endif

    {{-- SUMMARY CARDS --}}
    {{-- <div class="row g-3 mb-4">

        <div class="col-xl-3 col-md-6">

            <div class="employee-summary-card">

                <div class="summary-icon">
                    <i class="bi bi-people"></i>
                </div>

                <div>
                    <span>Total Employees</span>

                    <strong>
                        {{ $employees->count() }}
                    </strong>
                </div>

            </div>

        </div>

        <div class="col-xl-3 col-md-6">

            <div class="employee-summary-card">

                <div class="summary-icon active-icon">
                    <i class="bi bi-person-check"></i>
                </div>

                <div>
                    <span>Active Employees</span>

                    <strong>
                        {{ $employees->where('status', 'active')->count() }}
                    </strong>
                </div>

            </div>

        </div>

        <div class="col-xl-3 col-md-6">

            <div class="employee-summary-card">

                <div class="summary-icon inactive-icon">
                    <i class="bi bi-person-x"></i>
                </div>

                <div>
                    <span>Inactive Employees</span>

                    <strong>
                        {{ $employees->where('status', 'inactive')->count() }}
                    </strong>
                </div>

            </div>

        </div>

        <div class="col-xl-3 col-md-6">

            <div class="employee-summary-card">

                <div class="summary-icon department-icon">
                    <i class="bi bi-building"></i>
                </div>

                <div>
                    <span>Departments</span>

                    <strong>
                        {{ collect($departments)->filter()->count() }}
                    </strong>
                </div>

            </div>

        </div>

    </div> --}}

    {{-- DEPARTMENT QUICK FILTERS --}}
    <div class="employee-filter-card mb-3">

        <div class="department-filter-header">

            <div>
                <h5>
                    Departments
                </h5>

                <p>
                    Select a department to filter employees
                </p>
            </div>

            <div class="filter-header-icon">
                <i class="bi bi-diagram-3"></i>
            </div>

        </div>

        <div class="department-buttons">

            <button type="button"
                    class="department-btn active"
                    data-department="">

                <i class="bi bi-grid"></i>
                All

            </button>

            @foreach($departments as $department)

                <button type="button"
                        class="department-btn"
                        data-department="{{ strtolower(trim($department)) }}">

                    <i class="bi bi-building"></i>
                    {{ $department }}

                </button>

            @endforeach

        </div>

    </div>

    {{-- SEARCH AND FILTERS --}}
    <div class="employee-filter-card mb-4">

        <div class="row g-3 align-items-end">

            <div class="col-xl-4 col-md-6">

                <label class="filter-label">
                    Search Employee
                </label>

                <div class="filter-input-wrapper">

                    <i class="bi bi-search"></i>

                    <input type="text"
                           id="employeeSearch"
                           class="form-control filter-input"
                           placeholder="Search name, code, department...">

                </div>

            </div>

            <div class="col-xl-3 col-md-6">

                <label class="filter-label">
                    Department
                </label>

                <div class="filter-input-wrapper">

                    <i class="bi bi-building"></i>

                    <select id="departmentFilter"
                            class="form-select filter-input">

                        <option value="">
                            All Departments
                        </option>

                        @foreach($departments as $department)

                            <option value="{{ strtolower(trim($department)) }}">
                                {{ $department }}
                            </option>

                        @endforeach

                    </select>

                </div>

            </div>

            <div class="col-xl-3 col-md-6">

                <label class="filter-label">
                    Designation
                </label>

                <div class="filter-input-wrapper">

                    <i class="bi bi-person-workspace"></i>

                    <select id="designationFilter"
                            class="form-select filter-input">

                        <option value="">
                            All Designations
                        </option>

                        @foreach($designations as $designation)

                            <option value="{{ strtolower(trim($designation)) }}">
                                {{ $designation }}
                            </option>

                        @endforeach

                    </select>

                </div>

            </div>

            <div class="col-xl-2 col-md-6">

                <button type="button"
                        id="resetFilters"
                        class="reset-filter-btn">

                    <i class="bi bi-arrow-counterclockwise"></i>
                    Reset

                </button>

            </div>

        </div>

        <div class="filter-result-info">

            <span>
                Showing

                <strong id="visibleEmployeeCount">
                    {{ $employees->count() }}
                </strong>

                of

                <strong>
                    {{ $employees->count() }}
                </strong>

                employees
            </span>

            <strong id="selectedFilterText">
                All Employees
            </strong>

        </div>

    </div>

    {{-- EMPLOYEE TABLE --}}
    <div class="employees-table-card">

        <div class="employees-table-header">

            <div>
                <h4>
                    Employee List
                </h4>

                <p>
                    View employee details and manage attendance
                </p>
            </div>

            <div class="table-header-icon">
                <i class="bi bi-people-fill"></i>
            </div>

        </div>

        <div class="table-responsive">

            <table class="table employee-table align-middle mb-0">

                <thead>

                    <tr>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Designation</th>
                        <th>Salary</th>
                        <th>Status</th>
                        <th class="text-center action-column">
                            Actions
                        </th>
                    </tr>

                </thead>

                <tbody id="employeeTableBody">

                    @forelse($employees as $employee)

                        @php
                            $pictures = is_array($employee->pictures)
                                ? $employee->pictures
                                : (
                                    json_decode(
                                        $employee->pictures ?? '[]',
                                        true
                                    ) ?? []
                                );

                            $profile = $pictures[0] ?? null;

                            $employeeName = strtolower(
                                trim($employee->name ?? '')
                            );

                            $employeeCode = strtolower(
                                trim($employee->employee_code ?? '')
                            );

                            $employeeDepartment = strtolower(
                                trim($employee->department ?? '')
                            );

                            $employeeDesignation = strtolower(
                                trim($employee->designation ?? '')
                            );
                        @endphp

                        <tr class="employee-row"
                            data-name="{{ $employeeName }}"
                            data-code="{{ $employeeCode }}"
                            data-department="{{ $employeeDepartment }}"
                            data-designation="{{ $employeeDesignation }}">

                            {{-- EMPLOYEE PROFILE --}}
                            <td>

                                <div class="employee-profile-cell">

                                    <div class="employee-avatar-wrapper">

                                        @if($profile)

                                            <img src="{{ asset('storage/' . $profile) }}"
                                                 alt="{{ $employee->name }}"
                                                 class="employee-avatar">

                                        @else

                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($employee->name) }}&background=111111&color=ffffff&size=100"
                                                 alt="{{ $employee->name }}"
                                                 class="employee-avatar">

                                        @endif

                                        <span class="status-dot {{ $employee->status === 'active' ? 'online-dot' : 'offline-dot' }}"></span>

                                    </div>

                                    <div class="employee-primary-info">

                                        <a href="{{ route('employees.show', $employee->id) }}"
                                           class="employee-name-link">

                                            {{ $employee->name }}

                                        </a>

                                        <span>
                                            {{ $employee->employee_code ?? 'No employee code' }}
                                        </span>

                                        @if($employee->phone)

                                            <small>
                                                <i class="bi bi-telephone"></i>
                                                {{ $employee->phone }}
                                            </small>

                                        @endif

                                    </div>

                                </div>

                            </td>

                            {{-- DEPARTMENT --}}
                            <td>

                                @if($employee->department)

                                    <span class="employee-data-badge">

                                        <i class="bi bi-building"></i>
                                        {{ $employee->department }}

                                    </span>

                                @else

                                    <span class="empty-value">
                                        Not assigned
                                    </span>

                                @endif

                            </td>

                            {{-- DESIGNATION --}}
                            <td>

                                @if($employee->designation)

                                    <div class="designation-text">

                                        <i class="bi bi-person-workspace"></i>
                                        {{ $employee->designation }}

                                    </div>

                                @else

                                    <span class="empty-value">
                                        Not assigned
                                    </span>

                                @endif

                            </td>

                            {{-- SALARY --}}
                            <td>

                                <div class="salary-value">

                                    <small>Basic Salary</small>

                                    <strong>
                                        Rs {{ number_format($employee->basic_salary ?? 0, 2) }}
                                    </strong>

                                </div>

                            </td>

                            {{-- STATUS --}}
                            <td>

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

                            </td>

                            {{-- ICON ACTIONS --}}
                            <td>

                                <div class="employee-actions">

                                    {{-- View --}}
                                    <a href="{{ route('employees.show', $employee->id) }}"
                                       class="employee-action-btn view-action"
                                       title="View Employee"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top">

                                        <i class="bi bi-eye"></i>

                                    </a>

                                    {{-- Edit --}}
                                    <a href="{{ route('employees.edit', $employee->id) }}"
                                       class="employee-action-btn edit-action"
                                       title="Edit Employee"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top">

                                        <i class="bi bi-pencil-square"></i>

                                    </a>

                                    {{-- Attendance Calendar --}}
                                    <a href="{{ route('employees.attendance.calendar', $employee->id) }}"
                                       class="employee-action-btn attendance-action"
                                       title="Attendance Calendar"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top">

                                        <i class="bi bi-calendar2-check"></i>

                                    </a>

                                    {{-- Delete --}}
                                    <form action="{{ route('employees.destroy', $employee->id) }}"
                                          method="POST"
                                          class="d-inline delete-employee-form">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="employee-action-btn delete-action"
                                                title="Delete Employee"
                                                data-bs-toggle="tooltip"
                                                data-bs-placement="top"
                                                onclick="return confirm('Are you sure you want to delete {{ addslashes($employee->name) }}?')">

                                            <i class="bi bi-trash3"></i>

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6">

                                <div class="employees-empty-state">

                                    <div class="empty-state-icon">
                                        <i class="bi bi-people"></i>
                                    </div>

                                    <h5>
                                        No Employees Found
                                    </h5>

                                    <p>
                                        Add your first employee to start managing attendance and payroll.
                                    </p>

                                    <a href="{{ route('employees.create') }}"
                                       class="add-employee-btn">

                                        <i class="bi bi-person-plus"></i>
                                        Add Employee

                                    </a>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                    {{-- FILTER EMPTY RESULT --}}
                    <tr id="noFilterResult"
                        style="display: none;">

                        <td colspan="6">

                            <div class="employees-empty-state">

                                <div class="empty-state-icon">
                                    <i class="bi bi-search"></i>
                                </div>

                                <h5>
                                    No Matching Employees
                                </h5>

                                <p>
                                    Change the search text, department or designation filter.
                                </p>

                                <button type="button"
                                        id="clearEmptyFilters"
                                        class="empty-reset-btn">

                                    <i class="bi bi-arrow-counterclockwise"></i>
                                    Clear Filters

                                </button>

                            </div>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>

<style>
:root {
    --employee-black: #111111;
    --employee-dark: #242424;
    --employee-white: #ffffff;
    --employee-border: #e4e6eb;
    --employee-muted: #747b86;
    --employee-light: #f8f9fa;
    --manual-blue: #0d6efd;
    --attendance-green: #198754;
    --danger-red: #dc3545;
}

.employees-page {
    color: var(--employee-black);
}

/* Header */

.employees-page-header {
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
}

.employees-page-title {
    margin: 0;
    color: var(--employee-black);
    font-size: 28px;
    font-weight: 900;
}

.employees-page-subtitle {
    margin: 4px 0 0;
    color: var(--employee-muted);
    font-size: 13px;
}

.add-employee-btn {
    min-height: 42px;
    padding: 10px 16px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    border: 1px solid var(--employee-black);
    border-radius: 8px;
    color: var(--employee-white);
    background: var(--employee-black);
    text-decoration: none;
    font-size: 13px;
    font-weight: 900;
    transition: 0.2s ease;
}

.add-employee-btn:hover {
    color: var(--employee-black);
    background: var(--employee-white);
}

.employee-alert {
    border-radius: 10px;
}

/* Summary cards */

.employee-summary-card {
    min-height: 105px;
    padding: 18px;
    display: flex;
    align-items: center;
    gap: 14px;
    border: 1px solid var(--employee-border);
    border-radius: 13px;
    background: var(--employee-white);
    box-shadow: 0 5px 18px rgba(17, 24, 39, 0.05);
}

.summary-icon {
    flex: 0 0 48px;
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 11px;
    color: var(--employee-white);
    background: var(--employee-black);
    font-size: 21px;
}

.active-icon {
    background: #198754;
}

.inactive-icon {
    background: #dc3545;
}

.department-icon {
    background: #495057;
}

.employee-summary-card span {
    display: block;
    margin-bottom: 3px;
    color: var(--employee-muted);
    font-size: 11px;
    font-weight: 700;
}

.employee-summary-card strong {
    display: block;
    color: var(--employee-black);
    font-size: 23px;
    font-weight: 900;
}

/* Filters */

.employee-filter-card {
    padding: 19px;
    border: 1px solid var(--employee-border);
    border-radius: 13px;
    background: var(--employee-white);
    box-shadow: 0 5px 18px rgba(17, 24, 39, 0.05);
}

.department-filter-header {
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.department-filter-header h5 {
    margin: 0;
    color: var(--employee-black);
    font-size: 15px;
    font-weight: 900;
}

.department-filter-header p {
    margin: 3px 0 0;
    color: var(--employee-muted);
    font-size: 11px;
}

.filter-header-icon,
.table-header-icon {
    width: 39px;
    height: 39px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    color: var(--employee-white);
    background: var(--employee-black);
    font-size: 18px;
}

.department-buttons {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

.department-btn {
    min-height: 36px;
    padding: 7px 13px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border: 1px solid var(--employee-border);
    border-radius: 50px;
    color: #50555e;
    background: var(--employee-light);
    font-size: 11px;
    font-weight: 800;
    transition: 0.2s ease;
}

.department-btn:hover,
.department-btn.active {
    border-color: var(--employee-black);
    color: var(--employee-white);
    background: var(--employee-black);
}

.filter-label {
    margin-bottom: 7px;
    color: var(--employee-black);
    font-size: 10px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}

.filter-input-wrapper {
    position: relative;
}

.filter-input-wrapper > i {
    position: absolute;
    top: 50%;
    left: 13px;
    z-index: 2;
    color: var(--employee-black);
    font-size: 14px;
    transform: translateY(-50%);
    pointer-events: none;
}

.filter-input {
    min-height: 44px;
    padding-left: 39px;
    border: 1px solid var(--employee-border);
    border-radius: 8px;
    color: var(--employee-black);
    background: var(--employee-light);
    font-size: 12px;
    font-weight: 700;
    box-shadow: none !important;
}

.filter-input:focus {
    border-color: var(--employee-black);
    background: var(--employee-white);
}

.reset-filter-btn {
    width: 100%;
    min-height: 44px;
    padding: 9px 13px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    border: 1px solid var(--employee-black);
    border-radius: 8px;
    color: var(--employee-black);
    background: var(--employee-white);
    font-size: 12px;
    font-weight: 900;
    transition: 0.2s ease;
}

.reset-filter-btn:hover {
    color: var(--employee-white);
    background: var(--employee-black);
}

.filter-result-info {
    margin-top: 15px;
    padding-top: 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    border-top: 1px solid var(--employee-border);
    color: var(--employee-muted);
    font-size: 11px;
}

.filter-result-info strong {
    color: var(--employee-black);
}

/* Table */

.employees-table-card {
    overflow: hidden;
    border: 1px solid var(--employee-border);
    border-radius: 14px;
    background: var(--employee-white);
    box-shadow: 0 6px 22px rgba(17, 24, 39, 0.06);
}

.employees-table-header {
    min-height: 82px;
    padding: 19px 21px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid var(--employee-border);
}

.employees-table-header h4 {
    margin: 0;
    color: var(--employee-black);
    font-size: 18px;
    font-weight: 900;
}

.employees-table-header p {
    margin: 3px 0 0;
    color: var(--employee-muted);
    font-size: 11px;
}

.employee-table thead th {
    padding: 13px 17px;
    border-bottom: 1px solid var(--employee-border);
    color: #616773;
    background: var(--employee-light);
    font-size: 10px;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 0.45px;
    white-space: nowrap;
}

.employee-table tbody td {
    padding: 14px 17px;
    border-bottom: 1px solid #eef0f2;
    color: var(--employee-black);
    font-size: 12px;
    vertical-align: middle;
}

.employee-table tbody tr {
    transition: 0.18s ease;
}

.employee-table tbody tr.employee-row:hover {
    background: #fbfbfc;
}

.action-column {
    width: 215px;
}

/* Employee cell */

.employee-profile-cell {
    min-width: 235px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.employee-avatar-wrapper {
    position: relative;
    flex: 0 0 52px;
    width: 52px;
    height: 52px;
}

.employee-avatar {
    width: 52px;
    height: 52px;
    object-fit: cover;
    border: 2px solid var(--employee-white);
    border-radius: 50%;
    box-shadow:
        0 0 0 1px var(--employee-border),
        0 4px 12px rgba(0, 0, 0, 0.11);
}

.status-dot {
    position: absolute;
    right: 1px;
    bottom: 2px;
    width: 12px;
    height: 12px;
    border: 2px solid var(--employee-white);
    border-radius: 50%;
}

.online-dot {
    background: #198754;
}

.offline-dot {
    background: #dc3545;
}

.employee-primary-info {
    min-width: 0;
}

.employee-name-link {
    display: block;
    overflow: hidden;
    color: var(--employee-black);
    font-size: 13px;
    font-weight: 900;
    text-decoration: none;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.employee-name-link:hover {
    text-decoration: underline;
}

.employee-primary-info > span {
    display: block;
    margin-top: 2px;
    color: var(--employee-muted);
    font-size: 10px;
    font-weight: 700;
}

.employee-primary-info small {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-top: 4px;
    color: #606771;
    font-size: 10px;
}

.employee-data-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 10px;
    border: 1px solid var(--employee-border);
    border-radius: 50px;
    color: var(--employee-black);
    background: var(--employee-light);
    font-size: 10px;
    font-weight: 800;
}

.designation-text {
    display: flex;
    align-items: center;
    gap: 7px;
    color: #484e57;
    font-size: 11px;
    font-weight: 700;
}

.designation-text i {
    color: var(--employee-black);
}

.empty-value {
    color: #a0a5ac;
    font-size: 10px;
}

.salary-value small {
    display: block;
    margin-bottom: 2px;
    color: var(--employee-muted);
    font-size: 9px;
}

.salary-value strong {
    color: var(--employee-black);
    font-size: 12px;
    font-weight: 900;
}

/* Status */

.employee-status {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 6px 10px;
    border: 1px solid var(--employee-border);
    border-radius: 50px;
    background: var(--employee-white);
    font-size: 10px;
    font-weight: 900;
}

.employee-status span {
    width: 7px;
    height: 7px;
    border-radius: 50%;
}

.active-status {
    color: #146c43;
}

.active-status span {
    background: #198754;
}

.inactive-status {
    color: #b02a37;
}

.inactive-status span {
    background: #dc3545;
}

/* Action icons */

.employee-actions {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
}

.employee-action-btn {
    width: 37px;
    height: 37px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--employee-black);
    border-radius: 8px;
    color: var(--employee-black);
    background: var(--employee-white);
    text-decoration: none;
    font-size: 14px;
    cursor: pointer;
    transition: 0.18s ease;
}

.employee-action-btn:hover {
    color: var(--employee-white);
    background: var(--employee-black);
    transform: translateY(-2px);
}

.view-action:hover {
    border-color: #111111;
    background: #111111;
}

.edit-action:hover {
    border-color: #495057;
    background: #495057;
}

.attendance-action {
    border-color: #0d6efd;
    color: #0d6efd;
}

.attendance-action:hover {
    border-color: #0d6efd;
    color: var(--employee-white);
    background: #0d6efd;
}

.delete-action {
    border-color: #dc3545;
    color: #dc3545;
}

.delete-action:hover {
    border-color: #dc3545;
    color: var(--employee-white);
    background: #dc3545;
}

/* Empty state */

.employees-empty-state {
    min-height: 270px;
    padding: 40px 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: var(--employee-muted);
    text-align: center;
}

.empty-state-icon {
    width: 65px;
    height: 65px;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    color: var(--employee-white);
    background: var(--employee-black);
    font-size: 27px;
}

.employees-empty-state h5 {
    margin: 0 0 5px;
    color: var(--employee-black);
    font-size: 17px;
    font-weight: 900;
}

.employees-empty-state p {
    max-width: 430px;
    margin: 0 0 16px;
    font-size: 11px;
}

.empty-reset-btn {
    min-height: 39px;
    padding: 8px 14px;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    border: 1px solid var(--employee-black);
    border-radius: 8px;
    color: var(--employee-white);
    background: var(--employee-black);
    font-size: 11px;
    font-weight: 900;
}

/* Responsive */

@media (max-width: 991px) {
    .employees-page-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .add-employee-btn {
        width: 100%;
    }

    .filter-result-info {
        align-items: flex-start;
        flex-direction: column;
    }
}

@media (max-width: 767px) {
    .employees-table-card {
        border-radius: 11px;
    }

    .employee-table {
        min-width: 980px;
    }

    .employee-actions {
        justify-content: flex-start;
    }

    .employees-page-title {
        font-size: 23px;
    }
}

@media (max-width: 575px) {
    .employee-filter-card {
        padding: 15px;
    }

    .employees-table-header {
        padding: 16px;
    }

    .department-buttons {
        display: grid;
        grid-template-columns: 1fr 1fr;
    }

    .department-btn {
        justify-content: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('employeeSearch');
    const departmentFilter = document.getElementById('departmentFilter');
    const designationFilter = document.getElementById('designationFilter');

    const resetButton = document.getElementById('resetFilters');
    const clearEmptyFilters = document.getElementById('clearEmptyFilters');

    const departmentButtons = document.querySelectorAll(
        '.department-btn'
    );

    const employeeRows = document.querySelectorAll(
        '.employee-row'
    );

    const visibleEmployeeCount = document.getElementById(
        'visibleEmployeeCount'
    );

    const noFilterResult = document.getElementById(
        'noFilterResult'
    );

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

            button.classList.remove('active');

            if (buttonDepartment === selectedDepartment) {
                button.classList.add('active');
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

        const hasDepartment =
            normalize(departmentFilter.value) !== '';

        const hasDesignation =
            normalize(designationFilter.value) !== '';

        if (!hasDepartment && !hasDesignation) {
            selectedFilterText.textContent = 'All Employees';
            return;
        }

        if (hasDepartment && hasDesignation) {
            selectedFilterText.textContent =
                departmentText + ' / ' + designationText;

            return;
        }

        selectedFilterText.textContent = hasDepartment
            ? departmentText
            : designationText;
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
            const code = normalize(row.dataset.code);

            const department = normalize(
                row.dataset.department
            );

            const designation = normalize(
                row.dataset.designation
            );

            const completeText = [
                name,
                code,
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

        if (noFilterResult) {
            noFilterResult.style.display =
                employeeRows.length > 0 && visibleCount === 0
                    ? ''
                    : 'none';
        }

        updateDepartmentButtons(selectedDepartment);
        updateSelectedFilterText();
    }

    function resetAllFilters() {
        searchInput.value = '';
        departmentFilter.value = '';
        designationFilter.value = '';

        filterEmployees();
    }

    searchInput.addEventListener(
        'input',
        filterEmployees
    );

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
            departmentFilter.value = normalize(
                this.dataset.department
            );

            filterEmployees();
        });
    });

    resetButton.addEventListener(
        'click',
        resetAllFilters
    );

    if (clearEmptyFilters) {
        clearEmptyFilters.addEventListener(
            'click',
            resetAllFilters
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Bootstrap Tooltips
    |--------------------------------------------------------------------------
    */

    if (typeof bootstrap !== 'undefined') {
        const tooltipElements = document.querySelectorAll(
            '[data-bs-toggle="tooltip"]'
        );

        tooltipElements.forEach(function (element) {
            new bootstrap.Tooltip(element);
        });
    }

    filterEmployees();
});
</script>

@endsection
