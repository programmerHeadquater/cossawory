<?php
// ✅ 1. Set a custom session name (avoid default "PHPSESSID")
session_name("MySecureAppSession");

// ✅ 2. Secure cookie parameters
$secure = isset($_SERVER['HTTPS']); // True if using HTTPS

session_set_cookie_params([
    'lifetime' => 0, // Session lasts until browser closes
    'path' => '/',
    //'domain' => $_SERVER['HTTP_HOST'],
    'domain' => '',
    //'secure' => $secure,         //  Cookie only sent over HTTPS
    'secure' => false,         //  Cookie only sent over HTTPS
    'httponly' => true,          //  JavaScript can't access cookie
    'samesite' => 'Strict',      // Helps prevent CSRF
]);

// ✅ 3. Start session AFTER setting cookie params
session_start();

// ✅ 4. Prevent Session Hijacking by checking fingerprint
if (!isset($_SESSION['fingerprint'])) {
    // First time login/session start
    $_SESSION['fingerprint'] = hash('sha256', $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);
    $_SESSION['LAST_ACTIVITY'] = time(); // track user activity
} else {
    // Check if fingerprint matches
    $currentFingerprint = hash('sha256', $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);
    if ($_SESSION['fingerprint'] !== $currentFingerprint) {
        session_unset();
        session_destroy();
        die("Session hijacking detected.");
    }

    // ✅ 5. Auto-logout after inactivity (e.g. 15 minutes)
    if (time() - $_SESSION['LAST_ACTIVITY'] > 900) {
        session_unset();
        session_destroy();
        die("Session timed out.");
    }

    $_SESSION['LAST_ACTIVITY'] = time(); // Update activity time
}
?>
