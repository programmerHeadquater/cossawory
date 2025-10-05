<?php

namespace submission;
require_once("conn.php");
use function conn\closeDatabaseConnection;
use function conn\openDatabaseConnection;



function insertSubmission($title, $concern, $why_this_app, $disability, $review, $review_id)
{
    $message = null;
    $conn = openDatabaseConnection();

    if (!$conn) {
        $message = [
            "status" =>'fail',
            'message'=>"no connection"
        ];
    }

    if ($conn) {
        $sql = "INSERT INTO submission (title, concern, disability, why_this_app,review,review_id ) VALUES (?,?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            // log this
            $message = [
            "status" =>'fail',
            'message'=>"stmt fail"
        ];
            die("Preparing Fail. Sql querry did not match the table or something happen");
        }
        $stmt->bind_param("ssssss", $title, $concern, $why_this_app, $disability, $review, $review_id);
        if ($stmt->execute()) {
            $insert_id = $conn->insert_id;
            $message = [
            "status" =>'sucess',
            'message'=>"updated",
            'id'=>$insert_id
        ];
            
        } else {
            //log this
            $message = [
            "status" =>'fail',
            'message'=>"execution fail"
        ];
        }
        $stmt->close();
        closeDatabaseConnection($conn);
    }
    return $message;
}
function updateSubmission()
{
    return null;
}
function deleteSubmission($id)  
{
    
    $message = null;
    $conn = openDatabaseConnection();
    $stmt = $conn->prepare("DELETE FROM submission WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $message = ($stmt->affected_rows > 0) ? "yes" : "no";
    $stmt->close();
    closeDatabaseConnection($conn);
    return $message;
}

function getSubmission($startPoint){
    $conn = openDatabaseConnection();
    $stmt = $conn->prepare("SELECT * FROM submission LIMIT 2 OFFSET ?");
    $stmt->bind_param("i", $startPoint);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(\MYSQLI_ASSOC);
    return $data;
}
function getSubmissionsTotalCount(){
    $conn = openDatabaseConnection();
    $stmt = $conn->prepare("SELECT COUNT(*) FROM submission");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return (int)$row["COUNT(*)"];
}
function getSubmissionById(int $id){
   
    $conn = openDatabaseConnection();
    $stmt = $conn->prepare("SELECT * FROM submission WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    return $data;
}
function updateSubmissionReviewStatus($id){
    $conn = openDatabaseConnection();
    $stmt = $conn->prepare("UPDATE submission set review = 1 where id = ?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    $result = $stmt->get_result();
    return null;

}
?>