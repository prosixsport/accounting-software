@extends('layouts.app')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><div><h3>Loan Workers</h3><p class="text-muted mb-0">Workers with saved loan balances or installments.</p></div><a href="{{ route('salary-management.index') }}" class="btn btn-dark">Salary Management</a></div>
<input id="loan-search" class="form-control mb-3" placeholder="Search worker or department" aria-label="Search loan workers">
<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Worker</th><th>Department</th><th>Latest Loan Month</th><th>Recorded Loan Balance</th><th>Scheduled Installment</th><th></th></tr></thead><tbody>
@forelse($employees as $employee)
@php $row=$history->get($employee->id)->first(); $photo=$employee->pictures[0]??null; @endphp
<tr data-loan-worker="{{ strtolower($employee->name.' '.$employee->department) }}"><td><div class="d-flex align-items-center gap-2">@if($photo)<img src="{{ asset('storage/'.$photo) }}" alt="" style="width:42px;height:42px;object-fit:cover;border-radius:8px">@endif<div><strong>{{ $employee->name }}</strong><small class="d-block text-muted">{{ $employee->employee_code }}</small></div></div></td><td>{{ $employee->department??'-' }}</td><td>{{ $row->month->format('F Y') }}</td><td>Rs {{ number_format($row->loan_balance,2) }}</td><td>Rs {{ number_format($row->loan_deduction,2) }}</td><td><a href="{{ route('loan-workers.show',$employee) }}" class="btn btn-sm btn-outline-dark">View Details</a></td></tr>
@empty<tr><td colspan="6" class="text-center p-4">No loan records saved yet. Add the loan balance and installment in Salary Management.</td></tr>@endforelse
</tbody></table></div></div>
<p class="text-muted small mt-3">Balances are saved monthly snapshots. Repeated balances are not added together as new loans.</p>
<script>document.getElementById('loan-search').addEventListener('input',function(){const q=this.value.toLowerCase().trim();document.querySelectorAll('[data-loan-worker]').forEach(row=>row.hidden=!row.dataset.loanWorker.includes(q));});</script>
@endsection
