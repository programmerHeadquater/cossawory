<?php
namespace utils;

function logError(string $message)
{
    $logFile = __DIR__ . '/error.log';
    error_log("[" . date('Y-m-d H:i:s') . "] " . $message . "\n", 3, $logFile);
}
