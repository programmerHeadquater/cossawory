<title>All story</title>

<div class="story">


    <?php
    require_once "conn/conn.php";
    use function conn\closeDatabaseConnection;
    use function conn\openDatabaseConnection;
    require_once 'conn/submission.php';
    use function submission\getSubmissionsReviewedTotalCount;
    $startPoint = isset($_GET['startPoint']) ? (int) $_GET['startPoint'] : 0;

    $conn = openDatabaseConnection();
    $stmt = $conn->prepare(
        'SELECT s.*, r.review, r.created_at as review_created_at
     FROM (
         SELECT * FROM submission
         WHERE review = 1
         ORDER BY submitted_at DESC
         LIMIT 5 OFFSET ?
     ) AS s
     LEFT JOIN reviews r ON s.id = r.submission_id'

    );
    $stmt->bind_param('i', $startPoint);
    $stmt->execute();
    $result = $stmt->get_result();


    $submissions = [];
    while ($row = $result->fetch_assoc()) {
        $submission_id = $row['id'];

        if (!isset($submissions[$submission_id])) {
            $submissions[$submission_id] = [
                'id' => $row['id'],
                'form_data' => $row['form_data'],
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

    if ($submissions == null) {
        echo "<p>Sorry, No story yet<p>";
    } else {
        foreach ($submissions as $submission) {
            ?>
            <div class="list">

                <?php
                echo submissionTemplate($submission);
                echo reviewTemplate($submission['reviews']);
                ?>
            </div>

            <?php

        }
    }


    $stmt->close();
    closeDatabaseConnection($conn);

    echo pagination($startPoint, getSubmissionsReviewedTotalCount());


    ?>

    <?php
    function submissionTemplate($submission)
    {


        ob_start();
        ?>
        <div class="submissionData">
            <!-- <h2>Submited Query</h2> -->
            <h2>Issue</h2>
            <!-- <br> -->
            <?php

            $form_data = json_decode($submission['form_data'], true);
            foreach ($form_data as $index => $field): ?>

                <!-- <p class="submissionLabel"> -->
                <?php
                $field['label']
                    ?>
                <!-- </p> -->

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
                                <source src="<?= $field['value']['path'] ?>" type="audio/mpeg" alt="No audio found">
                                Your browser does not support the audio element.
                            </audio>
                            <?php
                        endif;
                    endif;
                endif;
            endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    function reviewTemplate($review)
    {
        ob_start();
        ?>
        <div class="review">
            <h2>Reviews:</h2>
            <br>
            <?php

            foreach ($review as $key => $value) {

                ?>
                <p class="reviewCard"><?= $value['review'] ?> </p>
                <br>
                <?php
            }
            ?>
        </div>
        <?php
        return ob_get_clean();
    }
    function pagination($startPoint, $total)
    {

        ob_start();
        $prevStart = max(0, $startPoint - 5);
        $nextStart = $startPoint + 5;
        ?>
        <div class="pagination">
            <?php if ($startPoint > 0): ?>
                <button><a href="index.php?page=story&startPoint=<?= $prevStart ?>">Previous</a></button>
            <?php endif; ?>

            <?php if ($nextStart < $total): ?>
                <button><a href="index.php?page=story&startPoint=<?= $nextStart ?>">Next</a></button>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    ?>
</div>