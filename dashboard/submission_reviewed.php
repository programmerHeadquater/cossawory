<?php
    require 'conn/submission.php';
    use function submission\getSubmissionsReviewedTotalCount;
    use function submission\getSubmissionReviewed;



    $startPoint = isset($_GET['startPoint']) ? (int) $_GET['startPoint'] : 0;
    


    $dataResponse = getSubmissionReviewed($startPoint);



    if ($dataResponse['status'] && $dataResponse['data']) {
    $data = $dataResponse['data'];
    foreach ($data as $submission) {
        echo submissionTemplate($submission, $startPoint);
    }
}
    $total = getSubmissionsReviewedTotalCount();
    echo pagination($startPoint, $total['data']);


    function submissionTemplate($row, $startPoint)
    {
        $form_data = json_decode($row['form_data'], true);
        ob_start();
        ?>
        <div class="reviewAll">
            <?php
            foreach ($form_data as $index => $field): ?>
                
                    <p class="label"><?= $field['label'] ?> </p>

                    <?php if ($field['type'] == 'text' || $field['type'] == 'textarea'): ?>
                        <p><?= $field['value'] ?></p>
                    <?php endif ?>
                    <?php if ($field['type'] == 'file' || $field['type'] == 'audio' ): ?>

                        <?php if (is_array($field['value'])): ?>
                            
                            <?php if ($field['value']['type'] == 'image/png' || $field['value']['type'] == 'image/jpeg'): ?>
                                <img class="uploadImg" src="<?= $field['value']['path'] ?>" alt="User send Image">
                            <?php endif;
                            if ($field['value']['type'] == 'audio/mpeg' || $field['value']['type'] == 'audio/mp3' || $field['value']['type'] == 'audio/wav' || $field['value']['type'] == 'audio/ogg' || $field['value']['type'] == 'audio/webm'): ?>
                            <audio controls>
                                <source src="<?=$field['value']['path']?>" type="audio/mpeg">
                                Your browser does not support the audio element.
                            </audio>
                            <?php
                            endif;
                        endif;
                    endif;
            endforeach;
            ?>
            
            <br>
            <p class="<?= $row['review'] ? 'reviewed' : 'pending' ?>">Status: <?= $row['review'] ? "Reviewed  " : "Pending" ?> </p>
            <br>
            <div class="option">


                <span><a class="greenBtn" href="dashboard.php?page=reviewSingle&id=<?= $row['id'] ?>">Review Now</a></span>


                <form action="dashboard/deleteSubmission.php" method="POST"  onsubmit="return confirm('Are you sure you want to delete this submission?');">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <input type="hidden" name="pageIn" value="submission_reviewed">
                    <button class="deleteBtn" type="submit">Delete</button>
                </form>


            </div>
        </div>
        <?php

        return ob_get_clean();
    }
    function pagination($startPoint, $total)
    {
        ob_start();
        $prevStart = max(0, $startPoint - 20);
        $nextStart = $startPoint + 20;
        ?>
        <div class="pagination">
            <?php if ($startPoint > 20): ?>
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