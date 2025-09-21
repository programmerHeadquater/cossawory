<?php
require_once 'conn/submission.php';
use function submission\insertSubmission;


$title = $_POST["title"];
$concern = $_POST["questions"];
$why_this_app = $_POST["why_this_app"];
$disability = isset($_POST["disability"]) && $_POST["disability"] !== "" ? $_POST["disability"] : "no value";
$review = "FALSE";
$review_id = "0";



$message = insertSubmission(
    $title,
    $concern,
    $why_this_app,
    $disability,
    $review,
    $review_id);

echo $message;

?>
