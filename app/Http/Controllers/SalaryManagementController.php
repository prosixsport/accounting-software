<?php
namespace App\Http\Controllers;
use App\Models\Employee;
use App\Models\SalaryManagementRow;
use App\Models\SalaryManagementAdvance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
class SalaryManagementController extends Controller {
 private function month(Request $request): Carbon {
  $data=$request->validate(['month'=>['nullable','date_format:Y-m']]);
  return Carbon::createFromFormat('!Y-m',$data['month']??now()->format('Y-m'))->startOfMonth();
 }
 public function index(Request $request) {
  $month=$this->month($request);
  $employees=Employee::orderBy('department')->orderBy('name')->get();
  $rows=SalaryManagementRow::with('advances')->whereDate('month',$month->toDateString())->get()->keyBy('employee_id');
  $loanSchedules=$employees->mapWithKeys(fn($e)=>[$e->id=>(new \App\Services\WorkerLoans)->schedule($e->id,$month->toDateString(),$rows->get($e->id)?->id)]);
  $overview=$this->overview($employees,$rows);
  $funds=[];
  $revision=hash('sha256',json_encode([$overview,$funds]).$rows->sortKeys()->toJson());
  if($request->expectsJson()) return response()->json(compact('overview','revision'));
  return view('salary-management.index',compact('month','employees','rows','overview','revision','funds','loanSchedules'));
 }
 private function overview($employees,$rows): array {
  return $employees->map(function($e) use($rows) {
   $r=$rows->get($e->id);$f=$r?->figures();
   return ['id'=>$e->id,'name'=>$e->name,'department'=>$e->department??'','saved'=>(bool)$r,'salary'=>(float)($r?->salary??0),'advance'=>(float)($f['advance']??0),'net'=>(float)($f['net']??0),'due'=>(float)($f['due']??0),'ledger'=>$r ? $r->advances->map(fn($a)=>['date'=>$a->advance_date->format('Y-m-d'),'week'=>(int)($a->advance_week??min(5,intdiv($a->advance_date->day-1,7)+1)),'amount'=>(float)$a->amount,'reason'=>$a->reason??''])->values()->all():[]];
  })->values()->all();
 }
 public function save(Request $request) {
  $month=$this->month($request);
  if($request->input('action')==='fund_entry') return redirect()->route('funds-management.index',['month'=>$month->format('Y-m')]);
  $rules=['employee_id'=>['required',Rule::exists('employees','id')],'notes'=>['nullable','string','max:2000']];
  foreach(['salary','loan_balance','absent_days','day_rate','ot_hours','ot_rate','loan_deduction','other_deduction','overdue','paid_amount'] as $f) $rules[$f]=['required','numeric','min:0','max:9999999999.99'];
  $rules['working_hours_per_day']=['required','numeric','multiple_of:1','min:1','max:24'];
  $rules['absent_days'][]='multiple_of:1';
  $rules['ot_hours'][]='multiple_of:1';
  $rules['ot_hours'][]='max:'.(30*24);
  $rules['absent_days'][]='max:30';
  $rules['salary_date']=['nullable','date_format:Y-m-d','after_or_equal:'.$month->toDateString(),'before_or_equal:'.$month->copy()->endOfMonth()->toDateString()];
  $rules['entry_advance_amount']=['nullable','numeric','min:0','max:9999999999.99'];
  $rules['entry_advance_week']=['nullable','integer','min:1','max:5'];
  $rules['entry_advance_date']=['nullable','date_format:Y-m-d','after_or_equal:'.$month->toDateString(),'before_or_equal:'.$month->copy()->endOfMonth()->toDateString()];
  $rules['entry_advance_reason']=['nullable','string','max:1000'];
  $data=$request->validate($rules);
  if (($data['entry_advance_amount']??0)>0 && (empty($data['entry_advance_date']) || empty($data['entry_advance_week']))) {
   throw \Illuminate\Validation\ValidationException::withMessages(['entry_advance_date'=>'Select advance date and week.']);
  }
  if(!empty($data['entry_advance_date'])) $data['entry_advance_week']=min(5,intdiv(Carbon::parse($data['entry_advance_date'])->day-1,7)+1);
  $data['day_rate']=round($data['salary']/30,2);
  $data['ot_rate']=round($data['day_rate']/$data['working_hours_per_day'],2);
  $row=DB::transaction(function() use($data,$month) {
   Employee::whereKey($data['employee_id'])->lockForUpdate()->firstOrFail();
   $existing=SalaryManagementRow::where('employee_id',$data['employee_id'])->whereDate('month',$month->toDateString())->lockForUpdate()->first();
   if($existing && (float)$existing->paid_amount>0)throw \Illuminate\Validation\ValidationException::withMessages(['salary'=>'Mark this record Unpaid before editing a paid salary.']);
   $schedule=(new \App\Services\WorkerLoans)->schedule($data['employee_id'],$month->toDateString(),$existing?->id);
   $salaryData=$data;
   if($schedule['managed']){ $salaryData['loan_balance']=$schedule['loan_balance'];$salaryData['loan_deduction']=$schedule['loan_deduction']; }
   foreach(['entry_advance_amount','entry_advance_week','entry_advance_date','entry_advance_reason'] as $field) unset($salaryData[$field]);
   $row=SalaryManagementRow::updateOrCreate(['employee_id'=>$data['employee_id'],'month'=>$month->toDateString()],$salaryData);
   if($schedule['managed'])(new \App\Services\WorkerLoans)->sync($row,$schedule);
   if (($data['entry_advance_amount']??0)>0) $row->advances()->create(['amount'=>$data['entry_advance_amount'],'advance_week'=>$data['entry_advance_week'],'advance_date'=>$data['entry_advance_date'],'reason'=>$data['entry_advance_reason']??null]);
   return $row;
  });
  if ($request->expectsJson()) return response()->json(['message'=>'Salary saved.','row'=>$row->fresh()->toArray(),'advance'=>$row->load('advances')->figures()['advance'],'ledger'=>$row->advances->map(fn($a)=>['date'=>$a->advance_date->format('Y-m-d'),'week'=>(int)($a->advance_week ?? min(5,intdiv($a->advance_date->day-1,7)+1)),'amount'=>(float)$a->amount,'reason'=>$a->reason ?? ''])->values()]);
  return redirect()->route('salary-management.index',['month'=>$month->format('Y-m')])->with('success','Salary details saved.')->with('active_employee',$data['employee_id'])->with('active_tab','salary');
 }
 public function paymentStatus(Request $request) {
  $month=$this->month($request);
  $data=$request->validate([
   'employee_id'=>['required_without:employee_ids','nullable','integer',Rule::exists('employees','id')],
   'employee_ids'=>['required_without:employee_id','array','min:1','max:1000'],
   'employee_ids.*'=>['required','integer','distinct',Rule::exists('employees','id')],
   'payment_status'=>['required',Rule::in(['paid','unpaid'])]
  ]);
  $ids=$data['employee_ids']??[$data['employee_id']];
  [$updated,$skipped]=DB::transaction(function() use($data,$month,$ids) {
   Employee::whereIn('id',$ids)->orderBy('id')->lockForUpdate()->get();
   $rows=SalaryManagementRow::whereIn('employee_id',$ids)->whereDate('month',$month->toDateString())->orderBy('id')->lockForUpdate()->get();
   if($rows->count()!==count($ids)) throw \Illuminate\Validation\ValidationException::withMessages(['employee_ids'=>'Only saved salary records from this month can be updated.']);
   $updated=0;$skipped=0;
   foreach($rows as $row) {
    $row->load('advances');
    $amount=round($row->figures()['net']+(float)$row->overdue,2);
    if($data['payment_status']==='paid' && $amount<=0){$skipped++;continue;}
    if($data['payment_status']==='paid') {
     if(round((float)$row->paid_amount,2)!==$amount){$row->paid_amount=$amount;$row->payment_date=now('Asia/Karachi')->toDateString();}
    } else {$row->paid_amount=0;$row->payment_date=null;}
    $row->save();$updated++;
   }
   return [$updated,$skipped];
  });
  return redirect()->route('salary-management.index',['month'=>$month->format('Y-m')])->with('success',$updated.' salary record(s) marked '.ucfirst($data['payment_status']).'.'.($skipped?' '.$skipped.' skipped: no positive salary payable.':''));
 }
 public function advance(Request $request) {
  $month=$this->month($request);
  $data=$request->validate(['employee_id'=>['required',Rule::exists('employees','id')],'advance_date'=>['required','date_format:Y-m-d','after_or_equal:'.$month->toDateString(),'before_or_equal:'.$month->copy()->endOfMonth()->toDateString()],'advance_week'=>['required','integer','min:1','max:5'],'amount'=>['required','numeric','min:0.01','max:9999999999.99'],'reason'=>['nullable','string','max:1000']]);
  $data['advance_week']=min(5,intdiv(Carbon::parse($data['advance_date'])->day-1,7)+1);
  DB::transaction(function() use($data,$month) {
   $employee=Employee::whereKey($data['employee_id'])->lockForUpdate()->firstOrFail();
   $row=SalaryManagementRow::firstOrCreate(['employee_id'=>$employee->id,'month'=>$month->toDateString()],['salary'=>$employee->basic_salary??0,'day_rate'=>round(($employee->basic_salary??0)/30,2)]);
   if((float)$row->paid_amount>0)throw \Illuminate\Validation\ValidationException::withMessages(['amount'=>'Mark salary Unpaid before adding an advance to a paid record.']);
   $schedule=(new \App\Services\WorkerLoans)->schedule($employee->id,$month->toDateString(),$row->id);
   if($schedule['managed']){$row->update(['loan_balance'=>$schedule['loan_balance'],'loan_deduction'=>$schedule['loan_deduction']]);(new \App\Services\WorkerLoans)->sync($row,$schedule);}
   $row->advances()->create(['advance_week'=>$data['advance_week'],'advance_date'=>$data['advance_date'],'amount'=>$data['amount'],'reason'=>$data['reason']??null]);
  });
  return redirect()->route('salary-management.index',['month'=>$month->format('Y-m')])->with('success','Advance added.')->with('active_employee',$data['employee_id'])->with('active_tab','advances');
 }
 public function deleteAdvance(Request $request, SalaryManagementAdvance $advance) {
  $row=SalaryManagementRow::findOrFail($advance->salary_row_id);
  $month=$row->month->format('Y-m');
  DB::transaction(function()use($row,$advance){Employee::whereKey($row->employee_id)->lockForUpdate()->firstOrFail();$locked=SalaryManagementRow::whereKey($row->id)->lockForUpdate()->firstOrFail();if((float)$locked->paid_amount>0)throw \Illuminate\Validation\ValidationException::withMessages(['amount'=>'Mark salary Unpaid before removing an advance from a paid record.']);$advance->delete();});
  return redirect()->route('salary-management.index',['month'=>$month])->with('success','Advance removed.')->with('active_employee',$row->employee_id)->with('active_tab','advances');
 }
 public function print(Request $request) {
  $month=$this->month($request);
  $data=$request->validate([
   'employee_id'=>['nullable',Rule::exists('employees','id')],
   'employee_ids'=>['nullable','array','min:1'],
   'employee_ids.*'=>['required','integer','distinct',Rule::exists('employees','id')],
   'scope'=>['nullable',Rule::in(['selected','all'])],
   'mode'=>['nullable',Rule::in(['dates','weeks','slips'])],
   'week_count'=>['nullable','integer','min:1','max:5'],
   'week'=>['nullable','integer','min:0','max:5'],
  ]);
  $single=!empty($data['employee_id']);
  $selected=($data['scope']??null)==='selected' || !empty($data['employee_ids']);
  if ($selected && empty($data['employee_ids'])) {
   return redirect()->route('salary-management.index',['month'=>$month->format('Y-m')])->withErrors(['print'=>'Select at least one saved employee to print.']);
  }
  $query=Employee::query();
  if ($single) $query->where('id',$data['employee_id']);
  elseif ($selected) $query->whereIn('id',$data['employee_ids']);
  else $query->whereIn('id',SalaryManagementRow::whereDate('month',$month->toDateString())->select('employee_id'));
  $employees=$query->orderBy('department')->orderBy('name')->get();
  if ($employees->isEmpty()) return redirect()->route('salary-management.index',['month'=>$month->format('Y-m')])->withErrors(['print'=>'No employees available to print.']);
  $rows=SalaryManagementRow::with('advances')->whereDate('month',$month->toDateString())->whereIn('employee_id',$employees->pluck('id'))->get()->keyBy('employee_id');
  $missing=$employees->filter(fn($e)=>!$rows->has($e->id));
  if ($missing->isNotEmpty()) {
   return redirect()->route('salary-management.index',['month'=>$month->format('Y-m')])->withErrors(['print'=>'Save salary details before printing: '.$missing->pluck('name')->implode(', ')]);
  }
  $mode=$data['mode']??'weeks';
  $weekCount=(int)($data['week_count']??5);
  $selectedWeek=(int)($data['week']??0);
  $weekStart=$selectedWeek>0 ? $selectedWeek : 1;
  $weekEnd=$selectedWeek>0 ? $selectedWeek : $weekCount;
  $single=$single || $mode==='slips';
  $dates=$rows->flatMap(fn($r)=>$r->advances->filter(fn($a)=>(int)($a->advance_week??min(5,intdiv($a->advance_date->day-1,7)+1))>=$weekStart && (int)($a->advance_week??min(5,intdiv($a->advance_date->day-1,7)+1))<=$weekEnd)->map(fn($a)=>$a->advance_date->format('Y-m-d')))->unique()->sort()->values();
  return view('salary-management.print',compact('month','employees','rows','mode','dates','single','weekCount','weekStart','weekEnd'));
 }
}
