<?php

use Illuminate\Support\Facades\Schedule;
use App\Models\MonthlyAlertSchedule;
use App\Models\MonthlyAlert;
use App\Models\Payroll;
use App\Models\ContractorBill;
use App\Models\Expense;
use Carbon\Carbon;

Schedule::call(function () {
    $now = Carbon::now('Asia/Karachi');

    $schedules = MonthlyAlertSchedule::where('status', 'pending')
        ->whereDate('alert_date', $now->toDateString())
        ->whereTime('alert_time', '<=', $now->format('H:i:s'))
        ->get();

    foreach ($schedules as $schedule) {

        $monthNumber = (int) $schedule->month;
        $monthName = Carbon::createFromDate($schedule->year, $monthNumber, 1)->format('F');
        $monthShort = Carbon::createFromDate($schedule->year, $monthNumber, 1)->format('M');
        $yearMonth = $schedule->year . '-' . str_pad($monthNumber, 2, '0', STR_PAD_LEFT);

        $employeesSalary = Payroll::where(function ($q) use ($monthNumber, $monthName, $monthShort, $yearMonth) {
                $q->where('month', $monthNumber)
                  ->orWhere('month', (string) $monthNumber)
                  ->orWhere('month', $monthName)
                  ->orWhere('month', $monthShort)
                  ->orWhere('month', $yearMonth);
            })
            ->sum('net_salary');

        $contractorBills = ContractorBill::whereMonth('bill_date', $monthNumber)
            ->whereYear('bill_date', $schedule->year)
            ->sum('balance');

     $factoryExpenses = Expense::whereMonth('expense_date', $monthNumber)
    ->whereYear('expense_date', $schedule->year)
    ->sum('amount');

        $totalRequired = $employeesSalary + $contractorBills + $factoryExpenses;

        MonthlyAlert::create([
            'month' => $monthNumber,
            'year' => $schedule->year,
            'employees_salary' => $employeesSalary,
            'contractor_bills' => $contractorBills,
            'factory_expenses' => $factoryExpenses,
            'total_required' => $totalRequired,
            'status' => 'pending',
        ]);

        $schedule->update([
            'status' => 'sent',
            'sent_at' => Carbon::now('Asia/Karachi'),
        ]);
    }
})->everyMinute();
