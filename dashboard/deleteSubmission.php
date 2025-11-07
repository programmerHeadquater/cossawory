<?php
session_name("MySecureAppSession");
session_start();
require_once "../conn/submission.php";
require_once "../conn/user.php";
use function submission\deleteSubmission;
use function user\user_canDeleteSubmission;

$sessionId = $_SESSION['user_id'] ?? 0;
$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$pageIn = $_POST['pageIn'] ?? "submission_pending";
$startPoint = $_POST['startPoint'] ?? 0;


if ($sessionId <= 0 || $id <= 0) {
    $message = "Invalid request or session expired";
} else {
   $response = user_canDeleteSubmission($sessionId);
   
    if (($response['status'] ?? false) && ($response['data'] ?? false)) {
        
        $result = deleteSubmission($id);
        
        if ($result['status'] ?? false) {
            $message = "Submission deleted successfully";
        } else {
            $message = $result['error'] ?? "Failed to delete submission";
        }
    } else {
        $message = $response['error'] ?? "No permission";
    }
}
header(header: "Location: ../dashboard.php?page=main&error=$message&startPoint=$startPoint&pageIn=$pageIn");
exit();
?>