{{-- Salary Management V5: 3 weekly advances, final payment, Select All, popup --}}
@extends('layouts.app')

@section('title', 'Salary Management')

@section('content')

@php

    $monthKey = $month->format('Y-m');

    $departments = $employees->pluck('department')->filter()->unique()->sort()->values();

    $savedRows = $rows->toBase()->only($employees->pluck('id')->all());

    $totalSalary = $savedRows->sum('salary');

    $totalAdvance = 0;

    $totalNet = 0;

    $totalDue = 0;

    foreach ($savedRows as $savedRow) {

        $numbers = $savedRow->figures();

        $totalAdvance += $numbers['advance'];

        $totalNet += $numbers['net'];

        $totalDue += $numbers['due'];

    }

    $activeEmployee = old('employee_id', session('active_employee'));

    $allSaved = $employees->isNotEmpty() && $savedRows->count() === $employees->count();

    $groups = [

        'salary' => ['title'=>'Salary & Loan', 'description'=>'Set the monthly salary and loan information.', 'fields'=>[

            'salary'=>'Monthly Salary', 'loan_balance'=>'Loan Balance',

        ]],

        'attendance' => ['title'=>'Attendance & Overtime', 'description'=>'Enter absents and the rates used to calculate deductions and overtime.', 'fields'=>[

            'absent_days'=>'Absent Days', 'day_rate'=>'Absence Rate / Day', 'ot_hours'=>'OT Hours', 'ot_rate'=>'OT Rate / Hour',

        ]],

        'deductions' => ['title'=>'Deductions & Payment', 'description'=>'Enter the deductions and salary payments for this month.', 'fields'=>[

            'loan_deduction'=>'Loan Deduction This Month', 'other_deduction'=>'Other Deduction', 'overdue'=>'Previous Due', 'paid_amount'=>'Final Salary Paid (Last Week)',

        ]],

    ];

@endphp

<style>
.salary-manager dialog.sm-editor{width:min(1080px,94vw);max-height:90vh;padding:0;margin:auto;overflow:auto;border:1px solid #dce3e8;box-shadow:0 24px 70px #0004}.salary-manager dialog.sm-editor::backdrop{background:#18263199;backdrop-filter:blur(3px)}.salary-manager dialog.sm-editor:not([open]){display:none}


.salary-manager{--sm-ink:#17232b;--sm-muted:#72808c;--sm-line:#e6ebef;--sm-accent:#137466;color:var(--sm-ink)}

.salary-manager *{box-sizing:border-box}.sm-heading{display:flex;justify-content:space-between;align-items:center;gap:16px;margin-bottom:24px}.sm-eyebrow{color:var(--sm-accent);font-size:11px;font-weight:800;letter-spacing:1.6px;text-transform:uppercase;margin-bottom:8px}.sm-heading h3{font-size:29px;font-weight:750;margin:0 0 7px}.sm-heading p{color:var(--sm-muted);margin:0;font-size:14px}

.sm-btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;border:1px solid var(--sm-line);background:white;color:var(--sm-ink);padding:10px 15px;border-radius:10px;text-decoration:none;font-size:13px;font-weight:650;cursor:pointer;white-space:nowrap}.sm-btn:hover{color:var(--sm-ink);background:#f0f4f6}.sm-btn:focus-visible,.salary-manager input:focus-visible,.salary-manager select:focus-visible{outline:3px solid #aad7cf;outline-offset:2px}.sm-btn-primary{background:var(--sm-ink);color:#fff;border-color:var(--sm-ink)}.sm-btn-primary:hover{background:#2c414e;color:#fff}.sm-btn:disabled{opacity:.45;cursor:not-allowed}.sm-btn-small{padding:7px 10px;font-size:12px}.sm-btn-delete{color:#b34747;border-color:#efd5d5}

.sm-stats{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:14px;margin-bottom:20px}.sm-stat{background:#fff;border:1px solid var(--sm-line);border-radius:14px;padding:18px}.sm-stat-label{color:var(--sm-muted);font-size:12px;margin-bottom:10px}.sm-stat strong{font-size:23px;display:block;letter-spacing:-.5px}.sm-stat small{display:block;font-size:11px;color:var(--sm-muted);margin-top:7px}.sm-stat-accent{background:#eff8f5;border-color:#d4e9e1}.sm-stat-accent strong{color:var(--sm-accent)}

.sm-card{background:#fff;border:1px solid var(--sm-line);border-radius:15px;overflow:hidden;margin-bottom:22px}.sm-toolbar{display:flex;align-items:end;justify-content:space-between;gap:16px;padding:20px;border-bottom:1px solid var(--sm-line);flex-wrap:wrap}.sm-month-form,.sm-actions{display:flex;gap:9px;align-items:end;flex-wrap:wrap}.sm-label{font-size:12px;font-weight:650;color:#51606b;display:block;margin-bottom:7px}.sm-control{border:1px solid #dce3e8;border-radius:9px;padding:10px 12px;font-size:13px;background:#fff;color:var(--sm-ink);min-height:41px;width:100%}.sm-month-form input{width:170px}.sm-filters{display:grid;grid-template-columns:minmax(210px,1fr) 190px 170px;gap:12px;padding:17px 20px;background:#fafcfd}.sm-search-wrap{position:relative}.sm-search-wrap i{position:absolute;top:13px;left:13px;color:var(--sm-muted)}.sm-search-wrap input{padding-left:38px}

.sm-list-title{display:flex;justify-content:space-between;gap:10px;padding:18px 20px 5px}.sm-list-title strong{font-size:14px}.sm-list-title small{color:var(--sm-muted)}.sm-table-wrap{overflow:auto}.sm-table{width:100%;border-collapse:collapse;font-size:13px;min-width:970px}.sm-table th{font-size:10px;text-transform:uppercase;letter-spacing:.7px;color:#7a8791;background:#fff;text-align:left;padding:15px 18px;border-bottom:1px solid var(--sm-line);white-space:nowrap}.sm-table td{padding:16px 18px;border-bottom:1px solid #edf1f4;vertical-align:middle}.sm-table tbody tr:last-child td{border-bottom:0}.sm-table tbody tr:hover{background:#fafcfd}.sm-table .sm-money{text-align:right;font-variant-numeric:tabular-nums;white-space:nowrap}.sm-person{display:flex;align-items:center;gap:11px;min-width:180px}.sm-person strong{font-size:13px;display:block}.sm-person small,.sm-cell-secondary{display:block;color:var(--sm-muted);font-size:11px;margin-top:4px}.sm-avatar{width:43px;height:43px;border-radius:12px;background:#eaf1f5;color:#496474;display:inline-flex;align-items:center;justify-content:center;position:relative;overflow:hidden;flex-shrink:0;font-weight:750;font-size:18px}.sm-avatar img{position:absolute;width:100%;height:100%;object-fit:cover;inset:0}.sm-avatar-large{width:58px;height:58px;border-radius:15px;font-size:25px}.sm-status{font-size:11px;font-weight:650;display:inline-flex;padding:5px 8px;border-radius:6px;white-space:nowrap}.sm-status-saved{background:#e9f6ef;color:#29734f}.sm-status-draft{background:#fff3dc;color:#946d20}.sm-row-actions{display:flex;gap:6px;justify-content:flex-end}.sm-note{margin:0;padding:12px 20px;background:#fafcfd;border-top:1px solid var(--sm-line);color:var(--sm-muted);font-size:12px}.sm-empty{text-align:center;padding:38px;color:var(--sm-muted)}

.sm-editor-header{padding:22px;display:flex;justify-content:space-between;gap:15px;align-items:center;border-bottom:1px solid var(--sm-line)}.sm-editor-header h4{font-size:18px;margin:0}.sm-editor-header p{font-size:12px;color:var(--sm-muted);margin:6px 0 0}.sm-editor-tabs{display:flex;gap:8px;padding:15px 22px;border-bottom:1px solid var(--sm-line)}.sm-tab[aria-selected="true"]{background:#edf7f4;color:var(--sm-accent);border-color:#d6eae3}.sm-editor-body{display:grid;grid-template-columns:minmax(0,1fr) 265px;gap:24px;padding:22px}.sm-section{margin-bottom:24px}.sm-section h5{font-size:14px;font-weight:750;margin-bottom:5px}.sm-section p{font-size:12px;color:var(--sm-muted);margin:0 0 15px}.sm-fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}.sm-hint{font-size:11px;color:var(--sm-muted);margin-top:5px}.sm-preview{border:1px solid #dceae5;background:#f4faf7;border-radius:12px;padding:18px;align-self:start}.sm-preview h5{font-size:14px;font-weight:750;margin:0 0 17px}.sm-preview-line{display:flex;justify-content:space-between;font-size:12px;gap:8px;margin:12px 0}.sm-preview-total{border-top:1px solid #d8e8df;padding-top:14px;margin-top:17px}.sm-preview-total strong{color:var(--sm-accent);font-size:18px}.sm-form-footer{display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap;margin-top:20px}.sm-form-footer small{color:var(--sm-muted);font-size:11px}.sm-week-grid{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:10px;margin:20px 0}.sm-week{background:#f7fafb;border:1px solid var(--sm-line);border-radius:10px;padding:12px}.sm-week small{font-size:10px;color:var(--sm-muted);display:block;margin:5px 0}.sm-week strong{font-size:13px;display:block}.salary-manager [hidden]{display:none!important}

@media(max-width:1100px){.sm-stats{grid-template-columns:repeat(3,minmax(0,1fr))}.sm-stat strong{font-size:20px}.sm-editor-body{grid-template-columns:1fr}.sm-preview{order:-1}.sm-week-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}

@media(max-width:640px){.sm-heading{align-items:start;flex-direction:column}.sm-heading h3{font-size:24px}.sm-stats{grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}.sm-stat{padding:14px}.sm-stat strong{font-size:18px}.sm-filters{grid-template-columns:1fr}.sm-toolbar,.sm-editor-header,.sm-editor-body{padding:16px}.sm-editor-header{align-items:start}.sm-fields{grid-template-columns:1fr}.sm-week-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.sm-actions{width:100%}.sm-actions .sm-btn{flex:1}.sm-person{min-width:0}.sm-person strong{overflow-wrap:anywhere}}

</style>

<div class="salary-manager" id="salary-manager">

    <header class="sm-heading">

        <div><div class="sm-eyebrow">Salary Workers / Monthly Records</div><h3>Salary Management</h3><p>3 weekly advances · Last-week salary payment · Employee slips</p></div>

        <a class="sm-btn" href="{{ route('payrolls.index') }}"><i class="bi bi-wallet2" aria-hidden="true"></i> Employees Payroll</a>

    </header>

    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif

    @if($errors->any())

        <div class="alert alert-danger" role="alert"><strong>Please check your entries.</strong><ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>

    @endif

    <div class="sm-stats">

        <div class="sm-stat"><div class="sm-stat-label">All Employees</div><strong>{{ $employees->count() }}</strong><small>{{ $savedRows->count() }} salary records saved</small></div>

        <div class="sm-stat"><div class="sm-stat-label">Monthly Salaries</div><strong>Rs {{ number_format($totalSalary, 2) }}</strong><small>{{ $month->format('F Y') }}</small></div>

        <div class="sm-stat"><div class="sm-stat-label">Total Advances</div><strong>Rs {{ number_format($totalAdvance, 2) }}</strong><small>All saved employee records</small></div>

        <div class="sm-stat"><div class="sm-stat-label">Net Salary</div><strong>Rs {{ number_format($totalNet, 2) }}</strong><small>After additions and deductions</small></div>

        <div class="sm-stat sm-stat-accent"><div class="sm-stat-label">Remaining Due</div><strong>Rs {{ number_format($totalDue, 2) }}</strong><small>Includes previous dues and payments</small></div>

    </div>

    <section class="sm-card" aria-label="All employee salary records">

        <div class="sm-toolbar">

            <form method="get" class="sm-month-form" action="{{ route('salary-management.index') }}">

                <div><label class="sm-label" for="salary-month">Salary Month</label><input class="sm-control" id="salary-month" type="month" name="month" value="{{ $monthKey }}" required></div>

                <button class="sm-btn sm-btn-primary" type="submit">Load Month</button>

            </form>

            <div class="sm-actions">

                @foreach(['dates'=>'Date-wise Sheet', 'weeks'=>'3-Week Salary Sheet'] as $mode=>$label)

                    @if($allSaved)

                        <a target="_blank" rel="noopener" class="sm-btn" href="{{ route('salary-management.print', ['month'=>$monthKey, 'mode'=>$mode]) }}"><i class="bi bi-printer" aria-hidden="true"></i> {{ $label }}</a>

                    @else

                        <button type="button" class="sm-btn" disabled title="Save salary details for all employees before printing">{{ $label }}</button>

                    @endif

                @endforeach

            </div>

        </div>

        <div class="sm-filters">

            <div class="sm-search-wrap"><i class="bi bi-search" aria-hidden="true"></i><input type="search" id="salary-search" class="sm-control" placeholder="Search name, employee code or phone" aria-label="Search employees"></div>

            <select id="salary-department" class="sm-control" aria-label="Filter department"><option value="">All Departments</option>@foreach($departments as $department)<option value="{{ $department }}">{{ $department }}</option>@endforeach</select>

            <select id="salary-status" class="sm-control" aria-label="Filter salary records"><option value="">All Salary Records</option><option value="saved">Saved</option><option value="draft">Not Saved</option></select>

        </div>

        <form id="salary-bulk-print" method="get" target="_blank" action="{{ route('salary-management.print') }}" style="padding:16px 20px;border-top:1px solid var(--sm-line)">
            <input type="hidden" name="month" value="{{ $monthKey }}">
            <input type="hidden" name="scope" value="selected">
            <div class="sm-actions">
                <label class="sm-label" for="salary-print-mode">Print format</label>
                <select class="sm-control" style="width:auto" id="salary-print-mode" name="mode"><option value="slips">Individual Salary Slips</option><option value="weeks">3-Week Salary Sheet</option><option value="dates">Date-wise Sheet</option></select>
                <button type="submit" id="salary-print-selected" class="sm-btn sm-btn-primary" disabled>Print Selected (0)</button>
            </div>
            <p class="sm-hint mb-0 mt-2">Select All selects saved employees shown by your filters. Unsaved records must be saved first. The buttons above print all employees.</p>
        </form>
        <div class="sm-list-title"><strong>Employee Directory</strong><small id="salary-count" aria-live="polite">{{ $employees->count() }} employees</small></div>

        <div class="sm-table-wrap"><table class="sm-table">

            <thead><tr><th><input type="checkbox" id="salary-select-all" aria-label="Select all saved employees shown by filters"></th><th>Employee</th><th>Department</th><th class="sm-money">Salary</th><th class="sm-money">Advance</th><th class="sm-money">Net Pay</th><th class="sm-money">Due</th><th>Record</th><th style="text-align:right">Actions</th></tr></thead>

            <tbody>

            @forelse($employees as $employee)

                @php

                    $row = $rows->get($employee->id);

                    $figures = $row ? $row->figures() : null;

                    $searchText = implode(' ', [$employee->name, $employee->employee_code, $employee->department, $employee->designation, $employee->phone]);

                @endphp

                <tr class="sm-employee-row" data-search="{{ $searchText }}" data-department="{{ $employee->department ?? '' }}" data-status="{{ $row ? 'saved' : 'draft' }}">

                    <td><input type="checkbox" class="sm-print-check" name="employee_ids[]" form="salary-bulk-print" value="{{ $employee->id }}" aria-label="Select {{ $employee->name }} for print" @unless($row) disabled @endunless></td><td><div class="sm-person">@php $person = $employee; $sizeClass = ''; @endphp

@php

    $pictures = $person->pictures;

    if (is_string($pictures)) {

        $pictures = json_decode($pictures, true) ?: [];

    }

    $photo = is_array($pictures) ? ($pictures[0] ?? null) : null;

    $photoUrl = null;

    if (is_string($photo) && trim($photo) !== '') {

        if ((str_starts_with(strtolower($photo), 'http://') || str_starts_with(strtolower($photo), 'https://'))) {

            $photoUrl = $photo;

        } else {

            $photoPath = ltrim($photo, '/');

            $photoUrl = asset(str_starts_with($photoPath, 'storage/') ? $photoPath : 'storage/'.$photoPath);

        }

    }

    $initial = mb_strtoupper(mb_substr(trim($person->name ?? '?'), 0, 1));

@endphp

<span class="sm-avatar {{ $sizeClass ?? '' }}">

    <span class="sm-initial" aria-hidden="true">{{ $initial }}</span>

    @if($photoUrl)

        <img src="{{ $photoUrl }}" alt="{{ $person->name }}" loading="lazy" onerror="this.remove()">

    @endif

</span>

<div><strong>{{ $employee->name }}</strong><small>{{ $employee->employee_code ?? 'No employee code' }} @if($employee->phone) · {{ $employee->phone }} @endif</small></div></div></td>

                    <td>{{ $employee->department ?? '—' }}<span class="sm-cell-secondary">{{ $employee->designation ?? '—' }}</span></td>

                    <td class="sm-money">{{ number_format($row ? $row->salary : ($employee->basic_salary ?? 0), 2) }} @unless($row)<span class="sm-cell-secondary">Basic salary · not saved</span>@endunless</td>

                    <td class="sm-money">{{ $row ? number_format($figures['advance'], 2) : '—' }}</td>

                    <td class="sm-money"><strong>{{ $row ? number_format($figures['net'], 2) : '—' }}</strong></td>

                    <td class="sm-money">{{ $row ? number_format($figures['due'], 2) : '—' }}</td>

                    <td><span class="sm-status {{ $row ? 'sm-status-saved' : 'sm-status-draft' }}">{{ $row ? 'Saved' : 'Not Saved' }}</span></td>

                    <td><div class="sm-row-actions">

                        <button type="button" class="sm-btn sm-btn-small" data-open-employee="{{ $employee->id }}" data-open-tab="salary" aria-controls="salary-editor-{{ $employee->id }}">Edit Details</button>

                        <button type="button" class="sm-btn sm-btn-small" data-open-employee="{{ $employee->id }}" data-open-tab="advances" aria-controls="salary-editor-{{ $employee->id }}">Advance</button>

                        @if($row)<a class="sm-btn sm-btn-small" target="_blank" rel="noopener" aria-label="Print salary slip for {{ $employee->name }}" href="{{ route('salary-management.print', ['month'=>$monthKey, 'employee_id'=>$employee->id]) }}"><i class="bi bi-printer" aria-hidden="true"></i> Slip</a>@endif

                    </div></td>

                </tr>

            @empty<tr><td colspan="9" class="sm-empty">No employees yet. Add employees from the Employees page first.</td></tr>@endforelse

            </tbody>

        </table></div>

        <div class="sm-empty" id="salary-no-results" hidden>No employees match your filters.</div>

        <p class="sm-note">Totals use saved records for {{ $month->format('F Y') }}. @unless($allSaved)Save salary details for all employees to enable the combined salary sheets.@endunless</p>

    </section>

    @foreach($employees as $employee)

        @php

            $row = $rows->get($employee->id);

            $figures = $row ? $row->figures() : ['advance'=>0, 'absence'=>0, 'ot'=>0, 'net'=>0, 'due'=>0];

            $restore = (string)old('employee_id') === (string)$employee->id;

            $advances = $row ? $row->advances : collect();

        @endphp

        <dialog class="sm-card sm-editor" id="salary-editor-{{ $employee->id }}" data-employee-id="{{ $employee->id }}" @if((string)$activeEmployee !== (string)$employee->id) hidden @endif>

            <header class="sm-editor-header"><div class="sm-person">@php $person = $employee; $sizeClass = 'sm-avatar-large'; @endphp

@php

    $pictures = $person->pictures;

    if (is_string($pictures)) {

        $pictures = json_decode($pictures, true) ?: [];

    }

    $photo = is_array($pictures) ? ($pictures[0] ?? null) : null;

    $photoUrl = null;

    if (is_string($photo) && trim($photo) !== '') {

        if ((str_starts_with(strtolower($photo), 'http://') || str_starts_with(strtolower($photo), 'https://'))) {

            $photoUrl = $photo;

        } else {

            $photoPath = ltrim($photo, '/');

            $photoUrl = asset(str_starts_with($photoPath, 'storage/') ? $photoPath : 'storage/'.$photoPath);

        }

    }

    $initial = mb_strtoupper(mb_substr(trim($person->name ?? '?'), 0, 1));

@endphp

<span class="sm-avatar {{ $sizeClass ?? '' }}">

    <span class="sm-initial" aria-hidden="true">{{ $initial }}</span>

    @if($photoUrl)

        <img src="{{ $photoUrl }}" alt="{{ $person->name }}" loading="lazy" onerror="this.remove()">

    @endif

</span>

<div><h4 tabindex="-1" class="sm-editor-title">{{ $employee->name }}</h4><p>{{ $employee->department ?? 'No department' }} · {{ $employee->designation ?? 'No designation' }} · {{ $month->format('F Y') }}</p></div></div><button type="button" class="sm-btn sm-btn-small" data-close-editor>Close</button></header>

            <div class="sm-editor-tabs"><button type="button" class="sm-btn sm-tab" data-tab="salary" aria-pressed="true" aria-selected="true">Salary Details</button><button type="button" class="sm-btn sm-tab" data-tab="advances" aria-pressed="false" aria-selected="false">Advance Ledger ({{ $advances->count() }})</button></div>

            <div data-tab-panel="salary">

                <div class="sm-editor-body">

                    <form method="post" action="{{ route('salary-management.save') }}" class="sm-salary-form" data-month-days="{{ $month->daysInMonth }}" data-advance-total="{{ $figures['advance'] }}">

                        @csrf

                        <input type="hidden" name="month" value="{{ $monthKey }}"><input type="hidden" name="employee_id" value="{{ $employee->id }}">

                        @foreach($groups as $group)

                            <section class="sm-section"><h5>{{ $group['title'] }}</h5><p>{{ $group['description'] }}</p><div class="sm-fields">

                                @foreach($group['fields'] as $field=>$label)

                                    @php

                                        $default = $field === 'salary' ? ($employee->basic_salary ?? 0) : ($field === 'day_rate' ? round(($employee->basic_salary ?? 0)/$month->daysInMonth, 2) : 0);

                                        $value = $row ? $row->{$field} : $default;

                                        if ($restore) $value = old($field, $value);

                                    @endphp

                                    <div><label class="sm-label" for="sm-{{ $employee->id }}-{{ $field }}">{{ $label }}</label><input class="sm-control" id="sm-{{ $employee->id }}-{{ $field }}" type="number" name="{{ $field }}" min="0" max="{{ $field === 'absent_days' ? $month->daysInMonth : '9999999999.99' }}" step="0.01" value="{{ $value }}" @if(in_array($field,['day_rate','ot_rate'])) readonly aria-readonly="true" @endif required>

                                        @if($field === 'loan_balance')<div class="sm-hint">Information only. Deduct the monthly loan installment separately.</div>@endif

                                    </div>

                                @endforeach

                            </div></section>

                        @endforeach

                        <label class="sm-label" for="sm-hours-{{ $employee->id }}">Working Hours Per Day</label>
                        <input class="sm-control" id="sm-hours-{{ $employee->id }}" type="number" name="working_hours_per_day" min="1" max="24" step="0.01" value="{{ $restore ? old('working_hours_per_day', $row?->working_hours_per_day ?? 8) : ($row?->working_hours_per_day ?? 8) }}" required>
                        <p class="sm-hint">Absence rate = monthly salary ÷ days in selected month. OT rate = absence rate ÷ working hours per day. Enter overtime as decimal hours (1 hour 30 minutes = 1.5).</p>
                        <label class="sm-label" for="sm-notes-{{ $employee->id }}">Additional Notes</label><textarea class="sm-control" id="sm-notes-{{ $employee->id }}" name="notes" rows="3" maxlength="2000" placeholder="Add salary adjustments, payment details or remarks">{{ $restore ? old('notes', $row?->notes) : $row?->notes }}</textarea>

                        <p class="sm-hint mt-3">Last week: enter the salary amount actually paid in Final Salary Paid, then save. Advances are already deducted from the net salary.</p><button type="button" class="sm-btn" data-fill-final>Fill Full Final Salary</button>
                        <div class="sm-form-footer"><button class="sm-btn sm-btn-primary" type="submit"><i class="bi bi-check2-circle" aria-hidden="true"></i> Save Salary Details</button><small>Changes save when you press this button.</small></div>

                    </form>

                    <aside class="sm-preview" aria-label="Salary calculation preview"><h5>Salary Preview</h5><div class="sm-preview-line"><span>Monthly Salary</span><b data-preview="salary">—</b></div><div class="sm-preview-line"><span>Overtime (+)</span><b data-preview="ot">—</b></div><div class="sm-preview-line"><span>Absents (−)</span><b data-preview="absence">—</b></div><div class="sm-preview-line"><span>Advances (−)</span><b data-preview="advance">—</b></div><div class="sm-preview-line"><span>Other & Loan (−)</span><b data-preview="deduction">—</b></div><div class="sm-preview-line sm-preview-total"><span>Net Pay</span><strong data-preview="net">—</strong></div><div class="sm-preview-line"><span>Previous Due (+)</span><b data-preview="overdue">—</b></div><div class="sm-preview-line"><span>Final Salary Paid (−)</span><b data-preview="paid">—</b></div><div class="sm-preview-line sm-preview-total"><span>Remaining Due</span><strong data-preview="due">—</strong></div><p class="sm-hint">Preview includes unsaved form entries. Save before printing.</p>@if($row)<a class="sm-btn w-100" target="_blank" rel="noopener" href="{{ route('salary-management.print', ['month'=>$monthKey, 'employee_id'=>$employee->id]) }}">Print Saved Slip</a>@endif</aside>

                </div>

            </div>

            <div data-tab-panel="advances" hidden style="padding:22px">

                <section class="sm-section"><h5>Add Weekly Advance</h5><p>Record advances for Week 1, Week 2 or Week 3. Pay the remaining salary in the last week using Salary Details.</p>

                    <form method="post" action="{{ route('salary-management.advance') }}">

                        @csrf

                        <input type="hidden" name="month" value="{{ $monthKey }}"><input type="hidden" name="employee_id" value="{{ $employee->id }}">

                        <label class="sm-label" for="sm-week-{{ $employee->id }}">Advance Week</label>
                        <select class="sm-control sm-advance-week mb-3" id="sm-week-{{ $employee->id }}" name="advance_week" required data-month="{{ $monthKey }}">
                            @for($week = 1; $week <= 3; $week++)
                                <option value="{{ $week }}" @selected($restore && (int)old('advance_week',1)===$week)>Week {{ $week }} Advance</option>
                            @endfor
                        </select>
                        <p class="sm-hint">Choose one of the three advance weeks and the actual payment date. The last-week salary is recorded separately; do not add it as another advance.</p>
                        <div class="sm-fields"><div><label class="sm-label" for="sm-date-{{ $employee->id }}">Advance Date</label><input class="sm-control" id="sm-date-{{ $employee->id }}" type="date" name="advance_date" min="{{ $month->toDateString() }}" max="{{ $month->copy()->endOfMonth()->toDateString() }}" value="{{ $restore ? old('advance_date', $month->isSameMonth(now()) ? now()->toDateString() : $month->toDateString()) : ($month->isSameMonth(now()) ? now()->toDateString() : $month->toDateString()) }}" required></div><div><label class="sm-label" for="sm-amount-{{ $employee->id }}">Advance Amount</label><input class="sm-control" id="sm-amount-{{ $employee->id }}" name="amount" type="number" min="0.01" max="9999999999.99" step="0.01" value="{{ $restore ? old('amount') : '' }}" required></div></div>

                        <label class="sm-label mt-3" for="sm-reason-{{ $employee->id }}">Reason / Remarks</label><input class="sm-control" id="sm-reason-{{ $employee->id }}" name="reason" maxlength="1000" value="{{ $restore ? old('reason') : '' }}" placeholder="Enter the purpose of this advance"><button type="submit" class="sm-btn sm-btn-primary mt-3">Add Advance</button>

                    </form>

                </section>

                <div class="sm-week-grid">@for($w=1; $w<=3; $w++)<div class="sm-week"><span class="sm-label">Week {{ $w }}</span><small>Advance installment</small><strong>Rs {{ number_format($advances->filter(fn($a)=>(int)($a->advance_week ?? min(3,intdiv($a->advance_date->day-1,7)+1))===$w)->sum('amount'),2) }}</strong></div>@endfor</div>

                <div class="sm-table-wrap"><table class="sm-table" style="min-width:600px"><thead><tr><th>Date</th><th>Week</th><th>Reason</th><th class="sm-money">Amount</th><th></th></tr></thead><tbody>

                    @forelse($advances as $advance)<tr><td>{{ $advance->advance_date->format('d M Y') }}</td><td>Week {{ $advance->advance_week ?? min(3,intdiv($advance->advance_date->day-1,7)+1) }}</td><td>{{ $advance->reason ?? '—' }}</td><td class="sm-money">Rs {{ number_format($advance->amount,2) }}</td><td><form method="post" action="{{ route('salary-management.advance.delete',$advance) }}" onsubmit="return confirm('Remove this advance payment?')">@csrf @method('DELETE')<button class="sm-btn sm-btn-small sm-btn-delete" type="submit">Remove</button></form></td></tr>@empty<tr><td colspan="5" class="sm-empty">No advances added for this employee this month.</td></tr>@endforelse

                </tbody><tfoot><tr><td colspan="3"><strong>Total Advance</strong></td><td class="sm-money"><strong>Rs {{ number_format($figures['advance'],2) }}</strong></td><td></td></tr></tfoot></table></div>

            </div>

        </dialog>

    @endforeach

</div>

<script>

(function () {

    const root = document.getElementById('salary-manager');

    if (!root) return;

    const search = root.querySelector('#salary-search');

    const department = root.querySelector('#salary-department');

    const status = root.querySelector('#salary-status');

    const employeeRows = Array.from(root.querySelectorAll('.sm-employee-row'));

    const editors = Array.from(root.querySelectorAll('.sm-editor'));

    let lastOpener = null;

    function applyFilters() {

        const query = search.value.toLocaleLowerCase().trim();

        let visible = 0;

        employeeRows.forEach(function (row) {

            const matches = row.dataset.search.toLocaleLowerCase().includes(query)

                && (!department.value || row.dataset.department === department.value)

                && (!status.value || row.dataset.status === status.value);

            row.hidden = !matches;
            if (!matches) row.querySelector('.sm-print-check').checked = false;

            if (matches) visible++;

        });

        root.querySelector('#salary-count').textContent = visible + ' of ' + employeeRows.length + ' employees';

        root.querySelector('#salary-no-results').hidden = visible > 0 || employeeRows.length === 0;
        refreshSelection();

    }

    const selectAll = root.querySelector('#salary-select-all');
    const printButton = root.querySelector('#salary-print-selected');
    function refreshSelection() {
        const available = employeeRows.filter(row => !row.hidden).map(row => row.querySelector('.sm-print-check')).filter(box => !box.disabled);
        const selected = available.filter(box => box.checked).length;
        selectAll.disabled = available.length === 0;
        selectAll.checked = available.length > 0 && selected === available.length;
        selectAll.indeterminate = selected > 0 && selected < available.length;
        printButton.disabled = selected === 0;
        printButton.textContent = 'Print Selected (' + selected + ')';
    }
    selectAll.addEventListener('change', function () {
        employeeRows.filter(row => !row.hidden).forEach(function (row) {
            const box = row.querySelector('.sm-print-check');
            if (!box.disabled) box.checked = selectAll.checked;
        });
        refreshSelection();
    });
    root.querySelectorAll('.sm-print-check').forEach(box => box.addEventListener('change', refreshSelection));
    root.querySelector('#salary-bulk-print').addEventListener('submit', function (event) {
        if (!root.querySelector('.sm-print-check:checked')) event.preventDefault();
    });
    refreshSelection();
    search.addEventListener('input', applyFilters);

    department.addEventListener('change', applyFilters);

    status.addEventListener('change', applyFilters);

    function setTab(editor, name) {

        editor.querySelectorAll('[data-tab]').forEach(function (button) {

            button.setAttribute('aria-selected', String(button.dataset.tab === name));

            button.setAttribute('aria-pressed', String(button.dataset.tab === name));

        });

        editor.querySelectorAll('[data-tab-panel]').forEach(function (panel) {

            panel.hidden = panel.dataset.tabPanel !== name;

        });

    }

    root.addEventListener('click', function (event) {

        const opener = event.target.closest('[data-open-employee]');

        if (opener) {

            lastOpener = opener;

            editors.forEach(function (editor) {

                if (editor.open) editor.close();
                editor.hidden = editor.dataset.employeeId !== opener.dataset.openEmployee;

                if (!editor.hidden) {

                    setTab(editor, opener.dataset.openTab);

                    if (!editor.open) editor.showModal();

                    editor.querySelector('.sm-editor-title').focus({ preventScroll: true });

                }

            });

        }

        const tab = event.target.closest('[data-tab]');

        if (tab) setTab(tab.closest('.sm-editor'), tab.dataset.tab);

        const close = event.target.closest('[data-close-editor]');

        if (close) {

            close.closest('.sm-editor').close();
            close.closest('.sm-editor').hidden = true;

            if (lastOpener) lastOpener.focus();

        }

    });

    const formatter = new Intl.NumberFormat('en-PK', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    const round = function (value) { return Math.round((value + Number.EPSILON) * 100) / 100; };

    root.querySelectorAll('.sm-salary-form').forEach(function (form) {

        function updateRates() {
            const salary = Number(form.elements.namedItem('salary').value) || 0;
            const days = Number(form.dataset.monthDays);
            const hours = Number(form.elements.namedItem('working_hours_per_day').value);
            const dayRate = round(salary / days);
            form.elements.namedItem('day_rate').value = dayRate.toFixed(2);
            form.elements.namedItem('ot_rate').value = (hours >= 1 && hours <= 24 ? round(dayRate / hours) : 0).toFixed(2);
        }
        function preview() {
            updateRates();

            const value = function (name) { const n = Number(form.elements.namedItem(name).value); return Number.isFinite(n) ? n : 0; };

            const numbers = { salary: value('salary'), advance: Number(form.dataset.advanceTotal),

                absence: round(value('absent_days') * value('day_rate')), ot: round(value('ot_hours') * value('ot_rate')),

                deduction: round(value('loan_deduction') + value('other_deduction')), overdue: value('overdue'), paid: value('paid_amount') };

            numbers.net = round(numbers.salary + numbers.ot - numbers.absence - numbers.advance - numbers.deduction);

            numbers.due = round(numbers.net + numbers.overdue - numbers.paid);

            form.closest('.sm-editor').querySelectorAll('[data-preview]').forEach(function (element) {

                element.textContent = formatter.format(numbers[element.dataset.preview]);

            });

        }

        form.querySelector('[data-fill-final]').addEventListener('click', function () {
            const value = name => Number(form.elements.namedItem(name).value) || 0;
            const amount = round(value('salary') + round(value('ot_hours')*value('ot_rate')) - round(value('absent_days')*value('day_rate')) - Number(form.dataset.advanceTotal) - value('loan_deduction') - value('other_deduction') + value('overdue'));
            form.elements.namedItem('paid_amount').value = Math.max(0, amount).toFixed(2);
            preview();
        });
        form.addEventListener('input', preview);

        preview();

    });

    const restoreTab = {{ \Illuminate\Support\Js::from(old('advance_week') !== null || old('advance_date') !== null || old('amount') !== null ? 'advances' : session('active_tab', 'salary')) }};

    editors.forEach(function (editor) {
        editor.addEventListener('close', function () { if (!editor.open) editor.hidden = true; });
        editor.addEventListener('click', function (event) { if (event.target === editor) { const r=editor.getBoundingClientRect(); if(event.clientX<r.left || event.clientX>r.right || event.clientY<r.top || event.clientY>r.bottom) editor.close(); } });
        if (!editor.hidden) { setTab(editor, restoreTab); editor.showModal(); }
    });

})();

</script>

@endsection
