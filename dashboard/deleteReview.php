<?php
    require_once './conn/user.php';
    require_once './conn/review.php';
    use function review\deleteReview;
    use function user\user_canDeleteReview;

    if(isset($_Post['id'])){
        $permissionToDelete = user_canDeleteReview($_session['id']);
        if($permissionToDelete){
            $deleteReview = deleteReview($_Post['id']);
            return "Sucessful";
        }else{
            return "Denied";
        }
    }
?>