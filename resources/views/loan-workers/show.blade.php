@extends('layouts.app')
@section('content')
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="d-flex justify-content-between align-items-center mb-3"><div><h3>{{ $employee->name }} — Loan Details</h3><span class="text-muted">{{ $employee->department??'-' }} · {{ $employee->employee_code }}</span></div><a class="btn btn-outline-dark" href="{{ route('loan-workers.index') }}">Back to Loan Workers</a></div>
@foreach($loans as $loan)
<div class="card mb-3 shadow-sm border-0"><div class="card-body"><div class="d-flex justify-content-between"><h5>Loan #{{ $loan->id }} · {{ \Illuminate\Support\Carbon::parse($loan->loan_date)->format('d M Y') }}</h5><span class="badge {{ $loan->remaining_amount<=0?'bg-success':'bg-secondary' }}">{{ $loan->remaining_amount<=0?'Repaid':'Outstanding' }}</span></div>
<p class="text-muted">Given by: {{ $loan->given_by }} · Installments from {{ \Illuminate\Support\Carbon::parse($loan->start_month)->format('F Y') }}</p>
<div class="row g-3 mb-3">@foreach(['Loan Amount'=>$loan->amount,'Monthly Installment'=>$loan->installment,'Recovered Through Paid Salary'=>$loan->recovered_amount,'Outstanding'=>$loan->remaining_amount,'Booked In Unsettled Salaries'=>$loan->reserved_amount] as $label=>$value)<div class="col"><small class="text-muted d-block">{{ $label }}</small><strong>Rs {{ number_format($value,2) }}</strong></div>@endforeach</div>
<div class="table-responsive"><table class="table"><thead><tr><th>Salary Month</th><th>Installment</th><th>Status</th><th>Salary Payment Date</th></tr></thead><tbody>@forelse($loan->payments as $payment)<tr><td>{{ \Illuminate\Support\Carbon::parse($payment->month)->format('F Y') }}</td><td>Rs {{ number_format($payment->amount,2) }}</td><td>{{ $payment->recovered?'Recovered':'Booked — Salary Not Fully Paid' }}</td><td>{{ $payment->payment_date??'-' }}</td></tr>@empty<tr><td colspan="4">No salary installment booked yet.</td></tr>@endforelse</tbody></table></div>
@if($loan->notes)<p class="mb-0 text-muted">{{ $loan->notes }}</p>@endif</div></div>
@endforeach
@if($records->isNotEmpty())<h5>Monthly Salary Loan Records</h5>@endif
<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Month</th><th>Loan Balance Before Installment</th><th>Scheduled Installment</th><th>Balance After Scheduled Installment</th><th>Salary Payment</th><th>Details</th></tr></thead><tbody>
@foreach($records as $row)
@php $f=$row->figures(); $payable=$f['net']+(float)$row->overdue; $settled=$payable>0 && (float)$row->paid_amount>=$payable; @endphp
<tr><td>{{ $row->month->format('F Y') }}</td><td>Rs {{ number_format($row->loan_balance,2) }}</td><td>Rs {{ number_format($row->loan_deduction,2) }}</td><td>Rs {{ number_format(max(0,$row->loan_balance-$row->loan_deduction),2) }}</td><td><span class="badge {{ $settled?'bg-success':'bg-secondary' }}">{{ $settled?'Paid':((float)$row->paid_amount>0?'Partially Paid':'Unpaid') }}</span></td><td><a href="{{ route('salary-management.index',['month'=>$row->month->format('Y-m')]) }}" class="btn btn-sm btn-outline-dark">Open Salary Month</a></td></tr>
@if($row->notes)<tr><td colspan="6" class="text-muted small">Notes: {{ $row->notes }}</td></tr>@endif
@endforeach
</tbody></table></div></div>
<p class="text-muted small mt-3">The remaining balance shown is after the scheduled installment. This is a monthly loan record, not proof of cash recovery. Loan issue dates and separate repayments are not recorded by the existing salary fields.</p>
@endsection
