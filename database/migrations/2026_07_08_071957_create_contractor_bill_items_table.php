<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contractor_bill_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('contractor_bill_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('contractor_item_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('item_name');
            $table->decimal('quantity', 12, 2)->default(0);
            $table->decimal('rate', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contractor_bill_items');
    }
};
