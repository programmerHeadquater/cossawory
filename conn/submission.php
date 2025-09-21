<?php

namespace submission;
require_once("conn/conn.php");
use function conn\closeDatabaseConnection;
use function conn\openDatabaseConnection;



function insertSubmission($title, $concern, $why_this_app, $disability, $review, $review_id)
{
    $message = null;
    $conn = openDatabaseConnection();

    if (!$conn) {
        $message = "<p>Unable to connect to the database</p>";
    }

    if ($conn) {
        $sql = "INSERT INTO submission (title, concern, disability, why_this_app,review,review_id ) VALUES (?,?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            // log this
            $message = "<p>Fail to bind the querry </p>";
            die("Preparing Fail. Sql querry did not match the table or something happen");
        }
        $stmt->bind_param("ssssss", $title, $concern, $why_this_app, $disability, $review, $review_id);
        if ($stmt->execute()) {
            $insert_id = $conn->insert_id;
            $message = "<p>Your reference id is  " . $insert_id . "</p> <br> <p>Add layout to display the reference output</p>";
            
        } else {
            //log this
            $message = "<p>The execution of sql fail<p>";
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
function deleteSubmission($id): string
{
    echo "<pre>";
    var_dump("$id");
    echo $id;
    echo "</pre>";
    $message = null;
    $conn = openDatabaseConnection();
    $stmt = $conn->prepare("DELETE FROM submission WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $message = ($stmt->affected_rows > 0) ? "yes" : "no";
    $stmt->close();
    echo "<br>message : $message <br>";
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
?>