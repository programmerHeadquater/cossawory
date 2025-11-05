<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../review.php';
require_once __DIR__ . '/../submission.php';

use function review\insertReview;
use function review\getReviewBySubmissionId;
use function review\updateReview;
use function review\deleteReview;
use function review\getTotalReviewBySubmissionId;

class ReviewTest extends TestCase
{
    private $submissionId = 1; // Set to an existing submission for testing
    private $userId = 1;       // Set to an existing user for testing
    private $reviewId;

    public function testInsertReview()
    {
        $response = insertReview($this->submissionId, $this->userId, 'This is a test review.');
        $this->assertTrue($response['status']);
        $this->reviewId = $response['data']['review_id'];
        $this->assertIsInt($this->reviewId);
    }

    /**
     * @depends testInsertReview
     */
    public function testGetReviewBySubmissionId()
    {
        $response = getReviewBySubmissionId($this->submissionId, 0);
        $this->assertTrue($response['status']);
        $this->assertNotEmpty($response['data']);
    }

    /**
     * @depends testInsertReview
     */
    public function testUpdateReview()
    {
        $response = updateReview($this->reviewId, 'Updated review content');
        $this->assertTrue($response['status']);
    }

    /**
     * @depends testInsertReview
     */
    public function testGetTotalReviewBySubmissionId()
    {
        $response = getTotalReviewBySubmissionId($this->submissionId);
        $this->assertTrue($response['status']);
        $this->assertGreaterThanOrEqual(1, $response['data']);
    }

    /**
     * @depends testInsertReview
     */
    public function testDeleteReview()
    {
        $response = deleteReview($this->reviewId);
        $this->assertTrue($response['status']);
    }
}
