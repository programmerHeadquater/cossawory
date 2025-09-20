<?php
namespace User;

require_once "conn/conn.php";

use function conn\openDatabaseConnection;
use function conn\closeDatabaseConnection;

function checkUser($username, $password)
{
    $conn = openDatabaseConnection();
    if (!$conn) {
        die("Database connection failed.");
    }

    $stmt = $conn->prepare("SELECT * FROM user WHERE username = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    $isValid = false;

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (\password_verify($password, $user['password'])) {
            $isValid = true;
        }

    }

    $stmt->close();
    closeDatabaseConnection($conn);

    return $isValid;
}
function createUser($username, $password)
{
    return null;
}
