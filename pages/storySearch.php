<?php
require_once 'conn/conn.php';
use function conn\closeDatabaseConnection;
use function conn\openDatabaseConnection;


$error = null;
$submission = null;
$reviews = null;
$queryId = isset($_POST['queryId']) ? (int) $_POST['queryId'] : null;
if ($queryId !== null) {
    if ($queryId !== null && filter_var($queryId, FILTER_VALIDATE_INT) !== false && $queryId > 0) {
        $conn = openDatabaseConnection();
        $query = "SELECT * FROM submission WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $queryId);
        $stmt->execute();
        $result = $stmt->get_result();
        $submission = $result->fetch_assoc();
       
        // If review = 1, join with review table
        if ($submission == null) {
            $error = "Submission no found";
        } elseif ($submission['review'] == 1 || $submission['review'] == "1") {
            $query = "SELECT *  FROM reviews
            WHERE submission_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $queryId);
            $stmt->execute();
            $result = $stmt->get_result();
            $reviews = $result->fetch_all(MYSQLI_ASSOC);
            closeDatabaseConnection($conn);
            

        } else {
            $reviews = null;
        }


    } else {
        $error = "Not a valid query Id";
    }
}
?>



<div class="story">
    <form class="search" action="index.php?page=storySearch" method="POST">
        <br>
        <p>Please enter Query Id:</p>
        <input name="queryId" type="number"><button>Search</button>
        <br>
    </form>
    <br>
    <br>
    <?php if ($error): ?>
        <p class="colorRed"><?= $error ?> </p>
    <?php endif; ?>
    <div class="list">
        <?php if ($submission !== null) {
            submissionTemplate($submission);
        } ?>
        <?php if ($reviews !== null) {
            reviewTemplate($reviews);
        } else { ?>
        <br>
            <h3 class="colorRed">This submission is not reviewed yet.</h3>
        <?php } ?>
    </div>
</div>






<?php

function submissionTemplate($submission)
{
    

    ob_start();
    ?>
    <div class="submissionData">
        <h2>Submited Query</h2>
        <br>
        <?php

        $form_data = json_decode($submission['form_data'], true);
        foreach ($form_data as $index => $field): ?>

            <p class="submissionLabel"><?= $field['label'] ?> </p>

            <?php if ($field['type'] == 'text' || $field['type'] == 'textarea'): ?>
                <p class="submissionText"><?= $field['value'] ?></p>
            <?php endif ?>
            <?php if ($field['type'] == 'file' || $field['type'] == 'audio'): ?>

                <?php if (is_array($field['value'])): ?>

                    <?php if ($field['value']['type'] == 'image/png' || $field['value']['type'] == 'image/jpeg'): ?>
                        <img class="submissionImg" src="<?= $field['value']['path'] ?>" alt="no image found">
                    <?php endif;
                    if ($field['value']['type'] == 'audio/mpeg' || $field['value']['type'] == 'audio/mp3' || $field['value']['type'] == 'audio/wav' || $field['value']['type'] == 'audio/ogg' || $field['value']['type'] == 'audio/webm'): ?>
                        <audio class="submissionAudido" controls>
                            <source src="<?= $field['value']['path'] ?>" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>
                        <?php
                    endif;
                endif;
            endif;
        endforeach; ?>
    </div>
    <?php
    echo ob_get_clean();
}

function reviewTemplate($reviews)
{
    
    ob_start();
    ?>
    <div class="review">
        <h2>Reviews:</h2>
        <br>
        <?php

        foreach ($reviews as $key => $value) {
            ?>
            <p class="reviewCard"><?= $value['review'] ?> </p>
            <br>
            <?php
        }
        ?>
    </div>
    <?php
    echo ob_get_clean();
}
?>