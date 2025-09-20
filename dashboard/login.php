<?php
// Show login error if any
$error = $_GET['error'] ?? '';
?>


<div class="login-container">
    <h2>Login</h2>

    <?php if ($error): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="dashboard/process_login.php">
        <input type="text" name="username" placeholder="Username" required autofocus />
        <br>
        <input type="password" name="password" placeholder="Password" required />
        <br>
        <button type="submit">Log In</button>
    </form>

    <div class="create-account">
        No account? <a href="register.php">Create one here</a>
    </div>
</div>

</body>
</html>
