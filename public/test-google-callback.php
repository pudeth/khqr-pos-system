<?php
// Quick test to see if callback URL is accessible
echo "✅ Google Callback URL is accessible!<br><br>";
echo "Current URL: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "Server: " . $_SERVER['SERVER_NAME'] . ":" . $_SERVER['SERVER_PORT'] . "<br>";
echo "Full URL: " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "<br><br>";

echo "Expected Laravel route: /auth/google/callback<br><br>";

echo "If you see this, the server is working. The issue is with Laravel routing.<br>";
echo "Make sure .htaccess is working and mod_rewrite is enabled.";
