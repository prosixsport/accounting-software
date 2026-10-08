@php

$weekStart=$weekStart??1;

$weekEnd=$weekEnd??($weekCount??5);

$weekLabel=$weekStart===$weekEnd ? 'Week '.$weekStart : 'Weeks '.$weekStart.'–'.$weekEnd;

$weekAdvances=fn($record)=>$record->advances->filter(fn($advance)=>(int)($advance->advance_week??min(5,intdiv($advance->advance_date->day-1,7)+1))>=$weekStart && (int)($advance->advance_week??min(5,intdiv($advance->advance_date->day-1,7)+1))<=$weekEnd);

@endphp

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



/* One employee slip per full A4 portrait page. */
@if($single)
@page{size:A4 portrait;margin:8mm}
.slip-pair{width:194mm;max-width:100%;margin:0 auto 20px;display:block;break-after:page}
.slip-pair:last-of-type{break-after:auto}
.salary-slip{width:100%;min-height:0;height:auto;box-sizing:border-box;margin:0;box-shadow:none;break-after:auto}
@media print{.slip-pair{width:100%;margin:0;break-after:page}.slip-pair:last-of-type{break-after:auto}.salary-slip{break-after:auto!important}}
@endif






.reference-slip{font-family:Arial,sans-serif;padding:4mm!important;border:1px solid #999;overflow:hidden;font-size:9px;color:#222}

.ref-header{display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #555;padding-bottom:2mm;gap:4mm;height:16mm;box-sizing:border-box}

.ref-logos{display:flex;align-items:center;gap:5mm;width:62%}.ref-logos img{object-fit:contain;max-height:13mm}.ref-p-logo{width:18mm}.ref-brand{width:48mm}.ref-logos span{height:10mm;border-left:2px solid #333}.ref-company{border-left:1px solid #555;padding-left:3mm;font-size:8px;line-height:1.25}

.ref-person{display:flex;position:relative;gap:4mm;padding:3mm 0;border-bottom:1px solid #555;min-height:25mm;box-sizing:border-box}.ref-photo{width:22mm;height:24mm;flex-shrink:0;position:relative;display:flex;align-items:center;justify-content:center;background:#eee;font-size:22px}.ref-photo img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}.ref-details{width:65%;position:relative;z-index:1}.ref-details>div{display:flex;gap:3mm;margin-bottom:1.5mm}.ref-details strong{width:21mm;flex-shrink:0}.ref-details span{overflow-wrap:anywhere}.ref-watermark{position:absolute;right:0;top:6mm;font-size:28px;font-weight:bold;color:#e6e6e6}

.ref-dates{display:grid;grid-template-columns:1fr 1fr;column-gap:35mm;margin:1.5mm 0;font-size:8px}.ref-dates>div{display:flex;justify-content:space-between;border-bottom:1px solid #777;padding:1mm 0}.ref-dates strong{font-weight:normal}

.reference-slip table{font-size:8px;table-layout:fixed;width:100%}.reference-slip th,.reference-slip td{border:0;padding:1mm 1mm!important;line-height:1.15}.reference-slip thead th{background:#d0d0d0!important;font-weight:bold}.ref-table th,.ref-table td{text-align:center!important}.ref-table th{width:auto!important}.ref-earnings th{width:70%;text-align:left}.ref-earnings td,.ref-earnings thead th:last-child{text-align:right}.ref-earnings tbody th{background:white;font-weight:600}.ref-totals{width:48%;margin:1mm 0 2mm auto;border-top:1px solid #333}.ref-totals th{background:white;width:65%;font-weight:600}.ref-totals td{text-align:right;white-space:nowrap}.ref-totals tr:last-child{font-weight:bold}.ref-weeks{margin-top:2mm}.ref-sign{display:flex;justify-content:space-between;margin-top:4mm;font-size:8px}

@media print{.reference-slip thead th{print-color-adjust:exact;-webkit-print-color-adjust:exact}}




/* Full-page spacing and readable type. */
.reference-slip{padding:8mm!important;min-height:0;overflow:visible;font-size:12px;display:flex;flex-direction:column}
.ref-header{height:25mm;padding-bottom:4mm;gap:5mm;flex-shrink:0}
.ref-logos img{max-height:20mm}.ref-p-logo{width:24mm}.ref-brand{width:58mm}.ref-logos{gap:5mm}.ref-company{font-size:10px;line-height:1.4}
.ref-person{min-height:43mm;padding:5mm 0;gap:5mm;flex-shrink:0}.ref-photo{width:29mm;height:34mm}.ref-details{font-size:12px;width:66%}.ref-details strong{width:27mm}.ref-details>div{margin-bottom:3mm}.ref-watermark{font-size:38px;top:14mm}
.ref-dates{font-size:11px;margin:3mm 0;column-gap:20mm}.ref-dates>div{padding:2mm 0}
.reference-slip table{font-size:11px}.reference-slip th,.reference-slip td{padding:2.2mm 1.5mm!important;line-height:1.3}.ref-totals{width:57%;margin:4mm 0 5mm auto}.ref-weeks{margin-top:5mm}.ref-sign{font-size:11px;margin-top:auto;padding-top:14mm;padding-bottom:3mm}
@media print{.reference-slip{min-height:0}.ref-header,.ref-person,.ref-dates,.ref-totals,.ref-sign{break-inside:avoid}}


/* Fit one complete slip within the A4 printable area. */
@if($single)
@page{size:A4 portrait;margin:8mm}
.slip-pair{width:194mm;max-width:100%;display:block;break-after:page}
.reference-slip{height:276mm;min-height:0!important;max-width:100%;padding:6mm!important;box-sizing:border-box;overflow:visible;break-inside:avoid;page-break-inside:avoid}
.ref-header{height:22mm;flex-shrink:0;padding-bottom:3mm}
.ref-person{min-height:0;height:39mm;padding:3mm 0;flex-shrink:0}
.ref-photo{width:27mm;height:32mm}.ref-details{font-size:11px}.ref-details>div{margin-bottom:2mm}
.ref-dates{font-size:10px;margin:2mm 0}.ref-dates>div{padding:1.5mm 0}
.reference-slip table{font-size:10.5px;flex-shrink:0}.reference-slip th,.reference-slip td{padding:1.6mm 1.5mm!important;line-height:1.2}
.ref-totals{margin:3mm 0 4mm auto;flex-shrink:0}.ref-weeks{margin-top:4mm}
.ref-sign{margin-top:auto;padding-top:8mm;padding-bottom:0;font-size:10px;flex-shrink:0}
@media print{
 html,body{margin:0!important;padding:0!important}
 .slip-pair{width:194mm;margin:0!important;break-after:page;page-break-after:always}
 .slip-pair:last-of-type{break-after:auto;page-break-after:auto}
 .reference-slip{height:276mm;min-height:0!important;margin:0!important;break-after:auto!important;page-break-after:auto!important}
}
@endif

</style></head><body><div class="toolbar"><button onclick="window.print()">Print / Save PDF</button> <a href="{{ route('salary-management.index',['month'=>$month->format('Y-m')]) }}">Back to Salary Management</a></div>





@if($single)

@foreach($employees->chunk(1) as $slipPair)

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



@php

$gross=round((float)$r->salary+$f['ot'],2);

$deductions=round($f['absence']+$f['advance']+(float)$r->loan_deduction+(float)$r->other_deduction,2);

$payDate=!empty($r->payment_date)?\Illuminate\Support\Carbon::parse($r->payment_date)->format('d/m/Y'):'Unpaid';

@endphp

<section class="salary-slip reference-slip">

<header class="ref-header">

<div class="ref-logos"><img class="ref-p-logo" src="{{ asset('assets/images/P LOGO BLACK.png') }}" alt="P"><span></span><img class="ref-brand" src="{{ asset('assets/images/PROSIX SPORTS LOGO PNG BLACK.png') }}" alt="Prosix Sports"></div>

<div class="ref-company"><strong>Prosix Sports</strong><br>Gajju Matah Near Kana Interchange<br>Lahore Pakistan 54000</div>

</header>

<div class="ref-person">

<div class="ref-photo">{{ $initial }}

@if($photoUrl)

<img src="{{ $photoUrl }}" alt="{{ $e->name }}" onerror="this.remove()">

@endif

</div>

<div class="ref-details"><div><strong>Name :</strong><span>{{ $e->name }}</span></div><div><strong>Designation :</strong><span>{{ $e->designation ?? '-' }}</span></div><div><strong>Address :</strong><span>{{ $e->address ?? '-' }}</span></div><div><strong>Cell # :</strong><span>{{ $e->phone ?? '-' }}</span></div></div>

<div class="ref-watermark">Pay Slip.</div>

</div>

<div class="ref-dates"><div><span>Period End</span><strong>{{ $month->copy()->endOfMonth()->format('d/m/Y') }}</strong></div><div><span>Print Date</span><strong class="current-print-date">{{ now('Asia/Karachi')->format('d/m/Y') }}</strong></div><div><span>Month</span><strong>{{ $month->format('F Y') }}</strong></div><div><span>Pay Date</span><strong>{{ $payDate }}</strong></div><div><span>Department</span><strong>{{ $e->department ?? '-' }}</strong></div></div>

<table class="ref-table"><thead><tr><th>Working Days</th><th>Present</th><th>Absent</th><th>OT Hours</th><th>OT Rate / Hour</th></tr></thead><tbody><tr><td>{{ $month->daysInMonth }}</td><td>{{ max(0,$month->daysInMonth-(int)$r->absent_days) }}</td><td>{{ (int)$r->absent_days }}</td><td>{{ (int)$r->ot_hours }}</td><td>Rs {{ number_format($r->ot_rate,2) }}</td></tr></tbody></table>

<table class="ref-earnings"><thead><tr><th>Earnings / Deductions</th><th>Amount</th></tr></thead><tbody>

@foreach(['Basic Salary'=>$r->salary,'Overtime'=>$f['ot'],'Absent Deduction'=>$f['absence'],'Weekly Advances (All Weeks)'=>$f['advance'],'Loan Installment'=>$r->loan_deduction,'Other Deduction'=>$r->other_deduction] as $label=>$amount)

<tr><th>{{ $label }}</th><td>Rs {{ number_format($amount,2) }}</td></tr>

@endforeach

</tbody></table>

<div class="ref-totals"><table>

@foreach(['Total Earnings'=>$gross,'Total Deduction'=>$deductions,'Previous Salary Due'=>$r->overdue,'Final Payable Salary'=>$f['net']+$r->overdue,'Salary Paid'=>$r->paid_amount,'Remaining Amount'=>$f['due']] as $label=>$amount)

<tr><th>{{ $label }}</th><td>Rs {{ number_format($amount,2) }}</td></tr>

@endforeach

</table></div>

<table class="ref-table"><thead><tr><th>Loan Balance Before Installment</th><th>Current Installment</th><th>Remaining Loan Balance</th></tr></thead><tbody><tr><td>Rs {{ number_format($r->loan_balance,2) }}</td><td>Rs {{ number_format($r->loan_deduction,2) }}</td><td>Rs {{ number_format(max(0,$r->loan_balance-$r->loan_deduction),2) }}</td></tr></tbody></table>

<table class="ref-table ref-weeks"><thead><tr>

@for($w=$weekStart;$w<=min($weekEnd,(int)ceil($month->daysInMonth/7));$w++)

<th>Week {{ $w }}</th>

@endfor

</tr></thead><tbody><tr>

@for($w=$weekStart;$w<=min($weekEnd,(int)ceil($month->daysInMonth/7));$w++)

<td>Rs {{ number_format($r->advances->filter(fn($a)=>(int)($a->advance_week??min(5,intdiv($a->advance_date->day-1,7)+1))===$w)->sum('amount'),2) }}</td>

@endfor

</tr></tbody></table>

<div class="ref-sign"><span>Employee Signature __________</span><span>Authorized Signature __________</span></div>

</section>



@endforeach



</div>



@endforeach





@else



@php

$allColumns=$mode==='weeks'?collect(range($weekStart,$weekEnd)):$dates;

$columnGroups=$mode==='dates' && $allColumns->count()>5 ? $allColumns->chunk(5) : collect([$allColumns]);



@endphp



@foreach($columnGroups as $columns)

@foreach($employees->chunk(20) as $pageEmployees)

@php $employeePage=$loop->iteration;

@endphp



<section class="sheet-page">

<h1>{{ strtoupper($month->format('F')) }} SALARY SHEET</h1><p style="text-align:center;font-size:9px;margin:0 0 2mm">Advance detail: {{ $weekLabel }}. Salary, net and due are monthly totals.</p>



@if($columnGroups->count()>1)<p style="font-size:11px">Advance dates · Part {{ $loop->iteration }} of {{ $columnGroups->count() }}. Monthly salary totals repeat in each part.</p>

@endif



@php

$totals=array_fill_keys(['loan','salary','advance','absent','absence','hours','ot','deduction','net','overdue','paid','due'],0);

$advanceTotals=[];$outsideTotal=0;



@endphp



<div class="scroll"><table><thead><tr><th>Sr#</th><th class="sheet-department">Department</th><th class="sheet-name">Name</th><th>Loan</th><th>Monthly Salary</th>

@foreach($columns as $c)<th>{{ $mode==='weeks'?'Week '.$c:\Illuminate\Support\Carbon::parse($c)->format('d/m/Y') }}

@if($mode==='weeks')<br><small>Advance</small>

@endif

</th>

@endforeach



<th>Selected Advance Total</th><th>Absents</th><th>Absent Amount</th><th>OT Hours</th><th>OT Amount</th><th>Deduction</th><th>Monthly Net Pay</th><th>Overdue</th><th>Final Salary Paid</th><th>Monthly Due</th><th>Sign</th></tr></thead><tbody>

@foreach($pageEmployees as $e)

@php

$r=$rows->get($e->id); $f=$r->figures();

$values=['loan'=>$r->loan_balance,'salary'=>$r->salary,'advance'=>$weekAdvances($r)->sum('amount'),'absent'=>$r->absent_days,'absence'=>$f['absence'],'hours'=>$r->ot_hours,'ot'=>$f['ot'],'deduction'=>$r->loan_deduction+$r->other_deduction,'net'=>$f['net'],'overdue'=>$r->overdue,'paid'=>$r->paid_amount,'due'=>$f['due']];

foreach($values as $key=>$v) $totals[$key]+=$v;





@endphp



<tr><td>{{ ($employeePage-1)*20+$loop->iteration }}</td><td>{{ $e->department ?? '-' }}</td><td>{{ $e->name }}</td><td class="money">{{ number_format($values['loan'],2) }}</td><td class="money">{{ number_format($values['salary'],2) }}</td>

@foreach($columns as $c)

@php $amount=$r->advances->filter(fn($a)=>$mode==='weeks'?(int)($a->advance_week ?? min(5,intdiv($a->advance_date->day-1,7)+1))===$c:($a->advance_date->format('Y-m-d')===$c && (int)($a->advance_week??min(5,intdiv($a->advance_date->day-1,7)+1))>=$weekStart && (int)($a->advance_week??min(5,intdiv($a->advance_date->day-1,7)+1))<=$weekEnd))->sum('amount'); $advanceTotals[$c]=($advanceTotals[$c]??0)+$amount;

@endphp



<td class="money">{{ number_format($amount,2) }}</td>



@endforeach







@foreach(['advance','absent','absence','hours','ot','deduction','net','overdue','paid','due'] as $key)<td class="money">{{ number_format($values[$key],2) }}</td>

@endforeach

<td class="sign"></td></tr>



@endforeach



</tbody><tfoot><tr><th colspan="3">Page Total</th><th>{{ number_format($totals['loan'],2) }}</th><th>{{ number_format($totals['salary'],2) }}</th>

@foreach($columns as $c)<th>{{ number_format($advanceTotals[$c]??0,2) }}</th>

@endforeach







@foreach(['advance','absent','absence','hours','ot','deduction','net','overdue','paid','due'] as $key)<th>{{ number_format($totals[$key],2) }}</th>

@endforeach

<th></th></tr></tfoot></table></div><div class="signatures"><span>Prepared by: ______________</span><span>Approved by: ______________</span></div>

</section>



@endforeach





@endforeach





@endif





<script>
(function(){
 function updatePrintDate(){
  const parts=Object.fromEntries(new Intl.DateTimeFormat('en-GB',{timeZone:'Asia/Karachi',day:'2-digit',month:'2-digit',year:'numeric'}).formatToParts(new Date()).filter(p=>p.type!=='literal').map(p=>[p.type,p.value]));
  document.querySelectorAll('.current-print-date').forEach(el=>el.textContent=parts.day+'/'+parts.month+'/'+parts.year);
 }
 updatePrintDate();window.addEventListener('beforeprint',updatePrintDate);
})();
</script>
</body></html>
