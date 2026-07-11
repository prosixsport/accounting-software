<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {

            $table->id();

            $table->foreignId('employee_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('month'); // June 2026

            $table->decimal('basic_salary', 15, 2)->default(0);

            $table->integer('present_days')->default(0);

            $table->integer('absent_days')->default(0);

            $table->integer('leave_days')->default(0);

            $table->decimal('overtime_hours', 8, 2)->default(0);

            $table->decimal('overtime_amount', 15, 2)->default(0);

            $table->decimal('bonus', 15, 2)->default(0);

            $table->decimal('advance_amount', 15, 2)->default(0);

            $table->decimal('deduction_amount', 15, 2)->default(0);

            $table->decimal('gross_salary', 15, 2)->default(0);

            $table->decimal('net_salary', 15, 2)->default(0);

            $table->enum('payment_status', [
                'pending',
                'paid'
            ])->default('pending');

            $table->date('payment_date')->nullable();

            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->unique([
                'employee_id',
                'month'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
