<?php
namespace seed;

require_once __DIR__ . '/../conn.php';
require_once __DIR__ . '/../utils.php';

use function conn\openDatabaseConnection;
use function conn\closeDatabaseConnection;
use function utils\logError;




function seedSubmissions() {
    $conn = openDatabaseConnection();
    if (!$conn) return false;

    $submissions = [
        ['form_data'=>json_encode(['field1'=>'data1']), 'review'=>0],
        ['form_data'=>json_encode(['field1'=>'data2']), 'review'=>0],
        ['form_data'=>json_encode(['field1'=>'data3']), 'review'=>1]
    ];

    $stmt = $conn->prepare("INSERT INTO submission (form_data, review) VALUES (?,?)");

    foreach ($submissions as $s) {
        $stmt->bind_param('si', $s['form_data'], $s['review']);
        if (!$stmt->execute()) logError("Failed to insert submission: ".$stmt->error);
    }

    $stmt->close();
    closeDatabaseConnection($conn);
}
?>
