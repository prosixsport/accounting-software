<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {

            $table->foreignId('expense_category_id')
                ->nullable()
                ->after('expense_no')
                ->constrained('expense_categories')
                ->nullOnDelete();

            $table->foreignId('expense_sub_category_id')
                ->nullable()
                ->after('expense_category_id')
                ->constrained('expense_sub_categories')
                ->nullOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {

            $table->dropForeign(['expense_category_id']);
            $table->dropForeign(['expense_sub_category_id']);

            $table->dropColumn([
                'expense_category_id',
                'expense_sub_category_id'
            ]);

        });
    }
};
