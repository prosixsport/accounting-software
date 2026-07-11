<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('customer')
            ->latest()
            ->get();

        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $customers = Customer::where('status', 'active')->get();

        return view('invoices.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required',
            'invoice_date' => 'required|date',
            'item_name.*' => 'required',
            'qty.*' => 'required|numeric',
            'rate.*' => 'required|numeric',
        ]);

        DB::transaction(function () use ($request) {

            $subtotal = 0;

            foreach ($request->item_name as $index => $itemName) {
                $qty = $request->qty[$index] ?? 0;
                $rate = $request->rate[$index] ?? 0;
                $subtotal += $qty * $rate;
            }

            $discount = $request->discount ?? 0;
            $tax = $request->tax ?? 0;
            $paidAmount = $request->paid_amount ?? 0;

            $totalAmount = ($subtotal - $discount) + $tax;
            $balanceAmount = $totalAmount - $paidAmount;

            if ($paidAmount <= 0) {
                $status = 'unpaid';
            } elseif ($paidAmount < $totalAmount) {
                $status = 'partial';
            } else {
                $status = 'paid';
            }

            $invoice = Invoice::create([
                'invoice_no' => 'INV-' . str_pad(Invoice::count() + 1, 4, '0', STR_PAD_LEFT),
                'customer_id' => $request->customer_id,
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'balance_amount' => $balanceAmount,
                'status' => $status,
                'notes' => $request->notes,
            ]);

            foreach ($request->item_name as $index => $itemName) {
                $qty = $request->qty[$index] ?? 0;
                $rate = $request->rate[$index] ?? 0;
                $amount = $qty * $rate;

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'item_name' => $itemName,
                    'description' => $request->description[$index] ?? null,
                    'qty' => $qty,
                    'rate' => $rate,
                    'amount' => $amount,
                ]);
            }
        });

        return redirect()
            ->route('invoices.index')
            ->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['customer', 'items']);

        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('items');

        $customers = Customer::where('status', 'active')->get();

        return view('invoices.edit', compact('invoice', 'customers'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'customer_id' => 'required',
            'invoice_date' => 'required|date',
            'item_name.*' => 'required',
            'qty.*' => 'required|numeric',
            'rate.*' => 'required|numeric',
        ]);

        DB::transaction(function () use ($request, $invoice) {

            $subtotal = 0;

            foreach ($request->item_name as $index => $itemName) {
                $qty = $request->qty[$index] ?? 0;
                $rate = $request->rate[$index] ?? 0;
                $subtotal += $qty * $rate;
            }

            $discount = $request->discount ?? 0;
            $tax = $request->tax ?? 0;
            $paidAmount = $request->paid_amount ?? 0;

            $totalAmount = ($subtotal - $discount) + $tax;
            $balanceAmount = $totalAmount - $paidAmount;

            if ($paidAmount <= 0) {
                $status = 'unpaid';
            } elseif ($paidAmount < $totalAmount) {
                $status = 'partial';
            } else {
                $status = 'paid';
            }

            $invoice->update([
                'customer_id' => $request->customer_id,
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'balance_amount' => $balanceAmount,
                'status' => $status,
                'notes' => $request->notes,
            ]);

            $invoice->items()->delete();

            foreach ($request->item_name as $index => $itemName) {
                $qty = $request->qty[$index] ?? 0;
                $rate = $request->rate[$index] ?? 0;
                $amount = $qty * $rate;

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'item_name' => $itemName,
                    'description' => $request->description[$index] ?? null,
                    'qty' => $qty,
                    'rate' => $rate,
                    'amount' => $amount,
                ]);
            }
        });

        return redirect()
            ->route('invoices.index')
            ->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()
            ->route('invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }
}
