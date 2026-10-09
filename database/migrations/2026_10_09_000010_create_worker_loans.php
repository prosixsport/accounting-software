<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void {
  Schema::create('worker_loans',function(Blueprint $t){$t->id();$t->foreignId('employee_id')->constrained('employees')->restrictOnDelete();$t->date('loan_date');$t->date('start_month');$t->decimal('amount',15,2);$t->decimal('installment',15,2);$t->string('given_by');$t->text('notes')->nullable();$t->uuid('submission_key')->unique();$t->foreignId('created_by')->constrained('users');$t->timestamps();});
  Schema::create('worker_loan_installments',function(Blueprint $t){$t->id();$t->foreignId('loan_id')->constrained('worker_loans')->restrictOnDelete();$t->foreignId('salary_row_id')->constrained('salary_management_rows')->cascadeOnDelete();$t->decimal('amount',15,2);$t->unique(['loan_id','salary_row_id']);$t->timestamps();});
  Schema::table('fund_returns',function(Blueprint $t){$t->string('returned_by')->nullable();});
 }
 public function down():void {Schema::table('fund_returns',fn(Blueprint $t)=>$t->dropColumn('returned_by'));Schema::dropIfExists('worker_loan_installments');Schema::dropIfExists('worker_loans');}
};
