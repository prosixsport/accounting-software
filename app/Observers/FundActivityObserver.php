<?php
namespace App\Observers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class FundActivityObserver {
 private function record(Model $model, string $action): void {
  DB::table('fund_activity')->insert([
   'source'=>$model->getTable(),'source_id'=>$model->getKey(),
   'user_id'=>auth()->id(),'actor'=>auth()->user()?->name ?? 'System / command',
   'action'=>$action,'before'=>$action==='created'?null:json_encode($model->getRawOriginal()),
   'after'=>$action==='deleted'?null:json_encode($model->getAttributes()),'created_at'=>now(),
  ]);
 }
 public function created(Model $m): void { $this->record($m,'created'); }
 public function updated(Model $m): void { $this->record($m,'updated'); }
 public function deleted(Model $m): void { $this->record($m,'deleted'); }
}
