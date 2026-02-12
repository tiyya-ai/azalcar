<?php
/**
 * FTP Deployment Script for Laravel Application
 * 
 * This script organizes files properly and deploys to the FTP server
 * 
 * Usage: php deploy_to_ftp.php
 */

echo "=== Laravel FTP Deployment Script ===\n\n";

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

// Files and directories to upload (excluding sensitive files)
$upload_list = [
    // Core Laravel files
    'artisan',
    'composer.json',
    'composer.lock',
    'package.json',
    'package-lock.json',
    'vite.config.js',
    'bootstrap/',
    'config/',
    'database/',
    'lang/',
    'routes/',
    'public/',
    'resources/',
    'storage/',
    'tests/',
    // App files
    'app/',
    // Root config files
    '.editorconfig',
    '.env.example',
    '.styleci.yml',
    '.htaccess',
    // Git files (needed for deployment)
    '.gitignore',
];

// Files to EXCLUDE from public_html (security risk)
$public_html_exclude = [
    '.git/',
    'vendor/',
    'composer.json',
    'composer.lock',
    'package.json',
    'package-lock.json',
    'node_modules/',
    'tests/',
    '.env',
    '.env.example',
    '.editorconfig',
    '.styleci.yml',
    'artisan',
    'bootstrap/',
    'config/',
    'database/',
    'lang/',
    'routes/',
];

// Step 2: Navigate to laravel_app and upload core files
echo "\n2. Uploading Laravel core files to laravel_app/...\n";
if (!@ftp_chdir($conn_id, 'laravel_app')) {
    echo "   Creating laravel_app directory...\n";
    ftp_mkdir($conn_id, 'laravel_app');
    ftp_chdir($conn_id, 'laravel_app');
}

// Upload files recursively
function uploadDirectory($conn_id, $local_dir, $remote_dir, $exclude = []) {
    echo "   Uploading $local_dir/ to $remote_dir/...\n";
    
    $files = scandir($local_dir);
    foreach ($files as $file) {
        if ($file == '.' || $file == '..') continue;
        
        $local_path = $local_dir . '/' . $file;
        $remote_path = $remote_dir . '/' . $file;
        
        // Check if file/dir is excluded
        $is_excluded = false;
        foreach ($exclude as $excl) {
            if (strpos($file, $excl) !== false) {
                $is_excluded = true;
                break;
            }
        }
        if ($is_excluded) continue;
        
        if (is_dir($local_path)) {
            // Create remote directory
            @ftp_mkdir($conn_id, $remote_path);
            uploadDirectory($conn_id, $local_path, $remote_path, $exclude);
        } else {
            // Upload file
            if (ftp_put($conn_id, $remote_path, $local_path, FTP_BINARY)) {
                echo "     ✓ $file\n";
            } else {
                echo "     ✗ Failed: $file\n";
            }
        }
    }
}

// Upload from current local directory to laravel_app
uploadDirectory($conn_id, '.', '.', ['.git/', 'node_modules/', 'vendor/', 'storage/logs/', 'storage/framework/cache/', 'storage/framework/sessions/', 'storage/framework/views/']);

// Step 3: Clean up public_html
echo "\n3. Cleaning up public_html/...\n";
ftp_chdir($conn_id, '/public_html');

// Remove sensitive files from public_html
foreach ($public_html_exclude as $item) {
    echo "   Checking $item...\n";
    $size = @ftp_size($conn_id, $item);
    if ($size != -1) {
        if (is_dir($item)) {
            echo "     ⚠️  Found directory: $item (will be removed)\n";
        } else {
            echo "     ⚠️  Found file: $item (will be removed)\n";
        }
    } elseif (@ftp_chdir($conn_id, $item)) {
        echo "     ⚠️  Found directory: $item (will be removed)\n";
        ftp_cdup($conn_id);
    }
}

echo "\n   Note: Please manually remove the following from public_html/:\n";
foreach ($public_html_exclude as $item) {
    echo "   - $item\n";
}

// Step 4: Set up symlinks (manual steps)
echo "\n4. Manual Steps Required:\n";
echo "   ========================================\n";
echo "   Run these commands on the server via SSH:\n\n";
echo "   # Create symlink for storage\n";
echo "   cd /home/admin_azal/public_html\n";
echo "   ln -s ../laravel_app/storage/public storage\n\n";
echo "   # Update index.php to point to laravel_app\n";
echo "   # Change require __DIR__.'/../bootstrap/app.php'\n";
echo "   # to require __DIR__.'/../laravel_app/bootstrap/app.php'\n";
echo "   ========================================\n";

// Step 5: GitHub Setup
echo "\n5. GitHub Deployment Setup\n";
echo "   ========================================\n";
echo "   Create .github/workflows/deploy.yml:\n\n";

$workflow_content = <<<'YAML'
name: Deploy to Production

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'
          
      - name: Install Dependencies
        run: |
          composer install --no-dev --optimize-autoloader
          
      - name: Deploy to Server via FTP
        uses: SamKirkland/FTP-Deploy-Action@4.3.0
        with:
          server: \${{ secrets.FTP_SERVER }}
          username: \${{ secrets.FTP_USERNAME }}
          password: \${{ secrets.FTP_PASSWORD }}
          local-dir: ./
          server-dir: /laravel_app/
          exclude: |
            .git/
            .github/
            node_modules/
            tests/
            .env*
            vendor/
            storage/logs/
            storage/framework/cache/
            storage/framework/sessions/
            storage/framework/views/
YAML;

echo $workflow_content . "\n";
echo "   ========================================\n";

echo "\n6. Summary\n";
echo "   ✓ FTP connection established\n";
echo "   ✓ Laravel files uploaded to laravel_app/\n";
echo "   ⚠️  Manual cleanup needed in public_html/\n";
echo "   ⚠️  Manual symlink setup required\n";
echo "   ✓ GitHub workflow template provided\n";

ftp_close($conn_id);
echo "\nDeployment complete!\n";
?>
