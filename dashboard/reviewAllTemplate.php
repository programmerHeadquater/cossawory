<?php
namespace reviewAllTemplate;
function reviewAll($row) {
    ob_start();
    ?>


    <div class="reviewAll">
        <h2><?php echo htmlspecialchars($row['title']) ?> 	</h2>
        <br>
        <h3 class="description"><?php echo htmlspecialchars($row['concern']) ?> </h3>
        <br>
        <button>Read more</button>
        <br>
        <br>
        <div class="option">
            <span><?php echo htmlspecialchars($row['id']) ?> </span>
            <span>Status: Pending</span>
            <span><a href="dashboard.php?page=reviewSingle&id=<?=$row['id']?>">Review Now</a></span>
            <span>Delete</span>
            
        </div>
    </div>
<?php

    return ob_get_clean();
}
?>
    


