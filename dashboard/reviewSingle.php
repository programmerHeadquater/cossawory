<button class="back"><a href="dashboard.php?page=main">back</a></button>

<?php

require_once 'conn/review.php';
require_once 'conn/submission.php';
use function review\getReviewBySybmissionId;
use function submission\getSubmissionById;
use function review\getTotalReviewBySubmissionId;
use function review\insertReview;




$id = (int) $_GET['id'];
$startPoint = isset($_GET['startPoint']) ? (int) $_GET['startPoint'] : 0;

//updating if the querry has data in the form 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review']) && $_POST['review'] !== "") {

    $review = $_POST['review'];
    insertReview($id, $review);

}


submissionTemplate(getSubmissionById($id));
reviewTemplate(getReviewBySybmissionId($id,$startPoint), $id);
addReviewTemplate($id);
pagination($startPoint, getTotalReviewBySubmissionId($id), $id);






?>

<?php
function submissionTemplate($submission)
{   
    ob_start();
    ?>

    <div class="submissionTemplate">
        <p>Submission Data:</p>
        <p>Id:<?= $submission['id'] ?></p>
        <p><?= $submission['title'] ?></p>
        <p><?= $submission['concern'] ?></p>
        <p><?= $submission['submitted_at'] ?></p>
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
function addReviewTemplate($id)
{
   
    ob_start(); ?>
    <div>
        <form method="POST" action="dashboard.php?page=reviewSingle&id=<?= $id ?>">

            <textarea style="width:100%;" name="review" id="" placeholder="Write a reiew here"
                value="Testing on"></textarea>
            <br><br>
            <button type="submit">Submit</button>
        </form>
        <br>
    </div>
    <?php
    echo ob_get_clean();
}

function pagination($startPoint, $total,$id)
{
    
    $prevStart = max(0, $startPoint - 2);
    $nextStart = $startPoint + 2;
    ?>
    <div class="pagination">
        <?php if ($startPoint > 0): ?>
            <button><a href="dashboard.php?page=reviewSingle&id=<?=$id?>&startPoint=<?= $prevStart ?>">Previous</a></button>
        <?php endif; ?>

        <?php if ($nextStart < $total): ?>
            <button><a href="dashboard.php?page=reviewSingle&id=<?=$id?>&startPoint=<?= $nextStart ?>">Next</a></button>
        <?php endif; ?>
    </div>
    <?php
    echo ob_get_clean();
}

?>
