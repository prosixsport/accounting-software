<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('salary_cash_entries', function(Blueprint $table){
   $table->id();$table->string('type',20);$table->date('entry_date')->index();$table->decimal('amount',12,2);$table->string('description',500);$table->unsignedBigInteger('created_by')->nullable();$table->timestamps();
  });
 }
 public function down(): void { Schema::dropIfExists('salary_cash_entries'); }
};
