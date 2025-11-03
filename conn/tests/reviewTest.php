<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// Load dependencies
require_once __DIR__ . '/../conn.php';
require_once __DIR__ . '/../review.php';
require_once __DIR__ . '/../submission.php';

// Import functions
use function review\insertReview;
use function review\updateReview;
use function review\deleteReview;
use function review\insertReviewIdIntoSubmission;
use function review\getReviewBySubmissionId;
use function review\getTotalReviewBySubmissionId;
use function review\logError;
use function conn\openDatabaseConnection;
use function conn\closeDatabaseConnection;

final class ReviewTest extends TestCase
{
    private int $testSubmissionId;
    private int $testUserId;
    private string $logFile;

    protected function setUp(): void
    {
        $this->logFile = __DIR__ . '/../error.log';
        if (file_exists($this->logFile)) {
            file_put_contents($this->logFile, '');
        }

        $conn = openDatabaseConnection();
        if (!$conn) {
            $this->fail("Cannot connect to database for testing.");
        }

        // Create test user
        $conn->query("INSERT INTO users (username,email,password,view) VALUES ('testuser','testuser@example.com','pass',1)");
        $this->testUserId = $conn->insert_id;

        // Create test submission
        $conn->query("INSERT INTO submission (title, description) VALUES ('Test Submission','Test Description')");
        $this->testSubmissionId = $conn->insert_id;

        closeDatabaseConnection($conn);
    }

    protected function tearDown(): void
    {
        $conn = openDatabaseConnection();

        // Clean up test data
        $conn->query("DELETE FROM reviews WHERE user_id = {$this->testUserId}");
        $conn->query("DELETE FROM submission WHERE id = {$this->testSubmissionId}");
        $conn->query("DELETE FROM users WHERE id = {$this->testUserId}");

        closeDatabaseConnection($conn);

        if (file_exists($this->logFile)) {
            file_put_contents($this->logFile, '');
        }
    }

    public function testInsertReviewSuccess(): void
    {
        $response = insertReview($this->testSubmissionId, $this->testUserId, 'This is a test review');
        $this->assertTrue($response['status']);
        $this->assertArrayHasKey('review_id', $response['data']);
    }

    public function testUpdateReviewSuccess(): void
    {
        $insert = insertReview($this->testSubmissionId, $this->testUserId, 'Update test review');
        $reviewId = $insert['data']['review_id'];

        $response = updateReview($reviewId, 'Updated review content');
        $this->assertTrue($response['status']);
        $this->assertStringContainsString('Review updated successfully', $response['data']);
    }

    public function testDeleteReviewSuccess(): void
    {
        $insert = insertReview($this->testSubmissionId, $this->testUserId, 'Delete me');
        $reviewId = $insert['data']['review_id'];

        $response = deleteReview($reviewId);
        $this->assertTrue($response['status']);
        $this->assertStringContainsString('Review deleted', $response['data']);
    }

    public function testInsertReviewIdIntoSubmission(): void
    {
        $insert = insertReview($this->testSubmissionId, $this->testUserId, 'Link test');
        $reviewId = $insert['data']['review_id'];

        $response = insertReviewIdIntoSubmission($reviewId, $this->testSubmissionId);
        $this->assertTrue($response['status']);
        $this->assertStringContainsString('Review linked to submission', $response['data']);
    }

    public function testGetReviewBySubmissionId(): void
    {
        insertReview($this->testSubmissionId, $this->testUserId, 'Pagination test');

        $response = getReviewBySubmissionId($this->testSubmissionId, 0);
        $this->assertTrue($response['status']);
        $this->assertNotEmpty($response['data']);
        $this->assertArrayHasKey('username', $response['data'][0]);
        $this->assertArrayHasKey('email', $response['data'][0]);
    }

    public function testGetTotalReviewBySubmissionId(): void
    {
        insertReview($this->testSubmissionId, $this->testUserId, 'Count test');

        $response = getTotalReviewBySubmissionId($this->testSubmissionId);
        $this->assertTrue($response['status']);
        $this->assertGreaterThanOrEqual(1, $response['data']);
    }

    public function testLogErrorWritesToFile(): void
    {
        logError('Test log message');
        $logContents = file_get_contents($this->logFile);
        $this->assertStringContainsString('Test log message', $logContents);
    }
}
