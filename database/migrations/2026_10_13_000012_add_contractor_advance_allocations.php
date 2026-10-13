<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL DDL can partially commit before an index error: resume safely.
        if (!Schema::hasColumn('contractor_advances', 'eligible_for_bills')) {
            Schema::table('contractor_advances', fn (Blueprint $t) => $t->boolean('eligible_for_bills')->default(false));
        }
        if (!Schema::hasColumn('contractor_advances', 'allocation_limit')) {
            Schema::table('contractor_advances', fn (Blueprint $t) => $t->decimal('allocation_limit', 15, 2)->nullable());
        }
        if (!Schema::hasColumn('contractor_advances', 'submission_key')) {
            Schema::table('contractor_advances', fn (Blueprint $t) => $t->uuid('submission_key')->nullable());
        }
        if (!Schema::hasColumn('contractor_advances', 'created_by')) {
            Schema::table('contractor_advances', fn (Blueprint $t) => $t->foreignId('created_by')->nullable());
        }
        $this->ensureUnique('contractor_advances', ['submission_key'], 'ca_submission_uq');
        $this->ensureForeign('contractor_advances', 'created_by', 'users', 'ca_created_by_fk', true);

        if (!Schema::hasColumn('contractor_bills', 'advance_adjusted')) {
            Schema::table('contractor_bills', fn (Blueprint $t) => $t->decimal('advance_adjusted', 15, 2)->default(0));
        }
        if (!Schema::hasColumn('contractor_bills', 'submission_key')) {
            Schema::table('contractor_bills', fn (Blueprint $t) => $t->uuid('submission_key')->nullable());
        }
        $this->ensureUnique('contractor_bills', ['submission_key'], 'cb_submission_uq');
        if (!Schema::hasColumn('contractor_bill_payments', 'submission_key')) {
            Schema::table('contractor_bill_payments', fn (Blueprint $t) => $t->uuid('submission_key')->nullable());
        }
        $this->ensureUnique('contractor_bill_payments', ['submission_key'], 'cbp_submission_uq');

        if (!Schema::hasTable('contractor_advance_allocations')) {
            Schema::create('contractor_advance_allocations', function (Blueprint $t) {
                $t->id();
                $t->foreignId('contractor_advance_id');
                $t->foreignId('contractor_bill_id');
                $t->decimal('amount', 15, 2);
                $t->timestamps();
            });
        }
        $this->ensureUnique('contractor_advance_allocations', ['contractor_advance_id', 'contractor_bill_id'], 'caa_advance_bill_uq');
        $this->ensureForeign('contractor_advance_allocations', 'contractor_advance_id', 'contractor_advances', 'caa_advance_fk');
        $this->ensureForeign('contractor_advance_allocations', 'contractor_bill_id', 'contractor_bills', 'caa_bill_fk');

        if (!Schema::hasTable('contractor_bill_sequence')) {
            Schema::create('contractor_bill_sequence', function (Blueprint $t) {
                $t->unsignedTinyInteger('id')->primary();
                $t->unsignedBigInteger('next_number');
            });
        }
        $max = 0;
        DB::table('contractor_bills')->orderBy('id')->chunkById(500, function ($rows) use (&$max) {
            foreach ($rows as $row) {
                if (preg_match('/^CB-(\d+)$/D', $row->bill_no, $matches)) {
                    $max = max($max, (int) $matches[1]);
                }
            }
        });
        DB::table('contractor_bill_sequence')->insertOrIgnore(['id' => 1, 'next_number' => $max + 1]);
        DB::table('contractor_bill_sequence')->where('id', 1)->where('next_number', '<', $max + 1)->update(['next_number' => $max + 1]);
    }

    private function ensureUnique(string $table, array $columns, string $name): void
    {
        foreach (Schema::getIndexes($table) as $index) {
            if ($index['unique'] && $index['columns'] === $columns) return;
        }
        Schema::table($table, fn (Blueprint $t) => $t->unique($columns, $name));
    }

    private function ensureForeign(string $table, string $column, string $target, string $name, bool $nullOnDelete = false): void
    {
        foreach (Schema::getForeignKeys($table) as $foreign) {
            if ($foreign['columns'] === [$column]) return;
        }
        Schema::table($table, function (Blueprint $t) use ($column, $target, $name, $nullOnDelete) {
            $foreign = $t->foreign($column, $name)->references('id')->on($target);
            if ($nullOnDelete) $foreign->nullOnDelete();
            else $foreign->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contractor_advance_allocations');
        Schema::dropIfExists('contractor_bill_sequence');
        Schema::table('contractor_bill_payments', fn (Blueprint $t) => $t->dropColumn('submission_key'));
        Schema::table('contractor_bills', fn (Blueprint $t) => $t->dropColumn(['advance_adjusted', 'submission_key']));
        foreach (Schema::getForeignKeys('contractor_advances') as $foreign) {
            if ($foreign['columns'] === ['created_by']) Schema::table('contractor_advances', fn (Blueprint $t) => $t->dropForeign($foreign['name']));
        }
        Schema::table('contractor_advances', function (Blueprint $t) {
            $t->dropColumn('created_by');
            $t->dropColumn(['submission_key', 'eligible_for_bills', 'allocation_limit']);
        });
    }
};
