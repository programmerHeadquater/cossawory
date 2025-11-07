<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/seedingDb.php';
require_once __DIR__ . '/../review.php';
require_once __DIR__ . '/../submission.php';

use function seed\seedDatabase;
use function seed\clearDatabase;
use function review\insertReview;
use function review\updateReview;
use function review\deleteReview;
use function review\getReviewBySubmissionId;
use function review\getTotalReviewBySubmissionId;

final class ReviewTest extends TestCase
{
    private static $reviewId;
    private static $submissionId;
    private static $userId;

    public static function setUpBeforeClass(): void
    {
        // Seed database first (users, submissions, reviews)
        seedDatabase();

        // Pick first submission and user
        self::$submissionId = 1;
        self::$userId = 1;
    }

    public static function tearDownAfterClass(): void
    {
        // Clean database after all tests
        clearDatabase();
    }

    public function test_it_should_insert_a_review(): void
    {
        $reviewText = "Unit test review";
        $response = insertReview(self::$submissionId, self::$userId, $reviewText);

        $this->assertTrue($response['status'], "Insert review failed: " . ($response['error'] ?? ''));
        $this->assertArrayHasKey('review_id', $response['data']);

        self::$reviewId = $response['data']['review_id'];
    }

    public function test_it_should_update_review(): void
    {
        $newText = "Updated review text";
        $response = updateReview(self::$reviewId, $newText);

        $this->assertTrue($response['status']);
        $this->assertStringContainsString("Review updated successfully", $response['data']);
    }

    public function test_it_should_get_review_by_submission_id(): void
    {
        $response = getReviewBySubmissionId(self::$submissionId, 0);

        $this->assertTrue($response['status']);
        $this->assertNotEmpty($response['data']);

        $found = false;
        foreach ($response['data'] as $review) {
            if ($review['id'] === self::$reviewId) {
                $found = true;
                $this->assertEquals(self::$userId, $review['user_id']);
                break;
            }
        }

        $this->assertTrue($found, "Inserted review not found for submission");
    }

    public function test_it_should_get_total_review_by_submission_id(): void
    {
        $response = getTotalReviewBySubmissionId(self::$submissionId);

        $this->assertTrue($response['status']);
        $this->assertGreaterThanOrEqual(1, $response['data']);
    }

    public function test_it_should_delete_review(): void
    {
        $response = deleteReview(self::$reviewId);

        $this->assertTrue($response['status']);
        $this->assertStringContainsString('Review deleted successfully', $response['data']);
    }
}
