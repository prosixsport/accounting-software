<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('salary_management_advances', function (Blueprint $table) {
            $table->unsignedTinyInteger('advance_week')->nullable();
        });
    }
    public function down(): void {
        Schema::table('salary_management_advances', function (Blueprint $table) {
            $table->dropColumn('advance_week');
        });
    }
};
