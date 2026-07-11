<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\EmployeeAdvance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PayrollController extends Controller
{
public function index(Request $request)
{
    $year = (int) ($request->year ?? date('Y'));
    $month = (int) ($request->month ?? date('m'));
    $search = $request->search;

    $monthDate = Carbon::createFromDate($year, $month, 1);

    /*
     * Database mein month isi format mein save hoga:
     * July 2026
     */
    $monthName = $monthDate->format('F Y');

    $startDate = $monthDate
        ->copy()
        ->startOfMonth()
        ->toDateString();

    $endDate = $monthDate
        ->copy()
        ->endOfMonth()
        ->toDateString();

    /*
     * Sirf active employees Payroll mein show honge.
     */
    $employees = Employee::with('biometric')
        ->where('status', 'active')
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('cnic', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%");
            });
        })
        ->latest()
        ->get();

    $payrollRows = $employees->map(function ($employee) use (
        $startDate,
        $endDate,
        $monthDate,
        $monthName
    ) {
        $presentDays = Attendance::where('employee_id', $employee->id)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->where('status', 'present')
            ->count();

        $absentDays = Attendance::where('employee_id', $employee->id)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->where('status', 'absent')
            ->count();

        $leaveDays = Attendance::where('employee_id', $employee->id)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->where('status', 'leave')
            ->count();

        $halfDays = Attendance::where('employee_id', $employee->id)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->where('status', 'half_day')
            ->count();

        $advanceAmount = EmployeeAdvance::where('employee_id', $employee->id)
            ->whereBetween('advance_date', [$startDate, $endDate])
            ->sum('amount');

        $daysInMonth = $monthDate->daysInMonth;

        $perDaySalary = $daysInMonth > 0
            ? $employee->basic_salary / $daysInMonth
            : 0;

        $earnedSalary =
            ($presentDays * $perDaySalary)
            + ($halfDays * ($perDaySalary / 2));

        $netSalary = max(
            0,
            $earnedSalary - $advanceAmount
        );

        /*
         * Payroll record selected month ke liye auto create hoga.
         * Default payment_status migration ki wajah se pending hoga.
         */
        $payroll = Payroll::firstOrCreate(
            [
                'employee_id' => $employee->id,
                'month' => $monthName,
            ],
            [
                'basic_salary' => $employee->basic_salary,
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'leave_days' => $leaveDays,
                'advance_amount' => $advanceAmount,
                'gross_salary' => $earnedSalary,
                'net_salary' => $netSalary,
                'payment_status' => 'pending',
            ]
        );

        /*
         * Attendance ya advance change ho to salary amounts update hon.
         * Payment status change nahi hoga.
         */
        $payroll->update([
            'basic_salary' => $employee->basic_salary,
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'leave_days' => $leaveDays,
            'advance_amount' => $advanceAmount,
            'gross_salary' => $earnedSalary,
            'net_salary' => $netSalary,
        ]);

        return [
            'employee' => $employee,
            'payroll' => $payroll,

            'basic_salary' => $employee->basic_salary,
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'leave_days' => $leaveDays,
            'half_days' => $halfDays,

            'advance_amount' => $advanceAmount,
            'earned_salary' => $earnedSalary,
            'net_salary' => $netSalary,

            'payment_status' => $payroll->payment_status,
            'payment_date' => $payroll->payment_date,
        ];
    });

    $months = collect(range(1, 12))
        ->map(function ($monthNumber) use ($year) {
            return [
                'number' => str_pad(
                    $monthNumber,
                    2,
                    '0',
                    STR_PAD_LEFT
                ),
                'name' => Carbon::createFromDate(
                    $year,
                    $monthNumber,
                    1
                )->format('F'),
            ];
        });

    return view('payrolls.index', compact(
        'payrollRows',
        'months',
        'year',
        'month',
        'search'
    ));
}

  public function updatePaymentStatus(Request $request)
{
    $validated = $request->validate([
        'payroll_id' => [
            'required',
            'exists:payrolls,id',
        ],

        'payment_status' => [
            'required',
            'in:pending,paid',
        ],
    ]);

    $payroll = Payroll::findOrFail(
        $validated['payroll_id']
    );

    $payroll->update([
        'payment_status' => $validated['payment_status'],

        'payment_date' =>
            $validated['payment_status'] === 'paid'
                ? now()->toDateString()
                : null,
    ]);

    return back()->with(
        'success',
        $validated['payment_status'] === 'paid'
            ? 'Salary marked as paid successfully.'
            : 'Salary marked as unpaid successfully.'
    );
}

    public function storeAdvance(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric|min:1',
            'advance_date' => 'required|date',
            'advance_time' => 'nullable',
            'remarks' => 'nullable|string',
        ]);

        EmployeeAdvance::create([
            'employee_id' => $request->employee_id,
            'amount' => $request->amount,
            'advance_date' => $request->advance_date,
            'advance_time' => $request->advance_time,
            'remarks' => $request->remarks,
        ]);

        return back()->with('success', 'Advance added successfully.');
    }

    public function printSlips(Request $request)
    {
        $employeeIds = $request->employee_ids ?? [];

        if (empty($employeeIds)) {
            return back()->with('success', 'Please select at least one active employee.');
        }

        $year = $request->year ?? date('Y');
        $month = $request->month ?? date('m');

        $monthDate = Carbon::createFromDate($year, $month, 1);
        $startDate = $monthDate->copy()->startOfMonth()->toDateString();
        $endDate = $monthDate->copy()->endOfMonth()->toDateString();

        $employees = Employee::whereIn('id', $employeeIds)
            ->where('status', 'active')
            ->get();

        if ($employees->isEmpty()) {
            return back()->with('success', 'Inactive employee slip cannot be generated.');
        }

        $slips = $employees->map(function ($employee) use ($startDate, $endDate, $monthDate) {
            $presentDays = Attendance::where('employee_id', $employee->id)
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->where('status', 'present')
                ->count();

            $absentDays = Attendance::where('employee_id', $employee->id)
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->where('status', 'absent')
                ->count();

            $halfDays = Attendance::where('employee_id', $employee->id)
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->where('status', 'half_day')
                ->count();

            $advances = EmployeeAdvance::where('employee_id', $employee->id)
                ->whereBetween('advance_date', [$startDate, $endDate])
                ->orderBy('advance_date')
                ->orderBy('advance_time')
                ->get();

            $advanceAmount = $advances->sum('amount');

            $perDaySalary = $monthDate->daysInMonth > 0
                ? $employee->basic_salary / $monthDate->daysInMonth
                : 0;

            $earnedSalary = ($presentDays * $perDaySalary) + ($halfDays * ($perDaySalary / 2));
            $netSalary = $earnedSalary - $advanceAmount;

            return [
                'employee' => $employee,
                'month_name' => $monthDate->format('F Y'),
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'half_days' => $halfDays,
                'basic_salary' => $employee->basic_salary,
                'earned_salary' => $earnedSalary,
                'advance_amount' => $advanceAmount,
                'net_salary' => $netSalary,
                'advances' => $advances,
            ];
        });

        return view('payrolls.slip', compact('slips'));
    }

    public function slip(Payroll $payroll)
    {
        $payroll->load('employee');

        return view('payrolls.slip', compact('payroll'));
    }

    public function create()
    {
        return redirect()->route('payrolls.index');
    }

    public function store(Request $request)
    {
        return redirect()->route('payrolls.index');
    }

    public function edit(Payroll $payroll)
    {
        return redirect()->route('payrolls.index');
    }

    public function update(Request $request, Payroll $payroll)
    {
        return redirect()->route('payrolls.index');
    }

    public function destroy(Payroll $payroll)
    {
        $payroll->delete();

        return redirect()
            ->route('payrolls.index')
            ->with('success', 'Payroll deleted successfully.');
    }
}
