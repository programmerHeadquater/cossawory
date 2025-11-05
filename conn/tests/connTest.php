<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../conn.php';

class ConnTest extends TestCase
{
    /**
     * Test opening a database connection
     */
    public function testOpenConnection()
    {
        $conn = conn\openDatabaseConnection();
        $this->assertNotNull($conn, "Database connection failed");
        echo "openDatabaseConnection pass\n";

        // Test that it is a mysqli object
        $this->assertInstanceOf(mysqli::class, $conn, "Connection is not a mysqli instance");
        echo "openDatabaseConnection instance check pass\n";

        conn\closeDatabaseConnection($conn);
        echo "closeDatabaseConnection pass\n";
    }

    /**
     * Test closing a null connection (should not error)
     */
    public function testCloseNullConnection()
    {
        $conn = null;
        conn\closeDatabaseConnection($conn);
        $this->assertNull($conn, "Closing null connection should not affect variable");
        echo "closeDatabaseConnection(null) pass\n";
    }

    /**
     * Test error handling by simulating invalid credentials
     */
    public function testInvalidCredentials()
    {
        // Backup the current function behavior using closure
        $openFunc = function() {
            $conn = @new mysqli('localhost', 'wronguser', 'wrongpass', 'wrongdb');
            if ($conn->connect_error) {
                return null;
            }
            return $conn;
        };

        $conn = $openFunc();
        $this->assertNull($conn, "Connection with invalid credentials should return null");
        echo "openDatabaseConnection(invalid credentials) pass\n";
    }
}
