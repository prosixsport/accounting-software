<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_alert_schedules', function (Blueprint $table) {
            $table->id();

            $table->string('title')->default('Monthly Salary Alert');
            $table->date('alert_date');
            $table->time('alert_time');

            $table->integer('month');
            $table->integer('year');

            $table->enum('status', ['pending', 'sent'])->default('pending');
            $table->timestamp('sent_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_alert_schedules');
    }
};
