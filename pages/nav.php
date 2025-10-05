<?php
    
?>


<div class="nav" id="nav">
    <ul>
        <li class= "<?=$page == 'home' ? 'active' : null ?>"><a href="index.php?page=home">Home</a></li>
        <li class= "<?=$page == 'submission'|| $page=='submissionSubmit' ? 'active' : null ?>"><a href="index.php?page=submission">Submission</a></li>
        <li class= "<?=$page == 'about' ? 'active' : null ?>"><a href="index.php?page=about">About Us</a></li>
        <li class= "<?=$page == 'story' ? 'active' : null ?>"><a href="index.php?page=story">Story review</a></li>
        <li class= "<?=$page == 'recent' ? 'active' : null ?>"><a href="index.php?page=recent">Recent Review</a></li>
        <li class= "<?=$page == 'submission' ? 'active' : null ?>" id="closeMenu"><p class="red">Close Menu</p></li>
    </ul>
</div>




