@extends('layouts.app')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><div><h3>{{ $employee->name }} — Loan Details</h3><span class="text-muted">{{ $employee->department??'-' }} · {{ $employee->employee_code }}</span></div><a class="btn btn-outline-dark" href="{{ route('loan-workers.index') }}">Back to Loan Workers</a></div>
<div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Month</th><th>Loan Balance Before Installment</th><th>Scheduled Installment</th><th>Balance After Scheduled Installment</th><th>Salary Payment</th><th>Details</th></tr></thead><tbody>
@foreach($records as $row)
@php $f=$row->figures(); $payable=$f['net']+(float)$row->overdue; $settled=$payable>0 && (float)$row->paid_amount>=$payable; @endphp
<tr><td>{{ $row->month->format('F Y') }}</td><td>Rs {{ number_format($row->loan_balance,2) }}</td><td>Rs {{ number_format($row->loan_deduction,2) }}</td><td>Rs {{ number_format(max(0,$row->loan_balance-$row->loan_deduction),2) }}</td><td><span class="badge {{ $settled?'bg-success':'bg-secondary' }}">{{ $settled?'Paid':((float)$row->paid_amount>0?'Partially Paid':'Unpaid') }}</span></td><td><a href="{{ route('salary-management.index',['month'=>$row->month->format('Y-m')]) }}" class="btn btn-sm btn-outline-dark">Open Salary Month</a></td></tr>
@if($row->notes)<tr><td colspan="6" class="text-muted small">Notes: {{ $row->notes }}</td></tr>@endif
@endforeach
</tbody></table></div></div>
<p class="text-muted small mt-3">The remaining balance shown is after the scheduled installment. This is a monthly loan record, not proof of cash recovery. Loan issue dates and separate repayments are not recorded by the existing salary fields.</p>
@endsection
