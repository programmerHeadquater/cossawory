<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../submission.php';
require_once __DIR__ . '/seedingDb.php';

use function submission\insertSubmissionFromJson;
use function submission\deleteSubmission;
use function submission\getSubmission;
use function submission\getSubmissionsTotalCount;
use function submission\getSubmissionById;
use function submission\updateSubmissionReviewStatus;
use function submission\getSubmissionsReviewedTotalCount;
use function submission\getSubmissionReviewPending;
use function submission\getSubmissionsReviewPendingTotalCount;
use function submission\getSubmissionReviewed;
use function submission\logError;

use function seed\seedDatabase;
use function seed\clearDatabase;

final class SubmissionTest extends TestCase
{
    private array $testSubmission =  [
    [
        "label" => "Tittle",
        "type" => "text",
        "required" => "yes",
        "name" => "tittle",
        "value" => "test"
    ],
    [
        "label" => "Description",
        "type" => "text",
        "required" => "yes",
        "name" => "description",
        "value" => "test"
    ]
];

    protected function setUp(): void
    {
        // Clear and seed database before each test
        clearDatabase();
        seedDatabase();
    }

    protected function tearDown(): void
    {
        // Clean database after each test
        // clearDatabase();
    }


    public function test_it_should_insert_a_submission(): void
    {
        $response = insertSubmissionFromJson($this->testSubmission);
        $this->assertTrue($response['status']);
        $this->assertIsInt($response['data']['id']);
        $this->assertNull($response['error']);
    }

  
    public function test_it_should_get_a_submission_by_id(): void
    {
        $insert = insertSubmissionFromJson($this->testSubmission);
        $id = $insert['data']['id'];

        $response = getSubmissionById($id);
        $this->assertTrue($response['status']);
        $this->assertEquals($id, $response['data']['id']);
        $this->assertNull($response['error']);
    }


    public function test_it_should_delete_a_submission(): void
    {
        $insert = insertSubmissionFromJson($this->testSubmission);
        $id = $insert['data']['id'];
        $delete = deleteSubmission($id);
        $this->assertTrue($delete['status']);
        $this->assertEquals('Deleted', $delete['data']);
    }


    public function test_it_should_update_submission_review_status(): void
    {
        $insert = insertSubmissionFromJson($this->testSubmission);
        $id = $insert['data']['id'];

        $update = updateSubmissionReviewStatus($id);
        $this->assertTrue($update['status']);
        $this->assertEquals('Review status updated', $update['data']);

        // Verify reviewed count increased
        $count = getSubmissionsReviewedTotalCount();
        $this->assertGreaterThanOrEqual(1, $count['data']);
    }


    public function test_it_should_get_submissions_with_pagination(): void
    {
        insertSubmissionFromJson($this->testSubmission);
        $response = getSubmission(0);
        $this->assertTrue($response['status']);
        $this->assertIsArray($response['data']);
    }

    public function test_it_should_get_total_submission_count(): void
    {
        insertSubmissionFromJson($this->testSubmission);
        $response = getSubmissionsTotalCount();
        $this->assertTrue($response['status']);
        $this->assertIsInt($response['data']);
        $this->assertGreaterThan(0, $response['data']);
    }


    public function test_it_should_get_submissions_pending_review(): void
    {
        insertSubmissionFromJson($this->testSubmission);
        $response = getSubmissionReviewPending(0);
        $this->assertTrue($response['status']);
        $this->assertIsArray($response['data']);
    }


    public function test_it_should_get_total_submissions_pending_review(): void
    {
        insertSubmissionFromJson($this->testSubmission);
        $response = getSubmissionsReviewPendingTotalCount();
        $this->assertTrue($response['status']);
        $this->assertIsInt($response['data']);
        $this->assertGreaterThan(0, $response['data']);
    }


    public function test_it_should_get_reviewed_submissions(): void
    {
        $insert = insertSubmissionFromJson($this->testSubmission);
        $id = $insert['data']['id'];
        updateSubmissionReviewStatus($id);

        $response = getSubmissionReviewed(0);
        $this->assertTrue($response['status']);
        $this->assertIsArray($response['data']);
        $this->assertNotEmpty($response['data']);
    }

 
    public function test_it_should_get_total_reviewed_submissions(): void
    {
        $insert = insertSubmissionFromJson($this->testSubmission);
        $id = $insert['data']['id'];
        updateSubmissionReviewStatus($id);

        $response = getSubmissionsReviewedTotalCount();
        $this->assertTrue($response['status']);
        $this->assertIsInt($response['data']);
        $this->assertGreaterThan(0, $response['data']);
    }


    public function test_it_should_log_errors(): void
    {
        logError("Unit test error");
        $logFile = __DIR__ . '/../error.log';
        $this->assertFileExists($logFile);
        $contents = file_get_contents($logFile);
        $this->assertStringContainsString("Unit test error", $contents);
    }

    public function testGetSubmission(): void
{
    // Fetch first page of submissions (startPoint = 0)
    $response = getSubmission(0);

    $this->assertTrue($response['status']);          // Ensure function returned success
    $this->assertIsArray($response['data']);         // Data should be an array
    $this->assertNotEmpty($response['data']);        // Array should contain at least one submission

    // Optional: check that each item has required fields
    $submission = $response['data'][0];
    $this->assertArrayHasKey('id', $submission);
    $this->assertArrayHasKey('form_data', $submission);
    $this->assertArrayHasKey('review', $submission);
}
}
