<?php
define('SCHOOL_NAME', 'Aunty Anne\'s International School');

// Set error reporting
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log');

// Custom error handler
set_error_handler(function ($severity, $message, $file, $line) {
    $error = sprintf(
        "[%s] PHP Error: %s in %s on line %d (Severity: %d)\n",
        date('Y-m-d H:i:s'),
        $message,
        $file,
        $line,
        $severity
    );
    file_put_contents(__DIR__ . '/error_log', $error, FILE_APPEND);
    if (in_array($severity, [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        http_response_code(500);
        die('A server error occurred. Please contact the administrator.');
    }
    return true;
});

// Custom exception handler
set_exception_handler(function ($exception) {
    $error = sprintf(
        "[%s] Uncaught Exception: %s in %s on line %d\nStack Trace: %s\n",
        date('Y-m-d H:i:s'),
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        $exception->getTraceAsString()
    );
    file_put_contents(__DIR__ . '/error_log', $error, FILE_APPEND);
    http_response_code(500);
    die('A server error occurred. Please contact the administrator.');
});

// Shutdown function for fatal errors
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        $error = sprintf(
            "[%s] Fatal Error: %s in %s on line %d\n",
            date('Y-m-d H:i:s'),
            $error['message'],
            $error['file'],
            $error['line']
        );
        file_put_contents(__DIR__ . '/error_log', $error, FILE_APPEND);
        http_response_code(500);
        die('A server error occurred. Please contact the administrator.');
    }
});

// Check for autoloader
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    $error = sprintf(
        "[%s] Autoloader Missing: vendor/autoload.php not found\n",
        date('Y-m-d H:i:s')
    );
    file_put_contents(__DIR__ . '/error_log', $error, FILE_APPEND);
    http_response_code(500);
    die('A server error occurred. Please contact the administrator.');
}

require_once 'vendor/autoload.php';

use Dotenv\Dotenv;

try {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $dbParams = [
        'host'     => getenv('DB_HOST') ?: 'localhost',
        'user'     => getenv('DB_USER') ?: 'auntyan1_admin',
        'password' => getenv('DB_PASS') ?: 'Tripled121',
        'dbname'   => getenv('DB_NAME') ?: 'auntyan1_AAIS_DB',
    ];

    $dsn = "mysql:host={$dbParams['host']};dbname={$dbParams['dbname']};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbParams['user'], $dbParams['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (Exception $e) {
    $error = sprintf(
        "[%s] Database Connection Failed: %s\nStack Trace: %s\n",
        date('Y-m-d H:i:s'),
        $e->getMessage(),
        $e->getTraceAsString()
    );
    file_put_contents(__DIR__ . '/error_log', $error, FILE_APPEND);
    http_response_code(500);
    die('A server error occurred. Please contact the administrator.');
}
?>