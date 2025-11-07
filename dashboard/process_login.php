<?php
require_once "../conn/secure_session.php";
require_once "../conn/User.php";
use function user\user_checkLogin;

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /dashboard.php');
    exit();
}

// Get user input
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validate input
if ($email === '' || $password === '') {
    header('Location: /dashboard.php?error=' . urlencode('Please fill in all fields.'));
    exit();
}

// Check login using the function
$response = user_checkLogin($email, $password);

if ($response['data'] && $response['status']) {
    $user = $response['data'];
    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['username'] = $user['username'];

    header('Location: /dashboard.php');
    exit();
} else {
    $errorMsg = $response['error'] ?? 'Invalid username or password.';
    header('Location: /dashboard.php?error=' . urlencode($errorMsg));
    exit();
}
