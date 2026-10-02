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
        $activity=DB::table('fund_activity')->orderByDesc('id')->paginate(30);
        return view('funds-management.index',compact('month','report','warnings','accessUsers','activity'));
    }
    public function store(Request $request)
    {
        abort_unless($request->user()->hasPermission('owner_funds'),403);
        $data=$request->validate([
            'receipt_date'=>['required','date_format:Y-m-d','before_or_equal:today'],
            'boss'=>['required','in:Boss Azeem,Boss Atif,Boss Kashif'],
            'amount'=>['required','regex:/^\d{1,13}(\.\d{1,2})?$/','numeric','min:0.01'],
            'method'=>['required','in:cash,bank'], 'notes'=>['nullable','string','max:2000'],
            'submission_key'=>['required','uuid'],
        ]);
        DB::transaction(function() use($data,$request) {
            \App\Models\FundReceipt::firstOrCreate(['submission_key'=>$data['submission_key']],$data+['created_by'=>$request->user()->id]);
        });
        return redirect()->route('funds-management.index',[])->with('success','Cash receipt saved.');
    }
}
