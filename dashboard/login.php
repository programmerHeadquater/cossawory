<?php
// Show login error if any
$error = $_GET['error'] ?? '';
?>


<div class="login-container">
    <h2>Login</h2>
    <br>
    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
        <br>
    <?php endif; ?>

    <form method="post" action="dashboard/process_login.php">
        <label for="email">Email</label>
        <br>
        <br>
        <input type="text" name="email" placeholder="email" required autofocus />
        <br>
        <br>
        <label for="password">Password</label>
        <br>
        <br>
        <input type="password" name="password" placeholder="Password" required />
        <br>
        <br>
        <button class="blueBtn" type="submit">Log In</button>
    </form>
     
</div>

</body>
</html>
