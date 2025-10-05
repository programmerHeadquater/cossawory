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

if($message['status'] === 'sucess'):?>
    <div class="submissionSucess">
        <h2 style="color:green">We receive you submission. <br> We will review as soon as possible.</h2>
    </div>
    <br>
    <div class="detail">
        <h2>Data received:</h2>
        <br>
        <p>Title:</p>
        <p><?= $title ?> </p>
        <br>
        <p>concern</p>
        <p><?= $concern ?> </p>
        <br>
    </div>
    <a href="index.php?page=submission">Go back to submission</a>
<?php
    endif;
if($message['status'] === 'fail'):?>
    <div class="submissionFail">
        <h2>Sorry We cannot process</h2>
        <h4><?= $message['message'] ?> </h4>
    </div>
    <?php
    endif;
?>
