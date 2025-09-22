<button class="back"><a href="dashboard.php?page=main">back</a></button>

<?php

require_once 'conn/review.php';
require_once 'conn/submission.php';
use function review\getReviewBySybmissionId;
use function submission\getSubmissionById;




$id = (int) $_GET['id'];
$message = "We are working on it";
//updating if the querry has data in the form 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review']) && $_POST['review'] !== "") {

    $review = $_POST['review'];
    [$review_id, $error] = insertReview($id, $review);
    if ($error === null) {

        $error = insertReviewIdIntoSubmission($review_id, $id);
    }
    if ($error != null) {
        $error = deleteReview($review_id);
    }
}


submissionTemplate(getSubmissionById($id));
reviewTemplate(getReviewBySybmissionId($id), $id);
formTemplate($id);





?>

<?php
function submissionTemplate($submission)
{
    ob_start();
    ?>


    <div class="submissionTemplate">
        <p>Submission Data:</p>
        <p>Id:<?= $submission[0]['id'] ?></p>
        <p><?= $submission[0]['title'] ?></p>
        <p><?= $submission[0]['concern'] ?></p>
        <p><?= $submission[0]['submitted_at'] ?></p>
    </div>


    <?php
    echo ob_get_clean();
    return;
}


function reviewTemplate($review, $id)
{

    ob_start();
    ?>
    <h2>Reviews:</h2>
    <?php
    foreach ($review as $key => $value) {
        ?>
        <div class="reviewTemplate">
            
            <p class="on"><?= $value['review'] ?> </p>
            <p>
                <button class="update">Update</button>
                <button class="delete">Delete</button>
            </p>
            <div class="updatReviewId">
                <form action="dashboard.php?page=deleteReview&id=<?= $id ?>&review_id=<?= $value['id'] ?>">
                    <textarea class="textfeild" name="review" id=""><?= $value['review'] ?></textarea>
                </form>
            </div>
            <div class="delete">
                <p>Are you sure</p>
                <p><a href="">Yes</a></p>
            </div>
            <br>
        </div>

        <?php
    }
    echo ob_get_clean();
    return;
}
function formTemplate($id)
{
    ob_start(); ?>
    <div>
        <form method="POST" action="dashboard.php?page=reviewSingle&id=<?= $id ?>">

            <textarea style="width:100%;" name="review" id="" placeholder="Write a reiew here"
                value="Testing on"></textarea>
            <br><br>
            <button type="submit">Submit</button>
        </form>
    </div>
    <?php
    echo ob_get_clean();
}
?>