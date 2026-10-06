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
        $today=now();
        $month=$today->copy()->startOfMonth();
        [$report,$warnings]=DB::transaction(function() use($service,$today){
            $entries=$service->entries();
            $report=['received'=>0,'spent'=>0,'closing'=>0,'bosses'=>[], 'categories'=>[], 'ledger'=>[], 'future'=>[]];
            foreach($entries as $entry) {
                if($entry['date']>$today->toDateString()) { $report['future'][]=$entry; continue; }
                $report['received']+=$entry['in']; $report['spent']+=$entry['out'];
                $report['closing']=$report['received']-$report['spent'];
                $entry['balance']=$report['closing']; $report['ledger'][]=$entry;
                if(in_array($entry['type'],['Boss funds','Legacy funds'],true)) $report['bosses'][$entry['party']]=($report['bosses'][$entry['party']]??0)+$entry['in'];
                if($entry['out']) $report['categories'][$entry['type']]=($report['categories'][$entry['type']]??0)+$entry['out'];
            }
            return [$report,$service->review($entries)];
        });
        $accessUsers=\App\Models\User::with('permissions')->get()->filter(fn($u)=>$u->hasPermission('owner_funds'));
        $editReceipt=$request->filled('edit') ? \App\Models\FundReceipt::findOrFail($request->validate(['edit'=>['required','integer','min:1']])['edit']) : null;
        $activity=DB::table('fund_activity')->orderByDesc('id')->paginate(30);
        return view('funds-management.index',compact('month','report','warnings','accessUsers','activity','editReceipt'));
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
        if($existing) return redirect()->route('funds-management.index')->with('success','Receipt already saved.');
        $path=$request->file('receiver_photo')->store('funds/receivers','public');
        $data['receiver_photo']=$path;
        try {
            $receipt=DB::transaction(fn()=>\App\Models\FundReceipt::firstOrCreate(['submission_key'=>$data['submission_key']],$data+['created_by'=>$request->user()->id]));
            if($receipt->receiver_photo!==$path) \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
        } catch(\Throwable $e) { \Illuminate\Support\Facades\Storage::disk('public')->delete($path); throw $e; }
        return redirect()->route('funds-management.index')->with('success','Cash receipt saved with receiver and photo.');
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
        return redirect()->route('funds-management.index')->with('success','Receipt corrected. Balance updated and change recorded.');
    }
}
