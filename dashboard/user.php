<div class="page">
    <?php
    require_once 'conn/user.php';
    use function User\user_canAddUser;
    use function User\user_canDeleteUser;
    use function user\user_getTotaluser;
    use function user\user_getUsers;
    
    ?>

    <br>
    <div class="userNav">
        <a class="active" href="dashboard.php?page=user">All Users</a>
        <a href="dashboard.php?page=addUser">Add User</a>
    </div>
    <br>
    <div class="userSearch">
        <form method="POST" action="">
            <input type="hidden" name="page" value="users">
            <input type="text" name="search" placeholder="Search users by email / id" style="padding: 5px;">
            <button type="submit" class="btn btn-success">Search</button>
        </form>
    </div>
    <br>
    <?php
    echo userTemplate();
    echo pagination();
    ?>

</div>


<?php

function pagination()
{

    $startPoint = isset($_GET['startPoint']) ? (int) $_GET['startPoint'] : 0;
    $total = user_getTotaluser();

    ob_start();
    $prevStart = max(0, $startPoint - 20);
    $nextStart = $startPoint + 20;
    ?>
    <div class="pagination">
        <?php if ($startPoint > 0): ?>
            <button><a href="dashboard.php?page=user&startPoint=<?= $prevStart ?>">Previous</a></button>
        <?php endif; ?>

        <?php if ($nextStart < $total): ?>
            <button><a href="dashboard.php?page=user&startPoint=<?= $nextStart ?>">Next</a></button>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

function userTemplate()
{
    $userList = user_getUsers(isset($_GET['startPoint']) ? (int) $_GET['startPoint'] : 0);

    ob_start();
    ?>
    <div class="userTemplate">
        <div class="userListHeader">
            <span>UserName</span>
            <span>Email</span>
            <span>Permision</span>
            <span>Action</span>
        </div>
        <?php
        foreach ($userList as $key => $user) {

            ?>
            <br>

            <div class="card">
                <div class="list">
                    <span><?= $user['username'] ?> </span>
                    <span><?= $user['email'] ?></span>
                    <span><button class="openEditPermission">Edit</button></span>
                     <span><button class="deleteUserButton" data-id="<?= $user['id'] ?>">Delete</button></span>
                </div>
                
                <div class="editPermision">
                    <p class="sucessMessage zero" style="display: none;">Here</p>
                    <h2 class="deletedMessage" style="display: none;">User deleted successfully.</h2>
                    <form class="" action="dashboard.php?page=user" method="POST" autocomplete="off">
                        <br>
                        <hr>
                        <br>
                        <h2>Edit Permissions for <?= $user['username'] ?> </h2><br>
                        <input type="hidden" name="id" value="<?= htmlspecialchars($user['id'] ?? '') ?>">

                        <label for="can_delete_submission">Can delete submission:</label>
                        <select name="can_delete_submission" required>
                            <option value="Yes" <?= $user['can_delete_submission'] === 1 ? 'selected' : '' ?>>Yes</option>
                            <option value="No" <?= $user['can_delete_submission'] === 0 ? 'selected' : '' ?>>No</option>
                        </select>
                        <br><br>
                        <label for="can_write_review">Can write review:</label>
                        <select name="can_write_review">
                            <option value="Yes" <?= $user['can_write_review'] === 1 ? 'selected' : '' ?>>Yes</option>
                            <option value="No" <?= $user['can_write_review'] === 0 ? 'selected' : '' ?>>No</option>
                        </select>

                        <br><br>

                        <label for="can_delete_review">Can delete / edit review:</label>
                        <select name="can_delete_review">
                            <option value="Yes" <?= $user['can_delete_review'] === 1 ? 'selected' : '' ?>>Yes</option>
                            <option value="No" <?= $user['can_delete_review'] === 0 ? 'selected' : '' ?>>No</option>
                        </select>
                        <br><br>

                        <label for="can_add_user">Can add user:</label>
                        <select name="can_add_user">
                            <option value="Yes" <?= $user['can_add_user'] === 1 ? 'selected' : '' ?>>Yes</option>
                            <option value="No" <?= $user['can_add_user'] === 0 ? 'selected' : '' ?>>No</option>
                        </select>
                        <br><br>

                        <label for="can_delete_user">Can delete user:</label>
                        <select name="can_delete_user">
                            <option value="Yes" <?= $user['can_delete_user'] === 1 ? 'selected' : '' ?>>Yes</option>
                            <option value="No" <?= $user['can_delete_user'] === 0 ? 'selected' : '' ?>>No</option>
                        </select>
                        <br><br>

                        <button type="submit">Submit</button>
                        <button type="button" class="closeEditPermission">Close</button>
                    </form>
                </div>
            </div>

            <?php
        }

        ?>

    </div>
    <br>
    <br>
    <?php
    return ob_get_clean();
}

?>