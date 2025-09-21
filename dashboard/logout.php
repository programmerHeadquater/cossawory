<?php
// Change session name before starting session
session_name("MySecureAppSession");
session_start();

// Clear all session variables
$_SESSION = [];

// Delete session cookie (important!)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000, // time in past to delete
        $params["path"], 
        $params["domain"], 
        $params["secure"], 
        $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to login/dashboard
header("Location: ../dashboard.php");
exit();
?>
