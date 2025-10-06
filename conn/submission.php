<?php

namespace submission;

require_once("conn.php");
use function conn\closeDatabaseConnection;
use function conn\openDatabaseConnection;

function logError($message) {
    $logFile = __DIR__ . '/error.log';
    error_log("[" . date('Y-m-d H:i:s') . "] " . $message . "\n", 3, $logFile);
   
}

function insertSubmissionFromJson(array $jsonData) {
    
    
    $conn = openDatabaseConnection();
    $message = [];

    if (!$conn) {
        logError("DB connection failed during insert");
        return [
            "status" => 'fail',
            "message" => "No database connection"
        ];
    }

    // Extract expected fields
    $form_data = json_encode($jsonData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    
    $review = $jsonData['review'] ?? false;

    $sql = "INSERT INTO submission (form_data, review) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        logError("Statement preparation failed: " . $conn->error);
        return [
            "status" => 'fail',
            "message" => "Statement preparation failed"
        ];
    }

    $stmt->bind_param("si", $form_data, $review);
    if ($stmt->execute()) {
        $insert_id = $conn->insert_id;
        $message = [
            "status" => 'success',
            "message" => "Inserted",
            "id" => $insert_id
        ];
    } else {
        logError("Insert execution failed: " . $stmt->error);
        $message = [
            "status" => 'fail',
            "message" => "Execution failed"
        ];
    }

    $stmt->close();
    closeDatabaseConnection($conn);
    return $message;
}

function deleteSubmission($id) {
    $conn = openDatabaseConnection();
    $stmt = $conn->prepare("DELETE FROM submission WHERE id = ?");
    if (!$stmt) {
        logError("Delete prepare failed for ID $id");
        return "no";
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $message = ($stmt->affected_rows > 0) ? "yes" : "no";
    $stmt->close();
    closeDatabaseConnection($conn);
    return $message;
}

function getSubmission($startPoint) {
    $conn = openDatabaseConnection();
    $stmt = $conn->prepare("SELECT * FROM submission LIMIT 20 OFFSET ?");
    if (!$stmt) {
        logError("Get submissions failed on LIMIT/OFFSET");
        return [];
    }
    $stmt->bind_param("i", $startPoint);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(\MYSQLI_ASSOC);
    $stmt->close();
    closeDatabaseConnection($conn);
    return $data;
}

function getSubmissionsTotalCount() {
    $conn = openDatabaseConnection();
    $stmt = $conn->prepare("SELECT COUNT(*) FROM submission");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    closeDatabaseConnection($conn);
    return (int)$row["COUNT(*)"];
}

function getSubmissionById(int $id) {
    $conn = openDatabaseConnection();
    $stmt = $conn->prepare("SELECT * FROM submission WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
    closeDatabaseConnection($conn);
    return $data;
}

function updateSubmissionReviewStatus($id) {
    $conn = openDatabaseConnection();
    $stmt = $conn->prepare("UPDATE submission SET review = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    closeDatabaseConnection($conn);
    return true;
}

function getSubmissionsReviewedTotalCount() {
    $conn = openDatabaseConnection();
    $sql = "SELECT COUNT(*) as count FROM submission WHERE review = 1";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    closeDatabaseConnection($conn);
    return $row['count'] ?? 0;
}


function getSubmissionReviewPending($startPoint)
{
    $conn = openDatabaseConnection();

    $stmt = $conn->prepare(
        "SELECT * FROM submission
         WHERE review = 0
         ORDER BY submitted_at DESC
         LIMIT 20 OFFSET ?"
    );
    $stmt->bind_param("i", $startPoint);
    $stmt->execute();
    $result = $stmt->get_result();

    $submissions = [];
    while ($row = $result->fetch_assoc()) {
        $submissions[] = $row;
    }

    $stmt->close();
    closeDatabaseConnection($conn);
    return $submissions;
}
function getSubmissionsReviewPendingTotalCount()
{
    $conn = openDatabaseConnection();

    $sql = "SELECT COUNT(*) as total FROM submission WHERE review = 0";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    closeDatabaseConnection($conn);
    return (int)$row['total'];
}

function getSubmissionReviewed($startPoint)
{
    $conn = openDatabaseConnection();

    $stmt = $conn->prepare(
        "SELECT * FROM submission
         WHERE review = 1
         ORDER BY submitted_at DESC
         LIMIT 20 OFFSET ?"
    );
    $stmt->bind_param("i", $startPoint);
    $stmt->execute();
    $result = $stmt->get_result();

    $submissions = [];
    while ($row = $result->fetch_assoc()) {
        $submissions[] = $row;
    }

    $stmt->close();
    closeDatabaseConnection($conn);
    return $submissions;
}




