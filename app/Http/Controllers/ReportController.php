<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Payroll;

class ReportController extends Controller
{
    public function dashboard()
    {
        $totalCustomers = Customer::count();
        $totalEmployees = Employee::count();

        $totalInvoices = Invoice::count();
        $totalSales = Invoice::sum('total_amount');

        $totalPayments = Payment::sum('amount');
        $totalExpenses = Expense::sum('amount');

        $totalPayroll = Payroll::sum('net_salary');

        $pendingReceivables = Invoice::sum('balance_amount');

        $netProfit = $totalSales - ($totalExpenses + $totalPayroll);

        return view('dashboard.index', compact(
            'totalCustomers',
            'totalEmployees',
            'totalInvoices',
            'totalSales',
            'totalPayments',
            'totalExpenses',
            'totalPayroll',
            'pendingReceivables',
            'netProfit'
        ));
    }

    public function profitLoss()
    {
        $totalSales = Invoice::sum('total_amount');
        $totalExpenses = Expense::sum('amount');
        $totalPayroll = Payroll::sum('net_salary');

        $totalCost = $totalExpenses + $totalPayroll;
        $netProfit = $totalSales - $totalCost;

        return view('reports.profit-loss', compact(
            'totalSales',
            'totalExpenses',
            'totalPayroll',
            'totalCost',
            'netProfit'
        ));
    }
}
