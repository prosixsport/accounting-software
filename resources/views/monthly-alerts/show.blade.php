@extends('layouts.app')

@section('content')

<div class="card shadow border-0">

<div class="card-header bg-danger text-white">

<h4 class="mb-0">

Monthly Salary Alert

</h4>

</div>

<div class="card-body">

<table class="table">

<tr>

<th>Month</th>

<td>

{{ date('F',mktime(0,0,0,$alert->month,1)) }}

{{ $alert->year }}

</td>

</tr>

<tr>

<th>Employees Salary</th>

<td>

Rs {{ number_format($alert->employees_salary,2) }}

</td>

</tr>

<tr>

<th>Contractor Bills</th>

<td>

Rs {{ number_format($alert->contractor_bills,2) }}

</td>

</tr>

<tr>

<th>Factory Expenses</th>

<td>

Rs {{ number_format($alert->factory_expenses,2) }}

</td>

</tr>

<tr class="table-warning">

<th>Total Required</th>

<td>

<strong>

Rs {{ number_format($alert->total_required,2) }}

</strong>

</td>

</tr>

<tr>

<th>Status</th>

<td>

{{ ucfirst($alert->status) }}

</td>

</tr>

</table>

</div>

</div>

@endsection
