

<div class="page">



    <button class="back"><a href="dashboard.php?page=main">back</a></button>

    <?php

    require_once 'conn/review.php';
    require_once 'conn/submission.php';
    use function review\getReviewBySubmissionId;
    use function submission\getSubmissionById;
    use function review\getTotalReviewBySubmissionId;
    use function review\insertReview;




    $id = (int) $_GET['id'];
    $startPoint = isset($_GET['startPoint']) ? (int) $_GET['startPoint'] : 0;

    //updating if the querry has data in the form 
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review']) && $_POST['review'] !== "") {

        $review = $_POST['review'];
        $user_id = $_SESSION['user_id'];
        insertReview($id,$user_id, $review);

    }

    addReviewTemplate($id);
    submissionTemplate(getSubmissionById($id)['data']??null);
    reviewTemplate(getReviewBySubmissionId($id, $startPoint)['data']??null, $id, $startPoint);
    pagination($startPoint, getTotalReviewBySubmissionId($id)['data']??null, $id);






    ?>

    <?php
    function submissionTemplate($submission)
    {
        
                   
                
        ob_start();
        ?>

        <div class="reviewAll">
            <h2>Submited Query</h2>
            <br>
            <?php
            $form_data = json_decode($submission['form_data'], true);
            foreach ($form_data as $index => $field): ?>
               
                    <p class="label"><?= $field['label'] ?> </p>

                    <?php if ($field['type'] == 'text' || $field['type'] == 'textarea'): ?>
                        <p><?= $field['value'] ?></p>
                    <?php endif ?>
                    <?php if ($field['type'] == 'file' || $field['type'] == 'audio' ): ?>

                        <?php if (is_array($field['value'])): ?>
                            
                            <?php if ($field['value']['type'] == 'image/png' || $field['value']['type'] == 'image/jpeg'): ?>
                                <img class="uploadImg" src="<?= $field['value']['path'] ?>" alt="no image found">
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
            endforeach;?>
        </div>


        <?php
        echo ob_get_clean();
        return;
    }


    function reviewTemplate($review, $id, $starPoint)
    {

        ob_start();
        ?>
        <h2>Reviews:</h2>
        <?php
        foreach ($review as $key => $value) {
            ?>
            <div class="reviewTemplate">

                <p class="on"><?= $value['review'] ?> </p>
                <p>
                    <button class="updateBtn ">Update</button>
                    <button class="deleteBtn">Delete</button>

                </p>
                <div class="updatReview Zero">
                    <form action="dashboard/updateReview.php?startPoint=<?= $starPoint ?>&id=<?= $id ?>" method="POST">
                        <input type="hidden" name="review_id" value="<?= $value['id'] ?>">
                        <input type="hidden" name="id" value="<?= $id ?>">
                        <textarea class="textfeild" name="review" id=""><?= $value['review'] ?></textarea>
                        <button type="submit">Update this</button>
                    </form>
                </div>
                <div class="deleteReview Zero">
                    <p>Are you sure</p>
                    <form action="dashboard/deleteReview.php?startPoint=<?= $starPoint ?>&id=<?= $id ?>" method="POST">
                        <input type="hidden" name="review_id" value="<?= $value['id'] ?>">
                        <button type="submit">Yes</button>
                    </form>
                </div>


            </div>

            <?php
        }
        echo ob_get_clean();
        return;
    }
    function addReviewTemplate($id)
    {

        ob_start(); ?>
        <div id="addReviewTemplate">
            <div id="addNewReviewBtnWrapper">
                <button class="greenBtn" id="addNewReviewBtn">Add review</button>
            </div>

            <form id="addNewReview" method="POST" action="dashboard.php?page=reviewSingle&id=<?= $id ?>">
                <div id="addNewReviewContent">
                    <h2>Type new review below:</h2>
                    <textarea name="review" placeholder="Write a reiew here"></textarea>
                    <br><br>
                    <button class="greenBtn" type="submit">Submit</button>
                    <button class="deleteBtn" type="button" id="cancelReviewBtn">Cancel</button>
                </div>
            </form>
        </div>

        
        <?php
        echo ob_get_clean();
    }

    function pagination($startPoint, $total, $id)
    {

        $prevStart = max(0, $startPoint - 20);
        $nextStart = $startPoint + 20;
        ?>
        <div class="pagination">
            <?php if ($startPoint > 0): ?>
                <button><a
                        href="dashboard.php?page=reviewSingle&id=<?= $id ?>&startPoint=<?= $prevStart ?>">Previous</a></button>
            <?php endif; ?>

            <?php if ($nextStart < $total): ?>
                <button><a href="dashboard.php?page=reviewSingle&id=<?= $id ?>&startPoint=<?= $nextStart ?>">Next</a></button>
            <?php endif; ?>
        </div>
        <?php
        echo ob_get_clean();
    }

    ?>



</div>