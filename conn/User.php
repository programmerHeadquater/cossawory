<?php
namespace User;

require_once "conn.php";

use function conn\openDatabaseConnection;
use function conn\closeDatabaseConnection;

/**
 * Get a user by ID
 */
function user_getById($id) {
    $conn = openDatabaseConnection();
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    closeDatabaseConnection($conn);
    return $user ?: null;
}

/**
 * Get a user by username
 */
function user_getByUsername($username) {
    $conn = openDatabaseConnection();
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    closeDatabaseConnection($conn);
    return $user ?: null;
}

/**
 * Validate user credentials
 */
function user_checkLogin($username, $password) {
    $user = user_getByUsername($username);
    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return null;
}

/**
 * Create a new user with permissions
 */
function user_createNewUser($username, $email, $password, $permissions = []) {
    $conn = openDatabaseConnection();
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Extract permission values into variables
    $can_view = $permissions['can_view'] ?? 0;
    $can_write_review = $permissions['can_write_review'] ?? 0;
    $can_delete_review = $permissions['can_delete_review'] ?? 0;
    $can_delete_query = $permissions['can_delete_query'] ?? 0;
    $can_add_user = $permissions['can_add_user'] ?? 0;
    $can_delete_user = $permissions['can_delete_user'] ?? 0;

    $stmt = $conn->prepare("
        INSERT INTO users (
            username, email, password,
            can_view, can_write_review, can_delete_review,
            can_delete_query, can_add_user, can_delete_user
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "sssiiiiii",
        $username,
        $email,
        $hashedPassword,
        $can_view,
        $can_write_review,
        $can_delete_review,
        $can_delete_query,
        $can_add_user,
        $can_delete_user
    );

    $result = $stmt->execute();
    $stmt->close();
    closeDatabaseConnection($conn);

    return $result;
}

/**
 * Delete user by ID
 */
function user_deleteById($id) {
    $conn = openDatabaseConnection();
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $result = $stmt->execute();
    $stmt->close();
    closeDatabaseConnection($conn);
    return $result;
}

/**
 * Check if user has a specific permission
 */
function user_hasPermission($userId, $permissionName) {
    $allowed = [
        'can_view',
        'can_write_review',
        'can_delete_review',
        'can_delete_query',
        'can_add_user',
        'can_delete_user'
    ];
    if (!in_array($permissionName, $allowed)) {
        throw new \InvalidArgumentException("Invalid permission: $permissionName");
    }

    $conn = openDatabaseConnection();
    $stmt = $conn->prepare("SELECT $permissionName FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    closeDatabaseConnection($conn);
    return isset($row[$permissionName]) && (bool)$row[$permissionName];
}

// Optional wrappers for common permission checks

function user_canView($userId) {
    return user_hasPermission($userId, 'can_view');
}

function user_canWriteReview($userId) {
    return user_hasPermission($userId, 'can_write_review');
}

function user_canDeleteReview($userId) {
    return user_hasPermission($userId, 'can_delete_review');
}

function user_canDeleteQuery($userId) {
    return user_hasPermission($userId, 'can_delete_query');
}

function user_canAddUser($userId) {
    return user_hasPermission($userId, 'can_add_user');
}

function user_canDeleteUser($userId) {
    return user_hasPermission($userId, 'can_delete_user');
}
