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

        'attendance' => ['title'=>'Attendance & Overtime', 'description'=>'Enter absent days and overtime hours as whole numbers: 1, 2, 3. Rates and amounts update automatically from salary.', 'fields'=>[

            'absent_days'=>'Absent Days', 'day_rate'=>'Absence Rate / Day', 'ot_hours'=>'OT Hours', 'ot_rate'=>'OT Rate / Hour',

        ]],

        'deductions' => ['title'=>'Deductions & Payment', 'description'=>'Enter the deductions and salary payments for this month.', 'fields'=>[

            'loan_deduction'=>'Loan Deduction This Month', 'other_deduction'=>'Other Deduction', 'overdue'=>'Previous Due', 'paid_amount'=>'Final Salary Paid (Final Settlement)',

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

        <div><div class="sm-eyebrow">Salary Workers / Monthly Records</div><h3>Salary Management</h3><p>Add salaries, record advances and print slips.</p></div>

        <div class="sm-actions"><button type="button" class="sm-btn sm-btn-primary" id="sm-add-salary">+ Add Salary</button><a class="sm-btn" href="{{ route('payrolls.index') }}"><i class="bi bi-wallet2" aria-hidden="true"></i> Employees Payroll</a></div>

    </header>

    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif

    @if($errors->any())

        <div class="alert alert-danger" role="alert"><strong>Please check your entries.</strong><ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>

    @endif

    <div class="sm-stats">

        <button type="button" class="sm-stat" data-summary="employees" aria-expanded="false" aria-controls="sm-summary-panel"><div class="sm-stat-label">All Employees</div><strong>{{ $employees->count() }}</strong><small>{{ $savedRows->count() }} salary records saved</small></button>

        <button type="button" class="sm-stat" data-summary="salary" aria-expanded="false" aria-controls="sm-summary-panel"><div class="sm-stat-label">Monthly Salaries</div><strong>Rs {{ number_format($totalSalary, 2) }}</strong><small>{{ $month->format('F Y') }}</small></button>

        <button type="button" class="sm-stat" data-summary="advance" aria-expanded="false" aria-controls="sm-summary-panel"><div class="sm-stat-label">Total Advances</div><strong>Rs {{ number_format($totalAdvance, 2) }}</strong><small>All saved employee records</small></button>

        <button type="button" class="sm-stat" data-summary="net" aria-expanded="false" aria-controls="sm-summary-panel"><div class="sm-stat-label">Net Salary</div><strong>Rs {{ number_format($totalNet, 2) }}</strong><small>After additions and deductions</small></button>

        <button type="button" class="sm-stat sm-stat-accent" data-summary="due" aria-expanded="false" aria-controls="sm-summary-panel"><div class="sm-stat-label">Remaining Due</div><strong>Rs {{ number_format($totalDue, 2) }}</strong><small>Includes previous dues and payments</small></button>

    </div>

<section id="sm-summary-panel" class="sm-card" hidden style="padding:18px;margin-bottom:18px"><div class="d-flex justify-content-between"><strong id="sm-summary-title"></strong><button type="button" class="sm-btn sm-btn-small" id="sm-summary-close">Close</button></div><p class="sm-hint" id="sm-summary-note"></p><div class="table-responsive" style="max-height:320px"><table class="table table-sm"><thead id="sm-summary-head"></thead><tbody id="sm-summary-body"></tbody><tfoot id="sm-summary-foot"></tfoot></table></div></section>
<p class="sm-hint" style="text-align:right" id="sm-live-state">Live totals · click a card to see employee details</p>
    <section class="sm-card" aria-label="All employee salary records">

        <p class="sm-hint" style="padding:16px 20px 0;margin:0">Records for {{ $month->format('F Y') }}</p>
        <div class="sm-toolbar">

            <form method="get" class="sm-month-form" action="{{ route('salary-management.index') }}">

                <div><label class="sm-label" for="salary-month">Salary Month</label><input class="sm-control" id="salary-month" type="month" name="month" value="{{ $monthKey }}" required></div>

                <button class="sm-btn sm-btn-primary" type="submit">Load</button>
                <a class="sm-btn" href="{{ route('salary-management.index', ['month'=>$month->copy()->subMonthNoOverflow()->format('Y-m')]) }}">← Previous</a>
                <a class="sm-btn" href="{{ route('salary-management.index', ['month'=>$month->copy()->addMonthNoOverflow()->format('Y-m')]) }}">Next →</a>
                <a class="sm-btn" href="{{ route('salary-management.index', ['month'=>now()->format('Y-m')]) }}">This Month</a>

            </form>

            <div class="sm-actions">
                <div><label class="sm-label" for="sm-weeks-show">Weeks to Show</label><select class="sm-control" id="sm-weeks-show">@for($w=1;$w<=5;$w++)<option value="{{ $w }}" @selected($w===5)>{{ $w }} {{ $w===1?'Week':'Weeks' }}</option>@endfor</select></div>
                @foreach(['dates'=>'Date-wise Sheet', 'weeks'=>'Weekly Salary Sheet'] as $mode=>$label)

                    @if($savedRows->isNotEmpty())

                        <a target="_blank" rel="noopener" class="sm-btn" data-saved-print href="{{ route('salary-management.print', ['month'=>$monthKey, 'mode'=>$mode,'scope'=>'all','week_count'=>5]) }}"><i class="bi bi-printer" aria-hidden="true"></i> {{ $label }}</a>

                    @else

                        <button type="button" class="sm-btn" disabled title="Save at least one salary record to print">{{ $label }}</button>

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
                <input type="hidden" name="week_count" id="sm-print-week-count" value="5"><select class="sm-control" style="width:auto" id="salary-print-mode" name="mode"><option value="slips">Individual Salary Slips</option><option value="weeks">Weekly Salary Sheet</option><option value="dates">Date-wise Sheet</option></select>
                <button type="submit" id="salary-print-selected" class="sm-btn sm-btn-primary" disabled>Print Selected (0)</button>
            </div>
            <p class="sm-hint mb-0 mt-2">Select workers, choose the print format, then press Print Selected.</p>
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

                    <td class="sm-money">{{ $row ? number_format($row->salary,2) : '—' }}</td>

                    <td class="sm-money">{{ $row ? number_format($figures['advance'], 2) : '—' }}</td>

                    <td class="sm-money"><strong>{{ $row ? number_format($figures['net'], 2) : '—' }}</strong></td>

                    <td class="sm-money">{{ $row ? number_format($figures['due'], 2) : '—' }}</td>

                    <td><span class="sm-status {{ $row ? 'sm-status-saved' : 'sm-status-draft' }}">{{ $row ? 'Saved' : 'Not Saved' }}</span></td>

                    <td><div class="sm-row-actions">

                        @if($row)<button type="button" class="sm-btn sm-btn-small" data-open-employee="{{ $employee->id }}" data-open-tab="salary" aria-controls="salary-editor-{{ $employee->id }}">Edit Details</button>

                        <button type="button" class="sm-btn sm-btn-small" data-open-employee="{{ $employee->id }}" data-open-tab="advances" aria-controls="salary-editor-{{ $employee->id }}">Advance</button>@else<span class="sm-hint">—</span>@endif

                        @if($row)<a class="sm-btn sm-btn-small" target="_blank" rel="noopener" aria-label="Print salary slip for {{ $employee->name }}" href="{{ route('salary-management.print', ['month'=>$monthKey, 'employee_id'=>$employee->id]) }}"><i class="bi bi-printer" aria-hidden="true"></i> Slip</a>@endif

                    </div></td>

                </tr>

            @empty<tr><td colspan="9" class="sm-empty">No employees yet. Add employees from the Employees page first.</td></tr>@endforelse

            </tbody>

        </table></div>

        <div class="sm-empty" id="salary-no-results" hidden>No employees match your filters.</div>

        <p class="sm-note">Totals use saved records for {{ $month->format('F Y') }}. Combined sheets print saved workers only; unsaved workers are skipped.</p>

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
                        <label class="sm-label">Salary Date</label><input class="sm-control" type="date" name="salary_date" min="{{ $month->toDateString() }}" max="{{ $month->copy()->endOfMonth()->toDateString() }}" value="{{ $restore ? old('salary_date', $row?->salary_date ?? ($month->isSameMonth(now()) ? now()->toDateString() : $month->toDateString())) : ($row?->salary_date ?? ($month->isSameMonth(now()) ? now()->toDateString() : $month->toDateString())) }}">

                        @foreach($groups as $group)

                            <section class="sm-section"><h5>{{ $group['title'] }}</h5><p>{{ $group['description'] }}</p><div class="sm-fields">

                                @foreach($group['fields'] as $field=>$label)

                                    @php

                                        $default = $field === 'salary' ? ($employee->basic_salary ?? 0) : ($field === 'day_rate' ? round(($employee->basic_salary ?? 0)/$month->daysInMonth, 2) : 0);

                                        $value = $row ? $row->{$field} : $default;

                                        if ($restore) $value = old($field, $value);

                                    @endphp

                                    <div><label class="sm-label" for="sm-{{ $employee->id }}-{{ $field }}">{{ $label }}</label><input class="sm-control" id="sm-{{ $employee->id }}-{{ $field }}" type="number" name="{{ $field }}" min="0" max="{{ $field === 'absent_days' ? $month->daysInMonth : '9999999999.99' }}" step="{{ in_array($field,['absent_days','ot_hours']) ? '1' : '0.01' }}" value="{{ $value }}" @if(in_array($field,['day_rate','ot_rate'])) readonly aria-readonly="true" @endif required>

                                        @if($field === 'loan_balance')<div class="sm-hint">Information only. Deduct the monthly loan installment separately.</div>@endif

                                    </div>

                                @endforeach

                            </div></section>

                        @endforeach

                        <label class="sm-label" for="sm-hours-{{ $employee->id }}">Working Hours Per Day</label>
                        <input class="sm-control" id="sm-hours-{{ $employee->id }}" type="number" name="working_hours_per_day" min="1" max="24" step="1" value="{{ $restore ? old('working_hours_per_day', $row?->working_hours_per_day ?? 8) : ($row?->working_hours_per_day ?? 8) }}" required>
                        <p class="sm-hint">Absence rate = monthly salary ÷ days in selected month. OT rate = absence rate ÷ working hours per day. Enter whole days and whole overtime hours: 1, 2, 3. The preview updates immediately; click Save Salary Details to save changes.</p>
                        <label class="sm-label" for="sm-notes-{{ $employee->id }}">Additional Notes</label><textarea class="sm-control" id="sm-notes-{{ $employee->id }}" name="notes" rows="3" maxlength="2000" placeholder="Add salary adjustments, payment details or remarks">{{ $restore ? old('notes', $row?->notes) : $row?->notes }}</textarea>

                        <p class="sm-hint mt-3">Last week: enter the salary amount actually paid in Final Salary Paid, then save. Advances are already deducted from the net salary.</p><button type="button" class="sm-btn" data-fill-final>Fill Full Final Salary</button>
                        <div class="sm-form-footer"><button class="sm-btn sm-btn-primary" type="submit"><i class="bi bi-check2-circle" aria-hidden="true"></i> Save Salary Details</button><small>Changes save when you press this button.</small></div>

                    </form>

                    <aside class="sm-preview" aria-label="Salary calculation preview"><h5>Salary Preview</h5><div class="sm-preview-line"><span>Monthly Salary</span><b data-preview="salary">—</b></div><div class="sm-preview-line"><span>Overtime (+)</span><b data-preview="ot">—</b></div><div class="sm-preview-line"><span>Absents (−)</span><b data-preview="absence">—</b></div><div class="sm-preview-line"><span>Advances (−)</span><b data-preview="advance">—</b></div><div class="sm-preview-line"><span>Other & Loan (−)</span><b data-preview="deduction">—</b></div><div class="sm-preview-line sm-preview-total"><span>Net Pay</span><strong data-preview="net">—</strong></div><div class="sm-preview-line"><span>Previous Due (+)</span><b data-preview="overdue">—</b></div><div class="sm-preview-line"><span>Final Salary Paid (−)</span><b data-preview="paid">—</b></div><div class="sm-preview-line sm-preview-total"><span>Remaining Due</span><strong data-preview="due">—</strong></div><p class="sm-hint">Preview includes unsaved form entries. Save before printing.</p>@if($row)<a class="sm-btn w-100" target="_blank" rel="noopener" href="{{ route('salary-management.print', ['month'=>$monthKey, 'employee_id'=>$employee->id]) }}">Print Saved Slip</a>@endif</aside>

                </div>

            </div>

            <div data-tab-panel="advances" hidden style="padding:22px">

                <section class="sm-section"><h5>Add Weekly Advance</h5><p>Record advances for Weeks 1 to 5. Pay the remaining salary in the last week using Salary Details.</p>

                    <form method="post" action="{{ route('salary-management.advance') }}">

                        @csrf

                        <input type="hidden" name="month" value="{{ $monthKey }}"><input type="hidden" name="employee_id" value="{{ $employee->id }}">

                        <label class="sm-label" for="sm-week-{{ $employee->id }}">Advance Week</label>
                        <select class="sm-control sm-advance-week mb-3" id="sm-week-{{ $employee->id }}" name="advance_week" required data-month="{{ $monthKey }}">
                            @for($week = 1; $week <= 5; $week++)
                                <option value="{{ $week }}" @selected($restore && (int)old('advance_week',1)===$week)>Week {{ $week }} Advance</option>
                            @endfor
                        </select>
                        <p class="sm-hint">Choose one of the five advance weeks and the actual payment date. The last-week salary is recorded separately; do not add it as another advance.</p>
                        <div class="sm-fields"><div><label class="sm-label" for="sm-date-{{ $employee->id }}">Advance Date</label><input class="sm-control" id="sm-date-{{ $employee->id }}" type="date" name="advance_date" min="{{ $month->toDateString() }}" max="{{ $month->copy()->endOfMonth()->toDateString() }}" value="{{ $restore ? old('advance_date', $month->isSameMonth(now()) ? now()->toDateString() : $month->toDateString()) : ($month->isSameMonth(now()) ? now()->toDateString() : $month->toDateString()) }}" required></div><div><label class="sm-label" for="sm-amount-{{ $employee->id }}">Advance Amount</label><input class="sm-control" id="sm-amount-{{ $employee->id }}" name="amount" type="number" min="0.01" max="9999999999.99" step="0.01" value="{{ $restore ? old('amount') : '' }}" required></div></div>

                        <label class="sm-label mt-3" for="sm-reason-{{ $employee->id }}">Reason / Remarks</label><input class="sm-control" id="sm-reason-{{ $employee->id }}" name="reason" maxlength="1000" value="{{ $restore ? old('reason') : '' }}" placeholder="Enter the purpose of this advance"><button type="submit" class="sm-btn sm-btn-primary mt-3">Add Advance</button>

                    </form>

                </section>

                <div class="sm-week-grid">@for($w=1; $w<=5; $w++)<div class="sm-week"><span class="sm-label">Week {{ $w }}</span><small>Advance installment</small><strong>Rs {{ number_format($advances->filter(fn($a)=>(int)($a->advance_week ?? min(5,intdiv($a->advance_date->day-1,7)+1))===$w)->sum('amount'),2) }}</strong></div>@endfor</div>

                <div class="sm-table-wrap"><table class="sm-table" style="min-width:600px"><thead><tr><th>Date</th><th>Week</th><th>Reason</th><th class="sm-money">Amount</th><th></th></tr></thead><tbody>

                    @forelse($advances as $advance)<tr><td>{{ $advance->advance_date->format('d M Y') }}</td><td>Week {{ $advance->advance_week ?? min(5,intdiv($advance->advance_date->day-1,7)+1) }}</td><td>{{ $advance->reason ?? '—' }}</td><td class="sm-money">Rs {{ number_format($advance->amount,2) }}</td><td><form method="post" action="{{ route('salary-management.advance.delete',$advance) }}" onsubmit="return confirm('Remove this advance payment?')">@csrf @method('DELETE')<button class="sm-btn sm-btn-small sm-btn-delete" type="submit">Remove</button></form></td></tr>@empty<tr><td colspan="5" class="sm-empty">No advances added for this employee this month.</td></tr>@endforelse

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


@php
$entryWorkers=$employees->map(function($e) use($rows) {
 $r=$rows->get($e->id);
 $data=[];
 foreach(['salary','loan_balance','absent_days','day_rate','ot_hours','ot_rate','loan_deduction','other_deduction','overdue','paid_amount','working_hours_per_day','salary_date','notes'] as $field) $data[$field]=$r?->{$field};
 return ['id'=>$e->id,'name'=>$e->name,'department'=>$e->department ?? '', 'code'=>$e->employee_code ?? '', 'basic_salary'=>$e->basic_salary ?? 0,'saved'=>(bool)$r,'ledger'=>$r ? $r->advances->map(fn($a)=>['date'=>$a->advance_date->format('Y-m-d'),'week'=>(int)($a->advance_week ?? min(5,intdiv($a->advance_date->day-1,7)+1)),'amount'=>(float)$a->amount,'reason'=>$a->reason ?? ''])->values() : [],'data'=>$data,'advance'=>$r ? $r->figures()['advance'] : 0];
})->values();
@endphp
<dialog id="sm-batch-dialog" class="salary-manager" style="width:min(960px,95vw);max-height:92vh;border:1px solid #ddd;border-radius:16px;padding:0;overflow:hidden">
 <div style="padding:24px;max-height:calc(90vh - 76px);overflow:auto"><div class="d-flex justify-content-between"><div><h4>Add Salary — {{ $month->format('F Y') }}</h4><p class="sm-hint">Save salary to open the next worker in this department.</p></div><button class="sm-btn" type="button" id="sm-batch-close">Back to List</button></div>
 <div id="sm-batch-message" role="status" class="alert" hidden></div>
 <form id="sm-batch-form" action="{{ route('salary-management.save') }}" method="post">
 @csrf <input type="hidden" name="month" value="{{ $monthKey }}">
 <div class="row g-3 mb-3"><div class="col-md-4"><label class="sm-label" for="sm-entry-dept">Department</label><select class="sm-control" id="sm-entry-dept"><option value="">All Departments</option>@foreach($departments as $d)<option value="{{ $d }}">{{ $d }}</option>@endforeach</select></div>
 <div class="col-md-4"><label class="sm-label" for="sm-entry-search">Search Worker</label><input class="sm-control" id="sm-entry-search" type="search" placeholder="Name or employee code"></div>
 <div class="col-md-4"><label class="sm-label" for="sm-entry-worker">Worker · Department · Record</label><select class="sm-control" id="sm-entry-worker" name="employee_id" required><option value="">Select Worker</option></select></div></div>
 <fieldset id="sm-entry-fields" disabled style="border:0;padding:0">
 <label class="sm-label">Salary Date</label><input class="sm-control mb-3" name="salary_date" type="date" min="{{ $month->toDateString() }}" max="{{ $month->copy()->endOfMonth()->toDateString() }}" value="{{ $month->isSameMonth(now()) ? now()->toDateString() : $month->toDateString() }}" required>
 <h5>Salary & Attendance</h5><div class="row g-3">
 @foreach(['salary'=>'Monthly Salary','absent_days'=>'Absent Days','ot_hours'=>'Overtime Hours','working_hours_per_day'=>'Working Hours Per Day'] as $field=>$label)
 <div class="col-md-3"><label class="sm-label">{{ $label }}</label><input class="sm-control" type="number" name="{{ $field }}" min="{{ $field==='working_hours_per_day'?1:0 }}" max="{{ $field==='absent_days'?$month->daysInMonth:($field==='working_hours_per_day'?24:'9999999999.99') }}" step="{{ $field==='salary'?'0.01':1 }}" value="{{ $field==='working_hours_per_day'?8:0 }}" required></div>
 @endforeach
 <input type="hidden" name="day_rate" value="0"><input type="hidden" name="ot_rate" value="0">
 </div><p class="sm-hint mt-2" id="sm-entry-rates">Daily and hourly rates calculate automatically.</p>
 <details id="sm-extra-details" class="sm-section mt-3"><summary style="cursor:pointer;font-weight:600">Loan, Other Deductions & Payment (Optional)</summary><div class="row g-3 mt-1">
 @foreach(['loan_balance'=>'Loan Balance — information only','loan_deduction'=>'Loan Installment to Deduct','other_deduction'=>'Other Deduction','overdue'=>'Previous Unpaid Salary (+)','paid_amount'=>'Final Salary Already Paid'] as $field=>$label)<div class="col-md-4"><label class="sm-label">{{ $label }}</label><input class="sm-control" name="{{ $field }}" type="number" min="0" max="9999999999.99" step="0.01" value="0" required></div>@endforeach
 <div class="col-12"><label class="sm-label">Notes</label><textarea class="sm-control" name="notes" maxlength="2000"></textarea></div>
 </div><p class="sm-hint mt-2">Loan balance does not reduce salary. Only the entered loan installment is deducted.</p></details>
 <section class="sm-section mt-3"><h5>Add Advance (Optional)</h5><p>Existing advances remain saved. These fields add one new payment.</p><div class="row g-3"><div class="col-md-3"><label class="sm-label">Week</label><select class="sm-control" name="entry_advance_week"><option value="1">Week 1</option><option value="2">Week 2</option><option value="3">Week 3</option><option value="4">Week 4</option><option value="5">Week 5</option></select></div><div class="col-md-3"><label class="sm-label">Advance Date</label><input class="sm-control" name="entry_advance_date" type="date" min="{{ $month->toDateString() }}" max="{{ $month->copy()->endOfMonth()->toDateString() }}"></div><div class="col-md-3"><label class="sm-label">New Advance Amount</label><input class="sm-control" name="entry_advance_amount" type="number" min="0" max="9999999999.99" step="0.01" value="0"></div><div class="col-md-3"><label class="sm-label">Reason</label><input class="sm-control" name="entry_advance_reason" maxlength="1000"></div></div></section>
 <section class="sm-section mt-3"><h5 id="sm-ledger-title">Saved Advance Record</h5><div class="table-responsive"><table class="table table-sm"><thead><tr><th>Date</th><th>Week</th><th>Amount</th><th>Reason</th></tr></thead><tbody id="sm-entry-ledger"></tbody></table></div><div class="sm-hint" id="sm-entry-week-totals"></div><p class="sm-hint">New advances appear here after saving. Each payment is deducted once.</p></section>
 <div class="alert alert-info" id="sm-entry-preview" aria-live="polite"></div>
 </fieldset></form></div>
 <footer style="padding:14px 24px;background:white;border-top:1px solid #e5e7eb;display:flex;justify-content:space-between;align-items:center;gap:12px"><span class="sm-hint" id="sm-entry-progress">Choose a department and worker.</span><button class="sm-btn sm-btn-primary" type="submit" form="sm-batch-form" id="sm-entry-save" disabled>Save & Next Worker</button></footer>
 <div id="sm-dept-complete" hidden role="dialog" aria-modal="true" aria-labelledby="sm-complete-heading" style="position:absolute;inset:0;background:#f8fafcf5;align-items:center;justify-content:center;padding:24px;z-index:2">
 <div style="max-width:440px;width:100%;padding:32px;background:white;border:1px solid #e5e7eb;border-radius:18px;text-align:center;box-shadow:0 12px 40px #0001"><div style="font-size:34px;color:#137466">✓</div><h4 id="sm-complete-heading" tabindex="-1">Department Complete</h4><p id="sm-complete-text"></p><div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap"><button type="button" class="sm-btn sm-btn-primary" id="sm-next-department">Next Department</button><button type="button" class="sm-btn" id="sm-finish-entry">Back to List</button></div></div>
 </div>
</dialog>
<script>
(function(){
 const workers={{ \Illuminate\Support\Js::from($entryWorkers) }};
 const dialog=document.getElementById('sm-batch-dialog'), form=document.getElementById('sm-batch-form');
 const dept=document.getElementById('sm-entry-dept'), search=document.getElementById('sm-entry-search'), select=document.getElementById('sm-entry-worker'), fields=document.getElementById('sm-entry-fields'), message=document.getElementById('sm-batch-message');
 let dateDefault=form.elements.salary_date.value; const days={{ $month->daysInMonth }};
 let changed=false, saving=false;
 const num=n=>Number(form.elements.namedItem(n).value)||0, round=n=>Math.round((n+Number.EPSILON)*100)/100;
 const money=n=>'Rs '+n.toLocaleString('en-PK',{minimumFractionDigits:2,maximumFractionDigits:2});
 function renderLedger(w){
  const body=document.getElementById('sm-entry-ledger');body.replaceChildren();
  document.getElementById('sm-ledger-title').textContent='Saved Advance Record'+(w?' — '+w.name:'');
  const ledger=w?.ledger||[], totals=[0,0,0,0,0];
  for(const a of ledger){
   const tr=document.createElement('tr');
   for(const text of [a.date,'Week '+a.week,money(Number(a.amount)),a.reason||'—']){const td=document.createElement('td');td.textContent=text;tr.appendChild(td);}body.appendChild(tr);
   if(a.week>=1&&a.week<=5)totals[a.week-1]+=Number(a.amount);
  }
  if(!ledger.length){const tr=document.createElement('tr'),td=document.createElement('td');td.colSpan=4;td.textContent='No saved advances for this worker.';tr.appendChild(td);body.appendChild(tr);}
  document.getElementById('sm-entry-week-totals').textContent=totals.map((n,i)=>'Week '+(i+1)+': '+money(n)).join(' · ');
 }
 function progress(){
  const list=workers.filter(w=>!dept.value||w.department===dept.value), saved=list.filter(w=>w.saved).length;
  document.getElementById('sm-entry-progress').textContent=(dept.value||'All Departments')+' · '+saved+' of '+list.length+' saved';
 }
 function pickNext(){
  search.value='';select.value='';options();
  const next=workers.find(w=>(!dept.value||w.department===dept.value)&&!w.saved);
  if(next){select.value=String(next.id);load();progress();dialog.querySelector('[name="salary"]').focus();return;}
  fields.disabled=true;document.getElementById('sm-entry-save').disabled=true;progress();
  const pending=workers.find(w=>!w.saved);
  document.getElementById('sm-complete-text').textContent=(dept.value||'All Employees')+' salary records are complete for this month.'+(pending?' Continue with the next department.':' All departments are complete.');
  document.getElementById('sm-next-department').hidden=!pending;
  const panel=document.getElementById('sm-dept-complete');panel.hidden=false;document.getElementById('sm-complete-heading').focus();
 }
 document.getElementById('sm-next-department').addEventListener('click',()=>{
  document.getElementById('sm-dept-complete').hidden=true;
  const next=workers.find(w=>!w.saved);if(next){dept.value=next.department;pickNext();}
 });
 document.getElementById('sm-finish-entry').addEventListener('click',()=>dialog.close());
 function options(){
  const selected=select.value, q=search.value.trim().toLocaleLowerCase();
  select.replaceChildren(new Option('Select Worker',''));
  workers.filter(w=>(!dept.value || w.department===dept.value) && (w.name+' '+w.code).toLocaleLowerCase().includes(q)).forEach(w=>select.add(new Option(w.name+' · '+(w.department||'No Department')+' · '+(w.saved?'Saved':'Not Saved'),String(w.id))));
  select.value=selected;
  if(!select.value){fields.disabled=true;document.getElementById('sm-entry-save').disabled=true;renderLedger(null);}progress();
 }
 function preview(){
  const w=workers.find(w=>String(w.id)===select.value);if(!w)return;
  const day=round(num('salary')/days), hrs=num('working_hours_per_day');
  form.elements.day_rate.value=day.toFixed(2);form.elements.ot_rate.value=(hrs>=1&&hrs<=24?round(day/hrs):0).toFixed(2);
  const advances=Number(w.advance)+num('entry_advance_amount');
  const net=round(num('salary')+round(num('ot_hours')*num('ot_rate'))-round(num('absent_days')*day)-advances-num('loan_deduction')-num('other_deduction'));
  document.getElementById('sm-entry-rates').textContent='Absence rate: '+money(day)+'/day · Overtime rate: '+money(num('ot_rate'))+'/hour';
  const preview=document.getElementById('sm-entry-preview');preview.replaceChildren();
  for(const [label,amount] of [['Monthly Salary (+)',num('salary')],['Overtime (+)',round(num('ot_hours')*num('ot_rate'))],['Absence Deduction (−)',round(num('absent_days')*day)],['Advances (−)',advances],['Loan Installment (−)',num('loan_deduction')],['Other Deduction (−)',num('other_deduction')],['Net Salary',net],['Previous Due (+)',num('overdue')],['Final Salary Paid (−)',num('paid_amount')],['Remaining Due',round(net+num('overdue')-num('paid_amount'))]]){
   if(amount===0 && !['Monthly Salary (+)','Net Salary','Remaining Due'].includes(label))continue;
   const line=document.createElement('div');line.style.cssText='display:flex;justify-content:space-between;gap:12px;padding:4px 0';
   const caption=document.createElement('span'),value=document.createElement('strong');caption.textContent=label;value.textContent=money(amount);line.appendChild(caption);line.appendChild(value);preview.appendChild(line);
  }
 }
 function load(){
  const w=workers.find(w=>String(w.id)===select.value);fields.disabled=!w;document.getElementById('sm-entry-save').disabled=!w;if(!w)return;
  for(const [key,val] of Object.entries(w.data)){if(form.elements.namedItem(key))form.elements.namedItem(key).value=val??(key==='salary'?w.basic_salary:key==='working_hours_per_day'?8:key==='salary_date'?dateDefault:key==='notes'?'':0);}
  for(const key of ['absent_days','ot_hours','working_hours_per_day'])form.elements.namedItem(key).value=Number(form.elements.namedItem(key).value);
  renderLedger(w);document.getElementById('sm-extra-details').open=['loan_balance','loan_deduction','other_deduction','overdue','paid_amount'].some(k=>num(k)>0);
  form.elements.entry_advance_amount.value=0;form.elements.entry_advance_date.value=form.elements.salary_date.value;form.elements.entry_advance_reason.value='';preview();
 }
 document.getElementById('sm-add-salary').addEventListener('click',()=>{document.getElementById('sm-dept-complete').hidden=true;options();dialog.showModal();});
 document.getElementById('sm-batch-close').addEventListener('click',()=>{if(!saving)dialog.close();});
 dialog.addEventListener('cancel',e=>{if(saving)e.preventDefault();});
 dialog.addEventListener('close',()=>{if(changed)window.location.reload();});
 dept.addEventListener('change',()=>{select.value='';search.value='';pickNext();});search.addEventListener('input',options);select.addEventListener('change',load);form.addEventListener('input',preview);
 form.addEventListener('submit',async e=>{
  e.preventDefault();if(saving || !form.reportValidity())return;
  const w=workers.find(w=>String(w.id)===select.value);if(!w)return;
  if(num('entry_advance_amount')>0 && !form.elements.entry_advance_date.value){form.elements.entry_advance_date.reportValidity();message.hidden=false;message.className='alert alert-danger';message.textContent='Enter the advance date.';return;}
  saving=true;const button=document.getElementById('sm-entry-save');button.disabled=true;button.textContent='Saving…';message.hidden=true;
  try{
   const response=await fetch(form.action,{method:'POST',body:new FormData(form),headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'},credentials:'same-origin'});
   const data=await response.json();if(!response.ok)throw new Error(data.errors?Object.values(data.errors).flat().join(' '):(data.message||'Unable to save.'));
   w.saved=true;w.data=data.row;w.advance=data.advance;w.ledger=data.ledger||[];changed=true;document.dispatchEvent(new Event('salary-record-saved'));
   const name=w.name, keptDepartment=dept.value, keptDate=form.elements.salary_date.value;dateDefault=keptDate;fields.disabled=false;form.reset();dept.value=keptDepartment;search.value='';form.elements.salary_date.value=keptDate;fields.disabled=true;select.value='';options();renderLedger(w);document.getElementById('sm-extra-details').open=false;document.getElementById('sm-entry-preview').replaceChildren();
   message.hidden=false;message.className='alert alert-success';message.textContent=name+' salary saved.';pickNext();
  }catch(error){message.hidden=false;message.className='alert alert-danger';message.textContent=error.message;}
  finally{saving=false;button.disabled=!select.value;button.textContent='Save & Next Worker';}
 });
})();
</script>


<style>
#sm-dept-complete[hidden]{display:none!important}#sm-dept-complete:not([hidden]){display:flex}
#sm-entry-save{display:inline-flex!important;background:#17232b!important;color:#fff!important;border:1px solid #17232b!important;min-height:44px;min-width:190px;opacity:1}
#sm-entry-save:hover{background:#2c414e!important}
#sm-entry-save:disabled{background:#64748b!important;border-color:#64748b!important;color:white!important;opacity:.65;cursor:not-allowed}
#sm-batch-dialog::backdrop{background:#17232b80;backdrop-filter:blur(2px)}
#sm-batch-dialog h4{font-size:22px}#sm-batch-dialog h5{font-size:16px}#sm-batch-dialog .sm-label{font-size:13px}#sm-batch-dialog .sm-hint{font-size:12px}
#sm-entry-preview{background:#f4f8f7;border:1px solid #dfe9e5;color:#17232b;margin:12px 0 0;border-radius:12px}
#sm-entry-ledger td,#sm-entry-ledger th{font-size:12px}
.salary-manager .sm-stat{padding:16px}.salary-manager .sm-stat small{font-size:12px}.salary-manager .sm-toolbar{gap:12px}.salary-manager .sm-table td{padding-top:12px;padding-bottom:12px}
@media(max-width:640px){#sm-batch-dialog footer{flex-direction:column;align-items:stretch!important}#sm-entry-progress{font-size:11px}#sm-entry-save{width:100%}}
</style>


<script>
(function(){
 let overview={{ \Illuminate\Support\Js::from($overview) }}, revision={{ \Illuminate\Support\Js::from($revision) }}, current=null, busy=false;
 const weeks=document.getElementById('sm-weeks-show'),panel=document.getElementById('sm-summary-panel');
 const money=n=>'Rs '+Number(n).toLocaleString('en-PK',{minimumFractionDigits:2,maximumFractionDigits:2});
 const selectedAdvance=w=>(w.ledger||[]).filter(a=>Number(a.week)<=Number(weeks.value)).reduce((sum,a)=>sum+Number(a.amount),0);
 function row(parent,values,tag='td') {const tr=document.createElement('tr');for(const val of values){const cell=document.createElement(tag);cell.textContent=val;tr.appendChild(cell);}parent.appendChild(tr);}
 function render(){
  const saved=overview.filter(w=>w.saved);
  const totals={employees:overview.length,salary:saved.reduce((s,w)=>s+w.salary,0),advance:saved.reduce((s,w)=>s+selectedAdvance(w),0),net:saved.reduce((s,w)=>s+w.net,0),due:saved.reduce((s,w)=>s+w.due,0)};
  document.querySelectorAll('[data-summary]').forEach(button=>{button.querySelector('strong').textContent=button.dataset.summary==='employees'?totals.employees:money(totals[button.dataset.summary]);button.setAttribute('aria-expanded',String(current===button.dataset.summary));});
  document.querySelector('[data-summary="employees"] small').textContent=saved.length+' saved · '+(overview.length-saved.length)+' pending';
  document.querySelector('[data-summary="advance"] small').textContent='Weeks 1–'+weeks.value+' · click for payment details';
  if(!current){panel.hidden=true;return;}panel.hidden=false;
  const titles={employees:'All Employees',salary:'Saved Monthly Salaries',advance:'Advance Payments — Weeks 1–'+weeks.value,net:'Monthly Net Salary',due:'Remaining Salary Due'};
  document.getElementById('sm-summary-title').textContent=titles[current];
  document.getElementById('sm-summary-note').textContent=current==='advance'?'Shows saved payments for the selected weeks.':'Salary, net and due are monthly totals. Net and due include every saved advance.';
  const head=document.getElementById('sm-summary-head'),body=document.getElementById('sm-summary-body'),foot=document.getElementById('sm-summary-foot');head.replaceChildren();body.replaceChildren();foot.replaceChildren();
  if(current==='advance'){
   row(head,['Worker','Department','Date','Week','Amount','Reason'],'th');
   let count=0;for(const w of saved)for(const a of w.ledger||[])if(Number(a.week)<=Number(weeks.value)){row(body,[w.name,w.department||'—',a.date,'Week '+a.week,money(a.amount),a.reason||'—']);count++;}
   if(!count)row(body,['No advance payments in these weeks.']);row(foot,['Total','','','',money(totals.advance),''],'th');
  }else{
   row(head,['Worker','Department',current==='employees'?'Record':titles[current]],'th');
   const list=current==='employees'?overview:saved;for(const w of list)row(body,[w.name,w.department||'—',current==='employees'?(w.saved?'Saved':'Not Saved'):money(w[current])]);
   if(!list.length)row(body,['No saved salary records yet.']);row(foot,['Total','',current==='employees'?list.length:money(totals[current])],'th');
  }
 }
 document.querySelectorAll('[data-summary]').forEach(button=>button.addEventListener('click',()=>{current=current===button.dataset.summary?null:button.dataset.summary;render();}));
 document.getElementById('sm-summary-close').addEventListener('click',()=>{current=null;render();});
 weeks.addEventListener('change',()=>{document.getElementById('sm-print-week-count').value=weeks.value;document.querySelectorAll('[data-saved-print]').forEach(a=>{const url=new URL(a.href,location.href);url.searchParams.set('week_count',weeks.value);a.href=url.href;});render();});
 async function refresh(afterSave=false){
  if(busy||document.hidden)return;
  if(!afterSave && document.querySelector('dialog[open]'))return;
  busy=true;try{
   const response=await fetch({{ \Illuminate\Support\Js::from(route('salary-management.index',['month'=>$monthKey])) }},{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'},credentials:'same-origin',cache:'no-store'});
   if(!response.ok)throw new Error();const data=await response.json();
   if(data.revision!==revision && !afterSave){try{sessionStorage.setItem('salary-view-'+{{ \Illuminate\Support\Js::from($monthKey) }},JSON.stringify({search:document.getElementById('salary-search').value,department:document.getElementById('salary-department').value,status:document.getElementById('salary-status').value,weeks:weeks.value,mode:document.getElementById('salary-print-mode').value,current,selected:Array.from(document.querySelectorAll('.sm-print-check:checked')).map(b=>b.value)}));}catch(e){}location.reload();return;}
   overview=data.overview;revision=data.revision;render();document.getElementById('sm-live-state').textContent='Updated '+new Date().toLocaleTimeString()+' · click a card for details';
  }catch(e){document.getElementById('sm-live-state').textContent='Live refresh unavailable. Reload to check the latest saved records.';}
  finally{busy=false;}
 }
 document.addEventListener('salary-record-saved',()=>refresh(true));setInterval(()=>refresh(),30000);
 document.addEventListener('visibilitychange',()=>{if(!document.hidden)refresh();});
 try{
  const key='salary-view-'+{{ \Illuminate\Support\Js::from($monthKey) }}, saved=JSON.parse(sessionStorage.getItem(key)||'null');sessionStorage.removeItem(key);
  if(saved){document.getElementById('salary-search').value=saved.search||'';document.getElementById('salary-department').value=saved.department||'';document.getElementById('salary-status').value=saved.status||'';weeks.value=saved.weeks||'5';document.getElementById('salary-print-mode').value=saved.mode||'slips';current=saved.current||null;
   weeks.dispatchEvent(new Event('change'));document.getElementById('salary-search').dispatchEvent(new Event('input'));
   document.querySelectorAll('.sm-print-check').forEach(b=>{b.checked=!b.disabled&&(saved.selected||[]).includes(b.value);});const first=document.querySelector('.sm-print-check');if(first)first.dispatchEvent(new Event('change'));
  }
 }catch(e){}render();
})();
</script>
<style>
.sm-stat{text-align:left;cursor:pointer;transition:border-color .15s,box-shadow .15s;width:100%;color:#17232b}.sm-stat:hover,.sm-stat[aria-expanded="true"]{border-color:#137466;box-shadow:0 3px 12px #13746614}.sm-stat:focus-visible{outline:3px solid #aad7cf;outline-offset:2px}
#sm-summary-panel th{color:#64748b;font-size:12px}#sm-summary-panel td{font-size:13px;padding:9px 6px}#sm-summary-panel tfoot{font-weight:700;border-top:2px solid #dce3e8}
</style>

@endsection
