<?php

// FTP Configuration
$ftp_server = '193.203.162.199';
$ftp_username = 'azalc1838';

// Get password from environment variable (for GitHub Actions) or fallback to manual
$ftp_password = getenv('FTP_PASSWORD') ?: 'YOUR_FTP_PASSWORD_HERE';

if ($ftp_password === 'YOUR_FTP_PASSWORD_HERE') {
    die("Error: FTP Password not set. Set FTP_PASSWORD env var or edit script.\n");
}

// Connection
$conn_id = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
if (@ftp_login($conn_id, $ftp_username, $ftp_password)) {
    echo "Connected as $ftp_username@$ftp_server\n";
} else {
    die("Could not connect as $ftp_username\n");
}

ftp_pasv($conn_id, true);

// Change to public_html
$target_dir = 'public_html';
if (!@ftp_chdir($conn_id, $target_dir)) {
    // Try to find it if we are not at root
    if (@ftp_chdir($conn_id, 'domains/azalcar.com/public_html')) {
        $target_dir = 'domains/azalcar.com/public_html';
    } else {
         die("Could not find public_html directory.\n");
    }
}
echo "Changed directory to: $target_dir\n";

// Recursive Upload Function
function uploadRecursive($conn_id, $local_dir, $remote_dir) {
    if (!is_dir($local_dir)) return;
    
    $files = scandir($local_dir);
    foreach ($files as $file) {
        if ($file == '.' || $file == '..' || $file == '.git' || $file == '.github' || $file == 'node_modules' || $file == 'vendor' || $file == 'tests' || $file == 'storage') continue;
        
        $local_path = $local_dir . '/' . $file;
        $remote_path = $remote_dir . '/' . $file;
        
        if (is_dir($local_path)) {
            // Create directory on server if it doesn't exist
            if (!@ftp_chdir($conn_id, $remote_path)) {
                if (ftp_mkdir($conn_id, $remote_path)) {
                    echo "Created directory: $remote_path\n";
                } else {
                    echo "Failed to create directory: $remote_path\n";
                }
            }
            // Go back to parent to continue, or pass absolute path? 
            // Better to pass relative path logic.
            // Actually, let's keep it simple: always start from root or track path.
            // Simplified: Just try to upload content.
            uploadRecursive($conn_id, $local_path, $remote_path);
        } else {
            // Upload file
            if (ftp_put($conn_id, $remote_path, $local_path, FTP_BINARY)) {
                echo "Uploaded: $remote_path\n";
            } else {
                echo "Failed to upload: $remote_path\n";
            }
        }
    }
}

// Simplified Upload: Just upload crucial files and folders we modified
// Full recursive upload might time out or be too risky without proper diffing.
// Let's stick to the critical paths we know changed.

$paths_to_sync = [
    'app/Http/Controllers',
    'resources/views',
    'public/assets/js',
    'routes',
    'public/deploy_test.txt',
    'public/hello_world_ftp.html'
];

foreach ($paths_to_sync as $path) {
    $local_path = __DIR__ . '/' . $path;
    // Helper to upload directory or file
    // (Simulated recursion for specific paths)
    if (is_dir($local_path)) {
        // Simple directory iterator
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($local_path));
        foreach ($iterator as $file) {
             if ($file->isDir()) continue;
             
             $file_path = $file->getPathname();
             $relative_path = substr($file_path, strlen(__DIR__) + 1);
             $relative_path = str_replace('\\', '/', $relative_path); // Fix windows paths
             
             // Ensure dir exists
             $server_dir = dirname($relative_path);
             $parts = explode('/', $server_dir);
             ftp_chdir($conn_id, '/'.$target_dir);
             foreach($parts as $part) {
                 if(!@ftp_chdir($conn_id, $part)) {
                     ftp_mkdir($conn_id, $part);
                     ftp_chdir($conn_id, $part);
                 }
             }
             
             // Upload
             if (ftp_put($conn_id, '/'.$target_dir.'/'.$relative_path, $file_path, FTP_BINARY)) {
                  echo "Uploaded: $relative_path\n";
             }
        }
    } else {
        ftp_chdir($conn_id, '/'.$target_dir);
        $relative_path = $path;
        if (ftp_put($conn_id, $relative_path, $local_path, FTP_BINARY)) {
            echo "Uploaded: $relative_path\n";
        }
    }
}

ftp_close($conn_id);
echo "Deployment Complete.\n";
?>
