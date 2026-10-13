<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class FundsTransactionController extends Controller {
 public function destroy(Request $request){
  abort_unless($request->user()->hasPermission('owner_funds'),403);
  $data=$request->validate(['month'=>['required','date_format:Y-m'],'entries'=>['required','array','min:1','max:1000'],'entries.*'=>['required','string','distinct']]);
  DB::transaction(function()use($data,$request){foreach($data['entries'] as $token){try{$t=json_decode(\Illuminate\Support\Facades\Crypt::decryptString($token),true,512,JSON_THROW_ON_ERROR);}catch(\Throwable $e){throw ValidationException::withMessages(['entries'=>'Invalid transaction selection. Reload.']);}$source=$t['source'];$id=$t['id'];$allowed=['fund_receipts','fund_returns','salary_management_advances','salary_management_rows','contractor_advances','contractor_bill_payments','expenses','expense_advances','worker_loans','payrolls','employee_advances','owner_funds','salary_cash_entries','payments'];if(!in_array($source,$allowed,true))throw ValidationException::withMessages(['entries'=>'Open the source module to correct this transaction.']);$record=DB::table($source)->where('id',$id)->lockForUpdate()->first();if(!$record||!hash_equals($t['version'],hash('sha256',json_encode($record))))throw ValidationException::withMessages(['entries'=>'A selected transaction changed. Reload before deleting.']);$r=clone $request;$r->replace(['version'=>hash('sha256',json_encode($record))]);
   switch($source){
    case 'fund_returns':app(FundsManagementController::class)->destroyReturn($r,$id);break;
    case 'fund_receipts':$m=\App\Models\FundReceipt::findOrFail($id);$r->merge(['version'=>hash('sha256',json_encode($m->getAttributes()))]);app(FundsManagementController::class)->destroyReceipt($r,$m);break;
    case 'worker_loans':app(LoanWorkerController::class)->destroy($r,$id);break;
    case 'expense_advances':$m=\App\Models\ExpenseAdvance::findOrFail($id);$r->merge(['version'=>hash('sha256',json_encode($m->getAttributes()))]);app(ExpenseAdvanceController::class)->destroy($r,$m);break;
    case 'contractor_advances':$m=\App\Models\ContractorAdvance::findOrFail($id);$r->merge(['version'=>hash('sha256',json_encode($m->getAttributes()))]);app(ContractorAdvanceController::class)->destroy($r,$m);break;
    case 'salary_management_advances':app(SalaryManagementController::class)->deleteAdvance($r,\App\Models\SalaryManagementAdvance::findOrFail($id));break;
    case 'salary_management_rows':$this->permission($request,'payrolls');$m=\App\Models\SalaryManagementRow::findOrFail($id);$r->replace(['month'=>$m->month->format('Y-m'),'employee_id'=>$m->employee_id,'payment_status'=>'unpaid']);app(SalaryManagementController::class)->paymentStatus($r);break;
    case 'contractor_bill_payments':$this->permission($request,'contractor_bills');app(ContractorBillController::class)->destroyPayment(\App\Models\ContractorBillPayment::findOrFail($id));break;
    case 'expenses':$this->permission($request,'expenses');app(ExpenseController::class)->destroy($r,\App\Models\Expense::findOrFail($id));break;
    case 'payrolls':$this->permission($request,'payrolls');$m=\App\Models\Payroll::findOrFail($id);$m->update(['payment_status'=>'pending','payment_date'=>null]);break;
    case 'employee_advances':$this->permission($request,'payrolls');throw ValidationException::withMessages(['entries'=>'Legacy employee advance: correct it in Employees Payroll first to keep its salary totals synchronized.']);
    case 'payments':$this->permission($request,'payments');app(PaymentController::class)->destroy(\App\Models\Payment::findOrFail($id));break;
    default:$this->audit($request,$source,$id,$record);DB::table($source)->where('id',$id)->delete();
   }
  }},3);
  return redirect()->route('funds-management.index',['month'=>$data['month']])->with('success','Selected transactions corrected. Salary payments were marked Unpaid; Funds recalculated.');
 }
 private function permission(Request $r,string $key):void {abort_unless($r->user()->hasPermission($key),403);}
 private function audit(Request $r,string $source,int $id,$before):void {DB::table('fund_activity')->insert(['source'=>$source,'source_id'=>$id,'user_id'=>$r->user()->id,'actor'=>$r->user()->name,'action'=>'deleted','before'=>json_encode($before),'after'=>null,'created_at'=>now()]);}
 public function resetSalary(Request $request){$this->permission($request,'payrolls');$data=$request->validate(['employee_id'=>['required','integer','exists:employees,id'],'month'=>['required','date_format:Y-m']]);DB::transaction(function()use($request,$data){\App\Models\Employee::whereKey($data['employee_id'])->lockForUpdate()->firstOrFail();$row=\App\Models\SalaryManagementRow::where('employee_id',$data['employee_id'])->whereDate('month',$data['month'].'-01')->lockForUpdate()->firstOrFail();foreach($row->advances()->get() as $a)$a->delete();DB::table('worker_loan_installments')->where('salary_row_id',$row->id)->delete();$row->delete();});return redirect()->route('salary-management.index',['month'=>$data['month']])->with('success','Salary reset to Not Saved. Advances and final salary payment removed; loan issue itself remains recorded.');}
 public function resetLegacyLoan(Request $request,int $employee){$this->permission($request,'payrolls');DB::transaction(function()use($employee){\App\Models\Employee::whereKey($employee)->lockForUpdate()->firstOrFail();if(DB::table('worker_loans')->where('employee_id',$employee)->exists())throw ValidationException::withMessages(['loan'=>'This worker has issued loans. Edit/delete those individually.']);$rows=\App\Models\SalaryManagementRow::where('employee_id',$employee)->where(function($q){$q->where('loan_balance','>',0)->orWhere('loan_deduction','>',0);})->lockForUpdate()->get();if($rows->contains(fn($r)=>(float)$r->paid_amount>0))throw ValidationException::withMessages(['loan'=>'Mark affected salaries Unpaid before clearing legacy loan fields.']);foreach($rows as $r)$r->update(['loan_balance'=>0,'loan_deduction'=>0]);});return redirect()->route('loan-workers.index')->with('success','Legacy loan fields cleared to zero. Salary details remain saved.');}
}
