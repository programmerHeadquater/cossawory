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
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Validate input
if ($username === '' || $password === '') {
    header('Location: /dashboard.php?error=' . urlencode('Please fill in all fields.'));
    exit();
}

// Check login using the function
$user = user_checkLogin($username, $password);

if ($user) {
    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];

    header('Location: /dashboard.php');
    exit();
} else {
    header('Location: /dashboard.php?error=' . urlencode('Invalid username or password.'));
    exit();
}
