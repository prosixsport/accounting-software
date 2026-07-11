<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contractor_bills', function (Blueprint $table) {
            $table->id();

            $table->string('bill_no')->unique();

            $table->foreignId('contractor_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('bill_date');

            $table->decimal('grand_total', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('balance', 12, 2)->default(0);

            $table->enum('status', [
                'Pending',
                'Partial',
                'Paid'
            ])->default('Pending');

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contractor_bills');
    }
};
