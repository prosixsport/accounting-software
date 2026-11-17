<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('house_expense_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 40);
            $table->unique(['user_id', 'name']);
        });

        Schema::table('house_expenses', function (Blueprint $table) {
            $table->string('location', 150)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('house_expenses', function (Blueprint $table) {
            $table->dropColumn('location');
        });
        Schema::dropIfExists('house_expense_categories');
    }
};
