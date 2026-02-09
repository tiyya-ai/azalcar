<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class ArchiveDuplicateTransactions extends Command
{
    protected $signature = 'transactions:archive-duplicates {--batch=200}';
    protected $description = 'Archive duplicate transactions by reference_id and normalize non-canonical rows (append -dup-<id>).';

    public function handle()
    {
        $batch = (int) $this->option('batch');

        // Ensure archive table exists (simple structured archive containing JSON payload)
        if (!Schema::hasTable('transactions_duplicates')) {
            Schema::create('transactions_duplicates', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('original_id')->index();
                $table->string('reference_id')->nullable()->index();
                $table->longText('payload')->nullable();
                $table->timestamp('archived_at')->useCurrent();
            });
            $this->info('Created table transactions_duplicates');
        }

        $duplicates = DB::table('transactions')
            ->select('reference_id', DB::raw('COUNT(*) as cnt'))
            ->whereNotNull('reference_id')
            ->where('reference_id', '!=', '')
            ->groupBy('reference_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('cnt', 'reference_id');

        if ($duplicates->isEmpty()) {
            $this->info('No duplicate reference_id values found. Nothing to do.');
            return 0;
        }

        $this->info('Found ' . $duplicates->count() . ' duplicate reference_id values. Archiving and normalizing in batches of ' . $batch);

        foreach ($duplicates as $ref => $cnt) {
            $rows = DB::table('transactions')->where('reference_id', $ref)->orderBy('id')->get();
            if ($rows->count() <= 1) continue;

            // Keep the first as canonical
            $first = true;
            foreach ($rows as $row) {
                if ($first) { $first = false; continue; }

                // Insert archive record
                DB::table('transactions_duplicates')->insert([
                    'original_id' => $row->id,
                    'reference_id' => $row->reference_id,
                    'payload' => json_encode($row),
                    'archived_at' => now(),
                ]);

                // Normalize the original row to make reference_id unique (append suffix)
                $newRef = $row->reference_id . '-dup-' . $row->id;
                $newDesc = ($row->description ?? '') . ' [duplicate reference_id normalized]';
                DB::table('transactions')->where('id', $row->id)->update([
                    'reference_id' => $newRef,
                    'description' => $newDesc,
                ]);
            }
            $this->line("Processed duplicates for reference_id={$ref} (count={$cnt})");
        }

        $this->info('Archiving/normalization complete. Run verification queries and then create unique index per runbook.');
        return 0;
    }
}
