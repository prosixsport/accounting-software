<?php
namespace Tests\Unit;
use App\Services\FundsLedger;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\TestCase;
class FundsLedgerTest extends TestCase
{
    public function test_money_is_exact_to_the_paisa(): void
    {
        $this->assertSame(30,FundsLedger::paise('0.10')+FundsLedger::paise('0.20'));
        $this->assertSame(-101,FundsLedger::paise('-1.01'));
        $this->assertSame('2,000,000.01',FundsLedger::money(FundsLedger::paise('2000000.01')));
    }
    private function entry($date,$in,$out,$reference='test#1'): array
    {
        return ['date'=>$date,'in'=>$in,'out'=>$out,'reference'=>$reference,'type'=>'Boss funds','party'=>'Azeem'];
    }
    public function test_closing_carries_to_next_month_and_future_payments_are_excluded(): void
    {
        $service=new FundsLedger;
        $entries=[$this->entry('2026-09-01',200000000,0),$this->entry('2026-09-30',0,50000000),$this->entry('2026-10-01',10000000,0),$this->entry('2026-10-01',0,6000000),$this->entry('2026-10-09',0,200000)];
        $today=Carbon::parse('2026-10-01');
        $sep=$service->month($entries,Carbon::parse('2026-09-01'),$today);
        $oct=$service->month($entries,Carbon::parse('2026-10-01'),$today);
        $this->assertSame(150000000,$sep['closing']);
        $this->assertSame($sep['closing'],$oct['opening']);
        $this->assertSame(154000000,$oct['closing']);
        $this->assertCount(1,$oct['future']);
        $this->assertSame($oct['closing'],$oct['ledger'][1]['balance']);
    }
    public function test_equal_cross_module_records_are_flagged_not_silently_removed(): void
    {
        $service=new FundsLedger;
        $entries=[$this->entry('2026-10-01',10000,0,'owner_funds#1'),$this->entry('2026-10-01',10000,0,'salary_cash_entries#1')];
        $this->assertCount(1,$service->review($entries));
        $report=$service->month($entries,Carbon::parse('2026-10-01'),Carbon::parse('2026-10-01'));
        $this->assertSame(20000,$report['received']);
    }
}
