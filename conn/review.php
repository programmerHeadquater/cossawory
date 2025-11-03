<?php
namespace review;

require_once 'conn.php';
require_once 'submission.php';

use function conn\closeDatabaseConnection;
use function conn\openDatabaseConnection;
use function submission\updateSubmissionReviewStatus;

/**
 * Logs an error message to a local file
 */
function logError($message)
{
    $logFile = __DIR__ . '/error.log';
    $timestamp = date('Y-m-d H:i:s');
    error_log("[$timestamp] $message\n", 3, $logFile);
}

/**
 * Helper to create a consistent response structure
 */
function makeResponse($status, $data = null, $error = null)
{
    return [
        'status' => $status,
        'data' => $data,
        'error' => $error
    ];
}

/**
 * Update review content by ID
 */
function updateReview($review_id, $review)
{
    $response = makeResponse(false);
    $conn = openDatabaseConnection();

    if ($conn === null) {
        $error = "Database connection failed.";
        logError($error);
        return makeResponse(false, null, $error);
    }

    $stmt = $conn->prepare('UPDATE reviews SET review = ? WHERE id = ?');
    if (!$stmt) {
        $error = "Prepare failed: " . $conn->error;
        logError($error);
        closeDatabaseConnection($conn);
        return makeResponse(false, null, $error);
    }

    if (!$stmt->bind_param('si', $review, $review_id)) {
        $error = "Bind failed: " . $stmt->error;
        logError($error);
    } elseif (!$stmt->execute()) {
        $error = "Execute failed: " . $stmt->error;
        logError($error);
    } else {
        $affectedRows = $stmt->affected_rows;
        $data = ($affectedRows > 0)
            ? "Review updated successfully."
            : "No rows were updated (ID not found or no changes).";
        $response = makeResponse(true, $data);
    }

    $stmt->close();
    closeDatabaseConnection($conn);
    return $response;
}

/**
 * Insert a new review with user_id
 */
function insertReview($submission_id, $user_id, $reviewText)
{
    $conn = openDatabaseConnection();
    $error = null;
    $review_id = null;

    if ($conn === null) {
        $error = "Database connection failed.";
        logError($error);
        return makeResponse(false, null, $error);
    }

    $stmt = $conn->prepare("INSERT INTO reviews (submission_id, user_id, review) VALUES (?, ?, ?)");
    if (!$stmt) {
        $error = "Statement prepare failed: " . $conn->error;
    } elseif (!$stmt->bind_param("iis", $submission_id, $user_id, $reviewText)) {
        $error = "Binding failed: " . $stmt->error;
    } elseif (!$stmt->execute()) {
        $error = "Execution failed: " . $stmt->error;
    } else {
        $review_id = $stmt->insert_id;
        updateSubmissionReviewStatus($submission_id);
    }

    if ($error) logError($error);

    $stmt?->close();
    closeDatabaseConnection($conn);

    return makeResponse(!$error, ['review_id' => $review_id], $error);
}

/**
 * Delete a review by ID
 */
function deleteReview($review_id)
{
    $conn = openDatabaseConnection();
    $error = null;

    if ($conn === null) {
        $error = "Database connection failed.";
        logError($error);
        return makeResponse(false, null, $error);
    }

    $stmt = $conn->prepare("DELETE FROM reviews WHERE id = ?");
    if (!$stmt) {
        $error = "Prepare failed: " . $conn->error;
    } elseif (!$stmt->bind_param("i", $review_id)) {
        $error = "Bind failed: " . $stmt->error;
    } elseif (!$stmt->execute()) {
        $error = "Execute failed: " . $stmt->error;
    }

    if ($error) logError($error);

    $stmt?->close();
    closeDatabaseConnection($conn);

    return makeResponse(!$error, "Review deleted.", $error);
}

/**
 * Update submission with review_id
 */
function insertReviewIdIntoSubmission($review_id, $submission_id)
{
    $conn = openDatabaseConnection();
    $error = null;

    if ($conn === null) {
        $error = "Database connection failed.";
        logError($error);
        return makeResponse(false, null, $error);
    }

    $stmt = $conn->prepare("UPDATE submission SET review = 1, review_id = ? WHERE id = ?");
    if (!$stmt) {
        $error = "Prepare failed: " . $conn->error;
    } elseif (!$stmt->bind_param("ii", $review_id, $submission_id)) {
        $error = "Bind failed: " . $stmt->error;
    } elseif (!$stmt->execute()) {
        $error = "Execute failed: " . $stmt->error;
    }

    if ($error) logError($error);

    $stmt?->close();
    closeDatabaseConnection($conn);

    return makeResponse(!$error, "Review linked to submission.", $error);
}

/**
 * Get paginated reviews for a submission (includes reviewer info)
 */
function getReviewBySubmissionId($submission_id, $startPoint)
{
    $conn = openDatabaseConnection();
    $error = null;
    $data = [];

    if ($conn === null) {
        $error = "Database connection failed.";
        logError($error);
        return makeResponse(false, [], $error);
    }

    $sql = "
        SELECT r.*, u.username, u.email
        FROM reviews r
        JOIN users u ON r.user_id = u.id
        WHERE r.submission_id = ?
        ORDER BY r.created_at DESC
        LIMIT 20 OFFSET ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $error = "Prepare failed: " . $conn->error;
    } elseif (!$stmt->bind_param("ii", $submission_id, $startPoint)) {
        $error = "Bind failed: " . $stmt->error;
    } elseif (!$stmt->execute()) {
        $error = "Execute failed: " . $stmt->error;
    } else {
        $result = $stmt->get_result();
        $data = $result->fetch_all(\MYSQLI_ASSOC);
    }

    if ($error) logError($error);

    $stmt?->close();
    closeDatabaseConnection($conn);

    return makeResponse(!$error, $data, $error);
}

/**
 * Get total review count for a submission
 */
function getTotalReviewBySubmissionId($submission_id)
{
    $conn = openDatabaseConnection();
    $error = null;
    $count = 0;

    if ($conn === null) {
        $error = "Database connection failed.";
        logError($error);
        return makeResponse(false, 0, $error);
    }

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM reviews WHERE submission_id = ?");
    if (!$stmt) {
        $error = "Prepare failed: " . $conn->error;
    } elseif (!$stmt->bind_param("i", $submission_id)) {
        $error = "Bind failed: " . $stmt->error;
    } elseif (!$stmt->execute()) {
        $error = "Execute failed: " . $stmt->error;
    } else {
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $count = $row['total'] ?? 0;
    }

    if ($error) logError($error);

    $stmt?->close();
    closeDatabaseConnection($conn);

    return makeResponse(!$error, $count, $error);
}
?>
