<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['customer', 'invoice'])
            ->latest()
            ->get();

        return view('payments.index', compact('payments'));
    }

    public function create()
    {
        $customers = Customer::where('status', 'active')->get();

        $invoices = Invoice::with('customer')
            ->where('balance_amount', '>', 0)
            ->latest()
            ->get();

        return view('payments.create', compact('customers', 'invoices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required',
        ]);

        DB::transaction(function () use ($request) {

            $payment = Payment::create([
                'payment_no' => 'PAY-' . str_pad(Payment::count() + 1, 4, '0', STR_PAD_LEFT),
                'customer_id' => $request->customer_id,
                'invoice_id' => $request->invoice_id,
                'payment_date' => $request->payment_date,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'reference_no' => $request->reference_no,
                'notes' => $request->notes,
            ]);

            if ($request->invoice_id) {
                $invoice = Invoice::find($request->invoice_id);

                if ($invoice) {
                    $invoice->paid_amount = $invoice->paid_amount + $payment->amount;
                    $invoice->balance_amount = $invoice->total_amount - $invoice->paid_amount;

                    if ($invoice->paid_amount <= 0) {
                        $invoice->status = 'unpaid';
                    } elseif ($invoice->paid_amount < $invoice->total_amount) {
                        $invoice->status = 'partial';
                    } else {
                        $invoice->status = 'paid';
                        $invoice->balance_amount = 0;
                    }

                    $invoice->save();
                }
            }
        });

        return redirect()
            ->route('payments.index')
            ->with('success', 'Payment received successfully.');
    }

    public function edit(Payment $payment)
    {
        $customers = Customer::where('status', 'active')->get();

        $invoices = Invoice::with('customer')
            ->latest()
            ->get();

        return view('payments.edit', compact('payment', 'customers', 'invoices'));
    }

    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'customer_id' => 'required',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required',
        ]);

        DB::transaction(function () use ($request, $payment) {

            if ($payment->invoice_id) {
                $oldInvoice = Invoice::find($payment->invoice_id);

                if ($oldInvoice) {
                    $oldInvoice->paid_amount = max(0, $oldInvoice->paid_amount - $payment->amount);
                    $oldInvoice->balance_amount = $oldInvoice->total_amount - $oldInvoice->paid_amount;

                    if ($oldInvoice->paid_amount <= 0) {
                        $oldInvoice->status = 'unpaid';
                    } elseif ($oldInvoice->paid_amount < $oldInvoice->total_amount) {
                        $oldInvoice->status = 'partial';
                    } else {
                        $oldInvoice->status = 'paid';
                        $oldInvoice->balance_amount = 0;
                    }

                    $oldInvoice->save();
                }
            }

            $payment->update([
                'customer_id' => $request->customer_id,
                'invoice_id' => $request->invoice_id,
                'payment_date' => $request->payment_date,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'reference_no' => $request->reference_no,
                'notes' => $request->notes,
            ]);

            if ($request->invoice_id) {
                $newInvoice = Invoice::find($request->invoice_id);

                if ($newInvoice) {
                    $newInvoice->paid_amount = $newInvoice->paid_amount + $payment->amount;
                    $newInvoice->balance_amount = $newInvoice->total_amount - $newInvoice->paid_amount;

                    if ($newInvoice->paid_amount <= 0) {
                        $newInvoice->status = 'unpaid';
                    } elseif ($newInvoice->paid_amount < $newInvoice->total_amount) {
                        $newInvoice->status = 'partial';
                    } else {
                        $newInvoice->status = 'paid';
                        $newInvoice->balance_amount = 0;
                    }

                    $newInvoice->save();
                }
            }
        });

        return redirect()
            ->route('payments.index')
            ->with('success', 'Payment updated successfully.');
    }

    public function destroy(Payment $payment)
    {
        DB::transaction(function () use ($payment) {

            if ($payment->invoice_id) {
                $invoice = Invoice::find($payment->invoice_id);

                if ($invoice) {
                    $invoice->paid_amount = max(0, $invoice->paid_amount - $payment->amount);
                    $invoice->balance_amount = $invoice->total_amount - $invoice->paid_amount;

                    if ($invoice->paid_amount <= 0) {
                        $invoice->status = 'unpaid';
                    } elseif ($invoice->paid_amount < $invoice->total_amount) {
                        $invoice->status = 'partial';
                    } else {
                        $invoice->status = 'paid';
                        $invoice->balance_amount = 0;
                    }

                    $invoice->save();
                }
            }

            $payment->delete();
        });

        return redirect()
            ->route('payments.index')
            ->with('success', 'Payment deleted successfully.');
    }
}
