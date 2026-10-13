<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void {
  if(!Schema::hasTable('expense_advances'))Schema::create('expense_advances',function(Blueprint $t){$t->id();$t->string('recipient_name');$t->date('advance_date');$t->decimal('amount',15,2);$t->string('purpose',1000)->nullable();$t->uuid('submission_key')->unique('ea_submit_uq');$t->unsignedBigInteger('created_by')->nullable();$t->timestamps();});
  if(!Schema::hasColumn('expenses','expense_advance_id'))Schema::table('expenses',function(Blueprint $t){$t->unsignedBigInteger('expense_advance_id')->nullable()->index('exp_advance_idx');$t->foreign('expense_advance_id','exp_advance_fk')->references('id')->on('expense_advances')->restrictOnDelete();});
 }
 public function down():void {if(Schema::hasColumn('expenses','expense_advance_id'))Schema::table('expenses',function(Blueprint $t){$t->dropForeign('exp_advance_fk');$t->dropColumn('expense_advance_id');});Schema::dropIfExists('expense_advances');}
};
