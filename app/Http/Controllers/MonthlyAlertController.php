<?php

namespace App\Http\Controllers;

use App\Mail\MonthlySalaryAlertMail;
use App\Models\ContractorBill;
use App\Models\Expense;
use App\Models\MonthlyAlert;
use App\Models\Payroll;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MonthlyAlertController extends Controller
{
    public function index()
    {
        $alerts = MonthlyAlert::latest()->paginate(12);

        return view('monthly-alerts.index', compact('alerts'));
    }

    public function generate(Request $request)
    {
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;

        $employeesSalary = Payroll::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->sum('net_salary');

        $contractorBills = ContractorBill::whereMonth('bill_date', $month)
            ->whereYear('bill_date', $year)
            ->sum('balance');

        $factoryExpenses = Expense::whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year)
            ->sum('amount');

        $totalRequired = $employeesSalary + $contractorBills + $factoryExpenses;

        $alert = MonthlyAlert::updateOrCreate(
            [
                'month' => $month,
                'year' => $year,
            ],
            [
                'employees_salary' => $employeesSalary,
                'contractor_bills' => $contractorBills,
                'factory_expenses' => $factoryExpenses,
                'total_required' => $totalRequired,
                'status' => 'pending',
            ]
        );

        $bosses = User::where('role', 'super_admin')->get();

        foreach ($bosses as $boss) {
            Mail::to($boss->email)->send(new MonthlySalaryAlertMail($alert));
        }

        $alert->update([
            'email_sent_at' => now(),
        ]);

        return redirect()
            ->route('monthly-alerts.index')
            ->with('success', 'Monthly alert generated and email sent successfully.');
    }

    public function markArranged(MonthlyAlert $monthlyAlert)
    {
        $monthlyAlert->update([
            'status' => 'arranged',
            'arranged_at' => now(),
        ]);

        return back()->with('success', 'Funds marked as arranged.');
    }
}
