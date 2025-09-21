<?php
session_name("MySecureAppSession");
session_start();
include("dashboard/header.php");
$page = isset($_GET["page"]) ? $_GET["page"] : "main";


?>
<div class="main">
    <?php
    // Check if user is logged in, e.g., if 'user_id' is stored in session
    if (isset($_SESSION['user_id'])) {
        // User logged in, include the main dashboard or homepage
        include 'dashboard/nav.php';
        include 'dashboard/'.$page.'.php';
    } else {
        // User not logged in, include login page
        session_destroy();
        include 'dashboard/login.php';
    }
    ?>
</div>

<?php




include 'dashboard/footer.php';
?>