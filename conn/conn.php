<?php
namespace conn;

define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_NAME", "cassowary_db");

/**
 * Write errors to log file
 */
function logError(string $message): void
{
    $logFile = __DIR__ . '/error.log';
    $timestamp = date('Y-m-d H:i:s');
    error_log("[$timestamp] $message\n", 3, $logFile);
}

/**
 * Open a connection to the MySQL database
 *
 * @return \mysqli|null
 */
function openDatabaseConnection(): ?\mysqli
{
    mysqli_report(MYSQLI_REPORT_OFF); // Disable warnings
    try {
        $conn = new \mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($conn->connect_errno) {
            logError("Database connection failed: (" . $conn->connect_errno . ") " . $conn->connect_error);
            return null;
        }

        // Optional: Set charset for safety
        if (!$conn->set_charset("utf8mb4")) {
            logError("Error loading character set utf8mb4: " . $conn->error);
        }

        return $conn;
    } catch (\mysqli_sql_exception $e) {
        logError("Database connection exception: " . $e->getMessage());
        return null;
    }
}

/**
 * Close a MySQL database connection
 *
 * @param \mysqli|null $conn
 * @return void
 */
function closeDatabaseConnection(?\mysqli $conn): void
{
    if ($conn instanceof \mysqli) {
        $conn->close();
    } else {
        logError('Attempted to close an invalid or null database connection.');
    }
}
