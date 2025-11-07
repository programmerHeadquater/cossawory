<?php
session_name("MySecureAppSession");
session_start();
require_once '../conn/review.php';
require_once '../conn/user.php';
use function review\updateReview;
use function user\user_canDeleteReview;




$startPoint = (int) $_GET['startPoint'];
$id = (int) $_GET['id'];


if (isset($_SESSION['user_id']) && isset($_POST['review_id']) && isset($_POST['review'])) {

    $sessionId = (int) $_SESSION['user_id'];
    $review_id = (int) $_POST['review_id'];
    $review = (string) $_POST['review'];


    if (user_canDeleteReview($sessionId)['status']?? false && filter_var($review_id, FILTER_VALIDATE_INT) !== false) {
        $message = updateReview($review_id,$review);

    } else {
        $message = "No permision To update";
    }
} else {
    $message = "if did not pass ";
}
echo $message;

header(header: "Location: ../dashboard.php?page=reviewSingle&error=$message&startPoint=$startPoint&id=$id");

?>