
<?php
    $pageIn = isset($_GET['pageIn']) ? $_GET['pageIn'] : 'submission_pending';
?>
<div class="page">
    <h1>Submissions</h1>
        <div class="submissionNav">
            <a class="<?=$pageIn == 'submission_pending'? 'active' : '';?>" href="dashboard.php?page=main&pageIn=submission_pending">Pending</a>
            <a class="<?=$pageIn == 'submission_all'? 'active' : '';?>" href="dashboard.php?page=main&pageIn=submission_all">All</a>
            <a class="<?=$pageIn == 'submission_reviewed'? 'active' : '';?>" href="dashboard.php?page=main&pageIn=submission_reviewed">Reviewed</a>
        </div> 
    <?php
         $error = isset($_GET['error']) ?  $_GET['error'] : null;
          if($error) {
            echo "<p class='error'>". $error ."</p>";

    }
        include_once 'dashboard/'.$pageIn.'.php';
    ?>
    
</div>