<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Payroll;
use App\Models\MonthlyAlert;

class DashboardController extends Controller
{
    public function index()
    {
        $totalCustomers = Customer::count();
        $totalEmployees = Employee::count();
        $totalInvoices = Invoice::count();

        $totalSales = Invoice::sum('total_amount');
        $totalPayments = Payment::sum('amount');
        $totalExpenses = Expense::sum('amount');
        $totalPayroll = Payroll::sum('net_salary');

        $receivables = $totalSales - $totalPayments;
        $pendingReceivables = $receivables;

        $netProfit = $totalSales - ($totalExpenses + $totalPayroll);

      $monthlyAlert = MonthlyAlert::where('status', 'pending')
    ->where('month', now()->month)
    ->where('year', now()->year)
    ->first();

        return view('dashboard.index', compact(
            'totalCustomers',
            'totalEmployees',
            'totalInvoices',
            'totalSales',
            'totalPayments',
            'totalExpenses',
            'totalPayroll',
            'receivables',
            'pendingReceivables',
            'netProfit',
            'monthlyAlert'
        ));
    }
}
