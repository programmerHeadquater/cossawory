<?php
session_start();
require_once '../conn/conn.php'; // Make sure this file returns a valid DB connection

use function conn\openDatabaseConnection;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Validate input
    if (empty($username) || empty($password)) {
        header('Location: dashboard.php?error=Please+fill+in+all+fields');
        exit();
    }

    $conn = openDatabaseConnection();

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            // Success - login the user
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            header('Location: /dashboard.php');
            exit();
        } else {
            // Password does not match
            header('Location: /dashboard.php?error=Incorrect+password');
            exit();
        }
    } else {
        // User not found
        header('Location: /dashboard.php?error=User+not+found');
        exit();
    }
} else {
    // Invalid request
    header('Location: dashboard.php');
    exit();
}
