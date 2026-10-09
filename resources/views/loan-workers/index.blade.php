@extends('layouts.app')
@section('content')
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

<div class="d-flex justify-content-between align-items-center mb-3"><div><h3>Loan Workers</h3><p class="text-muted mb-0">Workers with saved loan balances or installments.</p></div><a href="{{ route('salary-management.index') }}" class="btn btn-dark">Salary Management</a></div>
<button type="button" class="btn btn-dark mb-3" onclick="document.getElementById('loan-add').showModal()">+ Add Worker Loan</button>
<input id="loan-search" class="form-control mb-3" placeholder="Search worker or department" aria-label="Search loan workers">
<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Worker</th><th>Department</th><th>Loan Date / Legacy Month</th><th>Outstanding Loan Balance</th><th>Scheduled Installment</th><th></th></tr></thead><tbody>
@forelse($employees as $employee)
@php $row=$history->get($employee->id)?->first(); $workerLoans=$loans->get($employee->id,collect()); $latest=$workerLoans->first(); $photo=$employee->pictures[0]??null; @endphp
<tr data-loan-worker="{{ strtolower($employee->name.' '.$employee->department) }}"><td><div class="d-flex align-items-center gap-2">@if($photo)<img src="{{ asset('storage/'.$photo) }}" alt="" style="width:42px;height:42px;object-fit:cover;border-radius:8px">@endif<div><strong>{{ $employee->name }}</strong><small class="d-block text-muted">{{ $employee->employee_code }}</small></div></div></td><td>{{ $employee->department??'-' }}</td><td>{{ $latest?\Illuminate\Support\Carbon::parse($latest->loan_date)->format('d M Y'):$row->month->format('F Y') }}</td><td>Rs {{ number_format($latest?$workerLoans->sum('remaining_amount'):$row->loan_balance,2) }}</td><td>Rs {{ number_format($latest?$workerLoans->filter(fn($l)=>$l->remaining_amount>0)->sum('installment'):$row->loan_deduction,2) }}</td><td><a href="{{ route('loan-workers.show',$employee) }}" class="btn btn-sm btn-outline-dark">View Details</a></td></tr>
@empty<tr><td colspan="6" class="text-center p-4">No loan records saved yet. Add the loan balance and installment in Salary Management.</td></tr>@endforelse
</tbody></table></div></div>
<p class="text-muted small mt-3">Balances are saved monthly snapshots. Repeated balances are not added together as new loans.</p>
<script>document.getElementById('loan-search').addEventListener('input',function(){const q=this.value.toLowerCase().trim();document.querySelectorAll('[data-loan-worker]').forEach(row=>row.hidden=!row.dataset.loanWorker.includes(q));});</script>
<dialog id="loan-add" style="width:min(850px,95vw);max-height:94dvh;overflow:auto;border:0;padding:26px;border-radius:12px">
<div class="d-flex justify-content-between mb-3"><h4>Add Worker Loan</h4><button class="btn btn-outline-dark" type="button" onclick="this.closest('dialog').close()">Close</button></div>
<p class="text-muted small">Enter cash actually given to the worker. It will appear as a Funds outflow once. Choose the first installment month. Existing unpaid salary rows from that month will update; paid salary records are protected.</p>
<form method="post" action="{{ route('loan-workers.store') }}" class="row g-3">@csrf
<input type="hidden" name="submission_key" value="{{ old('submission_key',(string)\Illuminate\Support\Str::uuid()) }}">
<div class="col-12"><label class="form-label">Worker / Department</label><select name="employee_id" class="form-select" required><option value="">Select Worker</option>@foreach($allEmployees as $worker)<option value="{{ $worker->id }}" @selected(old('employee_id')==$worker->id)>{{ $worker->name }} · {{ $worker->department??'No Department' }}</option>@endforeach</select></div>
<div class="col-md-6"><label class="form-label">Date Loan Given</label><input class="form-control" type="date" name="loan_date" value="{{ old('loan_date',now('Asia/Karachi')->toDateString()) }}" max="{{ now('Asia/Karachi')->toDateString() }}" required></div>
<div class="col-md-6"><label class="form-label">Installments Start From</label><input class="form-control" type="month" name="start_month" value="{{ old('start_month',now('Asia/Karachi')->format('Y-m')) }}" required></div>
<div class="col-md-6"><label class="form-label">Loan Amount (Rs)</label><input class="form-control" type="number" name="amount" value="{{ old('amount') }}" min="0.01" step="0.01" required></div>
<div class="col-md-6"><label class="form-label">Monthly Installment (Rs)</label><input class="form-control" type="number" name="installment" value="{{ old('installment') }}" min="0.01" step="0.01" required></div>
<div class="col-12"><label class="form-label">Cash Given By — Name</label><input class="form-control" name="given_by" maxlength="255" value="{{ old('given_by',auth()->user()->name) }}" required></div>
<div class="col-12"><label class="form-label">Notes</label><textarea class="form-control" name="notes" maxlength="2000">{{ old('notes') }}</textarea></div>
<div class="col-12"><button class="btn btn-dark">Save Loan</button></div>
</form></dialog>
@endsection
