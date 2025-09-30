<?php
require_once 'conn/user.php';
use function User\user_canAddUser;
use function User\user_canDeleteUser;
?>
<div class="page">
    <div class="userNav">
        <a href="dashboard.php?page=users">All Users</a>
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
    <?= userTemplate([1, 2, 3]) ?>
    <?= pagination(1, 30) ?>

</div>


<?php

function pagination($startPoint, $total)
{
    ob_start();
    $prevStart = max(0, $startPoint - 2);
    $nextStart = $startPoint + 2;
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

function userTemplate($data)
{
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
        foreach ($data as $key => $value) {
            ?>
            <br>
            <div class="card">
                <div class="list">
                <span>admin</span>
                <span>firstlastgug@gmail.com</span>
                <span><button class="edit">Edit</button></span>
                <span>Delete</span>
            </div>
            <br>
            <div class="editPermision ">
                <form action="" class="">

                    <label for="Email">ID :</label>
                    <input type="text" value="id">
                    <br>
                    <label for="Email">ID :</label>
                    <input type="text" value="id">
                    <br>
                    <label for="Email">ID :</label>
                    <input type="text" value="id">
                    <br>
                    <label for="Email">ID :</label>
                    <input type="text" value="id">
                    <br>
                    <label for="Email">ID :</label>
                    <input type="text" value="id">
                    <br>
                    <label for="Email">ID :</label>
                    <input type="text" value="id">
                    <br>
                    <label for="Email">ID :</label>
                    <input type="text" value="id">
                    <br>
                    <label for="Email">ID :</label>
                    <input type="text" value="id">
                    <br>
                    
                </form>
            </div>
            </div>

            <br>

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