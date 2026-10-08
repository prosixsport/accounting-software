
@extends('layouts.app')

@section('title','Funds Management')

@section('content')

@php
 $money=fn($value)=>\App\Services\FundsLedger::money($value); 
@endphp

<div class="funds-screen">
<header class="funds-head"><div><h3>Funds Management</h3><small>{{ $month->format('F Y') }} · Cash flow through {{ $report['end'] }}</small></div><div class="funds-actions"><form method="get" action="{{ route('funds-management.index') }}" class="fund-month"><input aria-label="Report month" type="month" name="month" value="{{ $month->format('Y-m') }}" max="{{ now('Asia/Karachi')->format('Y-m') }}" required><button class="btn btn-outline-dark">Load</button></form><button type="button" class="btn btn-outline-dark" data-dialog="fund-return">Return Cash to Boss</button><button type="button" class="btn btn-outline-dark" data-dialog="fund-access" title="Who has access?" aria-label="View funds access"><i class="bi bi-eye"></i></button><button type="button" class="btn btn-dark" data-dialog="fund-receive">+ Receive Funds from Boss</button></div></header>

@if(session('success'))
<div class="alert alert-success py-2">{{ session('success') }}</div>

@endif


<div class="funds-stats">
@foreach(['opening'=>'Opening Balance','received'=>'Funds Received','spent'=>'Salary + Bills Paid','returned'=>'Returned to Bosses','closing'=>'Remaining Balance'] as $key=>$label)
<div><small>{{ $label }}</small><strong>Rs {{ $money($report[$key]) }}</strong></div>

@endforeach

</div>
<div class="funds-tabs" role="tablist" aria-label="Funds details">

@foreach(['overview'=>'Overview','ledger'=>'Transactions','returns'=>'Cash Returns','activity'=>'User Activity','review'=>'Review ('.count($warnings).')'] as $key=>$label)
<button type="button" role="tab" id="tab-{{ $key }}" aria-controls="panel-{{ $key }}" aria-selected="{{ $key==='overview'?'true':'false' }}" data-panel="{{ $key }}">{{ $label }}</button>

@endforeach


</div>
<div class="funds-body">
<section id="panel-overview" role="tabpanel" aria-labelledby="tab-overview"><div class="fw-section-title"><div><h5>Weekly Cash Flow</h5><small>Each week's remaining balance carries into the next week.</small></div><span>{{ $month->format('F Y') }}</span></div>
<div class="fw-weeks">
@foreach($report['weeks'] as $week)
<article class="fw-week"><header><div><strong>Week {{ $week['number'] }}</strong><small>{{ \Illuminate\Support\Carbon::parse($week['start'])->format('d M') }} – {{ \Illuminate\Support\Carbon::parse($week['end'])->format('d M') }}</small></div><button type="button" data-dialog="fund-week-{{ $week['number'] }}">Info ↗</button></header>
@if($week['future'])
<p class="funds-note">Upcoming week · no actual transactions yet</p>
@endif
@foreach(['opening'=>'Carry Forward','received'=>'New Funds','available'=>'Total Available','spent'=>'Salary + Bills','returned'=>'Cash Returned','closing'=>'Remaining'] as $key=>$label)
<div class="fw-line fw-{{ $key }}"><span>{{ $label }}</span><strong>Rs {{ $money($week[$key]) }}</strong></div>
@endforeach
</article>
@endforeach
</div>
<div class="fw-section-title"><div><h5>Funds Breakdown</h5><small>Payments below are included in total spent, counted once.</small></div></div>
<div class="funds-grid"><article><h5>Received from Bosses</h5><table class="table">
@foreach(['Boss Azeem','Boss Atif','Boss Kashif'] as $boss)
<tr><td>{{ $boss }}</td><th>Rs {{ $money($report['bosses'][$boss]??0) }}</th></tr>

@endforeach


@foreach($report['bosses'] as $boss=>$amount)

@if(!in_array($boss,['Boss Azeem','Boss Atif','Boss Kashif']))
<tr><td>{{ $boss }} <small>(Historical)</small></td><th>Rs {{ $money($amount) }}</th></tr>

@endif



@endforeach


</table></article><article><h5>Where Cash Was Used</h5><table class="table">
@forelse($report['categories'] as $type=>$amount)
<tr><td>{{ $type }}</td><th>Rs {{ $money($amount) }}</th></tr>

@empty

<tr><td>No payments recorded.</td></tr>

@endforelse

</table></article></div><details class="receipt-details"><summary>Who received funds from each boss?</summary><table class="table"><thead><tr><th>Date</th><th>Boss</th><th>Received By</th><th>Photo</th><th>Amount</th></tr></thead><tbody>
@foreach(array_reverse($report['ledger']) as $receiptRow)
@if($receiptRow['type']==='Boss funds')
<tr><td>{{ $receiptRow['date'] }}</td><td>{{ $receiptRow['party'] }}</td><td>{{ $receiptRow['receiver'] ?? 'Historical — receiver not recorded' }}<small class="d-block text-muted">Entry by {{ $receiptRow['actor'] }}</small></td><td>
@if($receiptRow['photo'])
<a target="_blank" rel="noopener" href="{{ asset('storage/'.$receiptRow['photo']) }}"><img class="receiver-photo" src="{{ asset('storage/'.$receiptRow['photo']) }}" alt="Receiver photo"></a>
@else
—

@endif

</td><th>Rs {{ $money($receiptRow['in']) }}</th></tr>

@endif


@endforeach

</tbody></table></details><p class="funds-note">Salary, contractor payments and expenses appear automatically after saving. Cash and bank are included in one combined balance.</p></section>
<section id="panel-ledger" role="tabpanel" aria-labelledby="tab-ledger" hidden><div class="table-responsive"><table class="table"><thead><tr><th>Date</th><th>Type</th><th>Person / Purpose</th><th>Reference / User</th><th>Payment Method</th><th>Received</th><th>Paid</th><th>Balance</th><th>Action</th></tr></thead><tbody>

@forelse($report['ledger'] as $row)

<tr><td>{{ $row['date'] }}</td><td>{{ $row['type'] }}</td><td>{{ $row['party'] }}
@if($row['receiver'])
<small class="d-block">Received by: {{ $row['receiver'] }}</small>

@endif

@if($row['photo'])
<a href="{{ asset('storage/'.$row['photo']) }}" target="_blank" rel="noopener"><img class="receiver-photo" src="{{ asset('storage/'.$row['photo']) }}" alt="Receiver photo"></a>

@endif

<small class="d-block text-muted">{{ $row['description'] }}</small></td><td><small>{{ $row['reference'] }}</small><small class="d-block text-muted">{{ $row['actor'] }}</small></td><td>{{ $row['method'] }}</td><td>{{ $money($row['in']) }}</td><td>{{ $money($row['out']) }}</td><th>{{ $money($row['balance']) }}</th><td>
@if($row['edit_url'])
<a class="btn btn-sm btn-outline-dark" href="{{ $row['edit_url'] }}">Edit</a>
@else
<small>Historical</small>

@endif

</td></tr>


@empty


<tr><td colspan="9">No payments or receipts yet.</td></tr>


@endforelse


</tbody><tfoot><tr><th colspan="5">Totals</th><th>{{ $money($report['received']) }}</th><th>{{ $money($report['spent']+$report['returned']) }}</th><th>{{ $money($report['closing']) }}</th></tr></tfoot></table></div></section>
<section id="panel-returns" role="tabpanel" aria-labelledby="tab-returns" hidden><h5>Returned Cash Register</h5><p class="funds-note">Actual cash handed back to a boss. This reduces the remaining balance.</p><table class="table"><thead><tr><th>Date</th><th>Returned To</th><th>Amount</th><th>Entry By</th><th>Remarks</th></tr></thead><tbody>
@forelse(array_filter($report['ledger'],fn($e)=>$e['type']==='Cash returned to boss') as $entry)
<tr><td>{{ $entry['date'] }}</td><td>{{ $entry['party'] }}</td><th>Rs {{ $money($entry['out']) }}</th><td>{{ $entry['actor'] }}</td><td>{{ $entry['description'] }}</td></tr>
@empty
<tr><td colspan="5">No cash returns in this month.</td></tr>
@endforelse
</tbody></table></section>
<section id="panel-activity" role="tabpanel" aria-labelledby="tab-activity" hidden>
@forelse($activity as $log)

@php
$decode=function($value){ if(is_string($value)) $value=json_decode($value,true); if(is_string($value)) $value=json_decode($value,true); return is_array($value)?$value:[]; };
$before=$decode($log->before); $after=$decode($log->after);
$fields=array_unique(array_merge(array_keys($before),array_keys($after)));
$normalizeActivity=function($v){ $text=(string)$v; if(preg_match('/^-?\d+(?:\.\d+)?$/D',$text) && str_contains($text,'.')) return rtrim(rtrim($text,'0'),'.'); return $text; };
$changed=array_filter($fields,fn($key)=>!in_array($key,['created_at','updated_at','submission_key']) && ($log->action!=='updated' || $normalizeActivity($before[$key]??'')!==$normalizeActivity($after[$key]??'')));
@endphp
<details class="fm-log"><summary><strong>{{ $log->actor }}</strong> · {{ ucfirst($log->action) }} · {{ ucwords(str_replace('_',' ',$log->source)) }} #{{ $log->source_id }}<small class="d-block text-muted">{{ $log->created_at }}</small></summary><table class="table table-sm"><thead><tr><th>Detail</th><th>Before</th><th>After</th></tr></thead><tbody>
@forelse($changed as $field)
<tr><th>{{ ucwords(str_replace('_',' ',$field)) }}</th>
@foreach([$before[$field]??null,$after[$field]??null] as $value)
<td>
@if($field==='receiver_photo' && $value)
<a href="{{ asset('storage/'.$value) }}" target="_blank" rel="noopener">View photo</a>
@else
{{ is_array($value) ? json_encode($value) : ($value ?? '—') }}

@endif

</td>

@endforeach

</tr>

@empty

<tr><td colspan="3">No financial values changed (historical automatic save).</td></tr>

@endforelse

</tbody></table></details>


@empty

<p>No recorded activity yet.</p>


@endforelse


{{ $activity->links() }}
</section>
<section id="panel-review" role="tabpanel" aria-labelledby="tab-review" hidden><h5>Records to Review</h5><p class="funds-note">Same date, person and amount may indicate duplicate entry. Check source references before correcting. Both entries stay included; this warning never deletes or deducts money by itself.</p>
@forelse($warnings as $warning)
<p class="alert alert-warning">{{ $warning }}</p>

@empty

<p>No possible duplicates detected.</p>

@endforelse


@if(count($report['future']))
<h6>Future entries · excluded from balance</h6>
@foreach($report['future'] as $row)
<p>{{ $row['date'] }} · {{ $row['party'] }} · Rs {{ $money($row['in']+$row['out']) }}</p>

@endforeach



@endif

</section>
</div></div>
<dialog id="fund-return" class="fund-dialog"><div class="dialog-head"><h4>Return Cash to Boss</h4><button type="button" data-close aria-label="Close">×</button></div>
@if(old('return_date') && $errors->any())
<div class="alert alert-danger">{{ $errors->first() }}</div>
@endif
<form method="post" action="{{ route('funds-management.returns.store') }}" class="row g-3">@csrf
<input type="hidden" name="submission_key" value="{{ old('return_date') ? old('submission_key') : (string)\Illuminate\Support\Str::uuid() }}">
<div class="col-md-6"><label class="form-label">Return Date</label><input class="form-control" type="date" name="return_date" value="{{ old('return_date',now('Asia/Karachi')->toDateString()) }}" max="{{ now('Asia/Karachi')->toDateString() }}" required></div>
<div class="col-md-6"><label class="form-label">Returned To</label><select name="boss" class="form-select" required>
@foreach(['Boss Azeem','Boss Atif','Boss Kashif'] as $boss)
<option @selected(old('boss')===$boss)>{{ $boss }}</option>
@endforeach
</select></div><div class="col-12"><label class="form-label">Amount Actually Returned</label><input class="form-control" type="number" name="amount" min="0.01" step="0.01" max="9999999999999.99" value="{{ old('return_date') ? old('amount') : '' }}" required></div><div class="col-12"><label class="form-label">Remarks</label><textarea name="notes" class="form-control" maxlength="2000">{{ old('return_date') ? old('notes') : '' }}</textarea></div><div class="col-12"><button class="btn btn-dark">Save Cash Return</button></div></form></dialog>
@foreach($report['weeks'] as $week)
<dialog id="fund-week-{{ $week['number'] }}" class="fund-dialog fw-detail"><div class="dialog-head"><h4>Week {{ $week['number'] }} · Transaction Details</h4><button type="button" data-close aria-label="Close">×</button></div><p>{{ $week['start'] }} to {{ $week['end'] }}</p><div class="table-responsive"><table class="table"><thead><tr><th>Date</th><th>Type</th><th>Person / Boss</th><th>Received</th><th>Paid / Returned</th><th>Recorded By</th></tr></thead><tbody>
@forelse($week['ledger'] as $entry)
<tr><td>{{ $entry['date'] }}</td><td>{{ $entry['type'] }}</td><td>{{ $entry['party'] }}<small class="d-block">{{ $entry['description'] }}</small></td><td>Rs {{ $money($entry['in']) }}</td><td>Rs {{ $money($entry['out']) }}</td><td>{{ $entry['actor'] }}</td></tr>
@empty
<tr><td colspan="6">No actual transactions recorded in this week.</td></tr>
@endforelse
</tbody></table></div><strong>Remaining: Rs {{ $money($week['closing']) }}</strong></dialog>
@endforeach
<dialog id="fund-receive" class="fund-dialog" aria-labelledby="receive-title"><div class="dialog-head"><h4 id="receive-title">{{ $editReceipt ? 'Edit Funds Receipt' : 'Receive Funds from Boss' }}</h4><button type="button" data-close aria-label="Close">×</button></div>

@if($errors->any())
<div class="alert alert-danger">
@foreach($errors->all() as $error)
<div>{{ $error }}</div>

@endforeach

</div>

@endif


<form method="post" enctype="multipart/form-data" action="{{ $editReceipt ? route('funds-management.update',$editReceipt) : route('funds-management.store') }}" class="row g-3 mt-2">
@csrf
@if($editReceipt)
@method('PUT')
<input type="hidden" name="version" value="{{ old('version',hash('sha256',json_encode($editReceipt->getAttributes()))) }}">

@endif

<input type="hidden" name="submission_key" value="{{ old('submission_key',(string)\Illuminate\Support\Str::uuid()) }}">
<div class="col-md-3"><label class="form-label">Boss</label><select name="boss" class="form-select" required><option value="">Select boss</option>
@foreach(['Boss Azeem','Boss Atif','Boss Kashif'] as $boss)
<option value="{{ $boss }}" @selected(old('boss',$editReceipt?->boss)===$boss)>{{ $boss }}</option>

@endforeach

</select></div>
<div class="col-md-3"><label class="form-label">Received Date</label><input class="form-control" type="date" name="receipt_date" value="{{ old('receipt_date',$editReceipt?->receipt_date?->toDateString() ?? now()->toDateString()) }}" max="{{ now()->toDateString() }}" required></div>
<div class="col-md-3"><label class="form-label">Amount (Rs)</label><input class="form-control" type="number" name="amount" step="0.01" min="0.01" value="{{ old('amount',$editReceipt?->amount) }}" required></div>
<div class="col-md-3"><label class="form-label">Received In</label><select class="form-select" name="method"><option value="cash" @selected(old('method',$editReceipt?->method)==='cash')>Cash</option><option value="bank" @selected(old('method',$editReceipt?->method)==='bank')>Bank</option></select></div>
<div class="col-md-6"><label class="form-label">Cash Received By</label><input class="form-control" name="receiver_name" maxlength="150" value="{{ old('receiver_name',$editReceipt?->receiver_name ?? auth()->user()->name) }}" required><small>The person who physically received the cash.</small></div>
<div class="col-md-6"><label class="form-label">Receiver Photo</label><input id="receiver-photo-input" class="form-control" type="file" name="receiver_photo" accept="image/jpeg,image/png,image/webp" capture="user" @required(!$editReceipt)><small>Take a photo on mobile, or choose a photo. JPG/PNG/WebP · max 5 MB.</small><img id="receiver-photo-preview" class="receiver-preview" alt="Photo preview" @if($editReceipt?->receiver_photo) src="{{ asset('storage/'.$editReceipt->receiver_photo) }}" @else hidden 
@endif
></div>
<div class="col-md-9"><label class="form-label">Purpose / Notes</label><input class="form-control" name="notes" maxlength="2000" value="{{ old('notes',$editReceipt?->notes) }}"></div>
<div class="col-md-3 align-self-end"><button class="btn btn-dark w-100">{{ $editReceipt ? 'Save Changes' : 'Save Receipt' }}</button></div>
</form>
</dialog>
<dialog id="fund-access" class="fund-dialog" aria-label="Funds access"><div class="dialog-head"><h4>Funds Access</h4><button type="button" data-close aria-label="Close">×</button></div><h5>Who Has Funds Access?</h5><p class="fm-note">Permissions follow Access Management. Users with no assigned permissions currently have full access under the application's existing rule.</p><div class="table-responsive"><table class="table"><thead><tr><th>User</th><th>Email</th><th>Role</th><th>Access Reason</th></tr></thead><tbody>
@foreach($accessUsers as $u)
<tr><td>{{ $u->name }}</td><td>{{ $u->email }}</td><td>{{ $u->role }}</td><td>{{ $u->role==='super_admin' ? 'Super admin' : ($u->permissions->isEmpty() ? 'Full access (no restrictions assigned)' : 'Funds permission assigned') }}</td></tr>

@endforeach

</tbody></table></div></dialog>
<style>
.receiver-photo{width:46px;height:46px;object-fit:cover;border-radius:9px}.receiver-preview{display:block;width:90px;height:100px;object-fit:cover;margin-top:10px;border-radius:10px}.receipt-details{margin:14px 0}.receipt-details summary{cursor:pointer;font-weight:600}.funds-screen{height:calc(100dvh - 110px);min-height:360px;display:flex;flex-direction:column;gap:16px;color:#183e43;overflow:hidden}.funds-head,.funds-actions,.dialog-head{display:flex;justify-content:space-between;align-items:center;gap:12px}.funds-head h3{font-weight:750;margin:0 0 4px}.funds-head small,.funds-note{color:#71868b}.funds-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}.funds-stats>div{background:white;padding:18px 22px;border:1px solid #e2ece9;border-radius:16px}.funds-stats>div:last-child{background:#e1f3eb}.funds-stats small,.funds-stats strong{display:block}.funds-stats strong{font-size:clamp(17px,2vw,26px);margin-top:8px}.funds-tabs{display:flex;gap:8px;flex-wrap:wrap}.funds-tabs button{border:0;border-radius:9px;padding:9px 16px;background:#edf3f2;color:#486368}.funds-tabs button[aria-selected=true]{background:#176b5e;color:white}.funds-body{background:white;border:1px solid #e0e9e6;border-radius:16px;padding:20px;flex:1;min-height:0;overflow:auto}.funds-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px}.funds-grid h5{font-size:16px;font-weight:700}.funds-screen table{font-size:13px}.funds-screen th,.funds-screen td{padding:10px}.funds-screen thead{position:sticky;top:0;background:#f1f7f4}.funds-note{font-size:12px;margin-bottom:0}.fund-dialog{width:min(760px,94vw);max-height:88dvh;overflow:auto;border:0;border-radius:20px;padding:26px;box-shadow:0 22px 90px #102e3c40}.fund-dialog::backdrop{background:#122b3f80}.dialog-head{margin-bottom:18px}.dialog-head h4{font-size:20px;font-weight:700;margin:0}.dialog-head button{border:0;background:#edf3f2;border-radius:50%;width:34px;height:34px;font-size:24px}.fm-log{padding:10px;border-bottom:1px solid #eee}.fm-log summary{cursor:pointer}.fm-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}.fm-log pre{font-size:11px;max-height:200px;overflow:auto;background:#f4f7f8;padding:10px}[hidden]{display:none!important}@media(max-width:700px){.funds-screen{height:calc(100dvh - 100px);gap:10px}.funds-head{align-items:flex-start}.funds-actions{gap:5px}.funds-actions .btn{font-size:12px;padding:8px}.funds-head h3{font-size:19px}.funds-stats{gap:6px}.funds-stats>div{padding:10px}.funds-stats small{font-size:11px}.funds-body{padding:12px}.funds-grid,.fm-grid{grid-template-columns:1fr}.funds-tabs button{font-size:12px;padding:7px 10px}}
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
let previewUrl;
document.getElementById('receiver-photo-input').addEventListener('change',function(){
 if(previewUrl) URL.revokeObjectURL(previewUrl);
 const preview=document.getElementById('receiver-photo-preview');
 if(!this.files[0]) { preview.hidden=true; return; }
 previewUrl=URL.createObjectURL(this.files[0]); preview.src=previewUrl; preview.hidden=false;
});
const activityPage=new URL(location.href).searchParams.has('page');
if(activityPage) document.getElementById('tab-activity').click();

@if(old('return_date') && $errors->any())
document.getElementById('fund-return').showModal();
@elseif($errors->any() || $editReceipt)

document.getElementById('fund-receive').showModal();


@endif


})();
</script>


<style>
.funds-screen{color:#203346;gap:14px}.funds-head{background:#fff;border:1px solid #e0e7ef;border-radius:12px;padding:18px 20px}.funds-head h3{font-size:23px;letter-spacing:-.5px}.funds-actions{flex-wrap:wrap;justify-content:flex-end}.funds-actions .btn{font-size:12px;border-radius:8px;padding:9px 12px}.fund-month{display:flex;gap:6px}.fund-month input{border:1px solid #dce5ee;border-radius:8px;padding:8px;font-size:12px}.funds-stats{grid-template-columns:repeat(5,minmax(0,1fr));gap:12px}.funds-stats>div{border-color:#e0e7ef;border-radius:12px;padding:15px;background:#fff}.funds-stats>div:last-child{background:#eaf8f2;border-color:#cfe9df}.funds-stats>div:nth-child(3) strong{color:#c84d51}.funds-stats>div:nth-child(4) strong{color:#3266cb}.funds-stats strong{font-size:clamp(16px,1.6vw,23px);font-variant-numeric:tabular-nums}.funds-tabs{border-bottom:1px solid #e1e8ee;padding-bottom:8px}.funds-tabs button{background:#eef3f8;border-radius:7px}.funds-tabs button[aria-selected=true]{background:#203c54}.funds-body{border-radius:12px;border-color:#e0e7ef;padding:18px}.fw-section-title{display:flex;justify-content:space-between;align-items:center;margin:0 0 14px;gap:12px}.fw-section-title h5{font-size:16px;font-weight:750;margin:0 0 4px}.fw-section-title small,.fw-section-title>span{font-size:11px;color:#7c8a98}.fw-weeks{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:12px;margin-bottom:22px}.fw-week{border:1px solid #e0e7ef;border-radius:10px;background:#fbfdff;padding:13px}.fw-week header{display:flex;justify-content:space-between;align-items:center;gap:6px;border-bottom:1px solid #e4eaf0;padding-bottom:10px;margin-bottom:9px}.fw-week header strong{font-size:13px;color:#2f6398}.fw-week header small{display:block;font-size:10px;color:#7b8a9a;margin-top:3px}.fw-week button{font-size:10px;border:1px solid #dbe7f3;background:#edf5ff;color:#35689c;border-radius:6px;padding:4px 6px}.fw-line{display:flex;justify-content:space-between;gap:5px;font-size:11px;margin:10px 0}.fw-line strong{white-space:nowrap;font-size:11px;font-variant-numeric:tabular-nums}.fw-available,.fw-closing{border-top:1px solid #e1e8ed;padding-top:10px}.fw-spent strong{color:#c35155}.fw-returned strong{color:#356ac4}.fw-closing strong{color:#16816a}.funds-grid article{border:1px solid #e4eaf0;border-radius:10px;padding:14px}.funds-grid{gap:14px}.funds-screen .table{margin-bottom:0}.fw-detail{width:min(1100px,95vw)}.fund-dialog{border-radius:12px}.funds-screen .table th{font-weight:650}.funds-screen .table td,.funds-screen .table th{font-size:12px;padding:9px;border-color:#edf1f5}
@media(max-width:1200px){.fw-weeks{grid-template-columns:repeat(3,minmax(0,1fr))}.funds-head{flex-wrap:wrap}.funds-stats strong{font-size:17px}}
@media(max-width:700px){.funds-stats{grid-template-columns:repeat(2,minmax(0,1fr))}.fw-weeks{grid-template-columns:1fr}.funds-screen{height:auto;overflow:visible}.funds-body{overflow:visible}.funds-head{padding:14px}.funds-actions{justify-content:flex-start}}
</style>

@endsection

