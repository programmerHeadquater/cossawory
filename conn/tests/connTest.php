<?php
use PHPUnit\Framework\TestCase;

// Include your main code and seeding functions
require_once __DIR__ . '/../conn.php';
require_once __DIR__ . '/seedingDb.php';

use function conn\openDatabaseConnection;
use function conn\closeDatabaseConnection;
use function conn\logError;
use function seed\seedDatabase;
use function seed\clearDatabase;

final class ConnTest extends TestCase
{
    private ?mysqli $conn = null;
    private string $logFile;

    /**
     * Runs before each test
     */
    protected function setUp(): void
    {
        // Set log file path and remove old log
        $this->logFile = __DIR__ . '/../error.log';
        if (file_exists($this->logFile)) {
            unlink($this->logFile);
        }

        // Open DB connection
        $this->conn = openDatabaseConnection();
        $this->assertInstanceOf(mysqli::class, $this->conn, "Database connection should succeed");

        // Seed database
        $seeded = seedDatabase();
        $this->assertTrue($seeded, "Seeding should succeed before each test");
    }

    /**
     * Runs after each test
     */
    protected function tearDown(): void
    {
        // Clear database after each test
        clearDatabase();

        // Close connection safely
        if ($this->conn instanceof mysqli && $this->conn) {
            closeDatabaseConnection($this->conn);
        }
        $this->conn = null;
    }

    // -----------------------
    // Helper function
    // -----------------------
    private function isConnectionAlive(?mysqli $conn): bool
    {
        return $conn instanceof mysqli && $conn;
    }

    // -----------------------
    // Test cases
    // -----------------------

    public function testItShouldOpenDatabaseConnection(): void
    {
        $this->assertInstanceOf(mysqli::class, $this->conn);
        $this->assertEquals(0, $this->conn->connect_errno, "Connection error number should be 0");
    }

    public function testItShouldSetCharsetToUtf8mb4(): void
    {
        $this->assertEquals('utf8mb4', $this->conn->character_set_name(), "Charset should be utf8mb4");
    }

    public function testItShouldFailWithInvalidCredentials(): void
    {
        $badConn = @new mysqli('localhost', 'wrong_user', 'wrong_pass', 'fake_db');
        $this->assertNotEquals(0, $badConn->connect_errno, "Connection should fail with invalid credentials");
    }

    public function testItShouldSeedAllTables(): void
    {
        $tables = ['user', 'submission', 'review'];
        foreach ($tables as $table) {
            $res = $this->conn->query("SELECT COUNT(*) AS cnt FROM $table");
            $row = $res->fetch_assoc();
            $this->assertGreaterThan(0, (int)$row['cnt'], "Table $table should have seeded data");
        }
    }

    public function testItShouldClearAllTables(): void
    {
        clearDatabase();
        $tables = ['user', 'submission', 'review'];
        foreach ($tables as $table) {
            $res = $this->conn->query("SELECT COUNT(*) AS cnt FROM $table");
            $row = $res->fetch_assoc();
            $this->assertEquals(0, (int)$row['cnt'], "Table $table should be empty after clearing");
        }
    }

    public function testItShouldCloseConnectionSafely(): void
    {
        if ($this->isConnectionAlive($this->conn)) {
            closeDatabaseConnection($this->conn);
            $this->conn = null;
        }

        // Confirm connection is closed
        $this->assertNull($this->conn);
    }

    public function testItShouldLogErrorForInvalidConnection(): void
    {
        closeDatabaseConnection(null);

        $this->assertFileExists($this->logFile, "Error log file should exist");
        $contents = file_get_contents($this->logFile);
        $this->assertStringContainsString(
            'Attempted to close an invalid or null database connection',
            $contents
        );
    }

    public function testItShouldWriteErrorMessagesToLog(): void
    {
        logError("Unit test log message");

        $this->assertFileExists($this->logFile, "Error log file should be created");
        $contents = file_get_contents($this->logFile);
        $this->assertStringContainsString("Unit test log message", $contents);
    }
}
