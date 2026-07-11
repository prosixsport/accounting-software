<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contractor_advances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('contractor_id')
                ->constrained('contractors')
                ->cascadeOnDelete();

            $table->decimal('amount', 15, 2);

            $table->date('advance_date');

            $table->time('advance_time')
                ->nullable();

            $table->text('remarks')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contractor_advances');
    }
};
