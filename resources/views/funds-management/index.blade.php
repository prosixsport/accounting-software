@extends('layouts.app')
@section('title','Funds Management')
@section('content')
@php $money=fn($value)=>\App\Services\FundsLedger::money($value); @endphp
<div class="fm">
<div class="fm-heading"><div><small>MONTHLY FUNDS & PAYMENTS</small><h2>Funds Management</h2><p>Every receipt and payment in one monthly ledger.</p></div><a class="btn btn-dark" onclick="document.getElementById('receive-cash').open=true" href="#receive-cash">+ Receive Boss Funds</a></div>
<div class="fm-tools"><form method="get"><input type="month" name="month" value="{{ $month->format('Y-m') }}" required><button class="btn btn-dark">Load Month</button></form><a href="{{ route('funds-management.index',['month'=>$month->copy()->subMonthNoOverflow()->format('Y-m')]) }}">← Previous</a><a href="{{ route('funds-management.index',['month'=>$month->copy()->addMonthNoOverflow()->format('Y-m')]) }}">Next →</a><button class="btn btn-outline-dark" onclick="window.print()">Print Report</button></div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif
<details id="receive-cash" class="fm-receive" @if($errors->any()) open @endif>
<summary>+ Receive Cash from Boss</summary>
<form method="post" action="{{ route('funds-management.store') }}" class="row g-3 mt-2">
@csrf
<input type="hidden" name="submission_key" value="{{ old('submission_key',(string)\Illuminate\Support\Str::uuid()) }}">
<div class="col-md-3"><label class="form-label">Boss</label><select name="boss" class="form-select" required><option value="">Select boss</option>@foreach(['Boss Azeem','Boss Atif','Boss Kashif'] as $boss)<option value="{{ $boss }}" @selected(old('boss')===$boss)>{{ $boss }}</option>@endforeach</select></div>
<div class="col-md-3"><label class="form-label">Received Date</label><input class="form-control" type="date" name="receipt_date" value="{{ old('receipt_date',now()->toDateString()) }}" max="{{ now()->toDateString() }}" required></div>
<div class="col-md-3"><label class="form-label">Amount (Rs)</label><input class="form-control" type="number" name="amount" step="0.01" min="0.01" value="{{ old('amount') }}" required></div>
<div class="col-md-3"><label class="form-label">Received In</label><select class="form-select" name="method"><option value="cash" @selected(old('method')==='cash')>Cash</option><option value="bank" @selected(old('method')==='bank')>Bank</option></select></div>
<div class="col-md-9"><label class="form-label">Purpose / Notes</label><input class="form-control" name="notes" maxlength="2000" value="{{ old('notes') }}"></div>
<div class="col-md-3 align-self-end"><button class="btn btn-dark w-100">Save Receipt</button></div>
</form></details>
<h5>{{ $month->format('F Y') }} <small class="text-muted">· Payments through {{ $report['end'] }}</small></h5>
<div class="fm-cards">
@foreach(['opening'=>'Opening Balance','received'=>'Received This Month','available'=>'Total Available','spent'=>'Paid This Month','closing'=>'Closing / Remaining'] as $key=>$label)
<div><small>{{ $label }}</small><strong>Rs {{ $money($report[$key]) }}</strong></div>
@endforeach
</div>
<p class="fm-note">Opening + receipts − payments = closing. Closing carries into the next month. Future-date entries are excluded until their payment date.</p>
@if(count($warnings))
<div class="alert alert-warning"><strong>Reconciliation Needed</strong>
@foreach($warnings as $warning)
<p class="mb-1">{{ $warning }}</p>
@endforeach
</div>
@endif
<div class="fm-grid"><section><h5>Boss-wise Receipts</h5><table class="table">
@forelse($report['bosses'] as $name=>$amount)
<tr><td>{{ $name }}</td><th>Rs {{ $money($amount) }}</th></tr>
@empty
<tr><td>No boss receipts this month.</td></tr>
@endforelse
</table></section><section><h5>Where Funds Were Used</h5><table class="table">
@foreach($report['categories'] as $name=>$amount)
@if($amount)
<tr><td>{{ $name }}</td><th>Rs {{ $money($amount) }}</th></tr>
@endif
@endforeach
</table></section></div>
<section><h5>Monthly Ledger</h5><p class="fm-note">Source references identify the original record. Make corrections in the original salary, contractor, receipt or expense module.</p><div class="table-responsive"><table class="table"><thead><tr><th>Date</th><th>Type</th><th>Person / Purpose</th><th>Reference / User</th><th>Payment Method</th><th>Received</th><th>Paid</th><th>Balance</th></tr></thead><tbody>
@forelse($report['ledger'] as $row)
<tr><td>{{ $row['date'] }}</td><td>{{ $row['type'] }}</td><td>{{ $row['party'] }}<small class="d-block text-muted">{{ $row['description'] }}</small></td><td><small>{{ $row['reference'] }}</small><small class="d-block text-muted">{{ $row['actor'] }}</small></td><td>{{ $row['method'] }}</td><td>{{ $money($row['in']) }}</td><td>{{ $money($row['out']) }}</td><th>{{ $money($row['balance']) }}</th></tr>
@empty
<tr><td colspan="8">No payments or receipts this month.</td></tr>
@endforelse
</tbody><tfoot><tr><th colspan="5">Monthly totals</th><th>{{ $money($report['received']) }}</th><th>{{ $money($report['spent']) }}</th><th>{{ $money($report['closing']) }}</th></tr></tfoot></table></div></section>
@if(count($report['future']))
<section><h5>Future-date Entries · Excluded from Balance</h5>
@foreach($report['future'] as $row)
<p>{{ $row['date'] }} · {{ $row['party'] }} · {{ $row['type'] }} · Rs {{ $money($row['in']+$row['out']) }}</p>
@endforeach
</section>
@endif
<section><h5>Who Has Funds Access?</h5><p class="fm-note">Permissions follow Access Management. Users with no assigned permissions currently have full access under the application's existing rule.</p><div class="table-responsive"><table class="table"><thead><tr><th>User</th><th>Email</th><th>Role</th><th>Access Reason</th></tr></thead><tbody>@foreach($accessUsers as $u)<tr><td>{{ $u->name }}</td><td>{{ $u->email }}</td><td>{{ $u->role }}</td><td>{{ $u->role==='super_admin' ? 'Super admin' : ($u->permissions->isEmpty() ? 'Full access (no restrictions assigned)' : 'Funds permission assigned') }}</td></tr>@endforeach</tbody></table></div></section>
<section><h5>User Activity · {{ $month->format('F Y') }}</h5><p class="fm-note">New records and Eloquent changes are recorded from installation onward. Expand a row for the before / after values.</p>
@forelse($activity as $log)
<details class="fm-log"><summary>{{ $log->created_at }} · {{ $log->actor }} · {{ ucfirst($log->action) }} · {{ $log->source }} #{{ $log->source_id }}</summary><div class="fm-grid"><div><strong>Before</strong><pre>{{ json_encode(json_decode($log->before ?? 'null'),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre></div><div><strong>After</strong><pre>{{ json_encode(json_decode($log->after ?? 'null'),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre></div></div></details>
@empty<p>No recorded activity in this month.</p>
@endforelse
{{ $activity->appends(['month'=>$month->format('Y-m')])->links() }}
</section>
<section><h5>12-Month History</h5><div class="table-responsive"><table class="table"><thead><tr><th>Month</th><th>Opening</th><th>Received</th><th>Paid</th><th>Closing</th></tr></thead><tbody>
@foreach($history as $row)
<tr><td><a href="{{ route('funds-management.index',['month'=>$row['month']]) }}">{{ $row['label'] }}</a></td><td>{{ $money($row['opening']) }}</td><td>{{ $money($row['received']) }}</td><td>{{ $money($row['spent']) }}</td><th>{{ $money($row['closing']) }}</th></tr>
@endforeach
</tbody></table></div></section>
<p class="fm-note">This is a combined funds balance. Salary and contractor payments currently have no cash/bank account assignment. Manual duplicate entries across modules must be reconciled; matching descriptions do not establish that two records represent the same payment.</p>
</div>
<style>
.fm-receive{background:#fff;border:1px solid #dce8e4;border-radius:16px;padding:20px;margin-bottom:22px}.fm-receive summary{cursor:pointer;font-weight:700;color:#13766e}.fm-log{border-bottom:1px solid #e5ecef;padding:12px 0}.fm-log summary{cursor:pointer}.fm-log pre{font-size:11px;max-height:260px;overflow:auto;background:#f4f7f9;padding:12px;margin-top:10px}.fm{color:#193845}.fm-heading,.fm-tools{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:24px}.fm-heading small{color:#13766e;font-weight:700;letter-spacing:2px}.fm-heading h2{font-weight:750;margin:8px 0}.fm-heading p,.fm-note{color:#667d89;font-size:13px}.fm-tools{justify-content:flex-start;background:white;border:1px solid #e1e9ef;padding:16px;border-radius:14px}.fm-tools form{display:flex;gap:10px}.fm-tools input{border:1px solid #dbe4eb;border-radius:8px;padding:8px}.fm-cards{display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin:18px 0}.fm-cards>div,.fm section{background:white;border:1px solid #e0e9ef;border-radius:16px;padding:20px}.fm-cards small{display:block;color:#68828e}.fm-cards strong{display:block;font-size:21px;margin-top:12px}.fm-cards>div:last-child{background:#e5f4ef}.fm-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}.fm section{margin-bottom:18px}.fm h5{font-weight:700}.fm table{font-size:13px}.fm td,.fm th{padding:12px 8px}.fm thead{background:#f2f7fa}@media(max-width:1000px){.fm-cards{grid-template-columns:repeat(2,1fr)}.fm-grid{grid-template-columns:1fr}}@media print{nav,aside,.fm-receive,.sidebar,.mobile-header,.fm-tools,.fm-heading>a{display:none!important}.main-content{margin:0!important;padding:0!important}.fm section{break-inside:auto}tr{break-inside:avoid}.fm-cards strong{font-size:14px}@page{size:A4 landscape;margin:10mm}}
</style>
@endsection
