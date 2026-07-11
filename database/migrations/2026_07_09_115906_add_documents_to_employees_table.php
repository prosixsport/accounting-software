<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->json('pictures')->nullable()->after('address');
            $table->json('cnic_pictures')->nullable()->after('pictures');
            $table->json('other_documents')->nullable()->after('cnic_pictures');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['pictures', 'cnic_pictures', 'other_documents']);
        });
    }
};
