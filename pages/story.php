<?php
require_once "conn/conn.php";
use function conn\closeDatabaseConnection;
use function conn\openDatabaseConnection;
$conn = openDatabaseConnection();
$stmt = $conn->prepare(
    'SELECT submission.* , reviews.review,reviews.created_at as review_created_at
            FROM submission
            LEFT JOIN reviews
            ON submission.id = reviews.submission_id
            WHERE submission.review = 1
    '
);

$stmt->execute();
$result = $stmt->get_result();


$submissions = [];
while ($row = $result->fetch_assoc()) {
    $submission_id = $row['id'];

    if (!isset($submissions[$submission_id])) {
        $submissions[$submission_id] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'concern' => $row['concern'],
            'submitted_at' => $row['submitted_at'],
            'reviews' => [],
        ];
    }
    if (!empty($row['review'])) {
        $submissions[$submission_id]['reviews'][] = [
            'review' => $row['review'],
            'review_created_at' => $row['review_created_at']
        ];

    }

}

foreach ($submissions as $submission) {
    echo submissionTemplate($submission);
    echo reviewTemplate($submission['reviews']);
}


$stmt->close();
closeDatabaseConnection($conn);



?>

<?php
function submissionTemplate($submission)
{
    ob_start();
    ?>

    <p>Submission Data:</p>
    <p>Id:<?= $submission['id'] ?></p>
    <p><?= $submission['title'] ?></p>
    <p><?= $submission['concern'] ?></p>
    <p>Submitted At: <?= $submission['submitted_at'] ?></p>
    <?php
    return ob_get_clean();
}

function reviewTemplate($review)
{
    ob_start();
    

    

    foreach ($review as $key => $value) {

        ?>
        <p><?= $value['review'] ?> </p>
        <br>
        <?php
    }
    return ob_get_clean();
}

?>