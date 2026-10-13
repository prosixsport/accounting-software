<?php
namespace App\Http\Controllers;
use App\Models\Employee;
use App\Models\SalaryManagementRow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\WorkerLoans;
class LoanWorkerController extends Controller
{
 public function index(Request $request) {
  abort_unless($request->user()->hasPermission('payrolls'),403);
  $history=SalaryManagementRow::where(function($q){$q->where('loan_balance','>',0)->orWhere('loan_deduction','>',0);})->orderByDesc('month')->orderByDesc('id')->get()->groupBy('employee_id');
  $loanIds=DB::table('worker_loans')->pluck('employee_id');
  $allEmployees=Employee::orderBy('department')->orderBy('name')->get();
  $loans=$loanIds->unique()->mapWithKeys(fn($id)=>[$id=>(new WorkerLoans)->details($id)]);
  $employees=Employee::whereIn('id',$history->keys()->merge($loanIds)->unique())->orderBy('department')->orderBy('name')->get();
  return view('loan-workers.index',compact('employees','history','allEmployees','loans'));
 }
 public function store(Request $request) {
  abort_unless($request->user()->hasPermission('payrolls'),403);
  $data=$request->validate(['employee_id'=>['required','integer','exists:employees,id'],'loan_date'=>['required','date_format:Y-m-d','before_or_equal:today'],'start_month'=>['required','date_format:Y-m'],'amount'=>['required','regex:/^\d{1,11}(\.\d{1,2})?$/','numeric','min:0.01'],'installment'=>['required','regex:/^\d{1,11}(\.\d{1,2})?$/','numeric','min:0.01','lte:amount'],'given_by'=>['required','string','max:255'],'notes'=>['nullable','string','max:2000'],'submission_key'=>['required','uuid']]);
  $data['start_month'].='-01';
  if(substr($data['loan_date'],0,7)>substr($data['start_month'],0,7))throw \Illuminate\Validation\ValidationException::withMessages(['start_month'=>'Installments cannot start before the loan month.']);
  DB::transaction(function()use($data,$request){
   Employee::whereKey($data['employee_id'])->lockForUpdate()->firstOrFail();
   if(DB::table('worker_loans')->where('submission_key',$data['submission_key'])->exists())return;
   if(SalaryManagementRow::where('employee_id',$data['employee_id'])->where('month','>=',$data['start_month'])->where('paid_amount','>',0)->exists())throw \Illuminate\Validation\ValidationException::withMessages(['start_month'=>'A salary in this period already has a payment. Choose a later start month or reverse that payment before scheduling the loan.']);
   $id=DB::table('worker_loans')->insertGetId($data+['created_by'=>$request->user()->id,'created_at'=>now(),'updated_at'=>now()]);
   foreach(SalaryManagementRow::where('employee_id',$data['employee_id'])->where('month','>=',$data['start_month'])->orderBy('month')->lockForUpdate()->get() as $salary){$schedule=(new WorkerLoans)->schedule($salary->employee_id,$salary->month->toDateString(),$salary->id);$salary->update(['loan_balance'=>$schedule['loan_balance'],'loan_deduction'=>$schedule['loan_deduction']]);(new WorkerLoans)->sync($salary,$schedule);}
   DB::table('fund_activity')->insert(['source'=>'worker_loans','source_id'=>$id,'user_id'=>$request->user()->id,'actor'=>$request->user()->name,'action'=>'created','before'=>null,'after'=>json_encode($data),'created_at'=>now()]);
  });
  return redirect()->route('loan-workers.show',$data['employee_id'])->with('success','Loan issued and monthly installment scheduled.');
 }
 public function update(Request $request,int $id){
  abort_unless($request->user()->hasPermission('payrolls'),403);
  $data=$request->validate(['loan_date'=>['required','date_format:Y-m-d','before_or_equal:'.now('Asia/Karachi')->toDateString()],'start_month'=>['required','date_format:Y-m'],'amount'=>['required','regex:/^\d{1,11}(\.\d{1,2})?$/','numeric','min:0.01'],'installment'=>['required','regex:/^\d{1,11}(\.\d{1,2})?$/','numeric','min:0.01','lte:amount'],'given_by'=>['required','string','max:255'],'notes'=>['nullable','string','max:2000'],'version'=>['required','string']]);$data['start_month'].='-01';if(substr($data['loan_date'],0,7)>substr($data['start_month'],0,7))throw \Illuminate\Validation\ValidationException::withMessages(['start_month'=>'Installments cannot precede loan month.']);
  $employeeId=DB::transaction(function()use($request,$id,$data){$initial=DB::table('worker_loans')->find($id);abort_unless($initial,404);Employee::whereKey($initial->employee_id)->lockForUpdate()->firstOrFail();$loan=DB::table('worker_loans')->where('id',$id)->lockForUpdate()->first();abort_unless($loan,404);if(!hash_equals(hash('sha256',json_encode($loan)),$data['version']))throw \Illuminate\Validation\ValidationException::withMessages(['loan'=>'Loan changed. Reload first.']);$start=min($loan->start_month,$data['start_month']);$this->protectPaid($loan->employee_id,$start);unset($data['version']);DB::table('worker_loans')->where('id',$id)->update($data+['updated_at'=>now()]);$this->reschedule($loan->employee_id,$start);$this->auditLoan($request,$id,'updated',$loan,DB::table('worker_loans')->find($id));return $loan->employee_id;});return redirect()->route('loan-workers.show',$employeeId)->with('success','Loan corrected. Unpaid installments and Funds recalculated.');
 }
 public function destroy(Request $request,int $id){
  abort_unless($request->user()->hasPermission('payrolls'),403);$request->validate(['version'=>['required','string']]);DB::transaction(function()use($request,$id){$initial=DB::table('worker_loans')->find($id);abort_unless($initial,404);Employee::whereKey($initial->employee_id)->lockForUpdate()->firstOrFail();$loan=DB::table('worker_loans')->where('id',$id)->lockForUpdate()->first();abort_unless($loan,404);if(!hash_equals(hash('sha256',json_encode($loan)),$request->input('version')))throw \Illuminate\Validation\ValidationException::withMessages(['loan'=>'Loan changed. Reload first.']);$this->protectPaid($loan->employee_id,$loan->start_month);DB::table('worker_loan_installments')->where('loan_id',$id)->delete();DB::table('worker_loans')->where('id',$id)->delete();$this->reschedule($loan->employee_id,$loan->start_month);$this->auditLoan($request,$id,'deleted',$loan,null);});return redirect()->route('loan-workers.index')->with('success','Mistaken loan deleted. Unpaid salary deductions and Funds recalculated.');
 }
 private function protectPaid(int $employeeId,string $start):void {if(SalaryManagementRow::where('employee_id',$employeeId)->where('month','>=',$start)->where('paid_amount','>',0)->exists())throw \Illuminate\Validation\ValidationException::withMessages(['loan'=>'A salary in this loan period already has a payment. Reverse that payment to Unpaid before correcting this loan; paid records are protected.']);}
 private function reschedule(int $employeeId,string $start):void {$rows=SalaryManagementRow::where('employee_id',$employeeId)->where('month','>=',$start)->orderBy('month')->lockForUpdate()->get();DB::table('worker_loan_installments')->whereIn('salary_row_id',$rows->pluck('id'))->delete();foreach($rows as $row){$schedule=(new WorkerLoans)->schedule($employeeId,$row->month->toDateString(),$row->id);$row->update(['loan_balance'=>$schedule['loan_balance'],'loan_deduction'=>$schedule['loan_deduction']]);(new WorkerLoans)->sync($row,$schedule);}}
 private function auditLoan(Request $request,int $id,string $action,$before,$after):void{DB::table('fund_activity')->insert(['source'=>'worker_loans','source_id'=>$id,'user_id'=>$request->user()->id,'actor'=>$request->user()->name,'action'=>$action,'before'=>$before?json_encode($before):null,'after'=>$after?json_encode($after):null,'created_at'=>now()]);}
 public function show(Request $request, Employee $employee) {
  abort_unless($request->user()->hasPermission('payrolls'),403);
  $records=SalaryManagementRow::with('advances')->where('employee_id',$employee->id)->where(function($q){$q->where('loan_balance','>',0)->orWhere('loan_deduction','>',0);})->orderByDesc('month')->get();
  $loans=(new WorkerLoans)->details($employee->id);
  abort_if($records->isEmpty() && $loans->isEmpty(),404);
  return view('loan-workers.show',compact('employee','records','loans'));
 }
}
