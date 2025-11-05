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
    $conn = openDatabaseConnection();
    if ($conn === null) {
        $error = "Database connection failed.";
        logError($error);
        return makeResponse(false, null, $error);
    }

    $stmt = $conn->prepare('UPDATE review SET review = ? WHERE id = ?');
    if (!$stmt) {
        $error = "Prepare failed: " . $conn->error;
        logError($error);
        closeDatabaseConnection($conn);
        return makeResponse(false, null, $error);
    }

    if (!$stmt->bind_param('si', $review, $review_id)) {
        $error = "Bind failed: " . $stmt->error;
        logError($error);
        $stmt->close();
        closeDatabaseConnection($conn);
        return makeResponse(false, null, $error);
    }

    if (!$stmt->execute()) {
        $error = "Execute failed: " . $stmt->error;
        logError($error);
        $stmt->close();
        closeDatabaseConnection($conn);
        return makeResponse(false, null, $error);
    }

    $affectedRows = $stmt->affected_rows;
    $data = ($affectedRows > 0)
        ? "Review updated successfully."
        : "No rows were updated (ID not found or no changes).";

    $stmt->close();
    closeDatabaseConnection($conn);

    return makeResponse(true, $data);
}

/**
 * Insert a new review with user_id
 */
function insertReview($submission_id, $user_id, $reviewText)
{
    $conn = openDatabaseConnection();
    if ($conn === null) {
        $error = "Database connection failed.";
        logError($error);
        return makeResponse(false, null, $error);
    }

    $stmt = $conn->prepare("INSERT INTO review (submission_id, user_id, review) VALUES (?, ?, ?)");
    if (!$stmt) {
        $error = "Prepare failed: " . $conn->error;
        logError($error);
        closeDatabaseConnection($conn);
        return makeResponse(false, null, $error);
    }

    if (!$stmt->bind_param("iis", $submission_id, $user_id, $reviewText)) {
        $error = "Bind failed: " . $stmt->error;
        logError($error);
        $stmt->close();
        closeDatabaseConnection($conn);
        return makeResponse(false, null, $error);
    }

    if (!$stmt->execute()) {
        $error = "Execute failed: " . $stmt->error;
        logError($error);
        $stmt->close();
        closeDatabaseConnection($conn);
        return makeResponse(false, null, $error);
    }

    $review_id = $stmt->insert_id;
    $stmt->close();
    closeDatabaseConnection($conn);

    // Update submission review status
    updateSubmissionReviewStatus($submission_id);

    return makeResponse(true, ['review_id' => $review_id]);
}

/**
 * Delete a review by ID
 */
function deleteReview($review_id)
{
    $conn = openDatabaseConnection();
    if ($conn === null) {
        $error = "Database connection failed.";
        logError($error);
        return makeResponse(false, null, $error);
    }

    $stmt = $conn->prepare("DELETE FROM review WHERE id = ?");
    if (!$stmt) {
        $error = "Prepare failed: " . $conn->error;
        logError($error);
        closeDatabaseConnection($conn);
        return makeResponse(false, null, $error);
    }

    if (!$stmt->bind_param("i", $review_id)) {
        $error = "Bind failed: " . $stmt->error;
        logError($error);
        $stmt->close();
        closeDatabaseConnection($conn);
        return makeResponse(false, null, $error);
    }

    if (!$stmt->execute()) {
        $error = "Execute failed: " . $stmt->error;
        logError($error);
        $stmt->close();
        closeDatabaseConnection($conn);
        return makeResponse(false, null, $error);
    }

    $affectedRows = $stmt->affected_rows;
    $data = ($affectedRows > 0)
        ? "Review deleted successfully."
        : "No review found with the given ID.";

    $stmt->close();
    closeDatabaseConnection($conn);

    return makeResponse(true, $data);
}

/**
 * Get paginated reviews for a submission (includes reviewer info)
 */
function getReviewBySubmissionId($submission_id, $startPoint)
{
    $conn = openDatabaseConnection();
    if ($conn === null) {
        $error = "Database connection failed.";
        logError($error);
        return makeResponse(false, [], $error);
    }

    $sql = "
        SELECT r.*, u.username, u.email
        FROM review r
        JOIN user u ON r.user_id = u.id
        WHERE r.submission_id = ?
        ORDER BY r.created_at DESC
        LIMIT 20 OFFSET ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $error = "Prepare failed: " . $conn->error;
        logError($error);
        closeDatabaseConnection($conn);
        return makeResponse(false, [], $error);
    }

    if (!$stmt->bind_param("ii", $submission_id, $startPoint)) {
        $error = "Bind failed: " . $stmt->error;
        logError($error);
        $stmt->close();
        closeDatabaseConnection($conn);
        return makeResponse(false, [], $error);
    }

    if (!$stmt->execute()) {
        $error = "Execute failed: " . $stmt->error;
        logError($error);
        $stmt->close();
        closeDatabaseConnection($conn);
        return makeResponse(false, [], $error);
    }

    $result = $stmt->get_result();
    $data = $result->fetch_all(\MYSQLI_ASSOC);

    $stmt->close();
    closeDatabaseConnection($conn);

    return makeResponse(true, $data);
}

/**
 * Get total review count for a submission
 */
function getTotalReviewBySubmissionId($submission_id)
{
    $conn = openDatabaseConnection();
    if ($conn === null) {
        $error = "Database connection failed.";
        logError($error);
        return makeResponse(false, 0, $error);
    }

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM review WHERE submission_id = ?");
    if (!$stmt) {
        $error = "Prepare failed: " . $conn->error;
        logError($error);
        closeDatabaseConnection($conn);
        return makeResponse(false, 0, $error);
    }

    if (!$stmt->bind_param("i", $submission_id)) {
        $error = "Bind failed: " . $stmt->error;
        logError($error);
        $stmt->close();
        closeDatabaseConnection($conn);
        return makeResponse(false, 0, $error);
    }

    if (!$stmt->execute()) {
        $error = "Execute failed: " . $stmt->error;
        logError($error);
        $stmt->close();
        closeDatabaseConnection($conn);
        return makeResponse(false, 0, $error);
    }

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $count = $row['total'] ?? 0;

    $stmt->close();
    closeDatabaseConnection($conn);

    return makeResponse(true, $count);
}
?>
