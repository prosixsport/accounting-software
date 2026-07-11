<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contractor_items', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            // Item Thumbnail
            $table->string('thumbnail')->nullable();

            // Unit
            $table->string('unit')->default('Piece');

            // Fixed Rate
            $table->decimal('rate', 12, 2)->default(0);

            // Status
            $table->enum('status', [
                'active',
                'inactive'
            ])->default('active');

            // Description
            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contractor_items');
    }
};
