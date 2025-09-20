

<?php
require_once 'conn/conn.php';
require_once 'dashboard/reviewAllTemplate.php';

use function conn\openDatabaseConnection;
use function conn\closeDatabaseConnection;
use function reviewAllTemplate\reviewAll;

$conn = openDatabaseConnection();
$sql = "SELECT * FROM submission ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $format = reviewAll($row);
    echo $format;
    
}
closeDatabaseConnection($conn);
?>