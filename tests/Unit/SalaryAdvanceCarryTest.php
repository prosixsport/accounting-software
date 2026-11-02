<?php
namespace Tests\Unit;
use App\Models\SalaryManagementRow;
use App\Models\SalaryManagementAdvance;
use App\Services\SalaryAdvanceCarry;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\TestCase;

class SalaryAdvanceCarryTest extends TestCase
{
    public function test_excess_is_recovered_once_across_months(): void
    {
        $service = new SalaryAdvanceCarry;
        $october = $service->balanceAfter(0, 4000000, 2000000);
        $this->assertSame(2000000, $october);
        $november = $service->balanceAfter($october, 500000, 2000000);
        $this->assertSame(500000, $november);
        $this->assertSame(0, $service->balanceAfter($november, 0, 2000000));
    }
    public function test_unused_salary_is_not_carried_as_advance_debt(): void
    {
        $service = new SalaryAdvanceCarry;
        $this->assertSame(0, $service->balanceAfter(0, 1000000, 2000000));
        $this->assertSame(4000000, $service->balanceAfter(0, 4000000, -100));
        $this->assertSame(1, $service->balanceAfter(0, 2000001, 2000000));
    }
    public function test_paid_salary_uses_recorded_carry_and_deducts_only_once(): void
    {
        $row = new SalaryManagementRow(['salary'=>20000,'paid_amount'=>5000,'advance_carry_in'=>10000,
            'absent_days'=>0,'ot_hours'=>0,'loan_deduction'=>0,'other_deduction'=>0,'overdue'=>0]);
        $row->setRelation('advances', new Collection([new SalaryManagementAdvance(['amount'=>5000])]));
        $figures = $row->figures();
        $this->assertSame(5000.0, $figures['net']);
        $this->assertSame(0.0, $figures['due']);
        $this->assertEquals(0, $figures['carry_out']);
        $this->assertSame(5000.0, $figures['advance']); // cash only, not carry
    }
}
