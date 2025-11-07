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
    if ($id == $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => 'Cannot delete yourself']);
        exit;
    }

   
    $result = user_canDeleteUser($_SESSION['user_id']);

    if (!isset($_SESSION['user_id']) || !($result['status'] ?? false) || !($result['data'] ?? false)) {
    echo json_encode(['success' => false, 'message' => 'You do not have permission to delete users']);
    exit;
}
   

    $response = user_deleteById((int) $id);

    if ($response['status']) {
       
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request method']);
