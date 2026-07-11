@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2 class="fw-bold">
            Monthly Alerts
        </h2>

        <p class="text-muted">
            Monthly salary & expense reminders
        </p>

    </div>

    <form action="{{ route('monthly-alerts.generate') }}" method="POST">

        @csrf

        <button class="btn btn-danger">

            <i class="bi bi-bell-fill"></i>

            Generate Alert

        </button>

    </form>

</div>

@if(session('success'))

<div class="alert alert-success">

    {{ session('success') }}

</div>

@endif

<div class="card shadow-sm border-0">

<div class="card-body p-0">

<table class="table table-hover align-middle mb-0">

<thead class="table-light">

<tr>

<th>#</th>

<th>Month</th>

<th>Employees</th>

<th>Contractors</th>

<th>Expenses</th>

<th>Total</th>

<th>Status</th>

<th width="220">

Action

</th>

</tr>

</thead>

<tbody>

@forelse($alerts as $alert)

<tr>

<td>

{{ $loop->iteration }}

</td>

<td>

{{ date('F', mktime(0,0,0,$alert->month,1)) }}

{{ $alert->year }}

</td>

<td>

Rs {{ number_format($alert->employees_salary,2) }}

</td>

<td>

Rs {{ number_format($alert->contractor_bills,2) }}

</td>

<td>

Rs {{ number_format($alert->factory_expenses,2) }}

</td>

<td class="fw-bold">

Rs {{ number_format($alert->total_required,2) }}

</td>

<td>

@if($alert->status=="pending")

<span class="badge bg-danger">

Pending

</span>

@else

<span class="badge bg-success">

Arranged

</span>

@endif

</td>

<td>

<a href="{{ route('monthly-alerts.show',$alert->id) }}"
class="btn btn-primary btn-sm">

View

</a>

@if($alert->status=="pending")

<form
action="{{ route('monthly-alerts.arranged',$alert->id) }}"
method="POST"
class="d-inline">

@csrf

<button class="btn btn-success btn-sm">

Funds Arranged

</button>

</form>

@endif

</td>

</tr>

@empty

<tr>

<td colspan="8"
class="text-center p-5 text-muted">

No alerts generated.

</td>

</tr>

@endforelse

</tbody>

</table>

</div>

</div>

@endsection
