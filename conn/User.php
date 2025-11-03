<?php
namespace User;

require_once "conn.php";

use function conn\openDatabaseConnection;
use function conn\closeDatabaseConnection;

/**
 * Log error messages to file
 */
function logError(string $message): void
{
    $logFile = __DIR__ . '/error.log';
    $timestamp = date('Y-m-d H:i:s');
    error_log("[$timestamp] $message\n", 3, $logFile);
}

/**
 * Create a unified response structure
 */
function makeResponse(bool $status, $data = null, ?string $error = null): array
{
    return [
        'status' => $status,
        'data' => $data,
        'error' => $error
    ];
}

/**
 * Get a user by ID
 */
function user_getById(int $id): array
{
    $conn = openDatabaseConnection();
    $error = null;
    $data = null;

    if (!$conn) {
        $error = "Database connection failed.";
        logError($error);
        return makeResponse(false, null, $error);
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    if (!$stmt) {
        $error = "Prepare failed: " . $conn->error;
    } elseif (!$stmt->bind_param("i", $id)) {
        $error = "Bind failed: " . $stmt->error;
    } elseif (!$stmt->execute()) {
        $error = "Execute failed: " . $stmt->error;
    } else {
        $result = $stmt->get_result();
        $data = $result->fetch_assoc() ?: null;
    }

    if ($error)
        logError($error);
    $stmt?->close();
    closeDatabaseConnection($conn);

    return makeResponse(!$error, $data, $error);
}

/**
 * Get a user by email
 */
function user_getByEmail(string $email): array
{
    $conn = openDatabaseConnection();
    $error = null;
    $data = null;

    if (!$conn) {
        $error = "Database connection failed.";
        logError($error);
        return makeResponse(false, null, $error);
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    if (!$stmt) {
        $error = "Prepare failed: " . $conn->error;
    } elseif (!$stmt->bind_param("s", $email)) {
        $error = "Bind failed: " . $stmt->error;
    } elseif (!$stmt->execute()) {
        $error = "Execute failed: " . $stmt->error;
    } else {
        $result = $stmt->get_result();
        $data = $result->fetch_assoc() ?: null;
    }

    if ($error)
        logError($error);
    $stmt?->close();
    closeDatabaseConnection($conn);

    return makeResponse(!$error, $data, $error);
}

/**
 * Validate user credentials
 */
function user_checkLogin(string $email, string $password): array
{
    $userResponse = user_getByEmail($email);

    if (!$userResponse['status']) {
        return $userResponse; // Already includes error info
    }

    $user = $userResponse['data'];
    if (!$user || !password_verify($password, $user['password'])) {
        return makeResponse(true, null, null); // Valid query, but credentials fail
    }

    return makeResponse(true, $user, null);
}

/**
 * Create a new user with permissions
 */
function user_addNewUser(array $formData): array
{
    $conn = openDatabaseConnection();
    $error = null;

    if (!$conn) {
        $error = "Database connection failed.";
        logError($error);
        return makeResponse(false, null, $error);
    }

    $username = $formData['username'] ?? null;
    $password = password_hash($formData['password'] ?? '', PASSWORD_DEFAULT);
    $email = $formData['email'] ?? null;

    $can_view = 1;
    $can_write_review = isset($formData['can_write_review']) && strtolower($formData['can_write_review']) === 'yes' ? 1 : 0;
    $can_delete_review = isset($formData['can_delete_review']) && strtolower($formData['can_delete_review']) === 'yes' ? 1 : 0;
    $can_delete_submission = isset($formData['can_delete_submission']) && strtolower($formData['can_delete_submission']) === 'yes' ? 1 : 0;
    $can_add_user = isset($formData['can_add_user']) && strtolower($formData['can_add_user']) === 'yes' ? 1 : 0;
    $can_delete_user = isset($formData['can_delete_user']) && strtolower($formData['can_delete_user']) === 'yes' ? 1 : 0;

    $stmt = $conn->prepare("
        INSERT INTO users (
            username, email, password,
            view, can_write_review, can_delete_review,
            can_delete_submission, can_add_user, can_delete_user
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        $error = "Prepare failed: " . $conn->error;
    } elseif (
        !$stmt->bind_param(
            "sssiiiiii",
            $username,
            $email,
            $password,
            $can_view,
            $can_write_review,
            $can_delete_review,
            $can_delete_submission,
            $can_add_user,
            $can_delete_user
        )
    ) {
        $error = "Bind failed: " . $stmt->error;
    } elseif (!$stmt->execute()) {
        $error = "Execute failed: " . $stmt->error;
    }

    if ($error)
        logError($error);

    $stmt?->close();
    closeDatabaseConnection($conn);

    return makeResponse(!$error, $error ? null : "User created successfully", $error);
}

/**
 * Delete a user by ID
 */
function user_deleteById(int $id): array
{
    $conn = openDatabaseConnection();
    $error = null;
    $deleted = false;

    if (!$conn) {
        $error = "Database connection failed.";
        logError($error);
        return makeResponse(false, null, $error);
    }

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    if (!$stmt) {
        $error = "Prepare failed: " . $conn->error;
    } elseif (!$stmt->bind_param("i", $id)) {
        $error = "Bind failed: " . $stmt->error;
    } elseif (!$stmt->execute()) {
        $error = "Execute failed: " . $stmt->error;
    } else {
        $deleted = $stmt->affected_rows > 0;
    }

    if ($error)
        logError($error);

    $stmt?->close();
    closeDatabaseConnection($conn);

    return makeResponse($deleted, $deleted ? "User deleted" : "User not found", $error);
}

/**
 * Check if user has a specific permission
 */
function user_hasPermission(int $userId, string $permissionName): array
{
    $allowed = [
        'can_view',
        'can_write_review',
        'can_delete_review',
        'can_delete_submission',
        'can_add_user',
        'can_delete_user'
    ];

    if (!in_array($permissionName, $allowed)) {
        $error = "Invalid permission name: $permissionName";
        logError($error);
        return makeResponse(false, false, $error);
    }

    $conn = openDatabaseConnection();
    $error = null;
    $hasPermission = false;

    if (!$conn) {
        $error = "Database connection failed.";
        logError($error);
        return makeResponse(false, false, $error);
    }

    $stmt = $conn->prepare("SELECT $permissionName FROM users WHERE id = ?");
    if (!$stmt) {
        $error = "Prepare failed: " . $conn->error;
    } elseif (!$stmt->bind_param("i", $userId)) {
        $error = "Bind failed: " . $stmt->error;
    } elseif (!$stmt->execute()) {
        $error = "Execute failed: " . $stmt->error;
    } else {
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $hasPermission = isset($row[$permissionName]) && (bool) $row[$permissionName];
    }

    if ($error)
        logError($error);
    $stmt?->close();
    closeDatabaseConnection($conn);

    return makeResponse(!$error, $hasPermission, $error);
}

/**
 * Get all users (paginated)
 */
function user_getUsers(int $startPoint): array
{
    $conn = openDatabaseConnection();
    $error = null;
    $data = [];

    if (!$conn) {
        $error = "Database connection failed.";
        logError($error);
        return makeResponse(false, [], $error);
    }

    $stmt = $conn->prepare("SELECT * FROM users LIMIT 20 OFFSET ?");
    if (!$stmt) {
        $error = "Prepare failed: " . $conn->error;
    } elseif (!$stmt->bind_param("i", $startPoint)) {
        $error = "Bind failed: " . $stmt->error;
    } elseif (!$stmt->execute()) {
        $error = "Execute failed: " . $stmt->error;
    } else {
        $result = $stmt->get_result();
        $data = $result->fetch_all(\MYSQLI_ASSOC);
    }

    if ($error)
        logError($error);
    $stmt?->close();
    closeDatabaseConnection($conn);

    return makeResponse(!$error, $data, $error);
}

/**
 * Get total user count
 */
function user_getTotalUser(): array
{
    $conn = openDatabaseConnection();
    $error = null;
    $count = 0;

    if (!$conn) {
        $error = "Database connection failed.";
        logError($error);
        return makeResponse(false, 0, $error);
    }

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM users");
    if (!$stmt) {
        $error = "Prepare failed: " . $conn->error;
    } elseif (!$stmt->execute()) {
        $error = "Execute failed: " . $stmt->error;
    } else {
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $count = (int) ($row['total'] ?? 0);
    }

    if ($error)
        logError($error);
    $stmt?->close();
    closeDatabaseConnection($conn);

    return makeResponse(!$error, $count, $error);
}

/**
 * Update user permissions
 */
function user_updatePermission(
    int $id,
    int $can_write_review,
    int $can_delete_review,
    int $can_delete_submission,
    int $can_add_user,
    int $can_delete_user
): array {
    $conn = openDatabaseConnection();
    $error = null;
    $updated = false;

    if (!$conn) {
        $error = "Database connection failed.";
        logError($error);
        return makeResponse(false, null, $error);
    }

    $stmt = $conn->prepare('
        UPDATE users SET 
            can_write_review = ?,
            can_delete_review = ?,
            can_delete_submission = ?,
            can_add_user = ?,
            can_delete_user = ?
        WHERE id = ?
    ');

    if (!$stmt) {
        $error = "Prepare failed: " . $conn->error;
    } elseif (
        !$stmt->bind_param(
            "iiiiii",
            $can_write_review,
            $can_delete_review,
            $can_delete_submission,
            $can_add_user,
            $can_delete_user,
            $id
        )
    ) {
        $error = "Bind failed: " . $stmt->error;
    } elseif (!$stmt->execute()) {
        $error = "Execute failed: " . $stmt->error;
    } else {
        $updated = $stmt->affected_rows > 0;
    }

    if ($error)
        logError($error);
    $stmt?->close();
    closeDatabaseConnection($conn);

    return makeResponse($updated, $updated ? "Permissions updated" : "No changes", $error);
}
?>