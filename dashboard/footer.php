
<div class="footer">
    Copyright @ 2025. All rights reserved.
</div>



</body>
<script src="js/dashboard.js"></script>
<?php if (isset($_GET['page']) && $_GET['page'] === 'reviewSingle'): ?>
<script src="js/reviewSingle.js"></script>
<?php endif; ?>

<?php if (isset($_GET['page']) && $_GET['page'] === 'user'): ?>
<script src="js/user.js"></script>
<?php endif; ?>
</html>