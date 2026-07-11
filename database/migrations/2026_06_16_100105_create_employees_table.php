<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {

            $table->id();

            $table->string('employee_code')->unique();

            $table->string('name');

            $table->string('father_name')->nullable();

            $table->string('phone')->nullable();

            $table->string('email')->nullable();

            $table->string('cnic')->nullable();

            $table->string('department')->nullable();

            $table->string('designation')->nullable();

            $table->decimal('basic_salary', 15, 2)->default(0);

            $table->date('joining_date')->nullable();

            $table->enum('status', [
                'active',
                'inactive'
            ])->default('active');

            $table->text('address')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
