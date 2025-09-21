<button><a href="dashboard.php?page=main">back</a></button>

<?php
require_once 'conn/conn.php';
require_once 'conn/review.php';
use function conn\openDatabaseConnection;
use function conn\closeDatabaseConnection;
use function review\insertReview;
use function review\insertReviewIdIntoSubmission;
use function review\deleteReview;



$id = (int) $_GET['id'];
$message = "We are working on it";
//updating if the querry has data in the form 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review']) && $_POST['review'] !=="") {

    $review = $_POST['review'];
    [$review_id, $error] = insertReview($id, $review);
    if ($error === null) {
        
        $error = insertReviewIdIntoSubmission($review_id,$id);
    }
    if ($error != null) {
        $error = deleteReview($review_id);
    }
}

$conn = openDatabaseConnection();
$stmt = $conn->prepare(
    'SELECT submission.* , reviews.review,reviews.created_at as review_created_at
            FROM submission
            LEFT JOIN reviews
            ON submission.id = reviews.submission_id
            WHERE submission.id = ?
    '
);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();



if ($result->num_rows > 0) {
    $review = [];
    $submission = null;
    while ($row = $result->fetch_assoc()) {
        if (!$submission) {
            $submission = [
                'id' => $row['id'],
                'title' => $row['title'],
                'concern' => $row['concern'],
                'submitted_at' => $row['submitted_at']
            ];

        }
        $review[] = [
            'review' => $row['review'],
            'review_created_at' => $row['review_created_at']
        ];


    }
    echo submissionTemplate($submission);
    echo reviewTemplate($review);

    $stmt->close();
    closeDatabaseConnection($conn);
}

?>


<br>
<form method="POST" action="dashboard.php?page=reviewSingle&id=<?= $id ?>">
    <p>Id: <?= $id ?> </p>
    <textarea style="width:100%;" name="review" id="" placeholder="Write a reiew here" value="Testing on"></textarea>
    <br><br>
    <button type="submit">Submit</button>
</form>




<?php
function submissionTemplate($submission)
{   
    ob_start();
    ?>
    

    <p>Submission Data:</p>
    <p>Id:<?= $submission['id'] ?></p>
    <p><?= $submission['title'] ?></p>
    <p><?= $submission['concern'] ?></p>
    <p><?= $submission['submitted_at'] ?></p>


    <?php
    return ob_get_clean();
}


function reviewTemplate($review){
    ob_start();
    echo "<p>Review: (required a good template):</p><br>";
    foreach ($review as $key => $value) {
        ?>

        <p><?= $value['review'] ?> </p>
        <br>

        <?php
    }
    return ob_get_clean();
}
?>