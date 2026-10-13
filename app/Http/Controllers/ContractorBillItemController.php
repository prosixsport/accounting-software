<?php
namespace App\Http\Controllers;
use App\Models\Contractor;
use App\Models\ContractorBill;
use App\Models\ContractorBillItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class ContractorBillItemController extends Controller {
 public function destroy(ContractorBillItem $contractorBillItem){
  DB::transaction(function()use($contractorBillItem){$id=$contractorBillItem->bill->contractor_id;Contractor::whereKey($id)->lockForUpdate()->firstOrFail();$bill=ContractorBill::whereKey($contractorBillItem->contractor_bill_id)->lockForUpdate()->firstOrFail();if((float)$bill->paid_amount>0 || (float)$bill->advance_adjusted>0)throw ValidationException::withMessages(['bill'=>'Items of a paid or advance-adjusted bill cannot be deleted.']);if($bill->items()->count()<=1)throw ValidationException::withMessages(['bill'=>'A bill must retain at least one item.']);$contractorBillItem->delete();$total=$bill->items()->sum('total');$bill->update(['grand_total'=>$total,'balance'=>$total,'status'=>'Pending']);});return back()->with('success','Item removed; unpaid bill total updated.');
 }
}
