<?php

    namespace review;
    require_once 'conn.php';
    use function conn\closeDatabaseConnection;
    use function conn\openDatabaseConnection;

   function updateReview($review_id,$review){
    $conn = openDatabaseConnection();
    $stmt = $conn->prepare('insert into ');
    $stmt = $conn->prepare('UPDATE reviews SET review = ? WHERE id = ?');
    if (!$stmt) {
        $message = "Prepare failed: " . $conn->error . "<br>";
        closeDatabaseConnection($conn);
        return $message;
    }
    if (!$stmt->bind_param('si', $review, $review_id)) {
        $message = "Bind failed: " . $stmt->error . "<br>";
        $stmt->close();
        closeDatabaseConnection($conn);
        return $message;
    }
    if (!$stmt->execute()) {
        $message = "Execute failed: " . $stmt->error . "<br>";
        $stmt->close();
        closeDatabaseConnection($conn);
        return $message;
    }
    $affectedRows = $stmt->affected_rows;
    $message = "Query executed. Affected rows: $affectedRows<br>";
    $stmt->close();
    closeDatabaseConnection($conn);

    if($affectedRows === 0) {
        $message = "No rows were updated. Either the ID does not exist, or the review value is the same.<br>";
    } else {
        $message = "The review was updated successfully.<br>";
    }
    
    return $message;
}

function insertReview($id, $reviewFormData){
    $review_id = null;
    $error = null;
    $conn = openDatabaseConnection();
    $stmt = $conn->prepare("INSERT INTO  reviews (submission_id , review ) VALUES ( ?,?)");

    if (!$stmt) {
        $error =  "Fail to log the data :".$stmt->error;
    }

    if (!$stmt->bind_param("is",$id, $reviewFormData)){
        $error = "Binding fail:". $stmt->error;
    }

    if(!$stmt->execute()) {
        $error = "Execution Fail:". $stmt->error;
    }

    $review_id = $stmt ->insert_id;
    $stmt->close();
    return [$review_id,$error];
}


// this will remove the data of review table full row
function deleteReview($review_id){
    $error = null;
    $conn = openDatabaseConnection();
    $stmt = $conn->prepare("DELETE * FROM review WHERE id = ?");
    if(!$stmt){
        $error = "Statement fail : ". $stmt->error;
    }
    if(!$stmt -> bind_param( "i",$review_id)){
        $error = "Binding fail : " . $stmt->error;
    }
    if(!$stmt -> execute()){
        $error = "Execution Fail: " . $stmt->error;
    }
    
    return $error;
}
function insertReviewIdIntoSubmission($review_id){
    $error = null;
    $conn = openDatabaseConnection();
    if(!$stmt = $conn->prepare("UPDATE submission SET review = 1 , review_id = ?")){
        $error = "Statment prepare error : ". $stmt->error;
    }
    if(!$stmt ->bind_param("i",$review_id)){
        $error = "Binding error: ". $stmt->error;
    }
    if(!$stmt -> execute()){
        $error = "Execution fail :". $stmt->error;
    }
    return $error;
}
?>