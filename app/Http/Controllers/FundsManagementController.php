<?php
namespace App\Http\Controllers;
use App\Services\FundsLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
class FundsManagementController extends Controller
{
    public function index(Request $request, FundsLedger $service)
    {
        abort_unless($request->user()->hasPermission('owner_funds'),403);
        $today=now('Asia/Karachi');
        $data=$request->validate(['month'=>['nullable','date_format:Y-m']]);
        $month=Carbon::createFromFormat('!Y-m',$data['month']??$request->session()->get('funds_management_month',$today->format('Y-m')))->startOfMonth();
        abort_if($month->format('Y-m')>$today->format('Y-m'),422,'Select the current or a previous month.');
        $request->session()->put('funds_management_month',$month->format('Y-m'));
        [$report,$warnings]=$this->buildReport($service,$today,$month);
        $returnRecords=DB::table('fund_returns')->get()->keyBy('id');
        $receiptRecords=\App\Models\FundReceipt::all()->keyBy('id');
        $revision=hash('sha256',json_encode($report));
        if($request->expectsJson())return response()->json(['revision'=>$revision]);
        $accessUsers=\App\Models\User::with('permissions')->get()->filter(fn($u)=>$u->hasPermission('owner_funds'));
        $editReceipt=$request->filled('edit') ? \App\Models\FundReceipt::findOrFail($request->validate(['edit'=>['required','integer','min:1']])['edit']) : null;
        $activity=DB::table('fund_activity')->orderByDesc('id')->paginate(30)->withQueryString();
        return view('funds-management.index',compact('month','report','warnings','accessUsers','activity','editReceipt','revision','returnRecords','receiptRecords'));
    }
    private function buildReport(FundsLedger $service, Carbon $today, Carbon $month): array
    {
        return DB::transaction(function() use($service,$today,$month){
            $entries=$service->entries();
            $report=$service->month($entries,$month,$today);
            $report['all_balance']=0;
            foreach($entries as $entry)if($entry['date']<=$today->toDateString())$report['all_balance']+=$entry['in']-$entry['out'];
            $report['returned']=0;
            foreach($report['ledger'] as $entry) if($entry['type']==='Cash returned to boss') $report['returned']+=$entry['out'];
            $report['spent']-=$report['returned'];
            unset($report['categories']['Cash returned to boss']);
            $weeks=[];$carry=$report['opening'];
            for($w=1;$w<=ceil($month->daysInMonth/7);$w++) {
                $start=$month->copy()->day(($w-1)*7+1)->toDateString();
                $end=$month->copy()->day(min($w*7,$month->daysInMonth))->toDateString();
                $rows=array_values(array_filter($report['ledger'],fn($e)=>$e['date']>=$start&&$e['date']<=$end));
                $incoming=0;$spent=0;$returned=0;
                foreach($rows as $e){$incoming+=$e['in'];if($e['type']==='Cash returned to boss')$returned+=$e['out'];else $spent+=$e['out'];}
                $weeks[]=['number'=>$w,'start'=>$start,'end'=>$end,'opening'=>$carry,'received'=>$incoming,'available'=>$carry+$incoming,'spent'=>$spent,'returned'=>$returned,'closing'=>$carry+$incoming-$spent-$returned,'ledger'=>$rows,'future'=>$start>$today->toDateString()];
                $carry+=$incoming-$spent-$returned;
            }
            $report['weeks']=$weeks;
            return [$report,$service->review($report['ledger'])];
        });
    }

    public function print(Request $request, FundsLedger $service)
    {
        abort_unless($request->user()->hasPermission('owner_funds'),403);
        $data=$request->validate(['month'=>['required','date_format:Y-m'],'week'=>['nullable','integer','min:1','max:5']]);
        $today=now('Asia/Karachi');
        $month=Carbon::createFromFormat('!Y-m',$data['month'])->startOfMonth();
        abort_if($month->format('Y-m')>$today->format('Y-m'),422,'Select the current or a previous month.');
        [$monthly,$warnings]=$this->buildReport($service,$today,$month);
        $week=$data['week']??null;
        if($week){
            abort_if($week>count($monthly['weeks']),422,'This month does not have that week.');
            $report=$monthly['weeks'][$week-1];
            if(!$report['future'])$report['end']=min($report['end'],$today->toDateString());
        } else {
            $report=$monthly;$report['start']=$month->toDateString();
        }
        $breakdown=[];
        foreach($report['ledger'] as $entry)if($entry['out']>0)$breakdown[$entry['type']]=($breakdown[$entry['type']]??0)+$entry['out'];
        $warnings=$service->review($report['ledger']);
        return view('funds-management.print',compact('month','week','report','breakdown','warnings'));
    }

    public function returnCash(Request $request)
    {
        abort_unless($request->user()->hasPermission('owner_funds'),403);
        $data=$request->validate(['returned_by'=>['required','string','max:255'],'return_date'=>['required','date_format:Y-m-d','before_or_equal:today'],'boss'=>['required','in:Boss Azeem,Boss Atif,Boss Kashif'],'amount'=>['required','regex:/^\d{1,13}(\.\d{1,2})?$/','numeric','min:0.01'],'notes'=>['nullable','string','max:2000'],'submission_key'=>['required','uuid']]);
        DB::transaction(function() use($data,$request){
            // Serialize duplicate submissions against the current authenticated user.
            DB::table('users')->where('id',$request->user()->id)->lockForUpdate()->first();
            if(DB::table('fund_returns')->where('submission_key',$data['submission_key'])->exists())return;
            $id=DB::table('fund_returns')->insertGetId($data+['created_by'=>$request->user()->id,'created_at'=>now(),'updated_at'=>now()]);
            DB::table('fund_activity')->insert(['source'=>'fund_returns','source_id'=>$id,'user_id'=>$request->user()->id,'actor'=>$request->user()->name,'action'=>'created','before'=>null,'after'=>json_encode($data),'created_at'=>now()]);
        });
        return redirect()->route('funds-management.index',['month'=>$request->session()->get('funds_management_month',substr($data['return_date'],0,7))])->with('success','Cash return recorded. Remaining balance updated.');
    }
    public function updateReturn(Request $request,int $id){
        abort_unless($request->user()->hasPermission('owner_funds'),403);
        $data=$request->validate(['return_date'=>['required','date_format:Y-m-d','before_or_equal:'.now('Asia/Karachi')->toDateString()],'boss'=>['required','in:Boss Azeem,Boss Atif,Boss Kashif'],'returned_by'=>['required','string','max:255'],'amount'=>['required','regex:/^\d{1,13}(\.\d{1,2})?$/','numeric','min:0.01'],'notes'=>['nullable','string','max:2000'],'version'=>['required','string']]);
        DB::transaction(function()use($request,$id,$data){$row=DB::table('fund_returns')->where('id',$id)->lockForUpdate()->first();abort_unless($row,404);if(!hash_equals(hash('sha256',json_encode($row)),$data['version']))throw \Illuminate\Validation\ValidationException::withMessages(['return'=>'Record changed. Reload before editing.']);unset($data['version']);DB::table('fund_returns')->where('id',$id)->update($data+['updated_at'=>now()]);$this->cashAudit($request,'fund_returns',$id,'updated',$row,DB::table('fund_returns')->find($id));});return back()->with('success','Cash return corrected. Funds recalculated.');
    }
    public function destroyReturn(Request $request,int $id){
        abort_unless($request->user()->hasPermission('owner_funds'),403);$request->validate(['version'=>['required','string']]);
        DB::transaction(function()use($request,$id){$row=DB::table('fund_returns')->where('id',$id)->lockForUpdate()->first();abort_unless($row,404);if(!hash_equals(hash('sha256',json_encode($row)),$request->input('version')))throw \Illuminate\Validation\ValidationException::withMessages(['return'=>'Record changed. Reload before deleting.']);DB::table('fund_returns')->where('id',$id)->delete();$this->cashAudit($request,'fund_returns',$id,'deleted',$row,null);});return back()->with('success','Mistaken cash return deleted. Funds recalculated.');
    }
    public function destroyReceipt(Request $request,\App\Models\FundReceipt $receipt){
        abort_unless($request->user()->hasPermission('owner_funds'),403);$request->validate(['version'=>['required','string']]);DB::transaction(function()use($request,$receipt){$row=\App\Models\FundReceipt::whereKey($receipt->id)->lockForUpdate()->firstOrFail();if(!hash_equals(hash('sha256',json_encode($row->getAttributes())),$request->input('version')))throw \Illuminate\Validation\ValidationException::withMessages(['receipt'=>'Record changed. Reload before deleting.']);$row->delete();});return back()->with('success','Mistaken receipt removed. Funds recalculated; photo retained for audit history.');
    }
    private function cashAudit(Request $request,string $source,int $id,string $action,$before,$after):void{
        DB::table('fund_activity')->insert(['source'=>$source,'source_id'=>$id,'user_id'=>$request->user()->id,'actor'=>$request->user()->name,'action'=>$action,'before'=>$before?json_encode($before):null,'after'=>$after?json_encode($after):null,'created_at'=>now()]);
    }
    private function receiptData(Request $request, bool $editing=false): array
    {
        return $request->validate([
            'receipt_date'=>['required','date_format:Y-m-d','before_or_equal:today'],
            'boss'=>['required','in:Boss Azeem,Boss Atif,Boss Kashif'],
            'amount'=>['required','regex:/^\d{1,13}(\.\d{1,2})?$/','numeric','min:0.01'],
            'method'=>['required','in:cash,bank'], 'notes'=>['nullable','string','max:2000'],
            'receiver_name'=>['required','string','max:150'],
            'receiver_photo'=>[$editing?'nullable':'required','image','mimes:jpg,jpeg,png,webp','max:5120'],
            'submission_key'=>[$editing?'nullable':'required','uuid'],
        ]);
    }
    public function store(Request $request)
    {
        abort_unless($request->user()->hasPermission('owner_funds'),403);
        $data=$this->receiptData($request);
        $existing=\App\Models\FundReceipt::where('submission_key',$data['submission_key'])->first();
        if($existing) return redirect()->route('funds-management.index',['month'=>$request->session()->get('funds_management_month',now('Asia/Karachi')->format('Y-m'))])->with('success','Receipt already saved.');
        $path=$request->file('receiver_photo')->store('funds/receivers','public');
        $data['receiver_photo']=$path;
        try {
            $receipt=DB::transaction(fn()=>\App\Models\FundReceipt::firstOrCreate(['submission_key'=>$data['submission_key']],$data+['created_by'=>$request->user()->id]));
            if($receipt->receiver_photo!==$path) \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
        } catch(\Throwable $e) { \Illuminate\Support\Facades\Storage::disk('public')->delete($path); throw $e; }
        return redirect()->route('funds-management.index',['month'=>$request->session()->get('funds_management_month',now('Asia/Karachi')->format('Y-m'))])->with('success','Cash receipt saved with receiver and photo.');
    }
    public function update(Request $request, \App\Models\FundReceipt $receipt)
    {
        abort_unless($request->user()->hasPermission('owner_funds'),403);
        $data=$this->receiptData($request,true);
        $request->validate(['version'=>['required','string']]);
        unset($data['submission_key'],$data['receiver_photo']);
        $path=$request->hasFile('receiver_photo') ? $request->file('receiver_photo')->store('funds/receivers','public') : null;
        try {
            DB::transaction(function() use($request,$receipt,$data,$path) {
                $locked=\App\Models\FundReceipt::lockForUpdate()->findOrFail($receipt->id);
                if(!hash_equals(hash('sha256',json_encode($locked->getAttributes())),$request->input('version'))) {
                    throw \Illuminate\Validation\ValidationException::withMessages(['receipt'=>'Receipt changed by another user. Reload before editing.']);
                }
                $locked->update($data+($path?['receiver_photo'=>$path]:[]));
            });
        } catch(\Throwable $e) { if($path) \Illuminate\Support\Facades\Storage::disk('public')->delete($path); throw $e; }
        // Retain earlier photos: activity history still references them.
        return redirect()->route('funds-management.index',['month'=>$request->session()->get('funds_management_month',now('Asia/Karachi')->format('Y-m'))])->with('success','Receipt corrected. Balance updated and change recorded.');
    }
}
