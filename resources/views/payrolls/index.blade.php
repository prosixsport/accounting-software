@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-0">Payroll / Salary</h3>
        <small class="text-muted">
            Manage monthly employee salaries
        </small>
    </div>
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
        <strong>Please fix the following errors:</strong>

        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Year, Month and Real-Time Search --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">

        <form method="GET"
              action="{{ route('payrolls.index') }}"
              id="payrollFilterForm">

            <div class="row align-items-end">

                <div class="col-md-3 mb-2">
                    <label class="form-label">
                        Year
                    </label>

                    <input type="number"
                           name="year"
                           id="payrollYear"
                           value="{{ $year }}"
                           min="2000"
                           max="2100"
                           class="form-control">
                </div>

                <div class="col-md-3 mb-2">
                    <label class="form-label">
                        Month
                    </label>

                    <select name="month"
                            id="payrollMonth"
                            class="form-select">

                        @foreach($months as $m)
                            <option value="{{ $m['number'] }}"
                                {{ (int) $month === (int) $m['number'] ? 'selected' : '' }}>

                                {{ $m['name'] }} {{ $year }}

                            </option>
                        @endforeach

                    </select>
                </div>

                <div class="col-md-6 mb-2">
                    <label class="form-label">
                        Search Employee
                    </label>

                    <div class="search-input-wrapper">

                        <span class="search-icon">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>

                        <input type="text"
                               id="payrollSearch"
                               class="form-control payroll-search-input"
                               placeholder="Search by name, phone, CNIC, department..."
                               autocomplete="off">

                        <button type="button"
                                id="clearPayrollSearch"
                                class="clear-search-btn"
                                title="Clear search">
                            <i class="fa-solid fa-xmark"></i>
                        </button>

                    </div>
                </div>

            </div>

        </form>

    </div>
</div>

{{-- Month Buttons --}}
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">

        <div class="d-flex flex-wrap gap-2">

            @foreach($months as $m)

                <a href="{{ route('payrolls.index', [
                        'year' => $year,
                        'month' => $m['number']
                    ]) }}"
                   class="month-btn
                        {{ (int) $month === (int) $m['number'] ? 'active' : '' }}">

                    <span>
                        {{ $m['name'] }}
                    </span>

                    <small>
                        {{ $year }}
                    </small>

                </a>

            @endforeach

        </div>

    </div>
</div>

{{-- Print Slips Form --}}
<form id="printSlipsForm"
      method="GET"
      action="{{ route('payrolls.print.slips') }}">

    <input type="hidden"
           name="year"
           value="{{ $year }}">

    <input type="hidden"
           name="month"
           value="{{ $month }}">

</form>

{{-- Results Information and Print Button --}}
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">

    <div class="employee-result-info">
        Showing
        <strong id="visibleEmployeeCount">
            {{ $payrollRows->count() }}
        </strong>
        employee(s)
    </div>

    <button type="submit"
            form="printSlipsForm"
            class="btn btn-success">

        <i class="fa-solid fa-print me-1"></i>
        Print Selected Slips

    </button>

</div>

{{-- Payroll Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle payroll-table">

                <thead>
                    <tr>

                        <th width="45">
                            <input type="checkbox"
                                   id="selectAll"
                                   class="form-check-input">
                        </th>

                        <th>Profile</th>
                        <th>Employee Details</th>
                        <th>Total Salary</th>
                        <th>Present</th>
                        <th>Absent</th>
                        <th>Earned Salary</th>
                        <th>Advance</th>
                        <th>Net Salary</th>
                        <th>Payment Status</th>
                        <th width="190">Action</th>

                    </tr>
                </thead>

                <tbody id="payrollTableBody">

                    @forelse($payrollRows as $row)

                        @php
                            $employee = $row['employee'];
                            $payroll = $row['payroll'];
                            $profile = $employee->pictures[0] ?? null;
                            $isPaid = $payroll->payment_status === 'paid';

                            $searchableText = strtolower(
                                implode(' ', [
                                    $employee->name ?? '',
                                    $employee->father_name ?? '',
                                    $employee->phone ?? '',
                                    $employee->cnic ?? '',
                                    $employee->email ?? '',
                                    $employee->department ?? '',
                                    $employee->designation ?? '',
                                    $employee->employee_code ?? '',
                                    $payroll->payment_status ?? '',
                                ])
                            );
                        @endphp

                        <tr class="employee-payroll-row
                                   {{ $isPaid ? 'paid-row' : 'unpaid-row' }}"
                            data-search="{{ $searchableText }}">

                            {{-- Select Employee --}}
                            <td>
                                <input type="checkbox"
                                       name="employee_ids[]"
                                       value="{{ $employee->id }}"
                                       form="printSlipsForm"
                                       class="form-check-input employee-check"
                                       {{ !$isPaid ? 'disabled' : '' }}>
                            </td>

                            {{-- Profile --}}
                            <td>

                                @if($profile)

                                    <img src="{{ asset('storage/' . $profile) }}"
                                         width="55"
                                         height="55"
                                         class="rounded-circle border employee-profile"
                                         alt="{{ $employee->name }}">

                                @else

                                    <div class="employee-avatar">
                                        {{ strtoupper(substr($employee->name, 0, 1)) }}
                                    </div>

                                @endif

                            </td>

                            {{-- Employee Details --}}
                            <td>

                                <strong class="employee-name">
                                    {{ $employee->name }}
                                </strong>

                                <br>

                                <small class="text-muted">
                                    {{ $employee->department ?? '-' }}
                                    |
                                    {{ $employee->designation ?? '-' }}
                                    |
                                    {{ $employee->phone ?? '-' }}
                                </small>

                                <br>

                                <small class="text-muted">
                                    {{ $employee->employee_code ?? '' }}
                                </small>

                            </td>

                            {{-- Basic Salary --}}
                            <td>
                                Rs {{ number_format($row['basic_salary'], 2) }}
                            </td>

                            {{-- Present --}}
                            <td>
                                <span class="badge bg-success-subtle text-success">
                                    {{ $row['present_days'] }}
                                </span>
                            </td>

                            {{-- Absent --}}
                            <td>
                                <span class="badge bg-danger-subtle text-danger">
                                    {{ $row['absent_days'] }}
                                </span>
                            </td>

                            {{-- Earned Salary --}}
                            <td>
                                Rs {{ number_format($row['earned_salary'], 2) }}
                            </td>

                            {{-- Advance --}}
                            <td class="text-danger">
                                Rs {{ number_format($row['advance_amount'], 2) }}
                            </td>

                            {{-- Net Salary --}}
                            <td>
                                <strong>
                                    Rs {{ number_format($row['net_salary'], 2) }}
                                </strong>
                            </td>

                            {{-- Payment Status --}}
                            <td>

                                <form method="POST"
                                      action="{{ route('payrolls.payment.status') }}"
                                      class="salary-status-form">

                                    @csrf

                                    <input type="hidden"
                                           name="payroll_id"
                                           value="{{ $payroll->id }}">

                                    <input type="hidden"
                                           name="payment_status"
                                           value="{{ $isPaid ? 'pending' : 'paid' }}">

                                    <button type="submit"
                                            class="payment-toggle
                                                   {{ $isPaid ? 'is-paid' : 'is-unpaid' }}">

                                        <span class="toggle-circle"></span>

                                        <span class="toggle-text">
                                            {{ $isPaid ? 'PAID' : 'UNPAID' }}
                                        </span>

                                    </button>

                                </form>

                                @if($isPaid && $payroll->payment_date)

                                    <small class="payment-date">
                                        Paid:
                                        {{ \Carbon\Carbon::parse(
                                            $payroll->payment_date
                                        )->format('d M Y') }}
                                    </small>

                                @else

                                    <small class="payment-source">
                                        Manual
                                    </small>

                                @endif

                            </td>

                            {{-- Actions --}}
                            <td>

                                @if($isPaid)

                                    <a href="{{ route('payrolls.print.slips', [
                                            'year' => $year,
                                            'month' => $month,
                                            'employee_ids' => [$employee->id]
                                        ]) }}"
                                       target="_blank"
                                       class="btn btn-info btn-sm mb-1">

                                        View Slip

                                    </a>

                                @else

                                    <button type="button"
                                            class="btn btn-secondary btn-sm mb-1"
                                            disabled>

                                        Unpaid

                                    </button>

                                @endif

                                <button type="button"
                                        class="btn btn-warning btn-sm mb-1"
                                        data-bs-toggle="modal"
                                        data-bs-target="#advanceModal{{ $employee->id }}">

                                    Advance

                                </button>

                            </td>

                        </tr>

                    @empty

                        <tr id="emptyDatabaseRow">

                            <td colspan="11"
                                class="text-center text-muted py-5">

                                <i class="fa-solid fa-users-slash fs-3 d-block mb-2"></i>

                                No active employee found

                            </td>

                        </tr>

                    @endforelse

                    {{-- Real-Time Search No Result --}}
                    <tr id="noSearchResults" style="display: none;">

                        <td colspan="11"
                            class="text-center text-muted py-5">

                            <i class="fa-solid fa-magnifying-glass fs-3 d-block mb-2"></i>

                            No matching employee found

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>
</div>

{{-- Advance Modals --}}
@foreach($payrollRows as $row)

    @php
        $employee = $row['employee'];
    @endphp

    <div class="modal fade"
         id="advanceModal{{ $employee->id }}"
         tabindex="-1"
         aria-hidden="true">

        <div class="modal-dialog">

            <form method="POST"
                  action="{{ route('payrolls.advance.store') }}"
                  class="modal-content">

                @csrf

                <input type="hidden"
                       name="employee_id"
                       value="{{ $employee->id }}">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Add Advance - {{ $employee->name }}
                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <div class="mb-3">

                        <label class="form-label">
                            Advance Amount *
                        </label>

                        <input type="number"
                               step="0.01"
                               min="1"
                               name="amount"
                               class="form-control"
                               required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Date *
                        </label>

                        <input type="date"
                               name="advance_date"
                               value="{{ date('Y-m-d') }}"
                               class="form-control"
                               required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Time
                        </label>

                        <input type="time"
                               name="advance_time"
                               value="{{ date('H:i') }}"
                               class="form-control">

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Remarks
                        </label>

                        <textarea name="remarks"
                                  rows="3"
                                  class="form-control"
                                  placeholder="Enter advance reason"></textarea>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">

                        Cancel

                    </button>

                    <button type="submit"
                            class="btn btn-primary">

                        Save Advance

                    </button>

                </div>

            </form>

        </div>

    </div>

@endforeach

<style>
.month-btn {
    min-width: 110px;
    padding: 12px 16px;
    border: 1px solid #d9dee7;
    border-radius: 12px;
    text-decoration: none;
    color: #111827;
    background: #ffffff;
    display: flex;
    flex-direction: column;
    gap: 2px;
    transition: 0.2s ease;
}

.month-btn span {
    font-weight: 700;
    line-height: 1;
}

.month-btn small {
    color: #6b7280;
    font-size: 12px;
}

.month-btn:hover,
.month-btn.active {
    background: #0d6efd;
    color: #ffffff;
    border-color: #0d6efd;
}

.month-btn:hover small,
.month-btn.active small {
    color: #eaf2ff;
}

.search-input-wrapper {
    position: relative;
}

.payroll-search-input {
    padding-left: 42px;
    padding-right: 42px;
}

.search-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #87909e;
    z-index: 2;
    pointer-events: none;
}

.clear-search-btn {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    width: 28px;
    height: 28px;
    border: 0;
    border-radius: 50%;
    background: transparent;
    color: #87909e;
    display: none;
    align-items: center;
    justify-content: center;
    transition: 0.2s ease;
}

.clear-search-btn:hover {
    background: #eef2f7;
    color: #dc3545;
}

.employee-result-info {
    color: #6b7280;
    font-size: 13px;
}

.employee-result-info strong {
    color: #111827;
}

.payroll-table {
    min-width: 1250px;
}

.employee-profile {
    object-fit: cover;
}

.employee-avatar {
    width: 55px;
    height: 55px;
    border-radius: 50%;
    background: #0d6efd;
    color: #ffffff;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 21px;
    font-weight: 800;
    border: 2px solid #e5e7eb;
}

.employee-name {
    color: #111827;
}

.unpaid-row {
    background: #fffafa;
}

.paid-row {
    background: #f8fff9;
}

.salary-status-form {
    display: inline-block;
    margin: 0;
}

.payment-toggle {
    width: 92px;
    height: 32px;
    border: 0;
    border-radius: 20px;
    padding: 3px;
    cursor: pointer;
    position: relative;
    display: inline-flex;
    align-items: center;
    transition: all 0.22s ease;
    box-shadow:
        inset 0 0 0 1px rgba(0, 0, 0, 0.08),
        0 2px 5px rgba(0, 0, 0, 0.12);
}

.payment-toggle .toggle-circle {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: #ffffff;
    position: absolute;
    top: 3px;
    transition: all 0.22s ease;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.22);
}

.payment-toggle .toggle-text {
    position: absolute;
    color: #ffffff;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.3px;
    line-height: 1;
}

.payment-toggle.is-unpaid {
    background: #dc3545;
}

.payment-toggle.is-unpaid .toggle-circle {
    left: 3px;
}

.payment-toggle.is-unpaid .toggle-text {
    right: 8px;
}

.payment-toggle.is-paid {
    background: #2eae55;
}

.payment-toggle.is-paid .toggle-circle {
    left: 63px;
}

.payment-toggle.is-paid .toggle-text {
    left: 13px;
}

.payment-toggle:hover {
    transform: translateY(-1px);
    filter: brightness(0.97);
}

.payment-toggle:active {
    transform: scale(0.97);
}

.payment-toggle:focus {
    outline: none;
    box-shadow:
        0 0 0 3px rgba(13, 110, 253, 0.12),
        0 2px 5px rgba(0, 0, 0, 0.12);
}

.payment-source {
    display: block;
    margin-top: 5px;
    font-size: 10px;
    color: #8a94a3;
    line-height: 1;
}

.payment-date {
    display: block;
    margin-top: 5px;
    font-size: 10px;
    font-weight: 600;
    color: #198754;
    line-height: 1;
}

@media (max-width: 767px) {
    .month-btn {
        min-width: calc(50% - 6px);
    }

    .payment-toggle {
        width: 86px;
        height: 30px;
    }

    .payment-toggle .toggle-circle {
        width: 24px;
        height: 24px;
    }

    .payment-toggle.is-paid .toggle-circle {
        left: 59px;
    }

    .payment-toggle .toggle-text {
        font-size: 9px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const filterForm = document.getElementById('payrollFilterForm');
    const yearInput = document.getElementById('payrollYear');
    const monthSelect = document.getElementById('payrollMonth');

    const searchInput = document.getElementById('payrollSearch');
    const clearSearchButton = document.getElementById('clearPayrollSearch');

    const employeeRows = document.querySelectorAll(
        '.employee-payroll-row'
    );

    const noSearchResults = document.getElementById(
        'noSearchResults'
    );

    const visibleEmployeeCount = document.getElementById(
        'visibleEmployeeCount'
    );

    const selectAll = document.getElementById('selectAll');
    const printForm = document.getElementById('printSlipsForm');

    let yearChangeTimer = null;

    /*
     * Month changes automatically.
     */
    if (monthSelect && filterForm) {
        monthSelect.addEventListener('change', function () {
            filterForm.submit();
        });
    }

    /*
     * Year changes automatically after short delay.
     */
    if (yearInput && filterForm) {
        yearInput.addEventListener('input', function () {

            clearTimeout(yearChangeTimer);

            yearChangeTimer = setTimeout(function () {

                const yearValue = parseInt(yearInput.value, 10);

                if (
                    Number.isInteger(yearValue) &&
                    yearValue >= 2000 &&
                    yearValue <= 2100
                ) {
                    filterForm.submit();
                }

            }, 700);

        });
    }

    /*
     * Real-time employee search.
     */
    function filterEmployees() {

        const searchValue = (
            searchInput?.value || ''
        )
            .toLowerCase()
            .trim();

        let visibleCount = 0;

        employeeRows.forEach(function (row) {

            const searchableText = (
                row.dataset.search || ''
            ).toLowerCase();

            const isMatching = searchableText.includes(
                searchValue
            );

            row.style.display = isMatching ? '' : 'none';

            if (isMatching) {
                visibleCount++;
            } else {
                const checkbox = row.querySelector(
                    '.employee-check'
                );

                if (checkbox) {
                    checkbox.checked = false;
                }
            }

        });

        if (visibleEmployeeCount) {
            visibleEmployeeCount.textContent = visibleCount;
        }

        if (noSearchResults) {
            noSearchResults.style.display =
                visibleCount === 0 &&
                employeeRows.length > 0
                    ? ''
                    : 'none';
        }

        if (clearSearchButton) {
            clearSearchButton.style.display =
                searchValue.length > 0
                    ? 'flex'
                    : 'none';
        }

        updateSelectAllState();
    }

    if (searchInput) {
        searchInput.addEventListener(
            'input',
            filterEmployees
        );
    }

    /*
     * Clear search.
     */
    if (clearSearchButton && searchInput) {

        clearSearchButton.addEventListener(
            'click',
            function () {

                searchInput.value = '';
                filterEmployees();
                searchInput.focus();

            }
        );

    }

    /*
     * Return only visible, enabled checkboxes.
     */
    function getVisibleEmployeeCheckboxes() {

        return Array.from(
            document.querySelectorAll(
                '.employee-check:not(:disabled)'
            )
        ).filter(function (checkbox) {

            const row = checkbox.closest(
                '.employee-payroll-row'
            );

            return row && row.style.display !== 'none';

        });

    }

    /*
     * Select all visible paid employees.
     */
    if (selectAll) {

        selectAll.addEventListener(
            'change',
            function () {

                const visibleCheckboxes =
                    getVisibleEmployeeCheckboxes();

                visibleCheckboxes.forEach(
                    function (checkbox) {

                        checkbox.checked =
                            selectAll.checked;

                    }
                );

            }
        );

    }

    /*
     * Keep Select All state correct.
     */
    function updateSelectAllState() {

        if (!selectAll) {
            return;
        }

        const visibleCheckboxes =
            getVisibleEmployeeCheckboxes();

        const checkedCheckboxes =
            visibleCheckboxes.filter(
                function (checkbox) {
                    return checkbox.checked;
                }
            );

        selectAll.checked =
            visibleCheckboxes.length > 0 &&
            checkedCheckboxes.length ===
                visibleCheckboxes.length;

        selectAll.indeterminate =
            checkedCheckboxes.length > 0 &&
            checkedCheckboxes.length <
                visibleCheckboxes.length;

    }

    document
        .querySelectorAll('.employee-check')
        .forEach(function (checkbox) {

            checkbox.addEventListener(
                'change',
                updateSelectAllState
            );

        });

    /*
     * Print validation.
     */
    if (printForm) {

        printForm.addEventListener(
            'submit',
            function (event) {

                const selectedEmployees =
                    document.querySelectorAll(
                        '.employee-check:checked'
                    );

                if (selectedEmployees.length === 0) {

                    event.preventDefault();

                    alert(
                        'Please select at least one paid employee.'
                    );

                }

            }
        );

    }

    filterEmployees();



});
</script>

@endsection
