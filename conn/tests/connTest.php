<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// Import your namespace functions
require_once __DIR__ . '/../conn.php';
use function conn\openDatabaseConnection;
use function conn\closeDatabaseConnection;
use function conn\logError;

final class ConnTest extends TestCase
{
    private string $logFile;

    protected function setUp(): void
    {
        // Path to your log file
        $this->logFile = __DIR__ . '/../error.log';

        // Clear log file before each test
        if (file_exists($this->logFile)) {
            file_put_contents($this->logFile, '');
        }
    }

    /**
     * Test that openDatabaseConnection returns a valid mysqli object
     */
    public function testOpenDatabaseConnectionSuccess(): void
    {
        $conn = openDatabaseConnection();
        $this->assertInstanceOf(mysqli::class, $conn);
        $this->assertEquals(0, $conn->connect_errno);

        // Close connection
        closeDatabaseConnection($conn);
    }

    /**
     * Test that closeDatabaseConnection handles null connection
     */
    public function testCloseDatabaseConnectionWithNull(): void
    {
        closeDatabaseConnection(null);

        $logContents = file_get_contents($this->logFile);
        $this->assertStringContainsString('Attempted to close an invalid or null database connection', $logContents);
    }

    /**
     * Test that logError writes a message to the log file
     */
    public function testLogErrorWritesToFile(): void
    {
        $message = 'Simulated log message for test';
        logError($message);

        $logContents = file_get_contents($this->logFile);
        $this->assertStringContainsString($message, $logContents);
    }

    /**
     * Test closing a valid connection runs without error
     */
    public function testCloseDatabaseConnectionWithValidConnection(): void
    {
        $conn = openDatabaseConnection();
        $this->assertInstanceOf(mysqli::class, $conn);

        // Just ensure closing does not throw
        closeDatabaseConnection($conn);

        // Connection is closed, we cannot reliably check properties
        // Just assert log file is empty since no error should be logged
        $logContents = file_get_contents($this->logFile);
        $this->assertEmpty($logContents);
    }

    /**
     * Test multiple logError messages
     */
    public function testMultipleLogErrors(): void
    {
        logError('First error');
        logError('Second error');

        $logContents = file_get_contents($this->logFile);
        $this->assertStringContainsString('First error', $logContents);
        $this->assertStringContainsString('Second error', $logContents);
    }
}
