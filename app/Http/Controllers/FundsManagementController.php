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
        $data=$request->validate(['month'=>['nullable','date_format:Y-m']]);
        $month=Carbon::createFromFormat('!Y-m',$data['month']??now()->format('Y-m'))->startOfMonth();
        $today=now();
        [$report,$history,$warnings]=DB::transaction(function() use($service,$month,$today){
            $entries=$service->entries();$report=$service->month($entries,$month,$today);$history=[];
            for($i=11;$i>=0;$i--){$date=$month->copy()->subMonthsNoOverflow($i);$history[]=['month'=>$date->format('Y-m'),'label'=>$date->format('F Y')]+$service->month($entries,$date,$today);}
            return [$report,$history,$service->review($entries)];
        });
        $accessUsers=\App\Models\User::with('permissions')->get()->filter(fn($u)=>$u->hasPermission('owner_funds'));
        $activity=DB::table('fund_activity')->whereBetween('created_at',[$month->copy()->startOfMonth(),$month->copy()->endOfMonth()])->orderByDesc('id')->paginate(30);
        return view('funds-management.index',compact('month','report','history','warnings','accessUsers','activity'));
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
        return redirect()->route('funds-management.index',['month'=>substr($data['receipt_date'],0,7)])->with('success','Cash receipt saved.');
    }
}
