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
  return view('salary-management.index',compact('month','employees','rows'));
 }
 public function save(Request $request) {
  $month=$this->month($request);
  $rules=['employee_id'=>['required',Rule::exists('employees','id')],'notes'=>['nullable','string','max:2000']];
  foreach(['salary','loan_balance','absent_days','day_rate','ot_hours','ot_rate','loan_deduction','other_deduction','overdue','paid_amount'] as $f) $rules[$f]=['required','numeric','min:0','max:9999999999.99'];
  $rules['absent_days'][]='max:'.$month->daysInMonth;
  $data=$request->validate($rules);
  SalaryManagementRow::updateOrCreate(['employee_id'=>$data['employee_id'],'month'=>$month->toDateString()],$data);
  return redirect()->route('salary-management.index',['month'=>$month->format('Y-m')])->with('success','Salary details saved.')->with('active_employee',$data['employee_id'])->with('active_tab','salary');
 }
 public function advance(Request $request) {
  $month=$this->month($request);
  $data=$request->validate(['employee_id'=>['required',Rule::exists('employees','id')],'advance_date'=>['required','date_format:Y-m-d','after_or_equal:'.$month->toDateString(),'before_or_equal:'.$month->copy()->endOfMonth()->toDateString()],'advance_week'=>['required','integer','min:1','max:3'],'amount'=>['required','numeric','min:0.01','max:9999999999.99'],'reason'=>['nullable','string','max:1000']]);
  DB::transaction(function() use($data,$month) {
   $employee=Employee::findOrFail($data['employee_id']);
   $row=SalaryManagementRow::firstOrCreate(['employee_id'=>$employee->id,'month'=>$month->toDateString()],['salary'=>$employee->basic_salary??0,'day_rate'=>round(($employee->basic_salary??0)/$month->daysInMonth,2)]);
   $row->advances()->create(['advance_week'=>$data['advance_week'],'advance_date'=>$data['advance_date'],'amount'=>$data['amount'],'reason'=>$data['reason']??null]);
  });
  return redirect()->route('salary-management.index',['month'=>$month->format('Y-m')])->with('success','Advance added.')->with('active_employee',$data['employee_id'])->with('active_tab','advances');
 }
 public function deleteAdvance(Request $request, SalaryManagementAdvance $advance) {
  $row=SalaryManagementRow::findOrFail($advance->salary_row_id);
  $month=$row->month->format('Y-m'); $advance->delete();
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
  ]);
  $single=!empty($data['employee_id']);
  $selected=($data['scope']??null)==='selected' || !empty($data['employee_ids']);
  if ($selected && empty($data['employee_ids'])) {
   return redirect()->route('salary-management.index',['month'=>$month->format('Y-m')])->withErrors(['print'=>'Select at least one saved employee to print.']);
  }
  $query=Employee::query();
  if ($single) $query->where('id',$data['employee_id']);
  elseif ($selected) $query->whereIn('id',$data['employee_ids']);
  $employees=$query->orderBy('department')->orderBy('name')->get();
  if ($employees->isEmpty()) return redirect()->route('salary-management.index',['month'=>$month->format('Y-m')])->withErrors(['print'=>'No employees available to print.']);
  $rows=SalaryManagementRow::with('advances')->whereDate('month',$month->toDateString())->whereIn('employee_id',$employees->pluck('id'))->get()->keyBy('employee_id');
  $missing=$employees->filter(fn($e)=>!$rows->has($e->id));
  if ($missing->isNotEmpty()) {
   return redirect()->route('salary-management.index',['month'=>$month->format('Y-m')])->withErrors(['print'=>'Save salary details before printing: '.$missing->pluck('name')->implode(', ')]);
  }
  $mode=$data['mode']??'weeks';
  $single=$single || $mode==='slips';
  $dates=$rows->flatMap(fn($r)=>$r->advances->map(fn($a)=>$a->advance_date->format('Y-m-d')))->unique()->sort()->values();
  return view('salary-management.print',compact('month','employees','rows','mode','dates','single'));
 }
}
