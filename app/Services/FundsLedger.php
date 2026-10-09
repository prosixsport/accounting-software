<?php
namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;

class FundsLedger
{
    // Money stays in integer paise throughout aggregation, never binary floats.
    public static function paise(string $amount): int
    {
        if (!preg_match('/^(-?)(\d+)(?:\.(\d{1,2}))?$/D', $amount, $parts)) {
            throw new InvalidArgumentException('Invalid money value: '.$amount);
        }
        return ($parts[1] === '-' ? -1 : 1) * ((int)$parts[2] * 100 + (int)str_pad($parts[3] ?? '', 2, '0'));
    }

    public static function money(int $paise): string
    {
        return ($paise < 0 ? '-' : '').number_format(intdiv(abs($paise), 100)).'.'.str_pad((string)(abs($paise) % 100), 2, '0', STR_PAD_LEFT);
    }

    public function entries(): array
    {
        $entries = [];
        $add = function ($source, $id, $date, $type, $party, $amount, $incoming, $description = '', $method = 'Unspecified') use (&$entries) {
            $amount = self::paise((string)$amount);
            if ($amount === 0) return;
            $entries[] = ['reference'=>$source.'#'.$id, 'date'=>Carbon::parse($date)->toDateString(), 'type'=>$type, 'party'=>$party ?: '—', 'description'=>$description ?: '', 'method'=>$method ?: 'Unspecified', 'in'=>$incoming ? $amount : 0, 'out'=>$incoming ? 0 : $amount];
        };
        foreach (DB::table('owner_funds')->where('is_active', true)->get() as $r) $add('owner_funds',$r->id,$r->fund_date,'Boss funds',$r->owner_name,$r->amount,true,$r->purpose,$r->received_in);
        if (Schema::hasTable('salary_cash_entries')) {
            foreach (DB::table('salary_cash_entries')->get() as $r) $add('salary_cash_entries',$r->id,$r->entry_date,$r->type==='receipt'?'Legacy funds':'Legacy expense',$r->description,$r->amount,$r->type==='receipt','','cash');
        }
        foreach (DB::table('fund_receipts')->get() as $r) $add('fund_receipts',$r->id,$r->receipt_date,'Boss funds',$r->boss,$r->amount,true,$r->notes,$r->method);
        if(Schema::hasTable('fund_returns')) foreach(DB::table('fund_returns')->get() as $r) $add('fund_returns',$r->id,$r->return_date,'Cash returned to boss',$r->boss,$r->amount,false,'Returned by: '.($r->returned_by??'Not recorded').($r->notes?' · '.$r->notes:''),'cash');
        $employees = DB::table('employees')->pluck('name','id');
        if(Schema::hasTable('worker_loans'))foreach(DB::table('worker_loans')->get() as $r)$add('worker_loans',$r->id,$r->loan_date,'Worker loan issued',$employees[$r->employee_id]??null,$r->amount,false,'Given by: '.$r->given_by.($r->notes?' · '.$r->notes:''),'cash');
        foreach (DB::table('salary_management_advances')->join('salary_management_rows','salary_management_rows.id','=','salary_management_advances.salary_row_id')->select('salary_management_advances.*','salary_management_rows.employee_id')->get() as $r) $add('salary_management_advances',$r->id,$r->advance_date,'Salary advance',$employees[$r->employee_id]??null,$r->amount,false,$r->reason);
        foreach (DB::table('salary_management_rows')->where('paid_amount','>',0)->get() as $r) $add('salary_management_rows',$r->id,($r->payment_date ?? null) ?: ($r->salary_date ?: $r->month),'Salary paid',$employees[$r->employee_id]??null,$r->paid_amount,false,'Salary '.substr($r->month,0,7));
        foreach (DB::table('employee_advances')->get() as $r) $add('employee_advances',$r->id,$r->advance_date,'Payroll advance',$employees[$r->employee_id]??null,$r->amount,false,$r->remarks);
        foreach (DB::table('payrolls')->where('payment_status','paid')->get() as $r) {
            if (!$r->payment_date) throw new InvalidArgumentException('Paid payroll #'.$r->id.' has no payment date. Correct its payment date before reconciling funds.');
            $add('payrolls',$r->id,$r->payment_date,'Payroll paid',$employees[$r->employee_id]??null,$r->net_salary,false,'Payroll '.$r->month);
        }
        $contractors=DB::table('contractors')->pluck('name','id');
        foreach (DB::table('contractor_advances')->get() as $r) $add('contractor_advances',$r->id,$r->advance_date,'Contractor advance',$contractors[$r->contractor_id]??null,$r->amount,false,$r->remarks);
        foreach (DB::table('contractor_bill_payments')->join('contractor_bills','contractor_bills.id','=','contractor_bill_payments.contractor_bill_id')->select('contractor_bill_payments.*','contractor_bills.contractor_id','contractor_bills.bill_no')->get() as $r) $add('contractor_bill_payments',$r->id,$r->payment_date,'Contractor bill paid',$contractors[$r->contractor_id]??null,$r->amount,false,$r->bill_no.' · '.$r->remarks);
        foreach (DB::table('expenses')->get() as $r) $add('expenses',$r->id,$r->expense_date,'Expense / bill',$r->vendor_name,$r->amount,false,$r->description,$r->payment_method);
        $customers=DB::table('customers')->pluck('customer_name','id');
        foreach (DB::table('payments')->get() as $r) $add('payments',$r->id,$r->payment_date,'Customer receipt',$customers[$r->customer_id]??null,$r->amount,true,$r->payment_no,$r->payment_method);
        $receipts=DB::table('fund_receipts')->get()->keyBy('id');
        $actors=DB::table('fund_activity')->where('action','created')->get()->keyBy(fn($r)=>$r->source.'#'.$r->source_id);
        foreach ($entries as &$entry) {
            $entry['actor']=$actors[$entry['reference']]->actor ?? 'Historical — user not recorded';
            $entry['receiver']=null; $entry['photo']=null; $entry['edit_url']=null;
            [$source,$id]=explode('#',$entry['reference']);
            if($source==='fund_receipts') {
                $receipt=$receipts[$id]; $entry['receiver']=$receipt->receiver_name;
                $entry['photo']=$receipt->receiver_photo;
                $entry['edit_url']=route('funds-management.index',['edit'=>$id]);
            } elseif($source==='expenses') $entry['edit_url']=route('expenses.edit',$id);
            elseif($source==='payments') $entry['edit_url']=route('payments.edit',$id);
            elseif($source==='payrolls') $entry['edit_url']=route('payrolls.edit',$id);
            elseif(in_array($source,['salary_management_rows','salary_management_advances'])) $entry['edit_url']=route('salary-management.index',['month'=>substr($entry['date'],0,7)]);
            elseif(in_array($source,['contractor_bill_payments','contractor_advances'])) $entry['edit_url']=route('contractor-bills.index');
        }
        unset($entry);
        usort($entries,fn($a,$b)=>[$a['date'],$a['reference']]<=>[$b['date'],$b['reference']]);
        return $entries;
    }

    public function review(array $entries): array
    {
        $groups=[];$warnings=[];
        foreach($entries as $entry){
            $key=$entry['date'].'|'.strtolower(trim($entry['party'])).'|'.$entry['in'].'|'.$entry['out'];
            $groups[$key][]=$entry;
        }
        foreach($groups as $group){
            if(count($group)<2)continue;
            $sources=array_unique(array_map(fn($entry)=>explode('#',$entry['reference'])[0],$group));
            $warnings[]='Review possible duplicate: '.implode(', ',array_column($group,'reference')).' · '.$group[0]['date'].' · '.$group[0]['party'].'. Both records are included until corrected in their source modules.';
        }
        return $warnings;
    }

    public function month(array $entries, Carbon $month, Carbon $today): array
    {
        $start=$month->copy()->startOfMonth()->toDateString();
        $end=min($month->copy()->endOfMonth()->toDateString(),$today->toDateString());
        $opening=0;$received=0;$spent=0;$ledger=[];$bosses=[];$categories=[];$future=[];
        foreach($entries as $entry){
            if($entry['date']<$start && $entry['date']<=$today->toDateString()){$opening+=$entry['in']-$entry['out'];continue;}
            if(substr($entry['date'],0,7)!==$month->format('Y-m'))continue;
            if($entry['date']>$end){$future[]=$entry;continue;}
            $received+=$entry['in'];$spent+=$entry['out'];$entry['balance']=$opening+$received-$spent;$ledger[]=$entry;
            if(in_array($entry['type'],['Boss funds','Legacy funds'],true))$bosses[$entry['party']]=($bosses[$entry['party']]??0)+$entry['in'];
            $categories[$entry['type']]=($categories[$entry['type']]??0)+$entry['out'];
        }
        return compact('opening','received','spent','ledger','bosses','categories','future','end')+['available'=>$opening+$received,'closing'=>$opening+$received-$spent];
    }
}
