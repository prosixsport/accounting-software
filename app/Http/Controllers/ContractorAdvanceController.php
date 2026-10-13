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
  $selection=$request->validate(['month'=>['nullable','date_format:Y-m'],'week'=>['nullable','integer','min:1','max:5']]);
  $month=\Illuminate\Support\Carbon::createFromFormat('!Y-m',$selection['month']??$request->session()->get('contractor_advances_month',now('Asia/Karachi')->format('Y-m')))->startOfMonth();
  $request->session()->put('contractor_advances_month',$month->format('Y-m'));
  $week=$selection['week']??null;
  abort_if($week && $week>(int)ceil($month->daysInMonth/7),422,'This month does not have that week.');
  $contractors=Contractor::with('department')->orderBy('name')->get();
  $balances=$contractors->mapWithKeys(fn($c)=>[$c->id=>(new ContractorAdvanceLedger)->available($c->id)]);
  $allAdvances=ContractorAdvance::with('contractor')->orderBy('advance_date')->orderBy('id')->get();
  $advances=$allAdvances->filter(fn($a)=>$a->advance_date->format('Y-m')===$month->format('Y-m'))->values();
  $used=DB::table('contractor_advance_allocations')->get()->groupBy('contractor_advance_id')->map(fn($rows)=>$rows->sum('amount'));
  $allocations=DB::table('contractor_advance_allocations')->join('contractor_bills','contractor_bills.id','=','contractor_advance_allocations.contractor_bill_id')->select('contractor_advance_allocations.*','contractor_bills.bill_no','contractor_bills.bill_date')->get()->groupBy('contractor_advance_id');
  return view('contractor-advances.index',compact('contractors','balances','advances','used','allocations','month','week','allAdvances'));
 }
 public function batch(Request $request){
  abort_unless($request->user()->hasPermission('contractor_bills'),403);
  $data=$request->validate(['contractor_id'=>['required','integer','exists:contractors,id'],'month'=>['required','date_format:Y-m'],'entries'=>['required','array','min:1','max:100'],'entries.*.week'=>['required','integer','min:1','max:5'],'entries.*.advance_date'=>['required','date_format:Y-m-d','before_or_equal:'.now('Asia/Karachi')->toDateString()],'entries.*.amount'=>['required','regex:/^\d{1,11}(\.\d{1,2})?$/','numeric','min:0.01'],'entries.*.remarks'=>['nullable','string','max:1000'],'entries.*.submission_key'=>['required','uuid','distinct']]);
  foreach($data['entries'] as $entry){$date=\Illuminate\Support\Carbon::parse($entry['advance_date']);if($date->format('Y-m')!==$data['month']||intdiv($date->day-1,7)+1!=(int)$entry['week'])throw ValidationException::withMessages(['entries'=>'Payment date must match the selected month and week.']);}
  DB::transaction(function()use($data,$request){Contractor::whereKey($data['contractor_id'])->lockForUpdate()->firstOrFail();foreach($data['entries'] as $entry){$existing=ContractorAdvance::where('submission_key',$entry['submission_key'])->first();if($existing){if($existing->contractor_id!=$data['contractor_id']||$existing->advance_date->toDateString()!==$entry['advance_date']||FundsLedger::paise((string)$existing->amount)!==FundsLedger::paise((string)$entry['amount'])||($existing->remarks??'')!==($entry['remarks']??''))throw ValidationException::withMessages(['entries'=>'A saved entry changed. Reload and edit its saved record.']);continue;}unset($entry['week']);ContractorAdvance::create($entry+['contractor_id'=>$data['contractor_id'],'eligible_for_bills'=>true,'created_by'=>$request->user()->id,'advance_time'=>now('Asia/Karachi')->format('H:i:s')]);}});
  return redirect()->route('contractor-advances.index',['month'=>$data['month']])->with('success','All queued advances saved. Funds updated once for each payment.');
 }
 public function destroy(Request $request,ContractorAdvance $advance){
  abort_unless($request->user()->hasPermission('contractor_bills'),403);$request->validate(['version'=>['required','string']]);
  DB::transaction(function()use($advance,$request){Contractor::whereKey($advance->contractor_id)->lockForUpdate()->firstOrFail();$row=ContractorAdvance::whereKey($advance->id)->lockForUpdate()->firstOrFail();if(!hash_equals(hash('sha256',json_encode($row->getAttributes())),$request->input('version')))throw ValidationException::withMessages(['advance'=>'Record changed. Reload before deleting.']);if($row->allocation_limit!==null||DB::table('contractor_advance_allocations')->where('contractor_advance_id',$row->id)->exists())throw ValidationException::withMessages(['advance'=>'Verified legacy or bill-linked advances cannot be deleted here.']);$row->delete();});return back()->with('success','Mistaken advance deleted. Funds balance recalculated.');
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
