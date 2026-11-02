<?php
namespace App\Services;

use App\Models\SalaryManagementRow;
use Illuminate\Validation\ValidationException;

class SalaryAdvanceCarry
{
    // Carry is a non-cash adjustment. Never create an advance payment for it.
    public function incoming(int $employeeId, string $month): float
    {
        $carry = 0;
        $rows = SalaryManagementRow::with('advances')->where('employee_id', $employeeId)
            ->where('month', '<', $month)->orderBy('month')->orderBy('id')->get();
        foreach ($rows as $row) {
            // Paid historical rows retain their recorded carry; unpaid rows recalculate.
            $incoming = (float)$row->paid_amount > 0
                ? FundsLedger::paise((string)($row->advance_carry_in ?? 0)) : $carry;
            $daily = (float)$row->salary / 30;
            $ot = round((float)$row->ot_hours * $daily / max(1, (float)($row->working_hours_per_day ?? 10)), 2);
            $absence = round((float)$row->absent_days * $daily, 2);
            $available = FundsLedger::paise(number_format(max(0, round((float)$row->salary + $ot - $absence
                - (float)$row->loan_deduction - (float)$row->other_deduction + (float)$row->overdue, 2)), 2, '.', ''));
            $advance = 0;
            foreach ($row->advances as $item) $advance += FundsLedger::paise((string)$item->amount);
            $carry = $this->balanceAfter($incoming, $advance, $available);
        }
        return $carry / 100;
    }

    public function balanceAfter(int $incoming, int $cashAdvances, int $availableSalary): int
    {
        return max(0, $incoming + $cashAdvances - max(0, $availableSalary));
    }

    public function assertEditable(int $employeeId, string $month): void
    {
        if (SalaryManagementRow::where('employee_id', $employeeId)->where('month', '>', $month)->where('paid_amount', '>', 0)->exists()) {
            throw ValidationException::withMessages(['salary'=>'A later month salary has already been paid. Mark the affected later salary Unpaid before changing this earlier record.']);
        }
    }
}
