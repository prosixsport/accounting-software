<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contractors', function (Blueprint $table) {
            if (!Schema::hasColumn('contractors', 'cnic_front')) {
                $table->string('cnic_front')->nullable()->after('notes');
            }

            if (!Schema::hasColumn('contractors', 'cnic_back')) {
                $table->string('cnic_back')->nullable()->after('cnic_front');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contractors', function (Blueprint $table) {
            if (Schema::hasColumn('contractors', 'cnic_back')) {
                $table->dropColumn('cnic_back');
            }

            if (Schema::hasColumn('contractors', 'cnic_front')) {
                $table->dropColumn('cnic_front');
            }
        });
    }
};
