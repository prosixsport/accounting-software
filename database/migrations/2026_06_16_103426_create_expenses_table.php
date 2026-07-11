<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {

            $table->id();

            $table->date('expense_date');

            $table->string('expense_no')->unique();

            $table->string('category');

            $table->unsignedBigInteger('account_id')->nullable();

            $table->string('vendor_name')->nullable();

            $table->string('paid_by')->nullable();

            $table->decimal('amount', 15, 2);

            $table->string('receipt')->nullable();

            $table->text('description')->nullable();

            $table->enum('payment_method', [
                'cash',
                'bank',
                'cheque',
                'online'
            ])->default('cash');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
