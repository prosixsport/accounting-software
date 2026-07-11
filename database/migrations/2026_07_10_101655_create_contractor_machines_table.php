<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contractor_machines', function (Blueprint $table) {
            $table->id();

            $table->foreignId('contractor_department_id')
                ->constrained('contractor_departments')
                ->cascadeOnDelete();

            $table->string('name');

            $table->enum('status', ['active', 'inactive'])
                ->default('active');

            $table->timestamps();

            $table->unique(
                ['contractor_department_id', 'name'],
                'department_machine_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contractor_machines');
    }
};
