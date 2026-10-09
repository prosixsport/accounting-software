<?php
namespace App\Services;
use App\Models\SalaryManagementRow;
use Illuminate\Support\Facades\DB;
class WorkerLoans {
 // The caller locks the employee row before changing salary or creating a loan.
 public function schedule(int $employeeId,string $month,?int $rowId=null):array {
  $loans=DB::table('worker_loans')->where('employee_id',$employeeId)->where('start_month','<=',$month)->orderBy('loan_date')->orderBy('id')->get();
  $balance=0;$deduction=0;$allocations=[];
  foreach($loans as $loan){
   $query=DB::table('worker_loan_installments')->where('loan_id',$loan->id);
   if($rowId)$query->where('salary_row_id','!=',$rowId);
   $booked=0;foreach($query->pluck('amount') as $v)$booked+=FundsLedger::paise((string)$v);
   $remaining=max(0,FundsLedger::paise((string)$loan->amount)-$booked);
   $amount=min($remaining,FundsLedger::paise((string)$loan->installment));
   $prior=0;foreach(DB::table('worker_loan_installments')->join('salary_management_rows','salary_management_rows.id','=','worker_loan_installments.salary_row_id')->where('loan_id',$loan->id)->where('salary_management_rows.month','<',$month)->pluck('worker_loan_installments.amount') as $v)$prior+=FundsLedger::paise((string)$v);
   $balance+=max(0,FundsLedger::paise((string)$loan->amount)-$prior);$deduction+=$amount;
   if($amount>0)$allocations[$loan->id]=number_format($amount/100,2,'.','');
  }
  return ['managed'=>$loans->isNotEmpty(),'loan_balance'=>number_format($balance/100,2,'.',''),'loan_deduction'=>number_format($deduction/100,2,'.',''),'allocations'=>$allocations];
 }
 public function sync(SalaryManagementRow $row,array $schedule):void {
  DB::table('worker_loan_installments')->where('salary_row_id',$row->id)->delete();
  foreach($schedule['allocations'] as $loanId=>$amount)DB::table('worker_loan_installments')->insert(['loan_id'=>$loanId,'salary_row_id'=>$row->id,'amount'=>$amount,'created_at'=>now(),'updated_at'=>now()]);
 }
 public function details(int $employeeId) {
  return DB::table('worker_loans')->where('employee_id',$employeeId)->orderByDesc('loan_date')->get()->map(function($loan){
   $loan->payments=DB::table('worker_loan_installments')->join('salary_management_rows','salary_management_rows.id','=','worker_loan_installments.salary_row_id')->where('loan_id',$loan->id)->select('worker_loan_installments.*','salary_management_rows.month','salary_management_rows.payment_date')->orderBy('month')->get();
   $recovered=0;$reserved=0;
   foreach($loan->payments as $payment){$row=SalaryManagementRow::with('advances')->find($payment->salary_row_id);$f=$row->figures();$payable=round($f['net']+(float)$row->overdue,2);$payment->recovered=$payable>0&&(float)$row->paid_amount>=$payable;$v=FundsLedger::paise((string)$payment->amount);$reserved+=$v;if($payment->recovered)$recovered+=$v;}
   $loan->recovered_amount=$recovered/100;$loan->remaining_amount=(FundsLedger::paise((string)$loan->amount)-$recovered)/100;$loan->reserved_amount=($reserved-$recovered)/100;return $loan;
  });
 }
}
