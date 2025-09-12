<?php
require_once("conn/conn.php");
require_once("dashboardPages/reviewAllTemplate.php");
session_start();  // Start the session to track user login status
// Check if the user is already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
    // If logged in, show the dashboard content
    $username = $_SESSION['username'];  // Get the username from session
} else {
    // If not logged in, process login
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Check the credentials (you can replace this with database validation later)
        if ($username == 'test' && $password == 'test') {
            // Successful login, store session variables
            $_SESSION['logged_in'] = true;
            $_SESSION['username'] = $username;  // Store username in session

            // Redirect to the dashboard page to show the content
            header('Location: dashboard.php');
            exit();
        } else {
            // Invalid credentials, show error message
            $error_message = 'Invalid username or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/dashboard.css?v=2">
    <title>Dashboard</title>
</head>

<body>

    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true): ?>
        <!-- User is logged in, show the dashboard -->
        <div class="header">
            <div class="left">
                <img id="birdLogo" src="images/bird.svg" alt="Logo" width="60px" height="60px">
                <p>Cossawary Project Dashboard</p>
            </div>
            <a id="logout" href="dashboardPages/logout.php">Logout</a>
        </div>
        <br>
        <h2 style="margin:10px 22px;">All Querry </h2>
        <br>
        <?php
            $conn = openDatabaseConnection();
            $sql = "SELECT * FROM submission ORDER BY id DESC";
            $result = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_assoc($result)) {
                $format = reviewAll($row);
                echo $format;
            }
        ?>
        






    <?php else: ?>
        <!-- User is not logged in, show login form -->
        <h2>Login to Your Dashboard</h2>
        <form method="POST" action="dashboard.php">
            <input type="text" aria-describedby="this is username" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="submit" value="Login">
        </form>

        <?php
        // Show error message if login fails
        if (isset($error_message)) {
            echo "<p style='color: red;'>$error_message</p>";

        }
        ?>
    <?php endif; ?>
</body>

</html>