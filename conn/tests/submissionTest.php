<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../submission.php';
require_once __DIR__ . '/../conn.php';

class SubmissionTest extends TestCase
{
    private array $seededIds = [];

    protected function setUp(): void
    {
        $conn = conn\openDatabaseConnection();
        if (!$conn) {
            $this->fail("Database connection failed in setup");
        }

        // Clear submission table
        $conn->query("DELETE FROM submission");

        // Seed 5 submissions
        for ($i = 1; $i <= 5; $i++) {
            $formData = [
                'title' => "Submission $i",
                'content' => "Content $i",
                'author' => "User $i"
            ];
            $resp = submission\insertSubmissionFromJson($formData);
            if ($resp['status']) {
                $this->seededIds[] = $resp['data']['id'];
            } else {
                $this->fail("Seeding submission $i failed: " . $resp['error']);
            }
        }

        conn\closeDatabaseConnection($conn);
    }

    protected function tearDown(): void
    {
        $conn = conn\openDatabaseConnection();
        if ($conn) {
            $conn->query("DELETE FROM submission");
            conn\closeDatabaseConnection($conn);
        }
        $this->seededIds = [];
    }

    public function testInsertSubmissionFromJson()
    {
        $formData = [
            'title' => 'New Submission',
            'content' => 'New content',
            'author' => 'Tester'
        ];
        $resp = submission\insertSubmissionFromJson($formData);
        $this->assertTrue($resp['status'], "insertSubmissionFromJson failed");
        $this->assertNotNull($resp['data']['id']);
        echo "insertSubmissionFromJson passed\n";
    }

   public function testGetSubmissionById()
{
    $id = $this->seededIds[0];
    $resp = submission\getSubmissionById($id);

    $this->assertTrue($resp['status'], "submission_getById failed");
    $this->assertNotNull($resp['data'], "submission data should not be null");

    $formData = json_decode($resp['data']['form_data'], true); // decode JSON
    $this->assertEquals("Submission 1", $formData['title']);
    echo "submission_getById query pass\n";
}

    public function testDeleteSubmission()
    {
        $id = $this->seededIds[1];
        $resp = submission\deleteSubmission($id);
        $this->assertTrue($resp['status'], "deleteSubmission failed");
        echo "deleteSubmission passed\n";
    }

    public function testUpdateSubmissionReviewStatus()
    {
        $id = $this->seededIds[2];
        $resp = submission\updateSubmissionReviewStatus($id);
        $this->assertTrue($resp['status'], "updateSubmissionReviewStatus failed");
        $respCheck = submission\getSubmissionById($id);
        $this->assertEquals(1, $respCheck['data']['review']);
        echo "updateSubmissionReviewStatus passed\n";
    }

    public function testGetSubmissionPagination()
    {
        $resp = submission\getSubmission(0);
        $this->assertTrue($resp['status'], "getSubmission failed");
        $this->assertGreaterThanOrEqual(1, count($resp['data']));
        echo "getSubmission pagination passed\n";
    }

    public function testCounts()
    {
        // Make sure one submission is reviewed
        $respUpdate = submission\updateSubmissionReviewStatus($this->seededIds[0]);
        $this->assertTrue($respUpdate['status']);

        $totalResp = submission\getSubmissionsTotalCount();
        $this->assertTrue($totalResp['status']);
        $this->assertGreaterThanOrEqual(5, $totalResp['data']);

        $pendingResp = submission\getSubmissionsReviewPendingTotalCount();
        $this->assertTrue($pendingResp['status']);
        $this->assertGreaterThanOrEqual(4, $pendingResp['data']); // 5 - 1 reviewed

        $reviewedResp = submission\getSubmissionsReviewedTotalCount();
        $this->assertTrue($reviewedResp['status']);
        $this->assertEquals(1, $reviewedResp['data']); // 1 reviewed
        echo "Counts tests passed\n";
    }

}
