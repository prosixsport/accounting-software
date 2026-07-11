<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_alerts', function (Blueprint $table) {
            $table->id();

            $table->integer('month');
            $table->integer('year');

            $table->decimal('employees_salary', 15, 2)->default(0);
            $table->decimal('contractor_bills', 15, 2)->default(0);
            $table->decimal('factory_expenses', 15, 2)->default(0);
            $table->decimal('total_required', 15, 2)->default(0);

            $table->enum('status', ['pending', 'arranged'])->default('pending');

            $table->timestamp('email_sent_at')->nullable();
            $table->timestamp('arranged_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_alerts');
    }
};
