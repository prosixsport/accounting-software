<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contractor_items', function (Blueprint $table) {
            $table->foreignId('contractor_machine_id')
                ->nullable()
                ->after('rate')
                ->constrained('contractor_machines')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('contractor_items', function (Blueprint $table) {
            $table->dropForeign([
                'contractor_machine_id',
            ]);

            $table->dropColumn(
                'contractor_machine_id'
            );
        });
    }
};
