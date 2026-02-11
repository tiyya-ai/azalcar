<?php
/**
 * Test File for GitHub Deployment
 * 
 * This file tests if GitHub Actions deployment works correctly.
 * Created: 2024-02-11
 */

echo "GitHub Deployment Test\n";
echo "======================\n";
echo "If you see this file on your server after pushing to GitHub,\n";
echo "then the deployment is working correctly!\n";
echo "\n";
echo "Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n";
