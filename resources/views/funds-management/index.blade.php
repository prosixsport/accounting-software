@extends('layouts.app')
@section('title','Funds Management')
@section('content')
@php $money=fn($value)=>\App\Services\FundsLedger::money($value); @endphp
<div class="funds-screen">
<header class="funds-head"><div><h3>Funds Management</h3><small>All records through {{ now()->format('d M Y') }}</small></div><div class="funds-actions"><button type="button" class="btn btn-outline-dark" data-dialog="fund-access" title="Who has access?" aria-label="View funds access"><i class="bi bi-eye"></i></button><button type="button" class="btn btn-dark" data-dialog="fund-receive">+ Receive Funds from Boss</button></div></header>
@if(session('success'))<div class="alert alert-success py-2">{{ session('success') }}</div>@endif
<div class="funds-stats">@foreach(['received'=>'Total Received','spent'=>'Total Used','closing'=>'Remaining Balance'] as $key=>$label)<div><small>{{ $label }}</small><strong>Rs {{ $money($report[$key]) }}</strong></div>@endforeach</div>
<div class="funds-tabs" role="tablist" aria-label="Funds details">
@foreach(['overview'=>'Overview','ledger'=>'Transactions','activity'=>'User Activity','review'=>'Review ('.count($warnings).')'] as $key=>$label)<button type="button" role="tab" id="tab-{{ $key }}" aria-controls="panel-{{ $key }}" aria-selected="{{ $key==='overview'?'true':'false' }}" data-panel="{{ $key }}">{{ $label }}</button>@endforeach
</div>
<div class="funds-body">
<section id="panel-overview" role="tabpanel" aria-labelledby="tab-overview"><div class="funds-grid"><article><h5>Received from Bosses</h5><table class="table">@foreach(['Boss Azeem','Boss Atif','Boss Kashif'] as $boss)<tr><td>{{ $boss }}</td><th>Rs {{ $money($report['bosses'][$boss]??0) }}</th></tr>@endforeach
@foreach($report['bosses'] as $boss=>$amount)
@if(!in_array($boss,['Boss Azeem','Boss Atif','Boss Kashif']))<tr><td>{{ $boss }} <small>(Historical)</small></td><th>Rs {{ $money($amount) }}</th></tr>@endif
@endforeach
</table></article><article><h5>Where Cash Was Used</h5><table class="table">@forelse($report['categories'] as $type=>$amount)<tr><td>{{ $type }}</td><th>Rs {{ $money($amount) }}</th></tr>@empty<tr><td>No payments recorded.</td></tr>@endforelse</table></article></div><p class="funds-note">Salary, contractor payments and expenses appear automatically after saving. Cash and bank are included in one combined balance.</p></section>
<section id="panel-ledger" role="tabpanel" aria-labelledby="tab-ledger" hidden><div class="table-responsive"><table class="table"><thead><tr><th>Date</th><th>Type</th><th>Person / Purpose</th><th>Reference / User</th><th>Payment Method</th><th>Received</th><th>Paid</th><th>Balance</th></tr></thead><tbody>
@forelse($report['ledger'] as $row)
<tr><td>{{ $row['date'] }}</td><td>{{ $row['type'] }}</td><td>{{ $row['party'] }}<small class="d-block text-muted">{{ $row['description'] }}</small></td><td><small>{{ $row['reference'] }}</small><small class="d-block text-muted">{{ $row['actor'] }}</small></td><td>{{ $row['method'] }}</td><td>{{ $money($row['in']) }}</td><td>{{ $money($row['out']) }}</td><th>{{ $money($row['balance']) }}</th></tr>
@empty
<tr><td colspan="8">No payments or receipts yet.</td></tr>
@endforelse
</tbody><tfoot><tr><th colspan="5">Totals</th><th>{{ $money($report['received']) }}</th><th>{{ $money($report['spent']) }}</th><th>{{ $money($report['closing']) }}</th></tr></tfoot></table></div></section>
<section id="panel-activity" role="tabpanel" aria-labelledby="tab-activity" hidden>@forelse($activity as $log)
<details class="fm-log"><summary>{{ $log->created_at }} · {{ $log->actor }} · {{ ucfirst($log->action) }} · {{ $log->source }} #{{ $log->source_id }}</summary><div class="fm-grid"><div><strong>Before</strong><pre>{{ json_encode(json_decode($log->before ?? 'null'),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre></div><div><strong>After</strong><pre>{{ json_encode(json_decode($log->after ?? 'null'),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre></div></div></details>
@empty<p>No recorded activity in this month.</p>
@endforelse
{{ $activity->links() }}
</section>
<section id="panel-review" role="tabpanel" aria-labelledby="tab-review" hidden><h5>Records to Review</h5>@forelse($warnings as $warning)<p class="alert alert-warning">{{ $warning }}</p>@empty<p>No possible duplicates detected.</p>@endforelse
@if(count($report['future']))<h6>Future entries · excluded from balance</h6>@foreach($report['future'] as $row)<p>{{ $row['date'] }} · {{ $row['party'] }} · Rs {{ $money($row['in']+$row['out']) }}</p>@endforeach@endif</section>
</div></div>
<dialog id="fund-receive" class="fund-dialog" aria-labelledby="receive-title"><div class="dialog-head"><h4 id="receive-title">Receive Funds from Boss</h4><button type="button" data-close aria-label="Close">×</button></div>
@if($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif
<form method="post" action="{{ route('funds-management.store') }}" class="row g-3 mt-2">
@csrf
<input type="hidden" name="submission_key" value="{{ old('submission_key',(string)\Illuminate\Support\Str::uuid()) }}">
<div class="col-md-3"><label class="form-label">Boss</label><select name="boss" class="form-select" required><option value="">Select boss</option>@foreach(['Boss Azeem','Boss Atif','Boss Kashif'] as $boss)<option value="{{ $boss }}" @selected(old('boss')===$boss)>{{ $boss }}</option>@endforeach</select></div>
<div class="col-md-3"><label class="form-label">Received Date</label><input class="form-control" type="date" name="receipt_date" value="{{ old('receipt_date',now()->toDateString()) }}" max="{{ now()->toDateString() }}" required></div>
<div class="col-md-3"><label class="form-label">Amount (Rs)</label><input class="form-control" type="number" name="amount" step="0.01" min="0.01" value="{{ old('amount') }}" required></div>
<div class="col-md-3"><label class="form-label">Received In</label><select class="form-select" name="method"><option value="cash" @selected(old('method')==='cash')>Cash</option><option value="bank" @selected(old('method')==='bank')>Bank</option></select></div>
<div class="col-md-9"><label class="form-label">Purpose / Notes</label><input class="form-control" name="notes" maxlength="2000" value="{{ old('notes') }}"></div>
<div class="col-md-3 align-self-end"><button class="btn btn-dark w-100">Save Receipt</button></div>
</form>
</dialog>
<dialog id="fund-access" class="fund-dialog" aria-label="Funds access"><div class="dialog-head"><h4>Funds Access</h4><button type="button" data-close aria-label="Close">×</button></div><h5>Who Has Funds Access?</h5><p class="fm-note">Permissions follow Access Management. Users with no assigned permissions currently have full access under the application's existing rule.</p><div class="table-responsive"><table class="table"><thead><tr><th>User</th><th>Email</th><th>Role</th><th>Access Reason</th></tr></thead><tbody>@foreach($accessUsers as $u)<tr><td>{{ $u->name }}</td><td>{{ $u->email }}</td><td>{{ $u->role }}</td><td>{{ $u->role==='super_admin' ? 'Super admin' : ($u->permissions->isEmpty() ? 'Full access (no restrictions assigned)' : 'Funds permission assigned') }}</td></tr>@endforeach</tbody></table></div></dialog>
<style>
.funds-screen{height:calc(100dvh - 110px);min-height:360px;display:flex;flex-direction:column;gap:16px;color:#183e43;overflow:hidden}.funds-head,.funds-actions,.dialog-head{display:flex;justify-content:space-between;align-items:center;gap:12px}.funds-head h3{font-weight:750;margin:0 0 4px}.funds-head small,.funds-note{color:#71868b}.funds-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}.funds-stats>div{background:white;padding:18px 22px;border:1px solid #e2ece9;border-radius:16px}.funds-stats>div:last-child{background:#e1f3eb}.funds-stats small,.funds-stats strong{display:block}.funds-stats strong{font-size:clamp(17px,2vw,26px);margin-top:8px}.funds-tabs{display:flex;gap:8px;flex-wrap:wrap}.funds-tabs button{border:0;border-radius:9px;padding:9px 16px;background:#edf3f2;color:#486368}.funds-tabs button[aria-selected=true]{background:#176b5e;color:white}.funds-body{background:white;border:1px solid #e0e9e6;border-radius:16px;padding:20px;flex:1;min-height:0;overflow:auto}.funds-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px}.funds-grid h5{font-size:16px;font-weight:700}.funds-screen table{font-size:13px}.funds-screen th,.funds-screen td{padding:10px}.funds-screen thead{position:sticky;top:0;background:#f1f7f4}.funds-note{font-size:12px;margin-bottom:0}.fund-dialog{width:min(760px,94vw);max-height:88dvh;overflow:auto;border:0;border-radius:20px;padding:26px;box-shadow:0 22px 90px #102e3c40}.fund-dialog::backdrop{background:#122b3f80}.dialog-head{margin-bottom:18px}.dialog-head h4{font-size:20px;font-weight:700;margin:0}.dialog-head button{border:0;background:#edf3f2;border-radius:50%;width:34px;height:34px;font-size:24px}.fm-log{padding:10px;border-bottom:1px solid #eee}.fm-log summary{cursor:pointer}.fm-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}.fm-log pre{font-size:11px;max-height:200px;overflow:auto;background:#f4f7f8;padding:10px}[hidden]{display:none!important}@media(max-width:700px){.funds-screen{height:calc(100dvh - 100px);gap:10px}.funds-head{align-items:flex-start}.funds-actions{gap:5px}.funds-actions .btn{font-size:12px;padding:8px}.funds-head h3{font-size:19px}.funds-stats{gap:6px}.funds-stats>div{padding:10px}.funds-stats small{font-size:11px}.funds-body{padding:12px}.funds-grid,.fm-grid{grid-template-columns:1fr}.funds-tabs button{font-size:12px;padding:7px 10px}}
</style>
<script>
(()=>{
const screen=document.querySelector('.funds-screen');
screen.querySelectorAll('[data-panel]').forEach(button=>button.addEventListener('click',()=>{
 screen.querySelectorAll('[data-panel]').forEach(b=>b.setAttribute('aria-selected',String(b===button)));
 screen.querySelectorAll('[role=tabpanel]').forEach(panel=>panel.hidden=panel.id!=='panel-'+button.dataset.panel);
}));
document.querySelectorAll('[data-dialog]').forEach(button=>button.addEventListener('click',()=>document.getElementById(button.dataset.dialog).showModal()));
document.querySelectorAll('[data-close]').forEach(button=>button.addEventListener('click',()=>button.closest('dialog').close()));
const activityPage=new URL(location.href).searchParams.has('page');
if(activityPage) document.getElementById('tab-activity').click();
@if($errors->any())
document.getElementById('fund-receive').showModal();
@endif
})();
</script>
@endsection
