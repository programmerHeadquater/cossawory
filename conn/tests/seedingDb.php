<?php
namespace seed;

require_once __DIR__ . '/../conn.php';
use function conn\openDatabaseConnection;
use function conn\closeDatabaseConnection;

/**
 * Seed the database with sample data for testing
 *
 * @return bool
 */
function seedDatabase(): bool
{
    $conn = openDatabaseConnection();
    if (!$conn) {
        return false;
    }

    // Disable FK checks to reset tables safely
    $conn->query("SET FOREIGN_KEY_CHECKS = 0");
    $conn->query("TRUNCATE TABLE review");
    $conn->query("TRUNCATE TABLE submission");
    $conn->query("TRUNCATE TABLE user");
    $conn->query("SET FOREIGN_KEY_CHECKS = 1");

    // --- 1. Seed users ---
    $users = [
        ['admin', 'admin@example.com', 'hashed_admin_pass', 1, 1, 1, 1, 1],
        ['reviewer', 'reviewer@example.com', 'hashed_reviewer_pass', 1, 0, 0, 0, 0],
        ['guest', 'guest@example.com', 'hashed_guest_pass', 0, 0, 0, 0, 0],
    ];

    $stmt = $conn->prepare("
        INSERT INTO user 
        (username, email, password, can_write_review, can_delete_review, can_delete_submission, can_add_user, can_delete_user) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    foreach ($users as $user) {
        $stmt->bind_param('sssiiiii', ...$user);
        $stmt->execute();
    }
    $stmt->close();

    // --- 2. Seed submissions ---
    $submissions = [
        ['{"field1":"value1","field2":"value2"}', 0],
        ['{"field1":"value3","field2":"value4"}', 1],
        ['{"field1":"value5","field2":"value6"}', 0],
    ];

    $stmt = $conn->prepare("INSERT INTO submission (form_data, review) VALUES (?, ?)");
    foreach ($submissions as $sub) {
        $stmt->bind_param('si', $sub[0], $sub[1]);
        $stmt->execute();
    }
    $stmt->close();

    // --- 3. Seed reviews ---
    $reviews = [
        [1, 1, 'Admin reviewed submission 1'],
        [2, 2, 'Reviewer reviewed submission 2'],
        [3, 1, 'Admin gave feedback on submission 3'],
    ];

    $stmt = $conn->prepare("INSERT INTO review (submission_id, user_id, review) VALUES (?, ?, ?)");
    foreach ($reviews as $rev) {
        $stmt->bind_param('iis', $rev[0], $rev[1], $rev[2]);
        $stmt->execute();
    }
    $stmt->close();

    closeDatabaseConnection($conn);
    return true;
}

/**
 * Clear all tables after tests
 *
 * @return bool
 */
function clearDatabase(): bool
{
    $conn = openDatabaseConnection();
    if (!$conn) {
        return false;
    }

    $conn->query("SET FOREIGN_KEY_CHECKS = 0");
    $conn->query("TRUNCATE TABLE review");
    $conn->query("TRUNCATE TABLE submission");
    $conn->query("TRUNCATE TABLE user");
    $conn->query("SET FOREIGN_KEY_CHECKS = 1");

    closeDatabaseConnection($conn);
    return true;
}
