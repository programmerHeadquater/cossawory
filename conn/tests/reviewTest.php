<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/seedingDb.php';
require_once __DIR__ . '/../review.php';
require_once __DIR__ . '/../submission.php';

use function review\insertReview;
use function review\getReviewBySubmissionId;
use function review\updateReview;
use function review\deleteReview;
use function review\getTotalReviewBySubmissionId;
use function seed\seedDatabase;
use function seed\clearDatabase;

final class ReviewTest extends TestCase
{
    private int $submissionId;
    private int $userId;
    private static int $reviewId;

    protected function setUp(): void
    {
        clearDatabase();
        seedDatabase();

        $this->submissionId = 1; // Assuming seeded submission ID exists
        $this->userId = 1;       // Assuming seeded user ID exists
    }

    protected function tearDown(): void
    {
        clearDatabase();
    }

    public function test_it_should_insert_a_review(): void
    {
        $response = insertReview($this->submissionId, $this->userId, 'Initial test review');
        $this->assertTrue($response['status'], 'Insert review failed');
        $this->assertArrayHasKey('review_id', $response['data']);
        self::$reviewId = $response['data']['review_id'];
        $this->assertIsInt(self::$reviewId);
    }

    /**
     * @depends test_it_should_insert_a_review
     */
    public function test_it_should_get_review_by_submission_id(): void
    {
        $response = getReviewBySubmissionId($this->submissionId, 0);
        $this->assertTrue($response['status']);
        $this->assertIsArray($response['data']);
        $this->assertNotEmpty($response['data']);
        $review = $response['data'][0];
        $this->assertArrayHasKey('id', $review);
        $this->assertArrayHasKey('review', $review);
        $this->assertArrayHasKey('username', $review);
        $this->assertArrayHasKey('email', $review);
    }

    /**
     * @depends test_it_should_insert_a_review
     */
    public function test_it_should_update_a_review(): void
    {
        $newContent = 'Updated review content';
        $response = updateReview(self::$reviewId, $newContent);
        $this->assertTrue($response['status']);
        $this->assertStringContainsString('Review updated', $response['data']);
    }

    /**
     * @depends test_it_should_insert_a_review
     */
    public function test_it_should_get_total_reviews_for_submission(): void
    {
        $response = getTotalReviewBySubmissionId($this->submissionId);
        $this->assertTrue($response['status']);
        $this->assertIsInt($response['data']);
        $this->assertGreaterThanOrEqual(1, $response['data']);
    }

    /**
     * @depends test_it_should_insert_a_review
     */
    public function test_it_should_delete_a_review(): void
    {
        $response = deleteReview(self::$reviewId);
        $this->assertTrue($response['status']);
        $this->assertStringContainsString('Review deleted', $response['data']);
    }
}
