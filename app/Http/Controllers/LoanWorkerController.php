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
 public function show(Request $request, Employee $employee) {
  abort_unless($request->user()->hasPermission('payrolls'),403);
  $records=SalaryManagementRow::with('advances')->where('employee_id',$employee->id)->where(function($q){$q->where('loan_balance','>',0)->orWhere('loan_deduction','>',0);})->orderByDesc('month')->get();
  $loans=(new WorkerLoans)->details($employee->id);
  abort_if($records->isEmpty() && $loans->isEmpty(),404);
  return view('loan-workers.show',compact('employee','records','loans'));
 }
}
