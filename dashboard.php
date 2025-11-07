<?php
//using PHP 8.2.12 
session_name("MySecureAppSession");
session_start();
include("dashboard/header.php");
require_once "conn/user.php";
use function user\user_getById;
$page = isset($_GET["page"]) ? $_GET["page"] : "main";


?>
<div class="main">
    <?php
    // Check if user is logged in, e.g., if 'user_id' is stored in session
   
    if (isset($_SESSION['user_id']) && user_getById($_SESSION['user_id'])['status']) {
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