<?php
    require_once "conn/submission.php";
    use function submission\deleteSubmission;
    $id = (int)$_GET['id'];
    $startPoint = (int)$_GET['startPoint'];
    


    echo ''.$id.'';

    $message = null;

    
    $message = deleteSubmission($id);
    
    header(header: "Location: ../dashboard.php?page=main&message=$message&startPoint=$startPoint");
?>

