<?php
namespace seed;

require_once __DIR__ . '/../conn.php';
require_once __DIR__ . '/../utils.php';

use function conn\openDatabaseConnection;
use function conn\closeDatabaseConnection;
use function utils\logError;




function seedUsers() {
    $conn = openDatabaseConnection();
    if (!$conn) {
        logError("Database connection failed.");
        return false;
    }

    $users = [
        ['username'=>'admin', 'email'=>'admin@example.com', 'password'=>password_hash('admin123', PASSWORD_DEFAULT), 'view'=>1, 'can_write_review'=>1, 'can_delete_review'=>1, 'can_delete_submission'=>1, 'can_add_user'=>1, 'can_delete_user'=>1],
        ['username'=>'reviewer', 'email'=>'reviewer@example.com', 'password'=>password_hash('reviewer123', PASSWORD_DEFAULT), 'view'=>1, 'can_write_review'=>1],
        ['username'=>'viewer', 'email'=>'viewer@example.com', 'password'=>password_hash('viewer123', PASSWORD_DEFAULT), 'view'=>1],
        ['username'=>'disabled', 'email'=>'disabled@example.com', 'password'=>password_hash('disabled123', PASSWORD_DEFAULT), 'view'=>0]
    ];

    $stmt = $conn->prepare("INSERT INTO users (username,email,password,view,can_write_review,can_delete_review,can_delete_submission,can_add_user,can_delete_user) VALUES (?,?,?,?,?,?,?,?,?)");
    if (!$stmt) {
        logError("Prepare failed: " . $conn->error);
        closeDatabaseConnection($conn);
        return false;
    }

    foreach ($users as $user) {
        // Assign variables explicitly for bind_param
        $username = $user['username'];
        $email = $user['email'];
        $password = $user['password'];
        $view = $user['view'] ?? 0;
        $can_write_review = $user['can_write_review'] ?? 0;
        $can_delete_review = $user['can_delete_review'] ?? 0;
        $can_delete_submission = $user['can_delete_submission'] ?? 0;
        $can_add_user = $user['can_add_user'] ?? 0;
        $can_delete_user = $user['can_delete_user'] ?? 0;

        if (!$stmt->bind_param(
            'sssiiiiii',
            $username,
            $email,
            $password,
            $view,
            $can_write_review,
            $can_delete_review,
            $can_delete_submission,
            $can_add_user,
            $can_delete_user
        )) {
            logError("Bind failed for user {$username}: " . $stmt->error);
            continue;
        }

        if (!$stmt->execute()) {
            logError("Failed to insert user {$username}: " . $stmt->error);
        }
    }

    $stmt->close();
    closeDatabaseConnection($conn);
    return true;
}
?>
