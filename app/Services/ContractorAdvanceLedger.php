<?php
namespace App\Services;
use App\Models\ContractorAdvance;
use App\Models\ContractorBill;
use Illuminate\Support\Facades\DB;
class ContractorAdvanceLedger {
 public function available(int $contractorId,?string $through=null):int {
  $query=ContractorAdvance::where('contractor_id',$contractorId)->where('eligible_for_bills',true);if($through)$query->whereDate('advance_date','<=',$through);
  $total=0;foreach($query->get() as $advance){$used=0;foreach(DB::table('contractor_advance_allocations')->where('contractor_advance_id',$advance->id)->pluck('amount') as $amount)$used+=FundsLedger::paise((string)$amount);$total+=max(0,FundsLedger::paise((string)($advance->allocation_limit??$advance->amount))-$used);}return $total;
 }
 // Caller holds contractor lock; FIFO by actual date, capped at bill total.
 public function allocate(ContractorBill $bill):int {
  $remaining=FundsLedger::paise((string)$bill->grand_total);$adjusted=0;
  foreach(ContractorAdvance::where('contractor_id',$bill->contractor_id)->where('eligible_for_bills',true)->whereDate('advance_date','<=',$bill->bill_date->toDateString())->orderBy('advance_date')->orderBy('id')->lockForUpdate()->get() as $advance){
   $used=0;foreach(DB::table('contractor_advance_allocations')->where('contractor_advance_id',$advance->id)->pluck('amount') as $amount)$used+=FundsLedger::paise((string)$amount);
   $amount=min($remaining,max(0,FundsLedger::paise((string)($advance->allocation_limit??$advance->amount))-$used));if($amount<=0)continue;
   DB::table('contractor_advance_allocations')->insert(['contractor_advance_id'=>$advance->id,'contractor_bill_id'=>$bill->id,'amount'=>number_format($amount/100,2,'.',''),'created_at'=>now(),'updated_at'=>now()]);$adjusted+=$amount;$remaining-=$amount;if($remaining<=0)break;
  }return $adjusted;
 }
}
