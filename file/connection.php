<?php
// Function to load .env variables securely
if (!function_exists('loadEnv')) {
    function loadEnv($envPath) {
        if (!file_exists($envPath)) {
            return;
        }
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '#') === 0) {
                continue;
            }
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value, " \t\n\r\0\x0B\"'");
                if (!array_key_exists($key, $_SERVER) && !array_key_exists($key, $_ENV)) {
                    putenv("$key=$value");
                    $_ENV[$key] = $value;
                    $_SERVER[$key] = $value;
                }
            }
        }
    }
}

// Load .env from project root
loadEnv(__DIR__ . '/../.env');

$servername = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? 'db');
$username   = getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? 'bloodbank_user');
$password   = getenv('DB_PASS') ?: ($_ENV['DB_PASS'] ?? 'SecurePassword123!');
$dbname     = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? 'bloodbank');

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die('Could not Connect MySql: ' . $conn->connect_error);
}
?>