<?php

// FTP Configuration
$ftp_server = '193.203.162.199';
$ftp_username = 'azalc1838';
$ftp_password = getenv('FTP_PASSWORD') ?: 'YOUR_FTP_PASSWORD_HERE';

if ($ftp_password === 'YOUR_FTP_PASSWORD_HERE') {
    die("Error: FTP Password not set. Please enter password when prompted.\n");
}

// Connection
echo "Connecting to FTP server...\n";
$conn_id = ftp_connect($ftp_server) or die("Could not connect to $ftp_server\n");

if (@ftp_login($conn_id, $ftp_username, $ftp_password)) {
    echo "✓ Connected as $ftp_username@$ftp_server\n\n";
} else {
    die("✗ Could not connect as $ftp_username\n");
}

ftp_pasv($conn_id, true);

// Try to find public_html directory
$target_dir = 'public_html';
if (!@ftp_chdir($conn_id, $target_dir)) {
    if (@ftp_chdir($conn_id, 'domains/azalcar.com/public_html')) {
        $target_dir = 'domains/azalcar.com/public_html';
    } else {
        die("Could not find public_html directory.\n");
    }
}

echo "Current directory: $target_dir\n";
echo "=" . str_repeat("=", 70) . "\n\n";

// Function to list directory contents recursively
function listFTPDirectory($conn_id, $dir, $level = 0, $max_level = 2) {
    if ($level > $max_level) return;
    
    $contents = @ftp_nlist($conn_id, $dir);
    if ($contents === false) return;
    
    $indent = str_repeat("  ", $level);
    
    foreach ($contents as $item) {
        $basename = basename($item);
        
        // Skip . and ..
        if ($basename == '.' || $basename == '..') continue;
        
        // Get file size
        $size = @ftp_size($conn_id, $item);
        
        if ($size == -1) {
            // It's a directory
            echo $indent . "📁 $basename/\n";
            listFTPDirectory($conn_id, $item, $level + 1, $max_level);
        } else {
            // It's a file
            $size_kb = round($size / 1024, 2);
            echo $indent . "📄 $basename ($size_kb KB)\n";
        }
    }
}

// List root directory contents
echo "Files and folders in hosting:\n";
echo "-" . str_repeat("-", 70) . "\n";
listFTPDirectory($conn_id, '.', 0, 2);

echo "\n" . "=" . str_repeat("=", 70) . "\n";

// Check for problematic files/folders
echo "\nChecking for problematic files...\n";
echo "-" . str_repeat("-", 70) . "\n";

$problematic_items = ['vendor', 'node_modules', '.env', '.git', 'storage/logs', 'tests'];
foreach ($problematic_items as $item) {
    $size = @ftp_size($conn_id, $item);
    if ($size != -1) {
        echo "⚠️  Found file: $item (" . round($size/1024, 2) . " KB)\n";
    } elseif (@ftp_chdir($conn_id, $item)) {
        echo "⚠️  Found directory: $item/\n";
        ftp_cdup($conn_id);
    } else {
        echo "✓ Not found: $item (Good)\n";
    }
}

echo "\n" . "=" . str_repeat("=", 70) . "\n";
echo "\nSummary:\n";
echo "- Server: $ftp_server\n";
echo "- Directory: $target_dir\n";
echo "- Status: Connected\n";

ftp_close($conn_id);
echo "\nConnection closed.\n";
?>
