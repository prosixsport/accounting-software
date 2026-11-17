<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('house_expenses', function (Blueprint $table) {
            $table->string('money_taken_by', 100)->nullable();
            $table->string('money_received_from', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('house_expenses', function (Blueprint $table) {
            $table->dropColumn(['money_taken_by', 'money_received_from']);
        });
    }
};
