<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>{{ $month->format('F Y') }} Salary</title><style>
body{font-family:Arial,sans-serif;color:#111;margin:24px}h1{font-size:21px;text-align:center}h2{font-size:18px}table{border-collapse:collapse;width:100%;font-size:8px;table-layout:fixed}th,td{border:1px solid #555;padding:6px 3px;overflow-wrap:anywhere}th{background:#eee}td.money{text-align:right;white-space:normal}.sign{width:58px;height:34px}.toolbar{margin-bottom:20px}.scroll{overflow:auto}.slip{max-width:800px;margin:auto;break-after:page;padding-top:8px}.slip:last-of-type{break-after:auto}.slip table{font-size:14px}.slip th{text-align:left;width:55%}.slip table{table-layout:auto}.sheet-page{break-after:page;margin-bottom:30px}.sheet-page:last-of-type{break-after:auto}.sheet-name{width:100px}.sheet-department{width:60px}.sm-avatar{display:inline-flex;position:relative;width:60px;height:60px;align-items:center;justify-content:center;background:#eef2f5;border-radius:12px;overflow:hidden;font-weight:bold;font-size:25px}.sm-avatar img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}.slip-person{display:flex;align-items:center;gap:15px;margin-bottom:22px}.slip-person h2{margin:0}.slip-person p{margin:7px 0 0}.signatures{display:flex;justify-content:space-between;margin-top:70px}button{padding:10px 20px;cursor:pointer}.note{white-space:pre-wrap}@page{size:A4 landscape;margin:10mm}@media print{body{margin:0}.toolbar{display:none}.scroll{overflow:visible}thead{display:table-header-group}tr{break-inside:avoid}th{-webkit-print-color-adjust:exact;print-color-adjust:exact}}
@if($single) @page{size:A4 portrait;margin:16mm}
@endif


.salary-slip{box-sizing:border-box;background:white;max-width:850px;margin:0 auto 24px;padding:24px;border:2px solid #111;box-shadow:0 4px 16px #0001;break-after:page}
.salary-slip:last-child{break-after:auto}.slip-top{display:flex;justify-content:space-between;align-items:flex-start}.slip-top h2{font-size:24px;margin:0}.slip-top p{margin:8px 0}.slip-photo{position:relative;width:100px;height:120px;border:2px solid #111;display:flex;align-items:center;justify-content:center;background:#eee;font-size:40px;flex-shrink:0}.slip-photo img{position:absolute;inset:4px;width:calc(100% - 8px);height:calc(100% - 8px);object-fit:cover}.employee-info{display:grid;grid-template-columns:1fr 1fr;gap:20px;font-size:12px}.employee-info p{margin:7px 0}.salary-slip table{font-size:12px;table-layout:auto}.salary-slip th{text-align:left}.salary-table th{width:62%}.salary-slip td,.salary-slip th{padding:5px 8px}.salary-slip h3{font-size:14px;margin:14px 0 8px}.final-row th,.final-row td{background:#d1e7dd;font-weight:bold}.slip-signatures{display:flex;justify-content:space-between;gap:40px;margin-top:40px}.slip-signatures span{border-top:1px solid #111;padding-top:8px;text-align:center;width:45%;font-size:12px}.page-label{text-align:center;font-size:10px}.sheet-page h1{margin:0 0 6px}.sheet-page table{font-size:8px}.sheet-page tbody td{height:6mm;box-sizing:border-box;padding:2px 3px}.sheet-page .signatures{margin-top:20px;font-size:11px}.note{font-size:11px}.sheet-page{margin-bottom:20px}.salary-slip,.final-row td{print-color-adjust:exact;-webkit-print-color-adjust:exact}
@media print{.salary-slip{max-width:none;box-shadow:none;margin:0;padding:7mm}.sheet-page{margin:0}.sheet-page h1{font-size:17px}.sheet-page tbody td{height:6mm}.salary-slip th{print-color-adjust:exact}.print-note{display:none}}

/* V7: full-width salary sheet, 20 employees per page. */
@page{size:A4 landscape;margin:5mm}
@if($single) @page{size:A4 portrait;margin:10mm}
@endif

.sheet-page{width:287mm;max-width:100%;margin:0 auto 24px;background:white;padding:0;box-sizing:border-box}
.sheet-page h1{font-size:21px;margin:0 0 4mm;line-height:1.2}
.sheet-page table{width:100%;height:164mm;font-size:9px;line-height:1.15;border:1.2px solid black}
.sheet-page th,.sheet-page td{border:1px solid #333;padding:3px;overflow-wrap:anywhere}
.sheet-page thead th{height:10mm;background:#eee;font-weight:700;text-align:center}
.sheet-page tbody td{height:auto;vertical-align:middle}
.sheet-page tfoot th{height:8mm;background:#eee}
.sheet-name{width:28mm}.sheet-department{width:17mm}.sheet-page .sign{width:18mm}
.sheet-page .signatures{margin-top:7mm;font-size:12px;padding:0 3mm}
@media print{
 html,body{padding:0!important;margin:0!important;background:white!important}
 .toolbar,.print-note,.page-label,nav,aside,iframe,[class*="buy-now"],[id*="buy-now"]{display:none!important}
 .sheet-page{width:100%;max-width:none;margin:0;break-after:page}
 .sheet-page:last-of-type{break-after:auto}
 .sheet-page h1{font-size:21px}
 .sheet-page table{height:164mm;font-size:9px}
 .sheet-page tbody td{height:auto}
 .sheet-page .scroll{overflow:visible}
 .salary-slip{break-after:page}.salary-slip:last-of-type{break-after:auto}
}

/* Two individual slips per A4 portrait sheet. */
@if($single)
@page{size:A4 portrait;margin:6mm}
.slip-pair{width:198mm;max-width:100%;margin:0 auto 20px;display:grid;grid-template-rows:140mm 140mm;gap:3mm;break-after:page}
.slip-pair:last-of-type{break-after:auto}
.salary-slip{width:100%;height:140mm;box-sizing:border-box;padding:5mm;margin:0;break-after:auto;box-shadow:none;border:1px solid black;overflow:visible}
.slip-top h2{font-size:17px}.slip-top p{font-size:10px;margin:3px 0}.slip-photo{width:18mm;height:22mm;font-size:20px}
.salary-slip hr{margin:3px 0}.employee-info{font-size:9px;gap:4mm}.employee-info p{margin:2px 0}
.salary-slip table{font-size:9px}.salary-slip td,.salary-slip th{padding:2px 5px;line-height:1.15}.salary-slip h3{font-size:10px;margin:5px 0 3px}.slip-signatures{margin-top:7mm;gap:10mm}.slip-signatures span{font-size:9px;padding-top:3px}
@media print{.slip-pair{width:100%;margin:0;break-after:page}.slip-pair:last-of-type{break-after:auto}.salary-slip{padding:5mm;break-after:auto!important}.slip-pair .salary-slip:last-of-type{break-after:auto!important}}

@endif

</style></head><body><div class="toolbar"><button onclick="window.print()">Print / Save PDF</button> <a href="{{ route('salary-management.index',['month'=>$month->format('Y-m')]) }}">Back to Salary Management</a></div>


@if($single)
@foreach($employees->chunk(2) as $slipPair)
<div class="slip-pair">
@foreach($slipPair as $e)
@php
$r=$rows->get($e->id); $f=$r->figures();
$pictures=$e->pictures;
if(is_string($pictures)) $pictures=json_decode($pictures,true) ?: [];
$photo=is_array($pictures)?($pictures[0]??null):null;
$photoUrl=null;
if(is_string($photo) && trim($photo)!=='') {
 $path=ltrim($photo,'/');
 $photoUrl=(str_starts_with(strtolower($photo),'http://') || str_starts_with(strtolower($photo),'https://')) ? $photo : asset(str_starts_with($path,'storage/')?$path:'storage/'.$path);
}
$initial=mb_strtoupper(mb_substr(trim($e->name??'?'),0,1));

@endphp

<section class="salary-slip">
<div class="slip-top"><div><h2>Accounts System</h2><p>Employee Salary Slip</p><p><strong>Month:</strong> {{ $month->format('F Y') }}</p></div><div class="slip-photo"><span>{{ $initial }}</span>
@if($photoUrl)<img src="{{ $photoUrl }}" alt="Employee Photo" onerror="this.remove()">
@endif
</div></div>
<hr>
<div class="employee-info"><div><p><strong>Name:</strong> {{ $e->name }}</p><p><strong>Father Name:</strong> {{ $e->father_name ?? '-' }}</p><p><strong>Phone:</strong> {{ $e->phone ?? '-' }}</p><p><strong>CNIC:</strong> {{ $e->cnic ?? '-' }}</p></div><div><p><strong>Department:</strong> {{ $e->department ?? '-' }}</p><p><strong>Designation:</strong> {{ $e->designation ?? '-' }}</p><p><strong>Slip Date:</strong> {{ $r->salary_date ? \Illuminate\Support\Carbon::parse($r->salary_date)->format('d M Y') : $month->format('d M Y') }}</p><p><strong>Employee Code:</strong> {{ $e->employee_code ?? '-' }}</p></div></div>
<table class="salary-table"><tbody>
@foreach(['Monthly Salary'=>$r->salary,'Absent Days'=>$r->absent_days,'Absence Rate / Day'=>$r->day_rate,'Absence Deduction'=>$f['absence'],'Salary After Absence Deduction'=>$r->salary-$f['absence'],'OT Hours'=>$r->ot_hours,'OT Rate / Hour'=>$r->ot_rate,'Overtime Amount'=>$f['ot'],'Total Advances (All Weeks)'=>$f['advance'],'Loan Deduction'=>$r->loan_deduction,'Other Deduction'=>$r->other_deduction,'Net Salary'=>$f['net'],'Previous Due'=>$r->overdue,'Final Payable Salary'=>$f['net']+$r->overdue,'Final Salary Already Paid'=>$r->paid_amount,'Remaining Due'=>$f['due']] as $label=>$value)
<tr class="{{ in_array($label,['Final Payable Salary','Remaining Due'])?'final-row':'' }}"><th>{{ $label }}</th><td>
@if($label==='Absent Days'){{ number_format($value,0) }} days
@elseif($label==='OT Hours'){{ number_format($value,0) }} hours
@else
 Rs {{ number_format($value,2) }}
@endif
</td></tr>

@endforeach

</tbody></table>
<h3>Weekly Advances (Weeks 1–{{ $weekCount }})</h3><table class="advance-table"><thead><tr>
@for($w=1;$w<=$weekCount;$w++)<th>Week {{ $w }}</th>
@endfor
@if($weekCount<5)<th>Other Weeks</th>
@endif
<th>Monthly Total Advance</th></tr></thead><tbody><tr>
@for($week=1;$week<=$weekCount;$week++)<td>Rs {{ number_format($r->advances->filter(fn($a)=>(int)($a->advance_week ?? min(5,intdiv($a->advance_date->day-1,7)+1))===$week)->sum('amount'),2) }}</td>
@endfor
@if($weekCount<5)<td>Rs {{ number_format($r->advances->filter(fn($a)=>(int)($a->advance_week??min(5,intdiv($a->advance_date->day-1,7)+1))>$weekCount)->sum('amount'),2) }}</td>
@endif
<td>Rs {{ number_format($f['advance'],2) }}</td></tr></tbody></table>
<div class="slip-signatures"><span>Employee Signature</span><span>Authorized Signature</span></div>
</section>

@endforeach

</div>

@endforeach


@else

@php
$allColumns=$mode==='weeks'?collect(range(1,$weekCount)):$dates;
$columnGroups=$mode==='dates' && $allColumns->count()>5 ? $allColumns->chunk(5) : collect([$allColumns]);

@endphp

@foreach($columnGroups as $columns)
@foreach($employees->chunk(20) as $pageEmployees)
@php $employeePage=$loop->iteration;
@endphp

<section class="sheet-page">
<h1>{{ strtoupper($month->format('F')) }} SALARY SHEET</h1><p style="text-align:center;font-size:9px;margin:0 0 2mm">Advance detail: Weeks 1–{{ $weekCount }}. Salary, total advances, net and due cover the full month.</p>

@if($columnGroups->count()>1)<p style="font-size:11px">Advance dates · Part {{ $loop->iteration }} of {{ $columnGroups->count() }}. Monthly salary totals repeat in each part.</p>
@endif

@php
$totals=array_fill_keys(['loan','salary','advance','absent','absence','hours','ot','deduction','net','overdue','paid','due'],0);
$advanceTotals=[];$outsideTotal=0;

@endphp

<div class="scroll"><table><thead><tr><th>Sr#</th><th class="sheet-department">Department</th><th class="sheet-name">Name</th><th>Loan</th><th>Salary</th>
@foreach($columns as $c)<th>{{ $mode==='weeks'?'Week '.$c:\Illuminate\Support\Carbon::parse($c)->format('d/m/Y') }}
@if($mode==='weeks')<br><small>Advance</small>
@endif
</th>
@endforeach
@if($weekCount<5)<th>Other Weeks</th>
@endif
<th>Monthly Total Advance</th><th>Absents</th><th>Absent Amount</th><th>OT Hours</th><th>OT Amount</th><th>Deduction</th><th>Net Pay</th><th>Overdue</th><th>Final Salary Paid</th><th>Due</th><th>Sign</th></tr></thead><tbody>
@foreach($pageEmployees as $e)
@php
$r=$rows->get($e->id); $f=$r->figures();
$values=['loan'=>$r->loan_balance,'salary'=>$r->salary,'advance'=>$f['advance'],'absent'=>$r->absent_days,'absence'=>$f['absence'],'hours'=>$r->ot_hours,'ot'=>$f['ot'],'deduction'=>$r->loan_deduction+$r->other_deduction,'net'=>$f['net'],'overdue'=>$r->overdue,'paid'=>$r->paid_amount,'due'=>$f['due']];
foreach($values as $key=>$v) $totals[$key]+=$v;
$outside=$r->advances->filter(fn($a)=>(int)($a->advance_week??min(5,intdiv($a->advance_date->day-1,7)+1))>$weekCount)->sum('amount');$outsideTotal+=$outside;

@endphp

<tr><td>{{ ($employeePage-1)*20+$loop->iteration }}</td><td>{{ $e->department ?? '-' }}</td><td>{{ $e->name }}</td><td class="money">{{ number_format($values['loan'],2) }}</td><td class="money">{{ number_format($values['salary'],2) }}</td>
@foreach($columns as $c)
@php $amount=$r->advances->filter(fn($a)=>$mode==='weeks'?(int)($a->advance_week ?? min(5,intdiv($a->advance_date->day-1,7)+1))===$c:($a->advance_date->format('Y-m-d')===$c && (int)($a->advance_week??min(5,intdiv($a->advance_date->day-1,7)+1))<=$weekCount))->sum('amount'); $advanceTotals[$c]=($advanceTotals[$c]??0)+$amount;
@endphp

<td class="money">{{ number_format($amount,2) }}</td>

@endforeach

@if($weekCount<5)<td class="money">{{ number_format($outside,2) }}</td>
@endif

@foreach(['advance','absent','absence','hours','ot','deduction','net','overdue','paid','due'] as $key)<td class="money">{{ number_format($values[$key],2) }}</td>
@endforeach
<td class="sign"></td></tr>

@endforeach

</tbody><tfoot><tr><th colspan="3">Page Total</th><th>{{ number_format($totals['loan'],2) }}</th><th>{{ number_format($totals['salary'],2) }}</th>
@foreach($columns as $c)<th>{{ number_format($advanceTotals[$c]??0,2) }}</th>
@endforeach

@if($weekCount<5)<th>{{ number_format($outsideTotal,2) }}</th>
@endif

@foreach(['advance','absent','absence','hours','ot','deduction','net','overdue','paid','due'] as $key)<th>{{ number_format($totals[$key],2) }}</th>
@endforeach
<th></th></tr></tfoot></table></div><div class="signatures"><span>Prepared by: ______________</span><span>Approved by: ______________</span></div>
</section>

@endforeach


@endforeach


@endif


</body></html>
