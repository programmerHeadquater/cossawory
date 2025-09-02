<?php
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>
<?php include_once "pages/header.php" ?>
<?php include_once "pages/nav.php" ?>


<div class="page">
    <?php include_once "pages/content.php" ?>
</div>

<?php include_once "pages/footer.php" ?>