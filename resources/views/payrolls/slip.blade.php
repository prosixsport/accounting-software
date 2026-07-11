@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between mb-4 no-print">
    <h3 class="fw-bold">Salary Slips</h3>

    <div>
        <button onclick="window.print()" class="btn btn-primary">Print Slips</button>
        <a href="{{ route('payrolls.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>

@foreach($slips as $slip)
@php
    $employee = $slip['employee'];
    $profile = $employee->pictures[0] ?? null;
@endphp

<div class="salary-slip mb-4">

    <div class="slip-top">
        <div>
            <h2 class="mb-0 fw-bold">Accounts System</h2>
            <div class="text-muted">Employee Salary Slip</div>
            <div><strong>Month:</strong> {{ $slip['month_name'] }}</div>
        </div>

        <div class="slip-photo">
            @if($profile)
                <img src="{{ asset('storage/'.$profile) }}" alt="Employee Photo">
            @else
                <img src="https://ui-avatars.com/api/?name={{ urlencode($employee->name) }}&background=0D6EFD&color=fff&size=140" alt="Employee Photo">
            @endif
        </div>
    </div>

    <hr>

    <div class="row mb-3">
        <div class="col-6">
            <p><strong>Name:</strong> {{ $employee->name }}</p>
            <p><strong>Father Name:</strong> {{ $employee->father_name ?? '-' }}</p>
            <p><strong>Phone:</strong> {{ $employee->phone ?? '-' }}</p>
            <p><strong>CNIC:</strong> {{ $employee->cnic ?? '-' }}</p>
        </div>

        <div class="col-6">
            <p><strong>Department:</strong> {{ $employee->department ?? '-' }}</p>
            <p><strong>Designation:</strong> {{ $employee->designation ?? '-' }}</p>
            <p><strong>Slip Date:</strong> {{ date('d M Y') }}</p>
            <p><strong>Total Salary:</strong> Rs {{ number_format($slip['basic_salary'], 2) }}</p>
        </div>
    </div>

    <table class="table table-bordered salary-table">
        <tr>
            <th>Total Salary</th>
            <td>Rs {{ number_format($slip['basic_salary'], 2) }}</td>
        </tr>
        <tr>
            <th>Present Days</th>
            <td>{{ $slip['present_days'] }}</td>
        </tr>
        <tr>
            <th>Absent Days</th>
            <td>{{ $slip['absent_days'] }}</td>
        </tr>
        <tr>
            <th>Salary According To Present Days</th>
            <td>Rs {{ number_format($slip['earned_salary'], 2) }}</td>
        </tr>
        <tr>
            <th>Total Advance</th>
            <td>Rs {{ number_format($slip['advance_amount'], 2) }}</td>
        </tr>
        <tr class="final-row">
            <th>Final Payable Salary</th>
            <td><strong>Rs {{ number_format($slip['net_salary'], 2) }}</strong></td>
        </tr>
    </table>

    <h6 class="fw-bold mt-4">Advance Details</h6>

    <table class="table table-sm table-bordered">
        <thead>
            <tr>
                <th>Date</th>
                <th>Time</th>
                <th>Amount</th>
                <th>Remarks</th>
            </tr>
        </thead>

        <tbody>
            @forelse($slip['advances'] as $advance)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($advance->advance_date)->format('d M Y') }}</td>
                    <td>{{ $advance->advance_time ?? '-' }}</td>
                    <td>Rs {{ number_format($advance->amount, 2) }}</td>
                    <td>{{ $advance->remarks ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">No advance found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="row mt-5">
        <div class="col-6 text-center">
            <hr>
            <strong>Employee Signature</strong>
        </div>

        <div class="col-6 text-center">
            <hr>
            <strong>Authorized Signature</strong>
        </div>
    </div>

</div>

@endforeach

<style>
.salary-slip {
    background: #fff;
    max-width: 850px;
    margin: 0 auto;
    padding: 30px;
    border: 2px solid #111;
    box-shadow: 0 4px 16px rgba(0,0,0,.08);
}

.slip-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.slip-photo {
    width: 120px;
    height: 145px;
    border: 2px solid #111;
    padding: 4px;
    background: #fff;
}

.slip-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.salary-table th {
    width: 55%;
    background: #f8f9fa;
}

.final-row th,
.final-row td {
    background: #d1e7dd !important;
    font-size: 18px;
}

@media print {
    .no-print,
    aside,
    nav {
        display: none !important;
    }

    main {
        padding: 0 !important;
        margin: 0 !important;
    }

    body {
        background: #fff !important;
    }

    .salary-slip {
        box-shadow: none !important;
        border: 2px solid #000 !important;
        page-break-after: always;
        max-width: 100%;
    }
}
</style>

@endsection
