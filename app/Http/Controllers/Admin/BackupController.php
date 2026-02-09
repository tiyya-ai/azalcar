<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class BackupController extends Controller
{
    /**
     * Display a listing of backups.
     */
    public function index()
    {
        $backups = collect(Storage::disk('backups')->files())
            ->filter(function ($file) {
                return str_ends_with($file, '.zip') || str_ends_with($file, '.sql');
            })
            ->map(function ($file) {
                return [
                    'name' => $file,
                    'size' => Storage::disk('backups')->size($file),
                    'created_at' => Storage::disk('backups')->lastModified($file),
                    'url' => route('admin.backups.download', $file)
                ];
            })
            ->sortByDesc('created_at')
            ->values();

        return view('admin.backups.index', compact('backups'));
    }

    /**
     * Create a new backup.
     */
    public function create(Request $request)
    {
        $type = $request->get('type', 'database'); // database, files, full

        try {
            $filename = $this->generateBackupFilename($type);

            switch ($type) {
                case 'database':
                    $this->createDatabaseBackup($filename);
                    break;
                case 'files':
                    $this->createFilesBackup($filename);
                    break;
                case 'full':
                    $this->createFullBackup($filename);
                    break;
            }

            return back()->with('success', ucfirst($type) . ' backup created successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Download a backup file.
     */
    public function download($filename)
    {
        if (!Storage::disk('backups')->exists($filename)) {
            abort(404);
        }

        return Storage::disk('backups')->download($filename);
    }

    /**
     * Delete a backup file.
     */
    public function destroy($filename)
    {
        if (!Storage::disk('backups')->exists($filename)) {
            return back()->with('error', 'Backup file not found.');
        }

        Storage::disk('backups')->delete($filename);

        return back()->with('success', 'Backup deleted successfully.');
    }

    /**
     * Generate a backup filename.
     */
    private function generateBackupFilename($type)
    {
        return $type . '_backup_' . date('Y-m-d_H-i-s') . '.zip';
    }

    /**
     * Create a database backup.
     */
    private function createDatabaseBackup($filename)
    {
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port', 3306);

        $command = "mysqldump --user={$username} --password={$password} --host={$host} --port={$port} {$database} > " . storage_path('app/backups/temp.sql');

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception('Database dump failed');
        }

        // Create zip file
        $zip = new \ZipArchive();
        $zipPath = storage_path('app/backups/' . $filename);

        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            $zip->addFile(storage_path('app/backups/temp.sql'), 'database.sql');
            $zip->close();
        }

        // Clean up temp file
        unlink(storage_path('app/backups/temp.sql'));
    }

    /**
     * Create a files backup.
     */
    private function createFilesBackup($filename)
    {
        $zip = new \ZipArchive();
        $zipPath = storage_path('app/backups/' . $filename);

        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            $this->addDirectoryToZip(storage_path('app/public'), $zip, 'storage');
            $zip->close();
        }
    }

    /**
     * Create a full backup.
     */
    private function createFullBackup($filename)
    {
        // Create database backup first
        $this->createDatabaseBackup('temp_db.zip');

        $zip = new \ZipArchive();
        $zipPath = storage_path('app/backups/' . $filename);

        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            // Add database backup
            $zip->addFile(storage_path('app/backups/temp_db.zip'), 'database.zip');

            // Add files
            $this->addDirectoryToZip(storage_path('app/public'), $zip, 'storage');

            $zip->close();
        }

        // Clean up temp database backup
        unlink(storage_path('app/backups/temp_db.zip'));
    }

    /**
     * Add directory to zip recursively.
     */
    private function addDirectoryToZip($directory, $zip, $zipPath = '')
    {
        $files = scandir($directory);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;

            $filePath = $directory . '/' . $file;
            $relativePath = $zipPath ? $zipPath . '/' . $file : $file;

            if (is_dir($filePath)) {
                $zip->addEmptyDir($relativePath);
                $this->addDirectoryToZip($filePath, $zip, $relativePath);
            } else {
                $zip->addFile($filePath, $relativePath);
            }
        }
    }

    /**
     * Get backup statistics.
     */
    public function stats()
    {
        $backups = collect(Storage::disk('backups')->files())
            ->filter(function ($file) {
                return str_ends_with($file, '.zip') || str_ends_with($file, '.sql');
            });

        $totalSize = $backups->sum(function ($file) {
            return Storage::disk('backups')->size($file);
        });

        $lastBackup = $backups->map(function ($file) {
            return Storage::disk('backups')->lastModified($file);
        })->max();

        return [
            'total_backups' => $backups->count(),
            'total_size' => $totalSize,
            'last_backup' => $lastBackup ? date('Y-m-d H:i:s', $lastBackup) : null,
        ];
    }
}