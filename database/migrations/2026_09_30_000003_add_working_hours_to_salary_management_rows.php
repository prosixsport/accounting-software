<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salary_management_rows', function (Blueprint $table) {
            $table->decimal('working_hours_per_day', 5, 2)->default(8);
        });
    }

    public function down(): void
    {
        Schema::table('salary_management_rows', function (Blueprint $table) {
            $table->dropColumn('working_hours_per_day');
        });
    }
};
