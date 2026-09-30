<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SalaryManagementRow extends Model {
 protected $guarded = ['id'];
 protected $casts = ['month'=>'date'];
 public function advances() { return $this->hasMany(SalaryManagementAdvance::class,'salary_row_id')->orderBy('advance_date')->orderBy('id'); }
 public function figures(): array {
  $advance=round((float)$this->advances->sum('amount'),2);
  $absence=round((float)$this->absent_days*(float)$this->day_rate,2);
  $ot=round((float)$this->ot_hours*(float)$this->ot_rate,2);
  $net=round((float)$this->salary+$ot-$absence-$advance-(float)$this->loan_deduction-(float)$this->other_deduction,2);
  return ['advance'=>$advance,'absence'=>$absence,'ot'=>$ot,'net'=>$net,'due'=>round($net+(float)$this->overdue-(float)$this->paid_amount,2)];
 }
}
