<div class="page">
    <h1>Submission </h1>

    <?php
    require 'conn/submission.php';
    use function submission\getSubmission;
    use function submission\getSubmissionsTotalCount;



    $startPoint = isset($_GET['startPoint']) ? (int) $_GET['startPoint'] : 0;


    $data = getSubmission($startPoint);



    foreach ($data as $submission) {
        echo submissionTemplate($submission, $startPoint);
    }
    $total = getSubmissionsTotalCount();
    echo pagination($startPoint, $total);


    function submissionTemplate($row, $startPoint)
    {
        $form_data = json_decode($row['form_data'], true);
        ob_start();
        ?>
        <div class="reviewAll">
            <?php
            foreach ($form_data as $index => $field): ?>
                <div class="submission">
                    <p><?= $field['label'] ?> </p>
                </div>
                <?php if ($field['type'] == 'file'): ?>
                    asdf
                    <?php if (is_array($field['value'])): ?>
                        
                        <?php foreach ($field['value'] as $subKey => $subValue ):?> 
                            
                            <img class="uploadImg" src="<?=$subValue?>" alt="">

                        <?php
                        endforeach;
                    endif;
                endif;
            endforeach;
            ?>

            <br>
            <p>Status: <?= $row['review'] ? "Reviewed  " : "pending" ?> </p>
            <br>
            <div class="option">


                <span><a href="dashboard.php?page=reviewSingle&id=<?= $row['id'] ?>">Review Now</a></span>


                <form action="dashboard/deleteSubmission.php" method="POST">

                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <button type="submit">Delete</button>
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
</div>