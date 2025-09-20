<?php
    require "conn/conn.php";
    use function conn\closeDatabaseConnection;
    use function conn\openDatabaseConnection;


    $tittle = $_POST["title"];
    $concern = $_POST["questions"];
    $why_this_app = $_POST["why_this_app"];
   $disability = isset($_POST["disability"]) && $_POST["disability"] !== "" ? $_POST["disability"] : "no value";
    $review = "FALSE";
    $review_id = "0";

    $conn = openDatabaseConnection();

    if(!$conn) {
         echo "<p>Unable to connect to the database</p>";
    }

    if($conn){
        $sql = "INSERT INTO submission (title, concern, disability, why_this_app,review,review_id ) VALUES (?,?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        if(!$stmt) {
            // log this
            echo "<p>Fail to bind the querry </p>";
            die("Preparing Fail. Sql querry did not match the table or something happen");
        }
        $stmt->bind_param("ssssss", $tittle,$concern,$why_this_app,$disability,$review,$review_id);
        if($stmt->execute()) {
            $insert_id = $conn->insert_id;
            echo "<p>Your reference id is  " . $insert_id ."</p>";
            echo "<br>";
            echo "<p>Add layout to display the reference output<p/>";
        } else { 
            //log this
            echo "<p>The execution of sql fail<p>";
        }
        $stmt->close();
        closeDatabaseConnection($conn);
    }
    
?>