<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('owner_funds', function (Blueprint $table) {
            $table->id();

            $table->date('fund_date');

            $table->string('owner_name');

            $table->decimal('amount', 15, 2);

            $table->enum('received_in', [
                'cash',
                'bank',
            ])->default('cash');

            $table->string('purpose');

            $table->text('description')->nullable();

            $table->string('reference_number')->nullable();

            $table->string('attachment')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('owner_funds');
    }
};
