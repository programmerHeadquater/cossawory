<?php
namespace User;

require_once "conn.php";

use function conn\openDatabaseConnection;
use function conn\closeDatabaseConnection;

/**
 * Helper: Standard response array
 */
function makeResponse($status, $data = null, $error = null)
{
    return ['status' => $status, 'data' => $data, 'error' => $error];
}

/**
 * Get a user by ID
 */
function user_getById($id)
{
    $conn = openDatabaseConnection();
    if ($conn === null) {
        return makeResponse(false, null, 'Database connection failed');
    }

    $stmt = $conn->prepare("SELECT * FROM user WHERE id = ?");
    if (!$stmt) {
        $error = "Prepare failed: " . $conn->error;
        closeDatabaseConnection($conn);
        return makeResponse(false, null, $error);
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if(!$user){
        return makeResponse(false, $user ?: null,"No data found");
    }
    $stmt->close();
    closeDatabaseConnection($conn);

    return makeResponse(true, $user ?: null);
}

/**
 * Get a user by email
 */
function user_getByEmail($email)
{
    $conn = openDatabaseConnection();
    if ($conn === null) {
        return makeResponse(false, null, 'Database connection failed');
    }

    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
    if (!$stmt) {
        $error = "Prepare failed: " . $conn->error;
        closeDatabaseConnection($conn);
        return makeResponse(false, null, $error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
if(!$user){
        return makeResponse(false, $user ?: null,"No data found");
    }
    $stmt->close();
    closeDatabaseConnection($conn);

    return makeResponse(true, $user ?: null);
}

/**
 * Validate user credentials
 */
function user_checkLogin($email, $password)
{
    $emailResp = user_getByEmail($email);
    if (!$emailResp['status']) {
        return $emailResp;
    }

    $user = $emailResp['data'];
    if (!$user) {
        return makeResponse(false, null, 'User not found');
    }
    
    if (password_verify($password, $user['password'])) {
        return makeResponse(true, $user);
    }

    return makeResponse(false, null, 'Invalid credentials');
}

/**
 * Create a new user with permissions
 */
function user_addNewUser($formData)
{
    $conn = openDatabaseConnection();
    if ($conn === null) {
        return makeResponse(false, null, 'Database connection failed');
    }

    $username = $formData['username'] ?? null;
    $email = $formData['email'] ?? null;
    $password = isset($formData['password']) ? password_hash($formData['password'], PASSWORD_DEFAULT) : null;

    if (!$username || !$email || !$password) {
        closeDatabaseConnection($conn);
        return makeResponse(false, null, 'Missing required fields');
    }


    $can_write_review = isset($formData['can_write_review']) && strtolower($formData['can_write_review']) === 'yes' ? 1 : 0;
    $can_delete_review = isset($formData['can_delete_review']) && strtolower($formData['can_delete_review']) === 'yes' ? 1 : 0;
    $can_delete_submission = isset($formData['can_delete_submission']) && strtolower($formData['can_delete_submission']) === 'yes' ? 1 : 0;
    $can_add_user = isset($formData['can_add_user']) && strtolower($formData['can_add_user']) === 'yes' ? 1 : 0;
    $can_delete_user = isset($formData['can_delete_user']) && strtolower($formData['can_delete_user']) === 'yes' ? 1 : 0;

    $stmt = $conn->prepare("
        INSERT INTO user (
            username, email, password,
            can_write_review, can_delete_review,
            can_delete_submission, can_add_user, can_delete_user
        ) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    if (!$stmt) {
        $error = "Prepare failed: " . $conn->error;
        closeDatabaseConnection($conn);
        return makeResponse(false, null, $error);
    }

    $stmt->bind_param(
        "sssiiiii",
        $username,
        $email,
        $password,
        $can_write_review,
        $can_delete_review,
        $can_delete_submission,
        $can_add_user,
        $can_delete_user
    );

    $result = $stmt->execute();
    $insertId = $stmt->insert_id ?? null;

    $stmt->close();
    closeDatabaseConnection($conn);

    return makeResponse($result, ['id' => $insertId], $result ? null : 'Insert failed');
}

/**  * Delete user by ID  */
function user_deleteById($id)
{
    $conn = openDatabaseConnection();
    if ($conn === null) {
        return makeResponse(false, null, 'Database connection failed');
    }

    $stmt = $conn->prepare("DELETE FROM user WHERE id = ?");
    if (!$stmt) {
        $error = "Prepare failed: " . $conn->error;
        closeDatabaseConnection($conn);
        return makeResponse(false, null, $error);
    }

    $stmt->bind_param("i", $id);
    $result = $stmt->execute();

    $stmt->close();
    closeDatabaseConnection($conn);

    return makeResponse($result, $result, $result ? null : 'Delete failed');
}

/**  * Check if user has a specific permission  */
function user_hasPermission($userId, $permissionName)
{
    $allowed = [

        'can_write_review',
        'can_delete_review',
        'can_delete_submission',
        'can_add_user',
        'can_delete_user'
    ];
    if (!in_array($permissionName, $allowed)) {
        return makeResponse(false, null, "Invalid permission: $permissionName");
    }

    $conn = openDatabaseConnection();
    if ($conn === null) {
        return makeResponse(false, null, 'Database connection failed');
    }

    $stmt = $conn->prepare("SELECT $permissionName FROM user WHERE id = ?");
    if (!$stmt) {
        $error = "Prepare failed: " . $conn->error;
        closeDatabaseConnection($conn);
        return makeResponse(false, null, $error);
    }

    $stmt->bind_param("i", $userId);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $hasPermission = isset($row[$permissionName]) && (bool) $row[$permissionName] ;
        
        return makeResponse(true, $hasPermission); // success = query worked, data = permission
    } else {
        return makeResponse(false, null, 'Failed to fetch permission');
    }
}


function user_canWriteReview($userId): array
{
    return user_hasPermission($userId, 'can_write_review');
}

function user_canDeleteReview($userId): array
{
    return user_hasPermission($userId, 'can_delete_review');
}

function user_canDeleteSubmission($userId): array
{
    return user_hasPermission($userId, 'can_delete_submission');
}

function user_canAddUser($userId): array
{
    return user_hasPermission($userId, 'can_add_user');
}

function user_canDeleteUser($userId): array
{
    return user_hasPermission($userId, 'can_delete_user');
}

/**  * Get list of users (pagination)  */
function user_getUsers($startPoint)
{
    $conn = openDatabaseConnection();
    if ($conn === null) {
        return makeResponse(false, [], 'Database connection failed');
    }

    $stmt = $conn->prepare("SELECT * FROM user LIMIT 20 OFFSET ?");
    if (!$stmt) {
        $error = "Prepare failed: " . $conn->error;
        closeDatabaseConnection($conn);
        return makeResponse(false, [], $error);
    }

    $stmt->bind_param("i", $startPoint);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(\MYSQLI_ASSOC);

    $stmt->close();
    closeDatabaseConnection($conn);

    return makeResponse(true, $data);
}

/**  * Get total user count  */
function user_getTotalUser()
{
    $conn = openDatabaseConnection();
    if ($conn === null) {
        return makeResponse(false, 0, 'Database connection failed');
    }

    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM user");
    if (!$stmt) {
        $error = "Prepare failed: " . $conn->error;
        closeDatabaseConnection($conn);
        return makeResponse(false, 0, $error);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $count = $row['total'] ?? 0;

    $stmt->close();
    closeDatabaseConnection($conn);

    return makeResponse(true, $count);
}

/**  * Update user permissions  */
function user_updatePermission($id, $can_write_review, $can_delete_review, $can_delete_submission, $can_add_user, $can_delete_user)
{
    $conn = openDatabaseConnection();
    if ($conn === null) {
        return makeResponse(false, null, 'Database connection failed');
    }

    $stmt = $conn->prepare('UPDATE user SET 
        can_write_review = ?,
        can_delete_review = ?,
        can_delete_submission = ?,
        can_add_user = ?,
        can_delete_user = ?
        WHERE id = ?'
    );

    if (!$stmt) {
        $error = "Prepare failed: " . $conn->error;
        closeDatabaseConnection($conn);
        return makeResponse(false, null, $error);
    }

    $stmt->bind_param(
        "iiiiii",
        $can_write_review,
        $can_delete_review,
        $can_delete_submission,
        $can_add_user,
        $can_delete_user,
        $id
    );

    $result = $stmt->execute();
    $stmt->close();
    closeDatabaseConnection($conn);

    return makeResponse($result, $result, $result ? null : 'Update failed');
}
?>