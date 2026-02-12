<?php
/**
 * FTP File Organization Script
 * 
 * This script will organize files on the FTP server properly:
 * - Move Laravel core files from public_html to laravel_app/
 * - Clean up public_html to only contain web-accessible files
 * - Create necessary symlinks
 * 
 * Usage: php organize_ftp.php
 */

echo "=== FTP File Organization Script ===\n\n";

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

// Files to move FROM public_html TO laravel_app
$files_to_move = [
    'app',
    'bootstrap',
    'config',
    'database',
    'lang',
    'routes',
    'tests',
    'artisan',
    'composer.json',
    'composer.lock',
    'package.json',
    'package-lock.json',
    'vite.config.js',
    '.editorconfig',
    '.styleci.yml',
    '.htaccess',
];

echo "\n2. Checking current structure...\n";
echo "   Root directory contents:\n";
$root = ftp_nlist($conn_id, '.');
foreach ($root as $item) {
    echo "   - " . basename($item) . "\n";
}

echo "\n3. Files that need to be moved TO laravel_app/:\n";
foreach ($files_to_move as $file) {
    echo "   - $file\n";
}

// Function to move files between directories on FTP server
function moveOnFTP($conn_id, $source, $dest) {
    echo "   Moving $source to $dest...\n";
    
    // Check if source exists
    $size = @ftp_size($conn_id, $source);
    $is_dir = @ftp_chdir($conn_id, $source);
    if ($is_dir) ftp_cdup($conn_id);
    
    if ($size == -1 && !$is_dir) {
        echo "   ⚠️  Source not found: $source\n";
        return false;
    }
    
    // Create destination directory if it doesn't exist
    $dest_dir = dirname($dest);
    if (!@ftp_chdir($conn_id, $dest_dir)) {
        echo "   Creating directory: $dest_dir\n";
        ftp_mkdir($conn_id, $dest_dir);
    }
    
    // Move using rename
    if (@ftp_rename($conn_id, $source, $dest)) {
        echo "   ✓ Moved: $source → $dest\n";
        return true;
    } else {
        echo "   ✗ Failed to move: $source\n";
        return false;
    }
}

echo "\n4. Moving files to laravel_app/...\n";

// Move files from public_html to laravel_app
ftp_chdir($conn_id, '/public_html');

foreach ($files_to_move as $file) {
    $source = "/public_html/$file";
    $dest = "/laravel_app/$file";
    
    // Check if file exists in public_html
    $size = @ftp_size($conn_id, $file);
    $is_dir = @ftp_chdir($conn_id, $file);
    if ($is_dir) ftp_cdup($conn_id);
    
    if ($size != -1 || $is_dir) {
        moveOnFTP($conn_id, $source, $dest);
    } else {
        echo "   ✓ Already in laravel_app or not found: $file\n";
    }
}

echo "\n5. Checking public_html after cleanup:\n";
ftp_chdir($conn_id, '/public_html');
$pub_contents = ftp_nlist($conn_id, '.');
echo "   Found " . count($pub_contents) . " items:\n";
foreach ($pub_contents as $item) {
    $basename = basename($item);
    $size = @ftp_size($conn_id, $item);
    if ($size == -1) {
        echo "   📁 $basename/\n";
    } else {
        echo "   📄 $basename (" . round($size/1024, 1) . " KB)\n";
    }
}

echo "\n6. Checking laravel_app:\n";
ftp_chdir($conn_id, '/laravel_app');
$laravel_contents = ftp_nlist($conn_id, '.');
echo "   Found " . count($laravel_contents) . " items:\n";
foreach ($laravel_contents as $item) {
    $basename = basename($item);
    $size = @ftp_size($conn_id, $item);
    if ($size == -1) {
        echo "   📁 $basename/\n";
    } else {
        echo "   📄 $basename (" . round($size/1024, 1) . " KB)\n";
    }
}

echo "\n7. Manual Steps Required (Run via SSH/Terminal):\n";
echo str_repeat("=", 60) . "\n";
echo "\n";
echo "   # 1. SSH into server and run:\n";
echo "   ssh admin_azal@193.203.162.199\n\n";
echo "   # 2. Navigate to public_html:\n";
echo "   cd ~/public_html\n\n";
echo "   # 3. Create symlink for storage:\n";
echo "   ln -s ../laravel_app/storage/public storage\n\n";
echo "   # 4. Update index.php to point to laravel_app:\n";
echo "   nano index.php\n";
echo "   # Change: require __DIR__.'/../bootstrap/app.php';\n";
echo "   # To:     require __DIR__.'/../laravel_app/bootstrap/app.php';\n\n";
echo "   # 5. Set proper permissions:\n";
echo "   chmod 775 laravel_app/storage\n";
echo "   chmod 775 laravel_app/bootstrap/cache\n";
echo str_repeat("=", 60) . "\n";

echo "\n8. GitHub Actions Workflow:\n";
echo "   Created: .github/workflows/deploy.yml\n";
echo "   - Auto-deploys to laravel_app/ on push to main\n";
echo "   - Excludes: .git, node_modules, vendor, cache files\n";

ftp_close($conn_id);
echo "\n✓ FTP organization script completed!\n";
?>
