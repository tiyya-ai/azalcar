<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Find duplicate non-null reference_id values
        $duplicates = DB::table('transactions')
            ->select('reference_id', DB::raw('COUNT(*) as cnt'))
            ->whereNotNull('reference_id')
            ->groupBy('reference_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        if ($duplicates->isNotEmpty()) {
            Log::warning('Found duplicate reference_id values in transactions table. Normalizing duplicates before adding unique index.', ['duplicates_count' => $duplicates->count()]);

            foreach ($duplicates as $dup) {
                $rows = DB::table('transactions')
                    ->where('reference_id', $dup->reference_id)
                    ->orderBy('id')
                    ->get();

                // Keep the first as canonical, and modify others to be unique by appending suffix
                $first = true;
                foreach ($rows as $row) {
                    if ($first) { $first = false; continue; }
                    $newRef = $row->reference_id . '-dup-' . $row->id;
                    DB::table('transactions')->where('id', $row->id)->update([
                        'reference_id' => $newRef,
                        'description' => ($row->description ?? '') . " [duplicate reference_id normalized]",
                    ]);
                }
            }
        }

        // Step 2: Add unique index on reference_id
        // Use Schema::table to add index; if DB supports concurrently, consider manual SQL
        try {
            Schema::table('transactions', function (Blueprint $table) {
                if (!Schema::hasColumn('transactions', 'reference_id')) {
                    // Nothing to do if column missing
                    return;
                }
                // Create unique index
                $table->unique('reference_id', 'transactions_reference_id_unique');
            });
        } catch (\Exception $e) {
            Log::warning('Could not create unique index transactions_reference_id_unique: ' . $e->getMessage());
        }
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'reference_id')) {
                $table->dropUnique('transactions_reference_id_unique');
            }
        });
    }
};
