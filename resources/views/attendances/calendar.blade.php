@extends('layouts.app')

@section('content')

@php
    $pictures = is_array($employee->pictures)
        ? $employee->pictures
        : (json_decode($employee->pictures ?? '[]', true) ?? []);

    $profile = $pictures[0] ?? null;

    $previousMonth = $monthDate->copy()->subMonth();
    $nextMonth = $monthDate->copy()->addMonth();
@endphp

<div class="quick-attendance-page">

    {{-- Header --}}
    <div class="attendance-header">

        <div class="header-left">

            <a href="{{ route('employees.index') }}"
               class="back-button"
               title="Back">

                <i class="bi bi-arrow-left"></i>

            </a>

            <div>
                <h3>Attendance Calendar</h3>

                <p>
                    Click a date and select attendance
                </p>
            </div>

        </div>

        <a href="{{ route('employees.show', $employee->id) }}"
           class="view-button">

            <i class="bi bi-person-vcard"></i>
            View Employee

        </a>

    </div>

    {{-- Employee Bar --}}
    <div class="employee-bar">

        <div class="employee-section">

            <div class="employee-photo">

                @if($profile)

                    <img src="{{ asset('storage/' . $profile) }}"
                         alt="{{ $employee->name }}">

                @else

                    <img src="https://ui-avatars.com/api/?name={{ urlencode($employee->name) }}&background=111111&color=ffffff&size=120"
                         alt="{{ $employee->name }}">

                @endif

            </div>

            <div class="employee-info">

                <span class="employee-code">
                    {{ $employee->employee_code ?? 'Employee' }}
                </span>

                <h4>{{ $employee->name }}</h4>

                <p>
                    <i class="bi bi-building"></i>
                    {{ $employee->department ?? 'No Department' }}

                    <span></span>

                    <i class="bi bi-person-workspace"></i>
                    {{ $employee->designation ?? 'No Designation' }}
                </p>

            </div>

        </div>

        <div class="selected-month">

            <small>Selected Month</small>

            <strong>
                {{ $monthDate->format('F Y') }}
            </strong>

        </div>

    </div>

    {{-- Compact Summary --}}
    <div class="attendance-counts">

        <div class="count-chip manual-chip">

            <i class="bi bi-hand-index-thumb"></i>

            <span>Manual</span>

            <strong>
                {{ $manualPresentDays }}
            </strong>

        </div>

        <div class="count-chip biometric-chip">

            <i class="bi bi-fingerprint"></i>

            <span>Biometric</span>

            <strong>
                {{ $biometricPresentDays }}
            </strong>

        </div>

        <div class="count-chip absent-chip">

            <i class="bi bi-x-circle"></i>

            <span>Absent</span>

            <strong>
                {{ $absentDays }}
            </strong>

        </div>

        <div class="count-chip leave-chip">

            <i class="bi bi-calendar2-minus"></i>

            <span>Leave</span>

            <strong>
                {{ $leaveDays }}
            </strong>

        </div>

        <div class="count-chip half-chip">

            <i class="bi bi-circle-half"></i>

            <span>Half Day</span>

            <strong>
                {{ $halfDays }}
            </strong>

        </div>

    </div>

    {{-- Calendar --}}
    <div class="calendar-card">

        <div class="calendar-toolbar">

            <div class="month-navigation">

                <a href="{{ route('employees.attendance.calendar', [
                    'employee' => $employee->id,
                    'year' => $previousMonth->year,
                    'month' => $previousMonth->month,
                ]) }}"
                   class="month-arrow">

                    <i class="bi bi-chevron-left"></i>

                </a>

                <div class="month-name">

                    <small>Attendance Month</small>

                    <strong>
                        {{ $monthDate->format('F Y') }}
                    </strong>

                </div>

                <a href="{{ route('employees.attendance.calendar', [
                    'employee' => $employee->id,
                    'year' => $nextMonth->year,
                    'month' => $nextMonth->month,
                ]) }}"
                   class="month-arrow">

                    <i class="bi bi-chevron-right"></i>

                </a>

            </div>

            <form method="GET"
                  action="{{ route('employees.attendance.calendar', $employee->id) }}"
                  class="month-filter">

                <select name="month"
                        class="form-select">

                    @foreach($months as $monthOption)

                        <option value="{{ $monthOption['number'] }}"
                            {{ (int) $month === (int) $monthOption['number'] ? 'selected' : '' }}>

                            {{ $monthOption['name'] }}

                        </option>

                    @endforeach

                </select>

                <select name="year"
                        class="form-select">

                    @for(
                        $yearOption = now()->year - 5;
                        $yearOption <= now()->year + 2;
                        $yearOption++
                    )

                        <option value="{{ $yearOption }}"
                            {{ (int) $year === $yearOption ? 'selected' : '' }}>

                            {{ $yearOption }}

                        </option>

                    @endfor

                </select>

                <button type="submit">

                    <i class="bi bi-funnel"></i>
                    Apply

                </button>

            </form>

        </div>

        {{-- Legend --}}
        <div class="calendar-legend">

            <span>
                <i class="legend-dot manual-dot"></i>
                Manual
            </span>

            <span>
                <i class="legend-dot biometric-dot"></i>
                Biometric
            </span>

            <span>
                <i class="legend-dot absent-dot"></i>
                Absent
            </span>

            <span>
                <i class="legend-dot leave-dot"></i>
                Leave
            </span>

            <span>
                <i class="legend-dot half-dot"></i>
                Half Day
            </span>

            <span>
                <i class="legend-dot empty-dot"></i>
                Empty
            </span>

        </div>

        {{-- Weekdays --}}
        <div class="calendar-weekdays">

            <div>Mon</div>
            <div>Tue</div>
            <div>Wed</div>
            <div>Thu</div>
            <div>Fri</div>
            <div>Sat</div>
            <div>Sun</div>

        </div>

        {{-- Calendar Days --}}
        <div class="calendar-grid">

            @foreach($calendarDays as $day)

                @if($day === null)

                    <div class="empty-calendar-cell"></div>

                @else

                    @php
                        $attendance = $day['attendance'];

                        $statusClass = 'status-empty';
                        $statusLabel = 'Empty';
                        $statusIcon = 'bi-circle';

                        if ($attendance) {
                            if (
                                $attendance->status === 'present'
                                && ($attendance->source ?? 'manual') === 'biometric'
                            ) {
                                $statusClass = 'status-biometric';
                                $statusLabel = 'Biometric';
                                $statusIcon = 'bi-fingerprint';
                            } elseif ($attendance->status === 'present') {
                                $statusClass = 'status-manual';
                                $statusLabel = 'Present';
                                $statusIcon = 'bi-check-lg';
                            } elseif ($attendance->status === 'absent') {
                                $statusClass = 'status-absent';
                                $statusLabel = 'Absent';
                                $statusIcon = 'bi-x-lg';
                            } elseif ($attendance->status === 'leave') {
                                $statusClass = 'status-leave';
                                $statusLabel = 'Leave';
                                $statusIcon = 'bi-calendar2-minus';
                            } elseif ($attendance->status === 'half_day') {
                                $statusClass = 'status-half';
                                $statusLabel = 'Half Day';
                                $statusIcon = 'bi-circle-half';
                            }
                        }
                    @endphp

                    <button type="button"
                            class="calendar-day {{ $statusClass }} {{ $day['is_today'] ? 'today-date' : '' }}"
                            data-date="{{ $day['date'] }}"
                            data-date-label="{{ $day['date_label'] }}"
                            data-status="{{ $attendance?->status ?? '' }}"
                            data-source="{{ $attendance?->source ?? 'manual' }}">

                        <div class="day-top">

                            <strong>
                                {{ $day['day'] }}
                            </strong>

                            @if($day['is_today'])

                                <small>Today</small>

                            @endif

                        </div>

                        <div class="day-status">

                            <i class="bi {{ $statusIcon }}"></i>

                            <span>{{ $statusLabel }}</span>

                        </div>

                    </button>

                @endif

            @endforeach

        </div>

    </div>

</div>

{{-- Quick Modal --}}
<div class="modal fade"
     id="quickAttendanceModal"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered quick-modal-dialog">

        <div class="modal-content quick-modal">

            <div class="modal-header">

                <div>
                    <h5>Quick Attendance</h5>

                    <p id="quickSelectedDate">
                        Select attendance
                    </p>
                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <input type="hidden"
                       id="quickAttendanceDate">

                <div id="biometricMessage"
                     class="biometric-message d-none">

                    <i class="bi bi-fingerprint"></i>

                    <div>
                        <strong>Biometric Record</strong>

                        <span>
                            This attendance cannot be changed manually.
                        </span>
                    </div>

                </div>

                <div id="quickAttendanceOptions"
                     class="quick-options">

                    <button type="button"
                            class="quick-option quick-present"
                            data-status="present">

                        <i class="bi bi-check-circle-fill"></i>

                        <span>Present</span>

                    </button>

                    <button type="button"
                            class="quick-option quick-absent"
                            data-status="absent">

                        <i class="bi bi-x-circle-fill"></i>

                        <span>Absent</span>

                    </button>

                    <button type="button"
                            class="quick-option quick-leave"
                            data-status="leave">

                        <i class="bi bi-calendar2-minus-fill"></i>

                        <span>Leave</span>

                    </button>

                    <button type="button"
                            class="quick-option quick-half"
                            data-status="half_day">

                        <i class="bi bi-circle-half"></i>

                        <span>Half Day</span>

                    </button>

                </div>

                <button type="button"
                        id="clearAttendanceButton"
                        class="clear-button d-none">

                    <i class="bi bi-trash3"></i>
                    Clear Attendance

                </button>

            </div>

        </div>

    </div>

</div>

{{-- Toast --}}
<div id="attendanceToast"
     class="attendance-toast">

    <i class="bi bi-check-circle-fill"></i>

    <span id="attendanceToastText">
        Attendance saved
    </span>

</div>

<style>
:root {
    --black: #111111;
    --white: #ffffff;
    --border: #e4e6eb;
    --light: #f7f8fa;
    --muted: #747b86;

    --manual: #2563eb;
    --biometric: #198754;
    --absent: #dc3545;
    --leave: #f59e0b;
    --half: #7c3aed;
}

.quick-attendance-page {
    color: var(--black);
}

/* Header */

.attendance-header {
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 12px;
}

.header-left h3 {
    margin: 0;
    font-size: 26px;
    font-weight: 900;
}

.header-left p {
    margin: 3px 0 0;
    color: var(--muted);
    font-size: 11px;
}

.back-button {
    width: 39px;
    height: 39px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--black);
    border-radius: 8px;
    color: var(--white);
    background: var(--black);
    text-decoration: none;
}

.view-button {
    min-height: 39px;
    padding: 8px 13px;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    border: 1px solid var(--black);
    border-radius: 8px;
    color: var(--black);
    background: var(--white);
    text-decoration: none;
    font-size: 11px;
    font-weight: 900;
}

/* Employee */

.employee-bar {
    margin-bottom: 14px;
    padding: 14px 17px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    border: 1px solid var(--border);
    border-radius: 12px;
    background: var(--white);
}

.employee-section {
    display: flex;
    align-items: center;
    gap: 11px;
}

.employee-photo,
.employee-photo img {
    width: 53px;
    height: 53px;
}

.employee-photo img {
    object-fit: cover;
    border-radius: 50%;
    box-shadow: 0 0 0 1px var(--border);
}

.employee-code {
    padding: 3px 7px;
    border-radius: 5px;
    color: var(--white);
    background: var(--black);
    font-size: 8px;
    font-weight: 900;
}

.employee-info h4 {
    margin: 4px 0 0;
    font-size: 16px;
    font-weight: 900;
}

.employee-info p {
    margin: 4px 0 0;
    display: flex;
    align-items: center;
    gap: 5px;
    color: var(--muted);
    font-size: 9px;
    font-weight: 700;
}

.employee-info p span {
    width: 3px;
    height: 3px;
    border-radius: 50%;
    background: #a4a8ae;
}

.selected-month {
    min-width: 145px;
    padding: 9px 12px;
    border: 1px solid var(--border);
    border-radius: 8px;
    background: var(--light);
    text-align: center;
}

.selected-month small {
    display: block;
    color: var(--muted);
    font-size: 8px;
    font-weight: 900;
    text-transform: uppercase;
}

.selected-month strong {
    display: block;
    margin-top: 2px;
    font-size: 13px;
}

/* Compact count chips */

.attendance-counts {
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

.count-chip {
    min-height: 38px;
    padding: 7px 11px;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    border: 1px solid var(--border);
    border-radius: 8px;
    background: var(--white);
    font-size: 9px;
    font-weight: 800;
}

.count-chip i {
    font-size: 14px;
}

.count-chip strong {
    min-width: 24px;
    height: 24px;
    padding: 0 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    color: var(--white);
    font-size: 10px;
}

.manual-chip i {
    color: var(--manual);
}

.manual-chip strong {
    background: var(--manual);
}

.biometric-chip i {
    color: var(--biometric);
}

.biometric-chip strong {
    background: var(--biometric);
}

.absent-chip i {
    color: var(--absent);
}

.absent-chip strong {
    background: var(--absent);
}

.leave-chip i {
    color: var(--leave);
}

.leave-chip strong {
    background: var(--leave);
}

.half-chip i {
    color: var(--half);
}

.half-chip strong {
    background: var(--half);
}

/* Calendar */

.calendar-card {
    overflow: hidden;
    border: 1px solid var(--border);
    border-radius: 13px;
    background: var(--white);
}

.calendar-toolbar {
    padding: 13px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    border-bottom: 1px solid var(--border);
}

.month-navigation {
    display: flex;
    align-items: center;
    gap: 10px;
}

.month-arrow {
    width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--black);
    border-radius: 7px;
    color: var(--black);
    background: var(--white);
    text-decoration: none;
}

.month-arrow:hover {
    color: var(--white);
    background: var(--black);
}

.month-name {
    min-width: 125px;
    text-align: center;
}

.month-name small {
    display: block;
    color: var(--muted);
    font-size: 7px;
    font-weight: 900;
    text-transform: uppercase;
}

.month-name strong {
    display: block;
    font-size: 15px;
}

.month-filter {
    display: flex;
    align-items: center;
    gap: 6px;
}

.month-filter .form-select {
    min-width: 95px;
    min-height: 36px;
    border: 1px solid var(--border);
    border-radius: 7px;
    background: var(--light);
    font-size: 10px;
    font-weight: 700;
    box-shadow: none;
}

.month-filter button {
    min-height: 36px;
    padding: 7px 11px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    border: 1px solid var(--black);
    border-radius: 7px;
    color: var(--white);
    background: var(--black);
    font-size: 9px;
    font-weight: 900;
}

.calendar-legend {
    padding: 9px 16px;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 11px;
    border-bottom: 1px solid var(--border);
    background: var(--light);
}

.calendar-legend span {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    color: #555b64;
    font-size: 8px;
    font-weight: 800;
}

.legend-dot {
    width: 8px;
    height: 8px;
    border-radius: 2px;
}

.manual-dot {
    background: var(--manual);
}

.biometric-dot {
    background: var(--biometric);
}

.absent-dot {
    background: var(--absent);
}

.leave-dot {
    background: var(--leave);
}

.half-dot {
    background: var(--half);
}

.empty-dot {
    border: 1px solid #b9bdc4;
    background: var(--white);
}

.calendar-weekdays,
.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, minmax(0, 1fr));
}

.calendar-weekdays {
    background: var(--black);
}

.calendar-weekdays div {
    padding: 9px 5px;
    color: var(--white);
    text-align: center;
    font-size: 8px;
    font-weight: 900;
    text-transform: uppercase;
}

.empty-calendar-cell,
.calendar-day {
    min-height: 100px;
    border: 0;
    border-right: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
}

.calendar-day {
    padding: 9px;
    position: relative;
    text-align: left;
    cursor: pointer;
    transition: 0.15s ease;
}

.calendar-day:hover {
    z-index: 2;
    box-shadow: inset 0 0 0 2px var(--black);
}

.calendar-day:nth-child(7n),
.empty-calendar-cell:nth-child(7n) {
    border-right: 0;
}

.day-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.day-top strong {
    width: 25px;
    height: 25px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    color: var(--black);
    background: rgba(255, 255, 255, 0.88);
    font-size: 10px;
}

.day-top small {
    padding: 3px 5px;
    border-radius: 4px;
    color: var(--white);
    background: var(--black);
    font-size: 6px;
    font-weight: 900;
    text-transform: uppercase;
}

.day-status {
    margin-top: 12px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.day-status i {
    font-size: 14px;
}

.day-status span {
    font-size: 8px;
    font-weight: 900;
}

.status-empty {
    color: var(--muted);
    background: var(--white);
}

.status-manual {
    color: var(--white);
    background: var(--manual);
}

.status-biometric {
    color: var(--white);
    background: var(--biometric);
}

.status-absent {
    color: var(--white);
    background: var(--absent);
}

.status-leave {
    color: #3f2d00;
    background: #fbbf24;
}

.status-half {
    color: var(--white);
    background: var(--half);
}

.today-date {
    box-shadow: inset 0 0 0 2px var(--black);
}

/* Quick modal */

.quick-modal-dialog {
    max-width: 370px;
}

.quick-modal {
    overflow: hidden;
    border: 0;
    border-radius: 14px;
}

.quick-modal .modal-header {
    padding: 15px 17px;
}

.quick-modal .modal-header h5 {
    margin: 0;
    font-size: 16px;
    font-weight: 900;
}

.quick-modal .modal-header p {
    margin: 2px 0 0;
    color: var(--muted);
    font-size: 9px;
}

.quick-modal .modal-body {
    padding: 16px;
}

.quick-options {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 9px;
}

.quick-option {
    min-height: 70px;
    padding: 10px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 5px;
    border: 1px solid var(--border);
    border-radius: 9px;
    background: var(--light);
    font-size: 10px;
    font-weight: 900;
    cursor: pointer;
    transition: 0.16s ease;
}

.quick-option i {
    font-size: 21px;
}

.quick-present {
    color: var(--manual);
}

.quick-present:hover {
    border-color: var(--manual);
    color: var(--white);
    background: var(--manual);
}

.quick-absent {
    color: var(--absent);
}

.quick-absent:hover {
    border-color: var(--absent);
    color: var(--white);
    background: var(--absent);
}

.quick-leave {
    color: #9a6700;
}

.quick-leave:hover {
    border-color: var(--leave);
    color: #3f2d00;
    background: #fbbf24;
}

.quick-half {
    color: var(--half);
}

.quick-half:hover {
    border-color: var(--half);
    color: var(--white);
    background: var(--half);
}

.quick-option:disabled {
    cursor: wait;
    opacity: 0.65;
}

.clear-button {
    width: 100%;
    min-height: 39px;
    margin-top: 10px;
    border: 1px solid var(--absent);
    border-radius: 8px;
    color: var(--absent);
    background: var(--white);
    font-size: 9px;
    font-weight: 900;
}

.clear-button:hover {
    color: var(--white);
    background: var(--absent);
}

.biometric-message {
    padding: 14px;
    display: flex;
    align-items: flex-start;
    gap: 10px;
    border: 1px solid rgba(25, 135, 84, 0.3);
    border-radius: 9px;
    color: #0f5132;
    background: #d1e7dd;
}

.biometric-message > i {
    font-size: 24px;
}

.biometric-message strong,
.biometric-message span {
    display: block;
}

.biometric-message strong {
    font-size: 11px;
}

.biometric-message span {
    margin-top: 2px;
    font-size: 9px;
}

/* Toast */

.attendance-toast {
    position: fixed;
    right: 20px;
    bottom: 20px;
    z-index: 9999;
    min-width: 230px;
    padding: 11px 14px;
    display: none;
    align-items: center;
    gap: 7px;
    border-radius: 8px;
    color: var(--white);
    background: var(--black);
    font-size: 10px;
    font-weight: 800;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.18);
}

.attendance-toast.show {
    display: flex;
}

/* Responsive */

@media (max-width: 991px) {
    .attendance-header,
    .calendar-toolbar {
        align-items: flex-start;
        flex-direction: column;
    }

    .view-button {
        width: 100%;
        justify-content: center;
    }

    .month-filter {
        width: 100%;
    }

    .month-filter .form-select,
    .month-filter button {
        flex: 1;
    }

    .employee-bar {
        align-items: flex-start;
        flex-direction: column;
    }

    .selected-month {
        width: 100%;
    }

    .calendar-card {
        overflow-x: auto;
    }

    .calendar-weekdays,
    .calendar-grid {
        min-width: 820px;
    }
}

@media (max-width: 575px) {
    .header-left h3 {
        font-size: 21px;
    }

    .attendance-counts {
        display: grid;
        grid-template-columns: 1fr 1fr;
    }

    .count-chip {
        justify-content: space-between;
    }

    .quick-options {
        grid-template-columns: 1fr 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarDays = document.querySelectorAll('.calendar-day');

    const modalElement = document.getElementById(
        'quickAttendanceModal'
    );

    const attendanceModal = new bootstrap.Modal(
        modalElement
    );

    const dateInput = document.getElementById(
        'quickAttendanceDate'
    );

    const dateLabel = document.getElementById(
        'quickSelectedDate'
    );

    const quickOptions = document.querySelectorAll(
        '.quick-option'
    );

    const clearButton = document.getElementById(
        'clearAttendanceButton'
    );

    const biometricMessage = document.getElementById(
        'biometricMessage'
    );

    const quickAttendanceOptions = document.getElementById(
        'quickAttendanceOptions'
    );

    const toast = document.getElementById(
        'attendanceToast'
    );

    const toastText = document.getElementById(
        'attendanceToastText'
    );

    let selectedStatus = '';
    let selectedSource = 'manual';

    calendarDays.forEach(function (day) {
        day.addEventListener('click', function () {
            dateInput.value = this.dataset.date;
            dateLabel.textContent = this.dataset.dateLabel;

            selectedStatus = this.dataset.status || '';
            selectedSource = this.dataset.source || 'manual';

            if (selectedSource === 'biometric') {
                biometricMessage.classList.remove('d-none');
                quickAttendanceOptions.classList.add('d-none');
                clearButton.classList.add('d-none');
            } else {
                biometricMessage.classList.add('d-none');
                quickAttendanceOptions.classList.remove('d-none');

                clearButton.classList.toggle(
                    'd-none',
                    selectedStatus === ''
                );
            }

            attendanceModal.show();
        });
    });

    quickOptions.forEach(function (option) {
        option.addEventListener('click', function () {
            saveAttendance(this.dataset.status);
        });
    });

    clearButton.addEventListener('click', function () {
        if (!confirm('Clear attendance for this date?')) {
            return;
        }

        saveAttendance('clear');
    });

    async function saveAttendance(status) {
        setLoading(true);

        try {
            const response = await fetch(
                '{{ route('employees.attendance.save', $employee->id) }}',
                {
                    method: 'POST',

                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },

                    body: JSON.stringify({
                        attendance_date: dateInput.value,
                        status: status,
                        check_in: null,
                        check_out: null,
                        overtime_hours: 0,
                        remarks: null
                    })
                }
            );

            const result = await response.json();

            if (!response.ok || !result.success) {
                let message =
                    result.message ||
                    'Attendance could not be saved.';

                if (result.errors) {
                    const firstError = Object.values(
                        result.errors
                    )[0];

                    if (Array.isArray(firstError)) {
                        message = firstError[0];
                    }
                }

                throw new Error(message);
            }

            attendanceModal.hide();

            showToast(result.message);

            setTimeout(function () {
                window.location.reload();
            }, 350);
        } catch (error) {
            showToast(error.message, true);
        } finally {
            setLoading(false);
        }
    }

    function setLoading(loading) {
        quickOptions.forEach(function (option) {
            option.disabled = loading;
        });

        clearButton.disabled = loading;
    }

    function showToast(message, error = false) {
        toastText.textContent = message;

        toast.style.background = error
            ? '#dc3545'
            : '#111111';

        toast.classList.add('show');

        clearTimeout(window.quickAttendanceToast);

        window.quickAttendanceToast = setTimeout(
            function () {
                toast.classList.remove('show');
            },
            2500
        );
    }
});
</script>

@endsection
