<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {Schema::create('fund_returns',function(Blueprint $table){$table->id();$table->date('return_date');$table->string('boss');$table->decimal('amount',15,2);$table->text('notes')->nullable();$table->uuid('submission_key')->unique();$table->foreignId('created_by')->constrained('users');$table->timestamps();$table->index('return_date');});}
 public function down(): void {Schema::dropIfExists('fund_returns');}
};
