<?php
session_name("MySecureAppSession");
session_start();
require_once '../conn/user.php';
require_once '../conn/review.php';
use function review\deleteReview;
use function user\user_canDeleteReview;



$reviewId = isset($_POST['review_id']) ? (int) $_POST['review_id'] : 0;
$startPoint = isset($_GET['startPoint']) ? (int) $_GET['startPoint'] : 0;
// this is submission id
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;



if (!isset($_SESSION['user_id']) || $reviewId <= 0) {
    $message = "Invalid request or session expired";
} else {
    $sessionId = (int) $_SESSION['user_id'];
    
    if (user_canDeleteReview($sessionId)['status']?? false) {
        $result = deleteReview($reviewId);

        if ($result['success'] ?? false) {
            $message = "Review deleted successfully";
        } else {
            $message = $result['error'] ?? "Failed to delete review";
        }
    } else {
        $message = "No permission";
    }
}

echo $message;

header(header: "Location: ../dashboard.php?page=reviewSingle&error=$message&startPoint=$startPoint&id=$id");
exit();
?>