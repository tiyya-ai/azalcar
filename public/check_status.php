<?php
// Script to check if files on the server are updated
header('Content-Type: text/plain');

$filesToCheck = [
    'app/Http/Controllers/AuthController.php',
    'routes/web.php',
    'resources/views/partials/auth-modal.blade.php',
    '.env', // Just to see if it exists
    'public/index.php'
];

echo "Checking essential files on: " . $_SERVER['HTTP_HOST'] . "\n";
echo "Current Server Time: " . date('Y-m-d H:i:s') . "\n\n";

foreach ($filesToCheck as $file) {
    $fullPath = __DIR__ . '/../' . $file;
    if (file_exists($fullPath)) {
        $mtime = date('Y-m-d H:i:s', filemtime($fullPath));
        $size = filesize($fullPath);
        $excerpt = '';
        
        // For AuthController, check for 'intended' keyword to confirm update
        if (str_contains($file, 'AuthController')) {
            $content = file_get_contents($fullPath);
            if (str_contains($content, 'intended')) {
                $excerpt = "[UPDATED: Contains 'intended' redirect]";
            } else {
                $excerpt = "[OLD: Missing 'intended' redirect]";
            }
        }
        
        echo "File: $file\n";
        echo "Status: EXISTS\n";
        echo "Last Modified: $mtime\n";
        echo "Size: $size bytes\n";
        echo "Info: $excerpt\n";
    } else {
        echo "File: $file\n";
        echo "Status: MISSING\n";
    }
    echo "-----------------------------------\n";
}
