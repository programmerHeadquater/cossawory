<?php
require_once 'conn/conn.php';
require_once 'conn/review.php';
use function conn\openDatabaseConnection;
use function conn\closeDatabaseConnection;
use function review\insertReview;
use function review\insertReviewIdIntoSubmission;
use function review\deleteReview;

$id = (int) $_GET['id'];
$message = "Working on it";
//updating if the querry has data in the form 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review'])) {
    $message = 'change detected';
    echo $message;
    $review = $_POST['review'];
    [$review_id, $error] = insertReview($id, $review);
    if ($error === null) {
        $error = insertReviewIdIntoSubmission($review_id);
    }
    if ($error != null) {
        $error = deleteReview($review_id);
    }
}
echo "are we at 21";
$conn = openDatabaseConnection();
$stmt = $conn->prepare(
    'SELECT submission.* , reviews.review,reviews.created_at as review_created_at
            FROM submission
            LEFT JOIN reviews
            ON submission.id = reviews.submission_id
            WHERE submission_id = ?
    ');
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    ob_start();
    echo '<br>';
    var_dump($row);
    ?>
    <br>
    <button><a href="dashboard.php?page=main">back</a></button>
    <div class="reviewSingle">
        <br>
        <p>Message: <?= $message ?> </p>
        <br>
        <p>ID:<?= $row['id'] ?> </p>
        <p>Tittle: <?= $row['title'] ?> </p>
        <br>
        <p>Concern:</p>
        <p><?= $row['concern'] ?> </p>
        <p>Review:</p>
        <p><?= $row['review'] ?> </p>
        <p><?= $row['review_id'] ?> </p>
        <br>
        <form method="POST" action="dashboard.php?page=reviewSingle&id=<?= $id ?>">
            <p>Id: <?= $id ?> </p>
            <textarea style="width:100%;" name="review" id="" placeholder="Write a reiew here"></textarea>
            <br><br>
            <button type="submit">Submit</button>
        </form>
    </div>
    <?php
    echo ob_get_clean();
    $stmt->close();
    closeDatabaseConnection($conn);
}

?>