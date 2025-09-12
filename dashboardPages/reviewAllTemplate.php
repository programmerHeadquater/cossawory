<?php
function reviewAll($row) {
    return '
    <div class="reviewAll">
        <h2>' . htmlspecialchars($row['title']) . '</h2>
        <br>
        <div class="option">
            <span>ID: ' . htmlspecialchars($row['id']) . '</span>
            <span>Status: Pending</span>
            <span><a href="#">Review Now</a></span>
        </div>
    </div>';
}
?>