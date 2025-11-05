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

// if (isset($_SESSION['user_id']) && isset($_POST['id'])) {

//     $sessionId = (int) $_SESSION['user_id'];
//     $id = (int) $_POST['id'];
//     $pageIn = isset($_POST['pageIn']) ? $_POST['pageIn'] : "submission_pending";
    

//     if (user_canDeleteSubmission($sessionId) && filter_var($id, FILTER_VALIDATE_INT) !== false) {
//         $message = deleteSubmission($id);

//     } else {
//         $message = "no permision";
//     }
// } else {
//     $message = "if did not pass ";
// }

if ($sessionId <= 0 || $id <= 0) {
    $message = "Invalid request or session expired";
} else {
   $response = user_canDeleteSubmission($sessionId);
    if (($response['success'] ?? false) && ($response['data'] ?? false)) {
        $result = deleteSubmission($id);

        if ($result['success'] ?? false) {
            $message = "Submission deleted successfully";
        } else {
            $message = $result['error'] ?? "Failed to delete submission";
        }
    } else {
        $message = $response['error'] ?? "No permission";
    }
}

header(header: "Location: ../dashboard.php?page=main&message=$message&startPoint=$startPoint&pageIn=$pageIn");
exit();
?>