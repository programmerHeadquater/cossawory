

<?php
require 'conn/submission.php';
use function submission\getSubmission;


$startPoint = isset($_GET['startPoint']) ? (int)$_GET['startPoint'] : 0 ;


$data = getSubmission($startPoint);



foreach ($data as $submission) {
    echo submissionTemplate($submission);
}
echo pagination($startPoint);


function submissionTemplate($row) {

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
            <span><a href="dashboard.php?page=deleteSubmission&id=<?=$row['id']?>">Delete</a></span>
            
        </div>
    </div>
<?php

    return ob_get_clean();
}
function pagination($startPoint) {
    ob_start();
    ?>
        <div class="pagination">
            <button><a href="dashboard.php?startPoint=<?=$startPoint-2?>">Pre</a></button>
            <button><a href="dashboard.php?startPoint=<?=$startPoint+2?>">Next</a></button>
        </div>
    <?php
    return ob_get_clean();
}



?>

