<?php
    require_once "conn/submission.php";
    use function submission\deleteSubmission;
    $id = (int)$_GET['id'];

    echo ''.$id.'';

    $message = null;

    
    $message = deleteSubmission($id);
    
    header(header: "Location: ../dashboard.php?page=main&message=$message");
?>

