<?php
namespace seed;

require_once 'userSeeder.php';
require_once 'submissionSeeder.php';
require_once 'reviewSeeder.php';
require_once __DIR__ . '/../utils.php';
require_once __DIR__ . '/../conn.php';

use function conn\openDatabaseConnection;
use function conn\closeDatabaseConnection;

function clearAllTables() {
    $conn = openDatabaseConnection();
    if (!$conn) return false;

    $tables = ['reviews','submission','users'];
    foreach ($tables as $table) {
        $conn->query("DELETE FROM $table");
        $conn->query("ALTER TABLE $table AUTO_INCREMENT=1");
    }

    closeDatabaseConnection($conn);
    return true;
}

// Run seeding
clearAllTables();
seedUsers();
seedSubmissions();
seedReviews();

echo "Database seeded successfully.\n";
?>
