<?php
/**
 * FTP Move Script - Move files from public_html to laravel_app WITHOUT deleting
 * 
 * This script moves Laravel core files from public_html to laravel_app/ while preserving them
 * 
 * Usage: php move_files_ftp.php
 */

echo "=== FTP Move Script (No Deletion) ===\n\n";

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

// Files and directories to MOVE from public_html TO laravel_app
$items_to_move = [
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
    '.git',
    '.gitignore',
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
    'README.md',
    'vite.config.js',
    'vite.config.vuexy.js',
    'vite.icons.plugin.js',
    '.ftp-deploy-sync-state.json',
];

echo "\n2. Current structure:\n";

// Check public_html
echo "\n   public_html/:\n";
ftp_chdir($conn_id, '/public_html');
$pub_contents = ftp_nlist($conn_id, '.');
foreach ($pub_contents as $item) {
    echo "   - " . basename($item) . "\n";
}

// Check laravel_app
echo "\n   laravel_app/:\n";
ftp_chdir($conn_id, '/laravel_app');
$laravel_contents = ftp_nlist($conn_id, '.');
foreach ($laravel_contents as $item) {
    echo "   - " . basename($item) . "\n";
}

// Function to download, upload, then delete (safer than direct move)
function moveFTPItem($conn_id, $source_path, $dest_path) {
    echo "   Moving $source_path → $dest_path...\n";
    
    // Check if source exists
    $is_dir = @ftp_chdir($conn_id, $source_path);
    if ($is_dir) {
        ftp_cdup($conn_id);
        echo "     📁 It's a directory\n";
    }
    
    $size = @ftp_size($conn_id, $source_path);
    
    if ($size == -1 && !$is_dir) {
        echo "     ⚠️  Source not found\n";
        return false;
    }
    
    // Create destination directory if needed
    $dest_dir = dirname($dest_path);
    if (!@ftp_chdir($conn_id, $dest_dir)) {
        echo "     Creating directory: $dest_dir\n";
        ftp_mkdir($conn_id, $dest_dir);
    }
    
    // If it's a directory, recursively move contents
    if ($is_dir) {
        ftp_chdir($conn_id, $source_path);
        $contents = ftp_nlist($conn_id, '.');
        
        foreach ($contents as $item) {
            if ($item == '.' || $item == '..') continue;
            
            $basename = basename($item);
            $sub_dest = $dest_path . '/' . $basename;
            $sub_source = $source_path . '/' . $basename;
            
            $sub_is_dir = @ftp_chdir($conn_id, $basename);
            if ($sub_is_dir) ftp_cdup($conn_id);
            
            $sub_size = @ftp_size($conn_id, $basename);
            
            if ($sub_size != -1 || $sub_is_dir !== false) {
                moveFTPItem($conn_id, $sub_source, $sub_dest);
            }
        }
        
        ftp_cdup($conn_id);
        @ftp_rmdir($conn_id, $source_path);
        echo "     ✓ Removed empty directory: $source_path\n";
    } else {
        // Download, upload, delete (safer)
        $local_file = sys_get_temp_dir() . '/' . basename($source_path);
        
        if (ftp_get($conn_id, $local_file, $source_path, FTP_BINARY)) {
            if (ftp_put($conn_id, $dest_path, $local_file, FTP_BINARY)) {
                echo "     ✓ Uploaded to: $dest_path\n";
                @ftp_delete($conn_id, $source_path);
                echo "     ✓ Removed from: $source_path\n";
                @unlink($local_file);
            } else {
                echo "     ✗ Failed to upload\n";
            }
        } else {
            echo "     ✗ Failed to download\n";
        }
    }
    
    return true;
}

echo "\n3. Moving items (this preserves files):\n";
echo "   Files will be MOVED, not deleted during process.\n\n";

$count = 0;
foreach ($items_to_move as $item) {
    $source = "/public_html/$item";
    $dest = "/laravel_app/$item";
    
    // Check if item exists in public_html
    $size = @ftp_size($conn_id, "/public_html/$item");
    $is_dir = @ftp_chdir($conn_id, "/public_html/$item");
    if ($is_dir) ftp_cdup($conn_id);
    
    if ($size != -1 || $is_dir !== false) {
        // Check if already exists in laravel_app
        $dest_size = @ftp_size($conn_id, $dest);
        $dest_is_dir = @ftp_chdir($conn_id, $dest);
        if ($dest_is_dir) ftp_cdup($conn_id);
        
        if ($dest_size != -1 || $dest_is_dir !== false) {
            echo "   ⚠️  $item - Already exists in laravel_app, skipping\n";
        } else {
            moveFTPItem($conn_id, $source, $dest);
            $count++;
        }
    } else {
        echo "   ✓ $item - Not found in public_html (already moved or doesn't exist)\n";
    }
}

echo "\n4. Final structure:\n";

// Check public_html
echo "\n   public_html/ (should be minimal):\n";
ftp_chdir($conn_id, '/public_html');
$pub_contents = ftp_nlist($conn_id, '.');
foreach ($pub_contents as $item) {
    $basename = basename($item);
    $size = @ftp_size($conn_id, $basename);
    if ($size == -1) {
        echo "   📁 $basename/\n";
    } else {
        echo "   📄 $basename\n";
    }
}

// Check laravel_app
echo "\n   laravel_app/ (should have Laravel files):\n";
ftp_chdir($conn_id, '/laravel_app');
$laravel_contents = ftp_nlist($conn_id, '.');
foreach ($laravel_contents as $item) {
    $basename = basename($item);
    $size = @ftp_size($conn_id, $basename);
    if ($size == -1) {
        echo "   📁 $basename/\n";
    } else {
        echo "   📄 $basename\n";
    }
}

echo "\n5. Manual Steps Required:\n";
echo str_repeat("=", 60) . "\n";
echo "\n";
echo "   # SSH into server and run:\n";
echo "   ssh admin_azal@193.203.162.199\n\n";
echo "   # Navigate to public_html:\n";
echo "   cd ~/public_html\n\n";
echo "   # Create symlink for storage:\n";
echo "   ln -s ../laravel_app/storage/public storage\n\n";
echo "   # Update index.php (line 16):\n";
echo "   # Change: require __DIR__.'/../bootstrap/app.php';\n";
echo "   # To:     require __DIR__.'/../laravel_app/bootstrap/app.php';\n\n";
echo "   # Set permissions:\n";
echo "   chmod 775 ../laravel_app/storage\n";
echo "   chmod 775 ../laravel_app/bootstrap/cache\n";
echo str_repeat("=", 60) . "\n";

ftp_close($conn_id);
echo "\n✓ Move script completed! ($count items processed)\n";
?>
