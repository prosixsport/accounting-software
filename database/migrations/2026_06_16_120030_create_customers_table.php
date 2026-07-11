<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {

            $table->id();

            $table->string('customer_code')->unique();

            $table->string('customer_name');

            $table->string('company_name')->nullable();

            $table->string('phone')->nullable();

            $table->string('email')->nullable();

            $table->text('address')->nullable();

            $table->decimal('opening_balance', 15, 2)
                ->default(0);

            $table->enum('status', [
                'active',
                'inactive'
            ])->default('active');

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
