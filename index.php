<?php
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>
<?php include_once "pages/header.php" ?>

<?php include_once "pages/nav.php" ?>


<main class="page">
    <?php
    $page = isset($_GET['page']) ? $_GET['page'] : 'home';
    include_once "pages/" . $page . ".php";
    ?>
</main>

<?php include_once "pages/footer.php" ?>


<!-- testing for git history change file -->