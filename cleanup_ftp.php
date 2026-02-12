<?php
/**
 * FTP Cleanup Script - Remove duplicates and ZIP files
 * 
 * This script:
 * 1. Removes duplicate Laravel files from public_html
 * 2. Deletes unnecessary ZIP files
 * 3. Organizes the server structure
 * 
 * Usage: php cleanup_ftp.php
 */

echo "=== FTP Cleanup Script ===\n\n";

// FTP Configuration
$ftp_server = '193.203.162.199';
$ftp_username = 'admin_azal';
$ftp_password = '8-BDK-@s17Laf6MH';

echo "1. Connecting to FTP server...\n";
$conn_id = ftp_connect($ftp_server) or die("ERROR: Could not connect to $ftp_server\n");

if (@ftp_login($conn_id, $ftp_username, $ftp_password)) {
    echo "   ✓ Connected as $ftp_username\n";
    ftp_pasv($conn_id, true);
} else {
    die("ERROR: Login failed\n");
}

// Files to delete from public_html (keep only web-accessible)
$delete_from_public = [
    '.git',
    'app',
    'bootstrap',
    'config',
    'database',
    'lang',
    'routes',
    'tests',
    'public',
    'resources',
    'scripts',
    'composer.json',
    'composer.lock',
    'package.json',
    'package-lock.json',
    'package.vuexy.json',
    'phpunit.xml',
    'README.md',
    'vite.config.vuexy.js',
    'vite.icons.plugin.js',
    '.editorconfig',
    '.styleci.yml',
    '.env.example',
    '.ftp-deploy-sync-state.json',
    'exec_test.php',
    'verify.php',
    'debug_exec.php',
    'fix_git.php',
    'deploy_test.md',
];

// ZIP files to delete from laravel_app
$delete_zips = [
    'vendor.zip',
    'missing_dirs.zip',
    'core_updates.zip',
];

// Function to delete file or directory on FTP
function deleteFTPItem($conn_id, $path) {
    echo "   Deleting: $path\n";
    
    // Try to delete as file
    if (@ftp_delete($conn_id, $path)) {
        echo "     ✓ File deleted: $path\n";
        return true;
    }
    
    // Try to delete as directory (recursive)
    if (@ftp_chdir($conn_id, $path)) {
        $contents = ftp_nlist($conn_id, '.');
        foreach ($contents as $item) {
            if ($item == '.' || $item == '..') continue;
            deleteFTPItem($conn_id, $item);
        }
        ftp_cdup($conn_id);
        if (@ftp_rmdir($conn_id, $path)) {
            echo "     ✓ Directory deleted: $path\n";
            return true;
        }
    }
    
    echo "     ⚠️  Not found or already deleted: $path\n";
    return false;
}

echo "\n2. Cleaning public_html (removing duplicate Laravel files)...\n";
echo "   Files to delete:\n";
foreach ($delete_from_public as $file) {
    echo "   - $file\n";
}

echo "\n   Deleting from public_html...\n";
ftp_chdir($conn_id, '/public_html');

$deleted_count = 0;
foreach ($delete_from_public as $file) {
    if (deleteFTPItem($conn_id, $file)) {
        $deleted_count++;
    }
}

echo "\n   ✓ Deleted $deleted_count items from public_html\n";

echo "\n3. Deleting unnecessary ZIP files from laravel_app...\n";
echo "   Files to delete:\n";
foreach ($delete_zips as $zip) {
    echo "   - $zip\n";
}

echo "\n   Deleting ZIP files...\n";
ftp_chdir($conn_id, '/laravel_app');

$deleted_zips = 0;
foreach ($delete_zips as $zip) {
    if (@ftp_delete($conn_id, $zip)) {
        echo "     ✓ Deleted: $zip\n";
        $deleted_zips++;
    } else {
        echo "     ⚠️  Not found: $zip\n";
    }
}

echo "\n   ✓ Deleted $deleted_zips ZIP files\n";

echo "\n4. Final structure check:\n";

// Check public_html
echo "\n   public_html (should have only 5 items):\n";
ftp_chdir($conn_id, '/public_html');
$pub_contents = ftp_nlist($conn_id, '.');
echo "   Found " . count($pub_contents) . " items:\n";
foreach ($pub_contents as $item) {
    $basename = basename($item);
    $size = @ftp_size($conn_id, $basename);
    if ($size == -1) {
        echo "   📁 $basename/\n";
    } else {
        echo "   📄 $basename (" . round($size/1024, 1) . " KB)\n";
    }
}

// Check laravel_app for remaining ZIPs
echo "\n   laravel_app ZIP files remaining:\n";
ftp_chdir($conn_id, '/laravel_app');
$laravel_contents = ftp_nlist($conn_id, '.');
$zips = [];
foreach ($laravel_contents as $item) {
    if (preg_match('/\.(zip|tar|gz)$/', $item)) {
        $size = @ftp_size($conn_id, $item);
        $zips[] = "$item (" . round($size/1024, 1) . " KB)";
    }
}
if (count($zips) > 0) {
    foreach ($zips as $zip) {
        echo "   📦 $zip\n";
    }
} else {
    echo "   ✓ No ZIP files remaining\n";
}

echo "\n5. Summary:\n";
echo "   ✓ Deleted $deleted_count items from public_html\n";
echo "   ✓ Deleted $deleted_zips ZIP files from laravel_app\n";
echo "   ✓ public_html now clean (only web-accessible files)\n";

echo "\n6. Next Steps (Manual):\n";
echo str_repeat("=", 60) . "\n";
echo "\n";
echo "   # SSH into server and create storage symlink:\n";
echo "   ssh admin_azal@193.203.162.199\n\n";
echo "   cd ~/public_html\n\n";
echo "   # Check if storage symlink exists:\n";
echo "   ls -la storage\n\n";
echo "   # If NOT exists, create it:\n";
echo "   ln -s ../laravel_app/storage/public storage\n\n";
echo "   # Update index.php if needed:\n";
echo "   nano index.php\n";
echo "   # Ensure line 16 points to laravel_app:\n";
echo "   # (require __DIR__.'/../laravel_app/bootstrap/app.php')\n\n";
echo "   # Set permissions:\n";
echo "   chmod 775 ../laravel_app/storage\n";
echo "   chmod 775 ../laravel_app/bootstrap/cache\n";
echo str_repeat("=", 60) . "\n";

ftp_close($conn_id);
echo "\n✓ Cleanup complete!\n";
?>
