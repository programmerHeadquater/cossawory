<?php
namespace User;

require_once "conn.php";

use function conn\openDatabaseConnection;
use function conn\closeDatabaseConnection;

/**
 * Get a user by ID
 */
function user_getById($id)
{
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
function user_getByEmail($email)
{
    $conn = openDatabaseConnection();
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
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
function user_checkLogin($email, $password)
{
    $user = user_getByEmail($email);
    
    if($user === null) {
        return null;
    }
    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return null;
}

/**
 * Create a new user with permissions
 */
function user_addNewUser($formData)
{


    $conn = openDatabaseConnection();
    $username = $formData['username'];
    $password = password_hash($formData['password'], PASSWORD_DEFAULT);
    $email = $formData['email'];

    // Convert Yes/No values to 1/0
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

    $stmt->bind_param(
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
    );

    $result = $stmt->execute();
    $stmt->close();
    closeDatabaseConnection($conn);

    return $result;
}

/**
 * Delete user by ID
 */
function user_deleteById($id)
{
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
function user_hasPermission($userId, $permissionName)
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
        // throw new \InvalidArgumentException("Invalid permission: $permissionName");
        return false;
    }

    $conn = openDatabaseConnection();
    $stmt = $conn->prepare("SELECT $permissionName FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    closeDatabaseConnection($conn);
    return isset($row[$permissionName]) && (bool) $row[$permissionName];
   
}

// Optional wrappers for common permission checks

function user_canView($userId)
{
    return user_hasPermission($userId, 'can_view');
}

function user_canWriteReview($userId)
{
    return user_hasPermission($userId, 'can_write_review');
}

function user_canDeleteReview($userId)
{
    return user_hasPermission($userId, 'can_delete_review');
}

function user_canDeleteSubmission($userId)
{
    return user_hasPermission($userId, 'can_delete_submission');
}

function user_canAddUser($userId)
{
    return user_hasPermission($userId, 'can_add_user');
}

function user_canDeleteUser($userId)
{
    return user_hasPermission($userId, 'can_delete_user');
}

function user_getUsers($startPoint)
{
    $conn = openDatabaseConnection();
    $stmt = $conn->prepare("SELECT * FROM users LIMIT 20 OFFSET ?");
    $stmt->bind_param("i", $startPoint);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(\MYSQLI_ASSOC);
    return $data;
}
function user_getTotaluser()
{
    $conn = openDatabaseConnection();
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return (int) $row["COUNT(*)"];
}

function user_updatePermision($id, $can_write_review, $can_delete_review, $can_delete_submission, $can_add_user, $can_delete_user )
{
    $conn = openDatabaseConnection();
    $stmt = $conn->prepare('    UPDATE users SET 
            can_write_review = ?,
            can_delete_review = ?,
            can_delete_submission = ?,
            can_add_user = ?,
            can_delete_user = ?
        WHERE id = ?
    ');
    if (!$stmt) {
        closeDatabaseConnection($conn);
        return false;
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
    $stmt->execute();
    $stmt->close();
    closeDatabaseConnection($conn);

    return true;
}


















// <?php
// namespace User;

// require_once "conn.php";

// use function conn\openDatabaseConnection;
// use function conn\closeDatabaseConnection;

// /**
//  * Get a user by ID
//  */
// function user_getById($id)
// {
//     try {
//         $conn = openDatabaseConnection();
//         $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
//         if (!$stmt) {
//             throw new \Exception("Prepare failed: " . $conn->error);
//         }
//         $stmt->bind_param("i", $id);
//         $stmt->execute();
//         $result = $stmt->get_result();
//         $user = $result->fetch_assoc();
//         $stmt->close();
//         closeDatabaseConnection($conn);
//         return ['status' => true, 'data' => $user ?: null];
//     } catch (\Exception $e) {
//         error_log("user_getById error: " . $e->getMessage());
//         return ['status' => false, 'error' => $e->getMessage()];
//     }
// }

// /**
//  * Get a user by email
//  */
// function user_getByEmail($email)
// {
//     try {
//         $conn = openDatabaseConnection();
//         $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
//         if (!$stmt) {
//             throw new \Exception("Prepare failed: " . $conn->error);
//         }
//         $stmt->bind_param("s", $email);
//         $stmt->execute();
//         $result = $stmt->get_result();
//         $user = $result->fetch_assoc();
//         $stmt->close();
//         closeDatabaseConnection($conn);
//         return ['status' => true, 'data' => $user ?: null];
//     } catch (\Exception $e) {
//         error_log("user_getByEmail error: " . $e->getMessage());
//         return ['status' => false, 'error' => $e->getMessage()];
//     }
// }

// /**
//  * Validate user credentials
//  */
// function user_checkLogin($email, $password)
// {
//     try {
//         $userResponse = user_getByEmail($email);
//         if (!$userResponse['status']) {
//             return ['status' => false, 'error' => $userResponse['error']];
//         }
//         $user = $userResponse['data'];
//         if ($user === null) {
//             return ['status' => true, 'data' => null];
//         }
//         if (password_verify($password, $user['password'])) {
//             return ['status' => true, 'data' => $user];
//         }
//         return ['status' => true, 'data' => null];
//     } catch (\Exception $e) {
//         error_log("user_checkLogin error: " . $e->getMessage());
//         return ['status' => false, 'error' => $e->getMessage()];
//     }
// }

// /**
//  * Create a new user with permissions
//  */
// function user_addNewUser($formData)
// {
//     try {
//         $conn = openDatabaseConnection();
//         $username = $formData['username'];
//         $password = password_hash($formData['password'], PASSWORD_DEFAULT);
//         $email = $formData['email'];

//         // Convert Yes/No values to 1/0
//         $can_view = 1;
//         $can_write_review = isset($formData['can_write_review']) && strtolower($formData['can_write_review']) === 'yes' ? 1 : 0;
//         $can_delete_review = isset($formData['can_delete_review']) && strtolower($formData['can_delete_review']) === 'yes' ? 1 : 0;
//         $can_delete_submission = isset($formData['can_delete_submission']) && strtolower($formData['can_delete_submission']) === 'yes' ? 1 : 0;
//         $can_add_user = isset($formData['can_add_user']) && strtolower($formData['can_add_user']) === 'yes' ? 1 : 0;
//         $can_delete_user = isset($formData['can_delete_user']) && strtolower($formData['can_delete_user']) === 'yes' ? 1 : 0;

//         $stmt = $conn->prepare("
//             INSERT INTO users (
//                 username, email, password,
//                 view, can_write_review, can_delete_review,
//                 can_delete_submission, can_add_user, can_delete_user
//             ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
//         ");
//         if (!$stmt) {
//             throw new \Exception("Prepare failed: " . $conn->error);
//         }
//         $stmt->bind_param(
//             "sssiiiiii",
//             $username,
//             $email,
//             $password,
//             $can_view,
//             $can_write_review,
//             $can_delete_review,
//             $can_delete_submission,
//             $can_add_user,
//             $can_delete_user
//         );

//         $result = $stmt->execute();
//         if (!$result) {
//             throw new \Exception("Execute failed: " . $stmt->error);
//         }

//         $stmt->close();
//         closeDatabaseConnection($conn);

//         return ['status' => true, 'data' => true];
//     } catch (\Exception $e) {
//         error_log("user_addNewUser error: " . $e->getMessage());
//         return ['status' => false, 'error' => $e->getMessage()];
//     }
// }

// /**
//  * Delete user by ID
//  */
// function user_deleteById($id)
// {
//     try {
//         $conn = openDatabaseConnection();
//         $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
//         if (!$stmt) {
//             throw new \Exception("Prepare failed: " . $conn->error);
//         }
//         $stmt->bind_param("i", $id);
//         $result = $stmt->execute();
//         if (!$result) {
//             throw new \Exception("Execute failed: " . $stmt->error);
//         }
//         $stmt->close();
//         closeDatabaseConnection($conn);
//         return ['status' => true, 'data' => true];
//     } catch (\Exception $e) {
//         error_log("user_deleteById error: " . $e->getMessage());
//         return ['status' => false, 'error' => $e->getMessage()];
//     }
// }

// /**
//  * Check if user has a specific permission
//  */
// function user_hasPermission($userId, $permissionName)
// {
//     try {
//         $allowed = [
//             'can_view',
//             'can_write_review',
//             'can_delete_review',
//             'can_delete_submission',
//             'can_add_user',
//             'can_delete_user'
//         ];
//         if (!in_array($permissionName, $allowed)) {
//             return ['status' => false, 'error' => "Invalid permission: $permissionName"];
//         }

//         $conn = openDatabaseConnection();
//         $stmt = $conn->prepare("SELECT $permissionName FROM users WHERE id = ?");
//         if (!$stmt) {
//             throw new \Exception("Prepare failed: " . $conn->error);
//         }
//         $stmt->bind_param("i", $userId);
//         $stmt->execute();
//         $result = $stmt->get_result();
//         $row = $result->fetch_assoc();
//         $stmt->close();
//         closeDatabaseConnection($conn);

//         $hasPermission = isset($row[$permissionName]) && (bool)$row[$permissionName];
//         return ['status' => true, 'data' => $hasPermission];
//     } catch (\Exception $e) {
//         error_log("user_hasPermission error: " . $e->getMessage());
//         return ['status' => false, 'error' => $e->getMessage()];
//     }
// }

// // Optional wrappers for common permission checks

// function user_canView($userId)
// {
//     return user_hasPermission($userId, 'can_view');
// }

// function user_canWriteReview($userId)
// {
//     return user_hasPermission($userId, 'can_write_review');
// }

// function user_canDeleteReview($userId)
// {
//     return user_hasPermission($userId, 'can_delete_review');
// }

// function user_canDeleteSubmission($userId)
// {
//     return user_hasPermission($userId, 'can_delete_submission');
// }

// function user_canAddUser($userId)
// {
//     return user_hasPermission($userId, 'can_add_user');
// }

// function user_canDeleteUser($userId)
// {
//     return user_hasPermission($userId, 'can_delete_user');
// }

// function user_getUsers($startPoint)
// {
//     try {
//         $conn = openDatabaseConnection();
//         $stmt = $conn->prepare("SELECT * FROM users LIMIT 20 OFFSET ?");
//         if (!$stmt) {
//             throw new \Exception("Prepare failed: " . $conn->error);
//         }
//         $stmt->bind_param("i", $startPoint);
//         $stmt->execute();
//         $result = $stmt->get_result();
//         $data = $result->fetch_all(\MYSQLI_ASSOC);
//         $stmt->close();
//         closeDatabaseConnection($conn);
//         return ['status' => true, 'data' => $data];
//     } catch (\Exception $e) {
//         error_log("user_getUsers error: " . $e->getMessage());
//         return ['status' => false, 'error' => $e->getMessage()];
//     }
// }

// function user_getTotaluser()
// {
//     try {
//         $conn = openDatabaseConnection();
//         $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM users");
//         if (!$stmt) {
//             throw new \Exception("Prepare failed: " . $conn->error);
//         }
//         $stmt->execute();
//         $result = $stmt->get_result();
//         $row = $result->fetch_assoc();
//         $stmt->close();
//         closeDatabaseConnection($conn);
//         return ['status' => true, 'data' => (int)$row["count"]];
//     } catch (\Exception $e) {
//         error_log("user_getTotaluser error: " . $e->getMessage());
//         return ['status' => false, 'error' => $e->getMessage()];
//     }
// }

// function user_updatePermision($id, $can_write_review, $can_delete_review, $can_delete_submission, $can_add_user, $can_delete_user)
// {
//     try {
//         $conn = openDatabaseConnection();
//         $stmt = $conn->prepare('UPDATE users SET 
//                 can_write_review = ?,
//                 can_delete_review = ?,
//                 can_delete_submission = ?,
//                 can_add_user = ?,
//                 can_delete_user = ?
//             WHERE id = ?
//         ');
//         if (!$stmt) {
//             throw new \Exception("Prepare failed: " . $conn->error);
//         }
//         $stmt->bind_param(
//             "iiiiii",
//             $can_write_review,
//             $can_delete_review,
//             $can_delete_submission,
//             $can_add_user,
//             $can_delete_user,
//             $id
//         );
//         $stmt->execute();
//         $stmt->close();
//         closeDatabaseConnection($conn);

//         return ['status' => true, 'data' => true];
//     } catch (\Exception $e) {
//         error_log("user_updatePermision error: " . $e->getMessage());
//         return ['status' => false, 'error' => $e->getMessage()];
//     }
// }
