<?php
namespace seed;

require_once __DIR__ . '/../conn.php';
require_once __DIR__ . '/../utils.php';

use function conn\openDatabaseConnection;
use function conn\closeDatabaseConnection;
use function utils\logError;


function seedReviews() {
    $conn = openDatabaseConnection();
    if (!$conn) return false;

    $reviews = [
        ['submission_id'=>1, 'user_id'=>1, 'review'=>'Excellent submission'],
        ['submission_id'=>1, 'user_id'=>2, 'review'=>'Needs improvement'],
        ['submission_id'=>2, 'user_id'=>2, 'review'=>'Average quality'],
        ['submission_id'=>3, 'user_id'=>1, 'review'=>'Already reviewed']
    ];

    $stmt = $conn->prepare("INSERT INTO reviews (submission_id, user_id, review) VALUES (?,?,?)");

    foreach ($reviews as $r) {
        $stmt->bind_param('iis', $r['submission_id'], $r['user_id'], $r['review']);
        if (!$stmt->execute()) logError("Failed to insert review: ".$stmt->error);
    }

    $stmt->close();
    closeDatabaseConnection($conn);
}
?>
