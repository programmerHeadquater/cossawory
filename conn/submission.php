<?php

namespace submission;

require_once("conn.php");
use function conn\closeDatabaseConnection;
use function conn\openDatabaseConnection;

/**
 * Logs an error to a file with timestamp
 */
function logError(string $message): void
{
    $logFile = __DIR__ . '/error.log';
    $timestamp = date('Y-m-d H:i:s');
    error_log("[$timestamp] $message\n", 3, $logFile);
}

/**
 * Helper: creates a consistent response array
 */
function makeResponse(bool $status, $data = null, ?string $error = null): array
{
    return [
        'status' => $status,
        'data' => $data,
        'error' => $error
    ];
}

/**
 * Inserts a new submission from JSON data
 */
function insertSubmissionFromJson(array $jsonData): array
{
    $conn = openDatabaseConnection();
    $error = null;
    $insertId = null;

    if (!$conn) {
        $error = "Database connection failed.";
        logError($error);
        return makeResponse(false, null, $error);
    }

    $form_data = json_encode($jsonData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $review = (int) ($jsonData['review'] ?? 0);

    $stmt = $conn->prepare("INSERT INTO submission (form_data, review) VALUES (?, ?)");
    if (!$stmt) {
        $error = "Statement preparation failed: " . $conn->error;
    } elseif (!$stmt->bind_param("si", $form_data, $review)) {
        $error = "Binding parameters failed: " . $stmt->error;
    } elseif (!$stmt->execute()) {
        $error = "Execution failed: " . $stmt->error;
    } else {
        $insertId = $conn->insert_id;
    }

    if ($error)
        logError($error);

    $stmt?->close();
    closeDatabaseConnection($conn);

    return makeResponse(!$error, ['id' => $insertId], $error);
}

/**
 * Deletes a submission by ID
 */
function deleteSubmission(int $id): array
{
    $conn = openDatabaseConnection();
    $error = null;
    $success = false;
    $submission = getSubmissionById($id);
    if ($submission['status']) {
        $form_data = json_decode($submission['data']['form_data'], true);
        foreach ($form_data as $index => $field) {
            if ($field['type'] == 'file') {
                $path = $field['value']['path'];
                $realPath =__DIR__ . '/../' . $path;
                
                if (file_exists($realPath)) {
                    unlink($realPath);
                }

            }
        }
    }
    if (!$conn) {
        $error = "Database connection failed.";
        logError($error);
        return makeResponse(false, null, $error);
    }

    $stmt = $conn->prepare("DELETE FROM submission WHERE id = ?");
    if (!$stmt) {
        $error = "Delete prepare failed: " . $conn->error;
    } elseif (!$stmt->bind_param("i", $id)) {
        $error = "Binding failed: " . $stmt->error;
    } elseif (!$stmt->execute()) {
        $error = "Execution failed: " . $stmt->error;
    } else {
        $success = $stmt->affected_rows > 0;
    }

    if ($error)
        logError($error);

    $stmt?->close();
    closeDatabaseConnection($conn);

    return makeResponse($success, $success ? "Deleted" : "Not found", $error);
}

/**
 * Gets submissions with pagination
 */
function getSubmission(int $startPoint): array
{
    $conn = openDatabaseConnection();
    $error = null;
    $data = [];

    if (!$conn) {
        $error = "Database connection failed.";
        logError($error);
        return makeResponse(false, [], $error);
    }

    $stmt = $conn->prepare("SELECT * FROM submission LIMIT 20 OFFSET ?");
    if (!$stmt) {
        $error = "Statement prepare failed: " . $conn->error;
    } elseif (!$stmt->bind_param("i", $startPoint)) {
        $error = "Binding failed: " . $stmt->error;
    } elseif (!$stmt->execute()) {
        $error = "Execution failed: " . $stmt->error;
    } else {
        $result = $stmt->get_result();
        $data = $result->fetch_all(\MYSQLI_ASSOC);
    }

    if ($error)
        logError($error);

    $stmt?->close();
    closeDatabaseConnection($conn);

    return makeResponse(!$error, $data, $error);
}

/**
 * Returns total count of all submissions
 */
function getSubmissionsTotalCount(): array
{
    $conn = openDatabaseConnection();
    $error = null;
    $count = 0;

    if (!$conn) {
        $error = "Database connection failed.";
        logError($error);
        return makeResponse(false, 0, $error);
    }

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM submission");
    if (!$stmt) {
        $error = "Prepare failed: " . $conn->error;
    } elseif (!$stmt->execute()) {
        $error = "Execute failed: " . $stmt->error;
    } else {
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $count = (int) ($row['total'] ?? 0);
    }

    if ($error)
        logError($error);

    $stmt?->close();
    closeDatabaseConnection($conn);

    return makeResponse(!$error, $count, $error);
}

/**
 * Fetches a single submission by ID
 */
function getSubmissionById(int $id): array
{
    $conn = openDatabaseConnection();
    $error = null;
    $data = null;

    if (!$conn) {
        $error = "Database connection failed.";
        logError($error);
        return makeResponse(false, null, $error);
    }

    $stmt = $conn->prepare("SELECT * FROM submission WHERE id = ?");
    if (!$stmt) {
        $error = "Prepare failed: " . $conn->error;
    } elseif (!$stmt->bind_param("i", $id)) {
        $error = "Bind failed: " . $stmt->error;
    } elseif (!$stmt->execute()) {
        $error = "Execution failed: " . $stmt->error;
    } else {
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
    }

    if ($error)
        logError($error);

    $stmt?->close();
    closeDatabaseConnection($conn);

    return makeResponse(!$error, $data, $error);
}

/**
 * Sets review status = 1 for a submission
 */
function updateSubmissionReviewStatus(int $id): array
{
    $conn = openDatabaseConnection();
    $error = null;
    $updated = false;

    if (!$conn) {
        $error = "Database connection failed.";
        logError($error);
        return makeResponse(false, null, $error);
    }

    $stmt = $conn->prepare("UPDATE submission SET review = 1 WHERE id = ?");
    if (!$stmt) {
        $error = "Prepare failed: " . $conn->error;
    } elseif (!$stmt->bind_param("i", $id)) {
        $error = "Bind failed: " . $stmt->error;
    } elseif (!$stmt->execute()) {
        $error = "Execute failed: " . $stmt->error;
    } else {
        $updated = $stmt->affected_rows > 0;
    }

    if ($error)
        logError($error);

    $stmt?->close();
    closeDatabaseConnection($conn);

    return makeResponse($updated, $updated ? "Review status updated" : "No changes", $error);
}

/**
 * Count reviewed submissions
 */
function getSubmissionsReviewedTotalCount(): array
{
    $conn = openDatabaseConnection();
    $error = null;
    $count = 0;

    if (!$conn) {
        $error = "Database connection failed.";
        logError($error);
        return makeResponse(false, 0, $error);
    }

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM submission WHERE review = 1");
    if (!$stmt) {
        $error = "Prepare failed: " . $conn->error;
    } elseif (!$stmt->execute()) {
        $error = "Execute failed: " . $stmt->error;
    } else {
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $count = (int) ($row['total'] ?? 0);
    }

    if ($error)
        logError($error);

    $stmt?->close();
    closeDatabaseConnection($conn);

    return makeResponse(!$error, $count, $error);
}

/**
 * Get submissions pending review (paginated)
 */
function getSubmissionReviewPending(int $startPoint): array
{
    $conn = openDatabaseConnection();
    $error = null;
    $data = [];

    if (!$conn) {
        $error = "Database connection failed.";
        logError($error);
        return makeResponse(false, [], $error);
    }

    $stmt = $conn->prepare("SELECT * FROM submission WHERE review = 0 ORDER BY submitted_at DESC LIMIT 20 OFFSET ?");
    if (!$stmt) {
        $error = "Prepare failed: " . $conn->error;
    } elseif (!$stmt->bind_param("i", $startPoint)) {
        $error = "Bind failed: " . $stmt->error;
    } elseif (!$stmt->execute()) {
        $error = "Execute failed: " . $stmt->error;
    } else {
        $result = $stmt->get_result();
        $data = $result->fetch_all(\MYSQLI_ASSOC);
    }

    if ($error)
        logError($error);

    $stmt?->close();
    closeDatabaseConnection($conn);

    return makeResponse(!$error, $data, $error);
}

/**
 * Count submissions pending review
 */
function getSubmissionsReviewPendingTotalCount(): array
{
    $conn = openDatabaseConnection();
    $error = null;
    $count = 0;

    if (!$conn) {
        $error = "Database connection failed.";
        logError($error);
        return makeResponse(false, 0, $error);
    }

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM submission WHERE review = 0");
    if (!$stmt) {
        $error = "Prepare failed: " . $conn->error;
    } elseif (!$stmt->execute()) {
        $error = "Execute failed: " . $stmt->error;
    } else {
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $count = (int) ($row['total'] ?? 0);
    }

    if ($error)
        logError($error);

    $stmt?->close();
    closeDatabaseConnection($conn);

    return makeResponse(!$error, $count, $error);
}

/**
 * Get reviewed submissions (paginated)
 */
function getSubmissionReviewed(int $startPoint): array
{
    $conn = openDatabaseConnection();
    $error = null;
    $data = [];

    if (!$conn) {
        $error = "Database connection failed.";
        logError($error);
        return makeResponse(false, [], $error);
    }

    $stmt = $conn->prepare("SELECT * FROM submission WHERE review = 1 ORDER BY submitted_at DESC LIMIT 20 OFFSET ?");
    if (!$stmt) {
        $error = "Prepare failed: " . $conn->error;
    } elseif (!$stmt->bind_param("i", $startPoint)) {
        $error = "Bind failed: " . $stmt->error;
    } elseif (!$stmt->execute()) {
        $error = "Execute failed: " . $stmt->error;
    } else {
        $result = $stmt->get_result();
        $data = $result->fetch_all(\MYSQLI_ASSOC);
    }

    if ($error)
        logError($error);

    $stmt?->close();
    closeDatabaseConnection($conn);

    return makeResponse(!$error, $data, $error);
}
?>