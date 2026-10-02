<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('fund_receipts', function(Blueprint $t) {
   $t->id(); $t->date('receipt_date'); $t->string('boss'); $t->decimal('amount',15,2);
   $t->string('method')->default('cash'); $t->text('notes')->nullable();
   $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
   $t->uuid('submission_key')->unique(); $t->timestamps();
  });
  Schema::create('fund_activity', function(Blueprint $t) {
   $t->id(); $t->string('source'); $t->unsignedBigInteger('source_id');
   $t->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
   $t->string('actor'); $t->string('action'); $t->json('before')->nullable(); $t->json('after')->nullable();
   $t->timestamp('created_at'); $t->index(['source','source_id']);
  });
 }
 public function down(): void { Schema::dropIfExists('fund_activity'); Schema::dropIfExists('fund_receipts'); }
};
