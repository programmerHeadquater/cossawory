<?php
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>
<?php include_once "pages/header.php" ?>

<?php include_once "pages/nav.php" ?>


<div class="page">
    <?php
    $page = isset($_GET['page']) ? $_GET['page'] : 'submission';
    include_once "pages/" . $page . ".php";
    ?>
</div>

<?php include_once "pages/footer.php" ?>