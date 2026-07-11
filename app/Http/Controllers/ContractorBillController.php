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

        return view(
            'contractor-bills.create',
            compact(
                'contractors',
                'items'
            )
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'contractor_id' => [
                'required',
                'exists:contractors,id',
            ],

            'bill_date' => [
                'required',
                'date',
            ],

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
            ],
        ]);

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
            $grandTotal = 0;
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

                $total = round(
                    $quantity * $rate,
                    2
                );

                $grandTotal += $total;

                $billItems[] = [
                    'contractor_item_id' => $item->id,
                    'order_no' => $row['order_no'],
                    'item_name' => $item->name,
                    'quantity' => $quantity,
                    'rate' => $rate,
                    'total' => $total,
                ];
            }

            $grandTotal = round($grandTotal, 2);

            $paidAmount = round(
                (float) ($data['paid_amount'] ?? 0),
                2
            );

            if ($paidAmount > $grandTotal) {
                throw ValidationException::withMessages([
                    'paid_amount' =>
                        'Paid amount cannot be greater than grand total.',
                ]);
            }

            $balance = round(
                $grandTotal - $paidAmount,
                2
            );

            $bill = ContractorBill::create([
                'bill_no' => $this->generateBillNumber(),
                'contractor_id' => $contractor->id,
                'bill_date' => $data['bill_date'],
                'grand_total' => $grandTotal,
                'paid_amount' => $paidAmount,
                'balance' => $balance,
                'status' => $this->calculateStatus(
                    $grandTotal,
                    $paidAmount
                ),
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($billItems as $billItem) {
                $bill->items()->create($billItem);
            }

            if ($paidAmount > 0) {
                $bill->payments()->create([
                    'amount' => $paidAmount,
                    'payment_date' => $data['bill_date'],
                    'payment_time' => now()->format('H:i'),
                    'remarks' => 'Opening payment',
                ]);
            }

            DB::commit();

            return redirect()
                ->route('contractor-bills.index')
                ->with(
                    'success',
                    'Contractor bill created successfully.'
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
                'date',
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
            $grandTotal = 0;
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

                $total = round(
                    $quantity * $rate,
                    2
                );

                $grandTotal += $total;

                $billItems[] = [
                    'contractor_item_id' => $item->id,
                    'order_no' => $row['order_no'],
                    'item_name' => $item->name,
                    'quantity' => $quantity,
                    'rate' => $rate,
                    'total' => $total,
                ];
            }

            $grandTotal = round($grandTotal, 2);
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
        $data = $request->validate([
            'contractor_bill_id' => [
                'required',
                'exists:contractor_bills,id',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'payment_date' => [
                'required',
                'date',
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

        $bill = ContractorBill::findOrFail(
            $data['contractor_bill_id']
        );

        if ((float) $data['amount'] > (float) $bill->balance) {
            return back()->withErrors([
                'amount' =>
                    'Advance amount cannot be greater than remaining balance.',
            ]);
        }

        DB::transaction(function () use ($bill, $data) {
            ContractorBillPayment::create([
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
            'Advance added successfully.'
        );
    }

    public function destroyPayment(
        ContractorBillPayment $payment
    ) {
        DB::transaction(function () use ($payment) {
            $bill = $payment->bill;

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
        $contractorBill->delete();

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

    private function generateBillNumber(): string
    {
        $nextId =
            (ContractorBill::max('id') ?? 0) + 1;

        return 'CB-' . str_pad(
            (string) $nextId,
            5,
            '0',
            STR_PAD_LEFT
        );
    }
}
