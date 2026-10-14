<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;use Illuminate\Support\Facades\DB;use App\Services\FundsLedger;use Illuminate\Validation\ValidationException;
class DirectLoanController extends Controller {
 public function index(Request $r){abort_unless($r->user()->hasPermission('payrolls'),403);$loans=DB::table('direct_loans')->orderByDesc('loan_date')->orderByDesc('id')->get();$payments=DB::table('direct_loan_repayments')->orderBy('payment_date')->orderBy('id')->get()->groupBy('direct_loan_id');foreach($loans as $loan){$loan->version=hash('sha256',json_encode($loan));$loan->paid=$payments->get($loan->id,collect())->sum(fn($p)=>FundsLedger::paise((string)$p->amount));$loan->remaining=FundsLedger::paise((string)$loan->amount)-$loan->paid;}return view('direct-loans.index',compact('loans','payments'));}
 public function store(Request $r){abort_unless($r->user()->hasPermission('payrolls'),403);$data=$r->validate(['person_name'=>['required','string','max:255'],'phone'=>['nullable','string','max:255'],'loan_date'=>['required','date_format:Y-m-d','before_or_equal:'.now('Asia/Karachi')->toDateString()],'amount'=>['required','regex:/^\d{1,11}(\.\d{1,2})?$/','numeric','min:0.01'],'given_by'=>['required','string','max:255'],'notes'=>['nullable','string','max:2000'],'submission_key'=>['required','uuid']]);DB::transaction(function()use($data,$r){DB::table('users')->where('id',$r->user()->id)->lockForUpdate()->first();if(DB::table('direct_loans')->where('submission_key',$data['submission_key'])->exists())return;$id=DB::table('direct_loans')->insertGetId($data+['created_by'=>$r->user()->id,'created_at'=>now(),'updated_at'=>now()]);$this->audit($r,'direct_loans',$id,$data);});return back()->with('success','Direct loan saved. Salary cutting disabled for this loan; cash deducted from Funds.');}
 public function repay(Request $r,int $id){abort_unless($r->user()->hasPermission('payrolls'),403);$data=$r->validate(['payment_date'=>['required','date_format:Y-m-d','before_or_equal:'.now('Asia/Karachi')->toDateString()],'amount'=>['required','regex:/^\d{1,11}(\.\d{1,2})?$/','numeric','min:0.01'],'received_by'=>['required','string','max:255'],'notes'=>['nullable','string','max:2000'],'submission_key'=>['required','uuid']]);DB::transaction(function()use($data,$r,$id){$loan=DB::table('direct_loans')->where('id',$id)->lockForUpdate()->first();abort_unless($loan,404);if(DB::table('direct_loan_repayments')->where('submission_key',$data['submission_key'])->exists())return;$paid=DB::table('direct_loan_repayments')->where('direct_loan_id',$id)->get()->sum(fn($p)=>FundsLedger::paise((string)$p->amount));if(FundsLedger::paise((string)$data['amount'])>FundsLedger::paise((string)$loan->amount)-$paid)throw ValidationException::withMessages(['amount'=>'Payment exceeds the remaining loan.']);if($data['payment_date']<$loan->loan_date)throw ValidationException::withMessages(['payment_date'=>'Repayment cannot precede loan date.']);$paymentId=DB::table('direct_loan_repayments')->insertGetId($data+['direct_loan_id'=>$id,'created_by'=>$r->user()->id,'created_at'=>now(),'updated_at'=>now()]);$this->audit($r,'direct_loan_repayments',$paymentId,$data+['direct_loan_id'=>$id]);});return back()->with('success','Repayment received. Funds increased and remaining loan reduced.');}
 public function update(Request $r,int $id){
  abort_unless($r->user()->hasPermission('payrolls'),403);
  $data=$r->validate(['person_name'=>'required|string|max:255','phone'=>'nullable|string|max:255','loan_date'=>['required','date_format:Y-m-d','before_or_equal:'.now('Asia/Karachi')->toDateString()],'amount'=>['required','regex:/^\d{1,11}(\.\d{1,2})?$/','numeric','min:0.01'],'given_by'=>'required|string|max:255','notes'=>'nullable|string|max:2000','version'=>'required|string']);
  DB::transaction(function()use($r,$id,$data){
   $loan=DB::table('direct_loans')->where('id',$id)->lockForUpdate()->first();abort_unless($loan,404);$this->checkVersion($r,$loan);
   $payments=DB::table('direct_loan_repayments')->where('direct_loan_id',$id)->get();
   $paid=$payments->sum(fn($p)=>FundsLedger::paise((string)$p->amount));
   if(FundsLedger::paise((string)$data['amount'])<$paid)throw ValidationException::withMessages(['amount'=>'Loan amount cannot be less than repayments already received.']);
   if($payments->contains(fn($p)=>$p->payment_date<$data['loan_date']))throw ValidationException::withMessages(['loan_date'=>'Loan date cannot be after an existing repayment.']);
   unset($data['version']);DB::table('direct_loans')->where('id',$id)->update($data+['updated_at'=>now()]);$this->changeAudit($r,'direct_loans',$id,'updated',(array)$loan,$data);
  });return back()->with('success','Loan updated. Funds and remaining balance recalculated.');
 }
 public function destroy(Request $r,int $id){
  abort_unless($r->user()->hasPermission('payrolls'),403);
  DB::transaction(function()use($r,$id){$loan=DB::table('direct_loans')->where('id',$id)->lockForUpdate()->first();abort_unless($loan,404);$this->checkVersion($r,$loan);
   if(DB::table('direct_loan_repayments')->where('direct_loan_id',$id)->exists())throw ValidationException::withMessages(['loan'=>'Delete incorrect repayment records first. A loan with repayments cannot be deleted.']);
   $this->changeAudit($r,'direct_loans',$id,'deleted',(array)$loan,null);DB::table('direct_loans')->where('id',$id)->delete();
  });return back()->with('success','Incorrect loan deleted and its Funds entry removed.');
 }
 public function destroyRepayment(Request $r,int $id){
  abort_unless($r->user()->hasPermission('payrolls'),403);
  $payment=DB::table('direct_loan_repayments')->where('id',$id)->first();abort_unless($payment,404);
  DB::transaction(function()use($r,$id,$payment){DB::table('direct_loans')->where('id',$payment->direct_loan_id)->lockForUpdate()->first();$record=DB::table('direct_loan_repayments')->where('id',$id)->lockForUpdate()->first();abort_unless($record,404);$this->checkVersion($r,$record);$this->changeAudit($r,'direct_loan_repayments',$id,'deleted',(array)$record,null);DB::table('direct_loan_repayments')->where('id',$id)->delete();});
  return back()->with('success','Incorrect repayment removed. Loan status and Funds updated.');
 }
 private function checkVersion(Request $r,object $row):void {if(!hash_equals(hash('sha256',json_encode($row)),(string)$r->input('version')))throw ValidationException::withMessages(['version'=>'This record changed. Refresh the page and try again.']);}
 private function changeAudit(Request $r,string $source,int $id,string $action,array $before,?array $after):void {DB::table('fund_activity')->insert(['source'=>$source,'source_id'=>$id,'user_id'=>$r->user()->id,'actor'=>$r->user()->name,'action'=>$action,'before'=>json_encode($before),'after'=>$after===null?null:json_encode($after),'created_at'=>now()]);}
 private function audit(Request $r,string $source,int $id,array $data):void {DB::table('fund_activity')->insert(['source'=>$source,'source_id'=>$id,'user_id'=>$r->user()->id,'actor'=>$r->user()->name,'action'=>'created','before'=>null,'after'=>json_encode($data),'created_at'=>now()]);}
}
