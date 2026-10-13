<?php

namespace App\Http\Controllers;

use App\Models\Contractor;
use App\Models\ContractorBill;
use App\Models\ContractorBillPayment;
use App\Models\ContractorItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;
use App\Services\ContractorAdvanceLedger;
use App\Services\FundsLedger;

class ContractorBillController extends Controller
{
    public function index()
    {
        $bills = ContractorBill::with([
            'contractor.department',
            'contractor.machine',
        ])
            ->latest('bill_date')
            ->latest('id')
            ->paginate(15);

        return view(
            'contractor-bills.index',
            compact('bills')
        );
    }

    public function create()
    {
        $contractors = Contractor::with([
            'department',
            'machine',
        ])
            ->where('status', 'active')
            ->whereNotNull('contractor_machine_id')
            ->orderBy('name')
            ->get();

        $items = ContractorItem::with([
            'machine.department',
        ])
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $advancePreview=\App\Models\ContractorAdvance::where('eligible_for_bills',true)->get()->map(function($a){$used=0;foreach(DB::table('contractor_advance_allocations')->where('contractor_advance_id',$a->id)->pluck('amount') as $v)$used+=FundsLedger::paise((string)$v);return ['contractor_id'=>$a->contractor_id,'date'=>$a->advance_date->toDateString(),'paise'=>max(0,FundsLedger::paise((string)($a->allocation_limit??$a->amount))-$used)];})->values();
        return view(
            'contractor-bills.create',
            compact(
                'contractors',
                'items',
                'advancePreview'
            )
        );
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->hasPermission('contractor_bills'),403);
        $data = $request->validate([
            'contractor_id' => [
                'required',
                'exists:contractors,id',
            ],

            'bill_date' => [
                'required',
                'date_format:Y-m-d',
                'before_or_equal:'.now('Asia/Karachi')->toDateString(),
            ],

            'pay_remaining_now'=>['required','boolean'],
            'payment_date'=>['nullable','date_format:Y-m-d','before_or_equal:'.now('Asia/Karachi')->toDateString()],
            'submission_key'=>['required','uuid'],
            'paid_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:3000',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.order_no' => [
                'required',
                'string',
                'max:100',
            ],

            'items.*.contractor_item_id' => [
                'required',
                'exists:contractor_items,id',
            ],

            'items.*.quantity' => [
                'required',
                'numeric',
                'min:0.01',
                'max:9999999999.99',
                'decimal:0,2',
            ],
        ]);

        if($existing=ContractorBill::where('submission_key',$data['submission_key'])->first())return redirect()->route('contractor-bills.show',$existing)->with('success','This bill was already saved.');
        $contractor = Contractor::query()
            ->where('id', $data['contractor_id'])
            ->where('status', 'active')
            ->firstOrFail();

        if (!$contractor->contractor_machine_id) {
            return back()
                ->withInput()
                ->withErrors([
                    'contractor_id' =>
                        'Selected contractor does not have a machine assigned.',
                ]);
        }

        DB::beginTransaction();

        try {
            $contractor=Contractor::whereKey($contractor->id)->lockForUpdate()->firstOrFail();
            if($existing=ContractorBill::where('submission_key',$data['submission_key'])->first()){DB::commit();return redirect()->route('contractor-bills.show',$existing);}
            $grandTotal = 0;
            $grandPaise = 0;
            $billItems = [];

            foreach ($data['items'] as $row) {
                $item = ContractorItem::query()
                    ->where('id', $row['contractor_item_id'])
                    ->where('status', 'active')
                    ->where(
                        'contractor_machine_id',
                        $contractor->contractor_machine_id
                    )
                    ->first();

                if (!$item) {
                    throw ValidationException::withMessages([
                        'items' =>
                            'One selected item does not belong to the contractor machine.',
                    ]);
                }

                $quantity = round(
                    (float) $row['quantity'],
                    2
                );

                $rate = round(
                    (float) $item->rate,
                    2
                );

                $quantityUnits=(int)round($quantity*100);$ratePaise=FundsLedger::paise(number_format($rate,2,'.',''));
                if($ratePaise<0 || $ratePaise>intdiv(PHP_INT_MAX-50,max(1,$quantityUnits)))throw ValidationException::withMessages(['items'=>'Item amount exceeds supported limits.']);
                $total = intdiv($quantityUnits*$ratePaise+50,100)/100;

                $grandPaise += FundsLedger::paise(number_format($total,2,'.',''));

                $billItems[] = [
                    'contractor_item_id' => $item->id,
                    'order_no' => $row['order_no'],
                    'item_name' => $item->name,
                    'quantity' => $quantity,
                    'rate' => $rate,
                    'total' => $total,
                ];
            }

            if($grandPaise>999999999999)throw ValidationException::withMessages(['items'=>'Bill total exceeds the supported amount.']);
            $grandTotal = $grandPaise/100;

            $bill = ContractorBill::create([
                'bill_no'=>$this->generateBillNumber(),'submission_key'=>$data['submission_key'],
                'contractor_id'=>$contractor->id,'bill_date'=>$data['bill_date'],
                'grand_total'=>$grandTotal,'paid_amount'=>0,'balance'=>$grandTotal,
                'status'=>'Pending','notes'=>$data['notes']??null,
            ]);
            $adjustedPaise=(new ContractorAdvanceLedger)->allocate($bill);
            $totalPaise=FundsLedger::paise(number_format($grandTotal,2,'.',''));
            $remainingPaise=$totalPaise-$adjustedPaise;
            $cashPaise=$data['pay_remaining_now']?$remainingPaise:FundsLedger::paise(number_format((float)($data['paid_amount']??0),2,'.',''));
            if($cashPaise>$remainingPaise)throw ValidationException::withMessages(['paid_amount'=>'Cash payment cannot exceed the bill balance after advance adjustment.']);
            if($cashPaise>0 && empty($data['payment_date']))throw ValidationException::withMessages(['payment_date'=>'Select the actual date cash was paid.']);
            if($cashPaise>0 && $data['payment_date']<substr($data['bill_date'],0,10))throw ValidationException::withMessages(['payment_date'=>'Payment before bill date must be entered as a contractor advance.']);
            $paidAmount=$cashPaise/100;
            $settledAmount=($cashPaise+$adjustedPaise)/100;
            $bill->update(['advance_adjusted'=>$adjustedPaise/100,'paid_amount'=>$settledAmount,'balance'=>($remainingPaise-$cashPaise)/100,'status'=>$this->calculateStatus($grandTotal,$settledAmount)]);

            foreach ($billItems as $billItem) {
                $bill->items()->create($billItem);
            }

            if ($paidAmount > 0) {
                $bill->payments()->create([
                    'amount' => $paidAmount,
                    'payment_date' => $data['payment_date'],
                    'payment_time' => now()->format('H:i'),
                    'remarks' => 'Opening payment',
                ]);
            }

            DB::commit();

            return redirect()
                ->route('contractor-bills.index')
                ->with(
                    'success',
                    'Bill saved. Available advances adjusted; only the recorded cash payment deducted from Funds.'
                );
        } catch (Throwable $exception) {
            DB::rollBack();

            if ($exception instanceof ValidationException) {
                throw $exception;
            }

            report($exception);

            return back()
                ->withInput()
                ->withErrors([
                    'error' =>
                        'Contractor bill could not be saved.',
                ]);
        }
    }

 public function show(ContractorBill $contractorBill)
{
    $contractorBill->load([
        'contractor.department',
        'contractor.machine',
        'items.item.machine',
        'payments' => function ($query) {
            $query
                ->orderBy('payment_date')
                ->orderBy('payment_time')
                ->orderBy('id');
        },
    ]);

    return view(
        'contractor-bills.show',
        compact('contractorBill')
    );
}

    public function edit(ContractorBill $contractorBill)
    {
        $contractorBill->load([
            'contractor.department',
            'contractor.machine',
            'items',
        ]);

        $contractors = Contractor::with([
            'department',
            'machine',
        ])
            ->where(function ($query) use ($contractorBill) {
                $query
                    ->where('status', 'active')
                    ->orWhere(
                        'id',
                        $contractorBill->contractor_id
                    );
            })
            ->whereNotNull('contractor_machine_id')
            ->orderBy('name')
            ->get();

        $items = ContractorItem::with([
            'machine.department',
        ])
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view(
            'contractor-bills.edit',
            compact(
                'contractorBill',
                'contractors',
                'items'
            )
        );
    }

    public function update(
        Request $request,
        ContractorBill $contractorBill
    ) {
        $data = $request->validate([
            'contractor_id' => [
                'required',
                'exists:contractors,id',
            ],

            'bill_date' => [
                'required',
                'date_format:Y-m-d',
                'before_or_equal:'.now('Asia/Karachi')->toDateString(),
            ],

            'notes' => [
                'nullable',
                'string',
                'max:3000',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.order_no' => [
                'required',
                'string',
                'max:100',
            ],

            'items.*.contractor_item_id' => [
                'required',
                'exists:contractor_items,id',
            ],

            'items.*.quantity' => [
                'required',
                'numeric',
                'min:0.01',
                'max:9999999999.99',
                'decimal:0,2',
            ],
        ]);

        $contractor = Contractor::query()
            ->where('id', $data['contractor_id'])
            ->firstOrFail();

        if (!$contractor->contractor_machine_id) {
            return back()
                ->withInput()
                ->withErrors([
                    'contractor_id' =>
                        'Selected contractor does not have a machine assigned.',
                ]);
        }

        DB::beginTransaction();

        try {
            Contractor::whereIn('id',[$contractorBill->contractor_id,$contractor->id])->orderBy('id')->lockForUpdate()->get();
            $contractorBill=ContractorBill::whereKey($contractorBill->id)->lockForUpdate()->firstOrFail();
            if((float)$contractorBill->paid_amount>0 || (float)$contractorBill->advance_adjusted>0)throw ValidationException::withMessages(['items'=>'A settled or advance-adjusted bill cannot be rewritten. Keep its financial history intact.']);
            $grandTotal = 0;
            $grandPaise = 0;
            $billItems = [];

            foreach ($data['items'] as $row) {
                $item = ContractorItem::query()
                    ->where('id', $row['contractor_item_id'])
                    ->where(
                        'contractor_machine_id',
                        $contractor->contractor_machine_id
                    )
                    ->first();

                if (!$item) {
                    throw ValidationException::withMessages([
                        'items' =>
                            'One selected item does not belong to the contractor machine.',
                    ]);
                }

                $quantity = round(
                    (float) $row['quantity'],
                    2
                );

                $rate = round(
                    (float) $item->rate,
                    2
                );

                $quantityUnits=(int)round($quantity*100);$ratePaise=FundsLedger::paise(number_format($rate,2,'.',''));
                if($ratePaise<0 || $ratePaise>intdiv(PHP_INT_MAX-50,max(1,$quantityUnits)))throw ValidationException::withMessages(['items'=>'Item amount exceeds supported limits.']);
                $total = intdiv($quantityUnits*$ratePaise+50,100)/100;

                $grandPaise += FundsLedger::paise(number_format($total,2,'.',''));

                $billItems[] = [
                    'contractor_item_id' => $item->id,
                    'order_no' => $row['order_no'],
                    'item_name' => $item->name,
                    'quantity' => $quantity,
                    'rate' => $rate,
                    'total' => $total,
                ];
            }

            if($grandPaise>999999999999)throw ValidationException::withMessages(['items'=>'Bill total exceeds the supported amount.']);
            $grandTotal = $grandPaise/100;
            $paidAmount = round(
                (float) $contractorBill->paid_amount,
                2
            );

            if ($paidAmount > $grandTotal) {
                throw ValidationException::withMessages([
                    'items' =>
                        'Grand total cannot be less than already paid amount.',
                ]);
            }

            $balance = round(
                $grandTotal - $paidAmount,
                2
            );

            $contractorBill->update([
                'contractor_id' => $contractor->id,
                'bill_date' => $data['bill_date'],
                'grand_total' => $grandTotal,
                'balance' => $balance,
                'status' => $this->calculateStatus(
                    $grandTotal,
                    $paidAmount
                ),
                'notes' => $data['notes'] ?? null,
            ]);

            $contractorBill->items()->delete();

            foreach ($billItems as $billItem) {
                $contractorBill
                    ->items()
                    ->create($billItem);
            }

            DB::commit();

            return redirect()
                ->route(
                    'contractor-bills.show',
                    $contractorBill->id
                )
                ->with(
                    'success',
                    'Contractor bill updated successfully.'
                );
        } catch (Throwable $exception) {
            DB::rollBack();

            if ($exception instanceof ValidationException) {
                throw $exception;
            }

            report($exception);

            return back()
                ->withInput()
                ->withErrors([
                    'error' =>
                        'Contractor bill could not be updated.',
                ]);
        }
    }

    public function storePayment(Request $request)
    {
        abort_unless($request->user()->hasPermission('contractor_bills'),403);
        $data = $request->validate([
            'submission_key'=>['required','uuid'],
            'contractor_bill_id' => [
                'required',
                'exists:contractor_bills,id',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:9999999999.99',
                'decimal:0,2',
            ],

            'payment_date' => [
                'required',
                'date_format:Y-m-d',
                'before_or_equal:'.now('Asia/Karachi')->toDateString(),
            ],

            'payment_time' => [
                'nullable',
                'date_format:H:i',
            ],

            'remarks' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        if(ContractorBillPayment::where('submission_key',$data['submission_key'])->exists())return back()->with('success','Payment already recorded.');
        $bill = ContractorBill::findOrFail(
            $data['contractor_bill_id']
        );

        if ((float) $data['amount'] > (float) $bill->balance) {
            return back()->withErrors([
                'amount' =>
                    'Payment cannot be greater than remaining balance.',
            ]);
        }

        DB::transaction(function () use ($bill, $data) {
            Contractor::whereKey($bill->contractor_id)->lockForUpdate()->firstOrFail();
            $bill=ContractorBill::whereKey($bill->id)->lockForUpdate()->firstOrFail();
            if(ContractorBillPayment::where('submission_key',$data['submission_key'])->exists())return;
            if((float)$data['amount']>(float)$bill->balance)throw ValidationException::withMessages(['amount'=>'Payment exceeds the current bill balance.']);
            if($data['payment_date']<$bill->bill_date->toDateString())throw ValidationException::withMessages(['payment_date'=>'Enter pre-bill payments in Contractor Advances.']);
            ContractorBillPayment::create([
                'submission_key'=>$data['submission_key'],
                'contractor_bill_id' => $bill->id,
                'amount' => $data['amount'],
                'payment_date' => $data['payment_date'],
                'payment_time' =>
                    $data['payment_time'] ?? null,
                'remarks' => $data['remarks'] ?? null,
            ]);

            $paidAmount = round(
                (float) $bill->paid_amount +
                (float) $data['amount'],
                2
            );

            $balance = round(
                (float) $bill->grand_total -
                $paidAmount,
                2
            );

            $bill->update([
                'paid_amount' => $paidAmount,
                'balance' => max(0, $balance),
                'status' => $this->calculateStatus(
                    (float) $bill->grand_total,
                    $paidAmount
                ),
            ]);
        });

        return back()->with(
            'success',
            'Bill payment recorded. Funds updated.'
        );
    }

    public function destroyPayment(
        ContractorBillPayment $payment
    ) {
        DB::transaction(function () use ($payment) {
            $contractorId=$payment->bill->contractor_id;Contractor::whereKey($contractorId)->lockForUpdate()->firstOrFail();
            $bill=ContractorBill::whereKey($payment->contractor_bill_id)->lockForUpdate()->firstOrFail();
            $payment=ContractorBillPayment::whereKey($payment->id)->lockForUpdate()->firstOrFail();

            $paidAmount = max(
                0,
                round(
                    (float) $bill->paid_amount -
                    (float) $payment->amount,
                    2
                )
            );

            $balance = round(
                (float) $bill->grand_total -
                $paidAmount,
                2
            );

            $payment->delete();

            $bill->update([
                'paid_amount' => $paidAmount,
                'balance' => max(0, $balance),
                'status' => $this->calculateStatus(
                    (float) $bill->grand_total,
                    $paidAmount
                ),
            ]);
        });

        return back()->with(
            'success',
            'Payment deleted successfully.'
        );
    }

    public function destroy(
        ContractorBill $contractorBill
    ) {
        DB::transaction(function()use($contractorBill){Contractor::whereKey($contractorBill->contractor_id)->lockForUpdate()->firstOrFail();$bill=ContractorBill::whereKey($contractorBill->id)->lockForUpdate()->firstOrFail();if((float)$bill->paid_amount>0 || (float)$bill->advance_adjusted>0)throw ValidationException::withMessages(['bill'=>'A paid or advance-adjusted bill cannot be deleted.']);$bill->delete();});

        return redirect()
            ->route('contractor-bills.index')
            ->with(
                'success',
                'Contractor bill deleted successfully.'
            );
    }

    private function calculateStatus(
        float $grandTotal,
        float $paidAmount
    ): string {
        if ($paidAmount <= 0) {
            return 'Pending';
        }

        if ($paidAmount >= $grandTotal) {
            return 'Paid';
        }

        return 'Partial';
    }

    private function generateBillNumber(): string {
        $sequence=DB::table('contractor_bill_sequence')->where('id',1)->lockForUpdate()->first();
        $number=(int)$sequence->next_number;
        do{$code='CB-'.str_pad((string)$number++,5,'0',STR_PAD_LEFT);}while(ContractorBill::where('bill_no',$code)->exists());
        DB::table('contractor_bill_sequence')->where('id',1)->update(['next_number'=>$number]);return $code;
    }
}
