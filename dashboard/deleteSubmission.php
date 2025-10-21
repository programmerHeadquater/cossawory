<?php
session_name("MySecureAppSession");
session_start();
require_once "../conn/submission.php";
require_once "../conn/user.php";
use function submission\deleteSubmission;
use function user\user_canDeleteSubmission;

$getId = (int) $_POST['id'];
echo $getId;
echo $_SESSION['user_id'];

if (isset($_SESSION['user_id']) && isset($_POST['id'])) {

    $sessionId = (int) $_SESSION['user_id'];
    $id = (int) $_POST['id'];
    $pageIn = isset($_POST['pageIn']) ? $_POST['pageIn'] : "submission_pending";
    

    if (user_canDeleteSubmission($sessionId) && filter_var($id, FILTER_VALIDATE_INT) !== false) {
        $message = deleteSubmission($id);

    } else {
        $message = "no permision";
    }
} else {
    $message = "if did not pass ";
}
echo $message;

header(header: "Location: ../dashboard.php?page=main&message=$message&startPoint=$startPoint&pageIn=$pageIn");
?>