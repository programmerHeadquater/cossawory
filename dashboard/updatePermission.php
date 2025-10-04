<?php
session_name("MySecureAppSession");
session_start();
require_once '../conn/user.php';
use function user\user_updatePermision;
use function user\user_canDeleteUser;
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = $_POST['id'] ?? null;

    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Missing user ID']);
        exit;
    }
    
    if (user_canDeleteUser((int) $_SESSION['user_id'])) {
        $can_write_review = isset($_POST['can_write_review']) && strtolower($_POST['can_write_review']) === 'yes' ? 1 : 0;
        $can_delete_review = isset($_POST['can_delete_review']) && strtolower($_POST['can_delete_review']) === 'yes' ? 1 : 0;
        $can_delete_submission = isset($_POST['can_delete_submission']) && strtolower($_POST['can_delete_submission']) === 'yes' ? 1 : 0;
        $can_add_user = isset($_POST['can_add_user']) && strtolower($_POST['can_add_user']) === 'yes' ? 1 : 0;
        $can_delete_user = isset($_POST['can_delete_user']) && strtolower($_POST['can_delete_user']) === 'yes' ? 1 : 0;
        $sucess = user_updatePermision($id, $can_write_review, $can_delete_review, $can_delete_submission, $can_add_user, $can_delete_user);
        echo json_encode(['success' => $sucess, 'message' => 'All feild are updated sucessfully.']);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'No permission to edit the user for this account']);
        exit;
    }



}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
