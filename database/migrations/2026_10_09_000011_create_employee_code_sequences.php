<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('employee_code_sequences',function(Blueprint $table){$table->unsignedTinyInteger('id')->primary();$table->unsignedBigInteger('next_number');});
  $highest=0;
  DB::table('employees')->select('id','employee_code')->orderBy('id')->chunkById(500,function($employees)use(&$highest){foreach($employees as $employee){if(preg_match('/^EMP-(\d+)$/D',(string)$employee->employee_code,$matches))$highest=max($highest,(int)$matches[1]);}});
  DB::table('employee_code_sequences')->insert(['id'=>1,'next_number'=>$highest+1]);
 }
 public function down(): void {Schema::dropIfExists('employee_code_sequences');}
};
