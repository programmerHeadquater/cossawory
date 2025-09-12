<?php
// Start the session to access session data
session_start();

// Destroy all session variables
session_unset(); // Unsets all session variables

// Destroy the session itself
session_destroy();

// Redirect to the login page
header(header: "Location: ../dashboard.php");
exit(); // Ensure no further code is executed
?>
