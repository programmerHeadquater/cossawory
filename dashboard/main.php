
<div class="page">
    <h1>Submission </h1>

<?php
require 'conn/submission.php';
use function submission\getSubmission;
use function submission\getSubmissionsTotalCount;



$startPoint = isset($_GET['startPoint']) ? (int)$_GET['startPoint'] : 0 ;


$data = getSubmission($startPoint);



foreach ($data as $submission) {
    echo submissionTemplate($submission,$startPoint);
}
$total = getSubmissionsTotalCount();
echo pagination($startPoint,$total);


function submissionTemplate($row,$startPoint) {

    ob_start();
    ?>
    <div class="reviewAll">
        <h2><?php echo htmlspecialchars($row['title']) ?> 	</h2>
        <br>
        <h3 class="description"><?php echo htmlspecialchars($row['concern']) ?> </h3>
        <br>
        <button>Read more</button>
        <br>
        <br>
        <div class="option">
            
            <span>Status: <?= $row['review'] ? "Review <br> At: " . $row['submitted_at'] : "pending"   ?> </span>
            <span><a href="dashboard.php?page=reviewSingle&id=<?=$row['id']?>">Review Now</a></span>
            <span><a href="dashboard.php?page=deleteSubmission&id=<?=$row['id']?>&startPoint=<?=$startPoint?>">Delete</a></span>
            
        </div>
    </div>
<?php

    return ob_get_clean();
}
function pagination($startPoint,$total) {
    ob_start();
    $prevStart = max(0, $startPoint - 2);
    $nextStart = $startPoint + 2;
    ?>
        <div class="pagination">
        <?php if ($startPoint > 0): ?>
            <button><a href="dashboard.php?startPoint=<?= $prevStart ?>">Previous</a></button>
        <?php endif; ?>

        <?php if ($nextStart < $total): ?>
            <button><a href="dashboard.php?startPoint=<?= $nextStart ?>">Next</a></button>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}



?>
</div>
