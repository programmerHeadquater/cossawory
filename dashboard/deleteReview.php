<?php
session_name("MySecureAppSession");
session_start();
require_once '../conn/user.php';
require_once '../conn/review.php';
use function review\deleteReview;
use function user\user_canDeleteReview;

$getId = (int) $_POST['review_id'];
echo $getId;
echo $_SESSION['user_id'];
$startPoint = (int) $_GET['startPoint'];
$id = (int) $_GET['id'];

if (isset($_SESSION['user_id']) && isset($_POST['review_id'])) {

    $sessionId = (int) $_SESSION['user_id'];
    $review_id = (int) $_POST['review_id'];


    if (user_canDeleteReview($sessionId) && filter_var($review_id, FILTER_VALIDATE_INT) !== false) {
        $message = deleteReview($review_id);

    } else {
        $message = "no permision";
    }
} else {
    $message = "if did not pass ";
}
echo $message;

header(header: "Location: ../dashboard.php?page=reviewSingle&message=$message&startPoint=$startPoint&id=$id");
?>