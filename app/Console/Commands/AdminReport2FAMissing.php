<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminReport2FAMissing extends Command
{
    protected $signature = 'admin:report-2fa-missing {--csv}';
    protected $description = 'Report admin accounts missing two-factor authentication.';

    public function handle()
    {
        $rows = DB::table('users')
            ->select('id', 'email')
            ->where(function ($q) {
                $q->where('role', 'admin')->orWhere('is_admin', 1);
            })
            ->where(function ($q) {
                $q->whereNull('two_factor_enabled')->orWhere('two_factor_enabled', 0);
            })
            ->get();

        if ($rows->isEmpty()) {
            $this->info('No admin accounts missing 2FA.');
            return 0;
        }

        $this->info('Found ' . $rows->count() . ' admin accounts missing 2FA:');
        foreach ($rows as $r) {
            $this->line("- {$r->id} <{$r->email}>");
        }

        if ($this->option('csv')) {
            $path = storage_path('app/reports');
            if (!is_dir($path)) mkdir($path, 0755, true);
            $file = $path . DIRECTORY_SEPARATOR . 'admins_missing_2fa.csv';
            $fh = fopen($file, 'w');
            fputcsv($fh, ['id', 'email']);
            foreach ($rows as $r) fputcsv($fh, [$r->id, $r->email]);
            fclose($fh);
            $this->info('CSV written to: ' . $file);
        }

        return 0;
    }
}
