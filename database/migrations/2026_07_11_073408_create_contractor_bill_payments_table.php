<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contractor_bill_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('contractor_bill_id')
                ->constrained('contractor_bills')
                ->cascadeOnDelete();

            $table->decimal('amount', 15, 2);

            $table->date('payment_date');

            $table->time('payment_time')
                ->nullable();

            $table->text('remarks')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contractor_bill_payments');
    }
};
