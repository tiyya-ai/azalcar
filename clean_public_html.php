<?php
/**
 * FTP Cleanup Script - Remove duplicate Laravel files from public_html
 * 
 * This script removes Laravel core files from public_html that should only be in laravel_app/
 * 
 * Usage: php clean_public_html.php
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

// Files and directories to DELETE from public_html (security risk)
$items_to_delete = [
    '.git/',
    '.gitignore',
    'app/',
    'bootstrap/',
    'config/',
    'database/',
    'lang/',
    'routes/',
    'tests/',
    '.editorconfig',
    '.styleci.yml',
    '.env.example',
    'artisan',
    'composer.json',
    'composer.lock',
    'package.json',
    'package-lock.json',
    'package.vuexy.json',
    'phpunit.xml',
    'vite.config.js',
    'vite.config.vuexy.js',
    'vite.icons.plugin.js',
    'README.md',
    'public/',
    'resources/',
    'scripts/',
    '.ftp-deploy-sync-state.json',
];

// Files to KEEP in public_html
$items_to_keep = [
    'assets/',
    'build/',
    'storage/',
    'index.php',
    '.htaccess',
];

echo "\n2. Current public_html contents:\n";
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

echo "\n3. Items to DELETE from public_html:\n";
$total_size = 0;
foreach ($items_to_delete as $item) {
    $size = @ftp_size($conn_id, $item);
    $is_dir = @ftp_chdir($conn_id, $item);
    if ($is_dir) ftp_cdup($conn_id);
    
    if ($size != -1) {
        $kb = round($size/1024, 1);
        $total_size += $size;
        echo "   ⚠️  $item ($kb KB) - WILL BE DELETED\n";
    } elseif ($is_dir !== false) {
        echo "   ⚠️  $item/ (directory) - WILL BE DELETED\n";
    } else {
        echo "   ✓ $item - Already removed or not found\n";
    }
}

echo "\n   Total size to free: " . round($total_size/1024/1024, 2) . " MB\n";

echo "\n4. Items to KEEP in public_html:\n";
foreach ($items_to_keep as $item) {
    $size = @ftp_size($conn_id, $item);
    $is_dir = @ftp_chdir($conn_id, $item);
    if ($is_dir) ftp_cdup($conn_id);
    
    if ($size != -1) {
        echo "   ✓ $item (will keep)\n";
    } elseif ($is_dir !== false) {
        echo "   ✓ $item/ (will keep)\n";
    } else {
        echo "   ⚠️  $item - Not found\n";
    }
}

// Function to recursively delete directory on FTP
function deleteFTPDir($conn_id, $dir) {
    echo "   Deleting directory: $dir/\n";
    
    // Change to the directory
    if (!@ftp_chdir($conn_id, $dir)) {
        echo "   ⚠️  Cannot change to: $dir\n";
        return false;
    }
    
    // Get all files and directories
    $contents = ftp_nlist($conn_id, '.');
    
    foreach ($contents as $item) {
        if ($item == '.' || $item == '..') continue;
        
        $basename = basename($item);
        
        // Try to delete file
        if (@ftp_delete($conn_id, $basename)) {
            echo "     ✓ Deleted file: $basename\n";
        } else {
            // Try to delete subdirectory
            if (@ftp_chdir($conn_id, $basename)) {
                ftp_cdup($conn_id);
                deleteFTPDir($conn_id, $basename);
                @ftp_rmdir($conn_id, $basename);
                echo "     ✓ Deleted dir: $basename\n";
            }
        }
    }
    
    // Go back to parent and remove directory
    ftp_cdup($conn_id);
    return @ftp_rmdir($conn_id, $dir);
}

echo "\n5. Deleting items (simulated - review first):\n";
echo "   This script will DELETE the following from public_html/:\n";
echo "   " . str_repeat("-", 50) . "\n";

$confirm = false; // Set to true to actually delete

foreach ($items_to_delete as $item) {
    $size = @ftp_size($conn_id, $item);
    $is_dir = @ftp_chdir($conn_id, $item);
    if ($is_dir) ftp_cdup($conn_id);
    
    if ($size != -1 || $is_dir !== false) {
        if (!$confirm) {
            echo "   - $item\n";
        } else {
            if ($is_dir !== false) {
                deleteFTPDir($conn_id, $item);
            } else {
                @ftp_delete($conn_id, $item);
                echo "   ✓ Deleted: $item\n";
            }
        }
    }
}

if (!$confirm) {
    echo "   " . str_repeat("-", 50) . "\n";
    echo "   ⚠️  Run with \$confirm = true; to actually delete\n";
}

echo "\n6. Required SSH Commands:\n";
echo str_repeat("=", 60) . "\n";
echo "\n";
echo "   # SSH into server:\n";
echo "   ssh admin_azal@193.203.162.199\n\n";
echo "   # Navigate to public_html:\n";
echo "   cd ~/public_html\n\n";
echo "   # Delete Laravel core files:\n";
echo "   rm -rf app bootstrap config database lang routes tests\n";
echo "   rm -rf .git .gitignore .editorconfig .styleci.yml\n";
echo "   rm -rf artisan composer.json composer.lock package*.json\n";
echo "   rm -rf phpunit.xml README.md vite.config*.js\n";
echo "   rm -rf public resources scripts .ftp-deploy-sync-state.json\n\n";
echo "   # Create symlink:\n";
echo "   ln -s ../laravel_app/storage/public storage\n\n";
echo "   # Update index.php:\n";
echo "   nano index.php\n";
echo "   # Change line 16 to:\n";
echo "   # (require_once __DIR__.'/../laravel_app/bootstrap/app.php')\n\n";
echo "   # Set permissions:\n";
echo "   chmod 775 ../laravel_app/storage\n";
echo "   chmod 775 ../laravel_app/bootstrap/cache\n";
echo str_repeat("=", 60) . "\n";

ftp_close($conn_id);
echo "\n✓ Cleanup script completed!\n";
?>
