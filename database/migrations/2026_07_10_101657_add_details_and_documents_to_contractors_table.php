<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contractors', function (Blueprint $table) {
            $table->string('father_name')->nullable()->after('name');

            $table->string('cnic', 15)
                ->nullable()
                ->unique()
                ->after('phone');

            $table->foreignId('contractor_department_id')
                ->nullable()
                ->after('cnic')
                ->constrained('contractor_departments')
                ->nullOnDelete();

            $table->foreignId('contractor_machine_id')
                ->nullable()
                ->after('contractor_department_id')
                ->constrained('contractor_machines')
                ->nullOnDelete();

            $table->json('pictures')->nullable();
            $table->json('cnic_pictures')->nullable();
            $table->json('other_documents')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('contractors', function (Blueprint $table) {
            $table->dropForeign(['contractor_department_id']);
            $table->dropForeign(['contractor_machine_id']);

            $table->dropColumn([
                'father_name',
                'cnic',
                'contractor_department_id',
                'contractor_machine_id',
                'pictures',
                'cnic_pictures',
                'other_documents',
            ]);
        });
    }
};
