<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;

class CustomerLedgerController extends Controller
{
    public function index()
    {
        $customers = Customer::with(['invoices', 'payments'])
            ->latest()
            ->get();

        return view('customer-ledgers.index', compact('customers'));
    }

    public function show(Customer $customer)
    {
        $customer->load(['invoices', 'payments']);

        $openingBalance = $customer->opening_balance;

        $totalInvoices = $customer->invoices->sum('total_amount');

        $totalPayments = $customer->payments->sum('amount');

        $balance = ($openingBalance + $totalInvoices) - $totalPayments;

        $transactions = collect();

        $transactions->push([
            'date' => $customer->created_at,
            'type' => 'Opening Balance',
            'reference' => '-',
            'debit' => $openingBalance,
            'credit' => 0,
        ]);

        foreach ($customer->invoices as $invoice) {
            $transactions->push([
                'date' => $invoice->invoice_date,
                'type' => 'Invoice',
                'reference' => $invoice->invoice_no,
                'debit' => $invoice->total_amount,
                'credit' => 0,
            ]);
        }

        foreach ($customer->payments as $payment) {
            $transactions->push([
                'date' => $payment->payment_date,
                'type' => 'Payment',
                'reference' => $payment->payment_no,
                'debit' => 0,
                'credit' => $payment->amount,
            ]);
        }

        $transactions = $transactions->sortBy('date')->values();

        return view('customer-ledgers.show', compact(
            'customer',
            'openingBalance',
            'totalInvoices',
            'totalPayments',
            'balance',
            'transactions'
        ));
    }
}
