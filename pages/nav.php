<?php
    
?>


<nav class="nav" id="nav">
    <ul>
        <li class= "<?=$page == 'home' ? 'active' : null ?>"><a href="index.php?page=home">Home</a></li>
        <li class= "<?=$page == 'submission'|| $page=='submissionSubmit' ? 'active' : null ?>"><a href="index.php?page=submission">Submit a New Request</a></li>
        <li class= "<?=$page == 'story' ? 'active' : null ?>"><a href="index.php?page=story">View All Submissions</a></li>
        <li class= "<?=$page == 'storySearch' ? 'active' : null ?>"><a href="index.php?page=storySearch">Search Submissions</a></li>
        <li class= "<?=$page == 'caseStudies' ? 'active' : null ?>"><a href="index.php?page=caseStudies">View Case Studies</a></li>
        <li class= "<?=$page == 'about' ? 'active' : null ?>"><a href="index.php?page=about">About Us</a></li>
    </ul>
</nav>




