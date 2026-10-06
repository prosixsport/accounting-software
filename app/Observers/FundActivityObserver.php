<?php
namespace App\Observers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class FundActivityObserver {
 private function record(Model $model, string $action): void {
  if ($action==='updated') {
   $changes=$model->getChanges(); unset($changes['updated_at']);
   $normalize=function($value) {
    $text=(string)$value;
    if(preg_match('/^-?\d+(?:\.\d+)?$/D',$text)) {
     if(str_contains($text,'.')) $text=rtrim(rtrim($text,'0'),'.');
     return $text==='-0'?'0':$text;
    }
    return $text;
   };
   $meaningful=array_filter($changes,fn($v,$k)=>$normalize($model->getRawOriginal($k))!==$normalize($v),ARRAY_FILTER_USE_BOTH);
   if(!$meaningful) return;
  }
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
