<?php
$ftp_server = '193.203.162.199';
$ftp_username = 'admin_azal';
$ftp_password = '8-BDK-@s17Laf6MH';

echo "1. Connecting to FTP server $ftp_server...\n";
$conn_id = ftp_connect($ftp_server);
if (!$conn_id) {
    die("ERROR: Could not connect to $ftp_server\n");
}
echo "2. Connected successfully\n";

echo "3. Attempting login as $ftp_username...\n";
if (@ftp_login($conn_id, $ftp_username, $ftp_password)) {
    echo "4. Login successful!\n";
    ftp_pasv($conn_id, true);
    echo "5. Passive mode enabled\n";
    
    echo "6. Current directory: " . ftp_pwd($conn_id) . "\n";
    
    echo "7. Listing root directory...\n";
    $contents = ftp_nlist($conn_id, '.');
    if ($contents === false) {
        echo "   ERROR: Could not list directory\n";
    } else {
        echo "   Found " . count($contents) . " items:\n";
        foreach ($contents as $i => $item) {
            if ($i < 30) {
                $basename = basename($item);
                $size = @ftp_size($conn_id, $item);
                if ($size == -1) {
                    echo "   📁 $basename/\n";
                } else {
                    echo "   📄 $basename (" . round($size/1024, 1) . " KB)\n";
                }
            }
        }
        if (count($contents) > 30) {
            echo "   ... and " . (count($contents) - 30) . " more items\n";
        }
    }
    
    // Check public_html directory
    echo "\n8. Checking public_html directory...\n";
    if (@ftp_chdir($conn_id, 'public_html')) {
        echo "   Current dir: " . ftp_pwd($conn_id) . "\n";
        $pub_contents = ftp_nlist($conn_id, '.');
        echo "   Found " . count($pub_contents) . " items:\n";
        foreach ($pub_contents as $i => $item) {
            if ($i < 30) {
                $basename = basename($item);
                $size = @ftp_size($conn_id, $item);
                if ($size == -1) {
                    echo "   📁 $basename/\n";
                } else {
                    echo "   📄 $basename (" . round($size/1024, 1) . " KB)\n";
                }
            }
        }
        if (count($pub_contents) > 30) {
            echo "   ... and " . (count($pub_contents) - 30) . " more items\n";
        }
        ftp_cdup($conn_id);
    }
    
    // Check laravel_app directory
    echo "\n9. Checking laravel_app directory...\n";
    if (@ftp_chdir($conn_id, 'laravel_app')) {
        echo "   Current dir: " . ftp_pwd($conn_id) . "\n";
        $laravel_contents = ftp_nlist($conn_id, '.');
        echo "   Found " . count($laravel_contents) . " items:\n";
        foreach ($laravel_contents as $i => $item) {
            if ($i < 30) {
                $basename = basename($item);
                $size = @ftp_size($conn_id, $item);
                if ($size == -1) {
                    echo "   📁 $basename/\n";
                } else {
                    echo "   📄 $basename (" . round($size/1024, 1) . " KB)\n";
                }
            }
        }
        if (count($laravel_contents) > 30) {
            echo "   ... and " . (count($laravel_contents) - 30) . " more items\n";
        }
        ftp_cdup($conn_id);
    }
    
    // Check for problematic files
    echo "\n10. Checking for problematic files...\n";
    $problematic_items = ['vendor', 'node_modules', '.env', '.git', 'storage/logs', 'tests', 'composer.json', 'package.json'];
    foreach ($problematic_items as $item) {
        $size = @ftp_size($conn_id, $item);
        if ($size != -1) {
            echo "    ⚠️  Found file: $item (" . round($size/1024, 2) . " KB)\n";
        } elseif (@ftp_chdir($conn_id, $item)) {
            echo "    ⚠️  Found directory: $item/\n";
            ftp_cdup($conn_id);
        } else {
            echo "    ✓ Not found: $item (Good)\n";
        }
    }
    
    echo "11. Closing connection...\n";
    ftp_close($conn_id);
    echo "12. Done!\n";
} else {
    echo "ERROR: Login failed\n";
    ftp_close($conn_id);
}
?>
