<?php
session_name("MySecureAppSession");
session_start();

header('Content-Type: application/json');
require_once '../conn/user.php';

use function user\user_deleteById;
use function user\user_canDeleteUser;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;

    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Missing user ID']);
        exit;
    }

    if (!isset($_SESSION['user_id']) || !user_canDeleteUser($_SESSION['user_id'])['status'] ?? false ) {
        echo json_encode(['success' => false, 'message' => 'You do not have permission to delete users']);
        exit;
    }
    if (!isset($_SESSION['user_id'])  || !user_canDeleteUser($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'You do not have permission to delete users']);
        exit;
    }
    $responce = user_deleteById((int)$id);
    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request method']);
