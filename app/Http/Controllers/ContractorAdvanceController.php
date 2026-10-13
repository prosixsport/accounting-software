<?php
namespace App\Http\Controllers;
use App\Models\Contractor;
use App\Models\ContractorAdvance;
use App\Services\ContractorAdvanceLedger;
use App\Services\FundsLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class ContractorAdvanceController extends Controller {
 public function index(Request $request){
  abort_unless($request->user()->hasPermission('contractor_bills'),403);
  $contractors=Contractor::with('department')->orderBy('name')->get();
  $balances=$contractors->mapWithKeys(fn($c)=>[$c->id=>(new ContractorAdvanceLedger)->available($c->id)]);
  $advances=ContractorAdvance::with('contractor')->orderByDesc('advance_date')->orderByDesc('id')->get();
  $used=DB::table('contractor_advance_allocations')->get()->groupBy('contractor_advance_id')->map(fn($rows)=>$rows->sum('amount'));
  $allocations=DB::table('contractor_advance_allocations')->join('contractor_bills','contractor_bills.id','=','contractor_advance_allocations.contractor_bill_id')->select('contractor_advance_allocations.*','contractor_bills.bill_no','contractor_bills.bill_date')->get()->groupBy('contractor_advance_id');
  return view('contractor-advances.index',compact('contractors','balances','advances','used','allocations'));
 }
 private function data(Request $request,bool $edit=false):array {return $request->validate(['contractor_id'=>['required','integer','exists:contractors,id'],'advance_date'=>['required','date_format:Y-m-d','before_or_equal:'.now('Asia/Karachi')->toDateString()],'amount'=>['required','regex:/^\d{1,11}(\.\d{1,2})?$/','numeric','min:0.01'],'remarks'=>['nullable','string','max:1000'],'submission_key'=>[$edit?'nullable':'required','uuid']]);}
 public function store(Request $request){abort_unless($request->user()->hasPermission('contractor_bills'),403);$data=$this->data($request);DB::transaction(function()use($data,$request){Contractor::whereKey($data['contractor_id'])->lockForUpdate()->firstOrFail();ContractorAdvance::firstOrCreate(['submission_key'=>$data['submission_key']],$data+['eligible_for_bills'=>true,'created_by'=>$request->user()->id,'advance_time'=>now('Asia/Karachi')->format('H:i:s')]);});return back()->with('success','Contractor advance saved. Funds balance updated.');}
 public function verify(Request $request,ContractorAdvance $advance){
  abort_unless($request->user()->hasPermission('contractor_bills'),403);$data=$request->validate(['unused_amount'=>['required','numeric','min:0','decimal:0,2','max:99999999999.99'],'version'=>['required','string']]);
  DB::transaction(function()use($data,$advance){Contractor::whereKey($advance->contractor_id)->lockForUpdate()->firstOrFail();$record=ContractorAdvance::whereKey($advance->id)->lockForUpdate()->firstOrFail();if(!hash_equals(hash('sha256',json_encode($record->getAttributes())),$data['version']))throw ValidationException::withMessages(['unused_amount'=>'Record changed; reload before verifying.']);if($record->eligible_for_bills||DB::table('contractor_advance_allocations')->where('contractor_advance_id',$record->id)->exists())throw ValidationException::withMessages(['unused_amount'=>'This advance is already verified or bill linked.']);if((float)$data['unused_amount']>(float)$record->amount)throw ValidationException::withMessages(['unused_amount'=>'Unused amount exceeds original cash advance.']);$record->update(['eligible_for_bills'=>true,'allocation_limit'=>$data['unused_amount']]);});return back()->with('success','Legacy unused advance verified without creating a new cash payout.');
 }
 public function update(Request $request,ContractorAdvance $advance){
  abort_unless($request->user()->hasPermission('contractor_bills'),403);$data=$this->data($request,true);$request->validate(['version'=>['required','string']]);unset($data['submission_key']);
  DB::transaction(function()use($advance,$data,$request){Contractor::whereIn('id',[$advance->contractor_id,$data['contractor_id']])->orderBy('id')->lockForUpdate()->get();$record=ContractorAdvance::whereKey($advance->id)->lockForUpdate()->firstOrFail();if(!hash_equals(hash('sha256',json_encode($record->getAttributes())),$request->input('version')))throw ValidationException::withMessages(['amount'=>'Record changed elsewhere; reload before editing.']);if($record->allocation_limit!==null || DB::table('contractor_advance_allocations')->where('contractor_advance_id',$record->id)->exists())throw ValidationException::withMessages(['amount'=>'This advance is already adjusted against a bill. It cannot be edited here.']);$record->update($data);});return back()->with('success','Advance corrected; Funds updated.');
 }
}
