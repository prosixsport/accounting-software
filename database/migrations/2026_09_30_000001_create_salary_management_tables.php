<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('salary_management_rows', function(Blueprint $t) {
   $t->id(); $t->unsignedBigInteger('employee_id'); $t->date('month');
   foreach(['salary','loan_balance','absent_days','day_rate','ot_hours','ot_rate','loan_deduction','other_deduction','overdue','paid_amount'] as $field) $t->decimal($field,12,2)->default(0);
   $t->text('notes')->nullable(); $t->timestamps(); $t->unique(['employee_id','month']);
  });
  Schema::create('salary_management_advances', function(Blueprint $t) {
   $t->id(); $t->foreignId('salary_row_id')->constrained('salary_management_rows')->cascadeOnDelete();
   $t->date('advance_date'); $t->decimal('amount',12,2); $t->text('reason')->nullable(); $t->timestamps();
  });
 }
 public function down(): void { Schema::dropIfExists('salary_management_advances'); Schema::dropIfExists('salary_management_rows'); }
};
