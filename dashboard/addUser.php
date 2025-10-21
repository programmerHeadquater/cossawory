<?php
require_once 'conn/user.php';
use function user\user_canAddUser;
use function user\user_addNewUser;
?>

<?php if (isset($_GET['page']) && $_GET['page'] === 'addUser'): ?>

    <?php
    // Define expected fields
    $requiredFields = [
        'username',
        'email',
        'password',
        'can_delete_submission',
        'can_write_review',
        'can_delete_review',
        'can_add_user',
        'can_delete_user'
    ];

    $errorMessage = null;
    $successMessage = null;
    $formData = [];

    // Process form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $missingFields = [];

        foreach ($requiredFields as $field) {
            if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
                $missingFields[] = $field;
            } else {
                $formData[$field] = trim($_POST[$field]);
            }
        }

        if (!empty($missingFields)) {
            $errorMessage = "Please fill in the following fields: " . implode(', ', $missingFields);
        } else {
            if (user_canAddUser($_SESSION['user_id'])) {
                user_addNewUser($formData);
                $successMessage = "User added successfully!";

            } else {
                $errorMessage = "Operation fail: No permission to add user for this account.";
                $successMessage = null;
            }
        }
    }
    ?>

    <div class="page">
        <div class="userNav">
            <a href="dashboard.php?page=user">All Users</a>
            <a class="active" href="dashboard.php?page=addUser">Add User</a>
        </div>
        <br>

        <br>

        <?php if ($errorMessage): ?>
            <div style="color: red; font-weight: bold;">
                <?= htmlspecialchars($errorMessage) ?>
            </div>
        <?php elseif ($successMessage): ?>
            <div style="color: green; font-weight: bold;">
                <?= htmlspecialchars($successMessage) ?>
            </div>
            <pre><?php print_r($formData); ?></pre>
        <?php endif; ?>
        <a href="dashboard.php?page=addUser" class="<?= $successMessage !== null ? '' : 'dNone' ?>">Add another user</a href="#">
        <!-- FORM START -->
        <form class="<?= $successMessage !== null ? 'dNone' : '' ?>" action="dashboard.php?page=addUser" method="POST"
            autocomplete="off">
            <label for="username">Username:</label><br>
            <input type="text" name="username" autocomplete="off"
                value="<?= htmlspecialchars($formData['new_username'] ?? '') ?>">
            <br><br>

            <label for="email">Email:</label><br>
            <input type="text" name="email" autocomplete="off" required
                value="<?= htmlspecialchars($formData['email'] ?? '') ?>">
            <br><br>

            <label for="password">Password:</label><br>
            <input type="text" name="password" value="<?= htmlspecialchars($formData['password'] ?? 'password@123') ?>">
            <br><br>

            <h2>Permissions</h2><br>

            <label for="can_delete_submission">Can delete submission:</label>
            <select name="can_delete_submission" required>
                <option value="Yes" <?= (isset($formData['can_delete_submission']) && $formData['can_delete_submission'] === 'Yes') ? 'selected' : '' ?>>Yes</option>
                <option value="No" <?= (!isset($formData['can_delete_submission']) || $formData['can_delete_submission'] === 'No') ? 'selected' : '' ?>>No</option>
            </select>
            <br><br>

            <label for="can_write_review">Can write review:</label>
            <select name="can_write_review">
                <option value="Yes" <?= (isset($formData['can_write_review']) && $formData['can_write_review'] === 'Yes') ? 'selected' : '' ?>>Yes</option>
                <option value="No" <?= (isset($formData['can_write_review']) && $formData['can_write_review'] === 'No') ? 'selected' : '' ?>>No</option>
            </select>
            <br><br>

            <label for="can_delete_review">Can delete / edit review:</label>
            <select name="can_delete_review">
                <option value="Yes" <?= (isset($formData['can_delete_review']) && $formData['can_delete_review'] === 'Yes') ? 'selected' : '' ?>>Yes</option>
                <option value="No" <?= (!isset($formData['can_delete_review']) || $formData['can_delete_review'] === 'No') ? 'selected' : '' ?>>No</option>
            </select>
            <br><br>

            <label for="can_add_user">Can add user:</label>
            <select name="can_add_user">
                <option value="Yes" <?= (isset($formData['can_add_user']) && $formData['can_add_user'] === 'Yes') ? 'selected' : '' ?>>Yes</option>
                <option value="No" <?= (isset($formData['can_add_user']) && $formData['can_add_user'] === 'No') ? 'selected' : '' ?>>No</option>
            </select>
            <br><br>

            <label for="can_delete_user">Can delete user:</label>
            <select name="can_delete_user">
                <option value="Yes" <?= (isset($formData['can_delete_user']) && $formData['can_delete_user'] === 'Yes') ? 'selected' : '' ?>>Yes</option>
                <option value="No" <?= (!isset($formData['can_delete_user']) || $formData['can_delete_user'] === 'No') ? 'selected' : '' ?>>No</option>
            </select>
            <br><br>

            <button class="greenBtn" type="submit">Submit</button>
        </form>
        <!-- FORM END -->

    <?php endif; ?>

</div>