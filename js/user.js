document.addEventListener('DOMContentLoaded', function () {
    const container = document.querySelector('.page');
    // Or use a more specific parent wrapper, e.g. document.querySelector('.userTemplate')

    // Initialize: hide all success and deleted messages
    container.querySelectorAll('.editPermision').forEach(card => {
        const form = card.querySelector('form');
        const msg = card.querySelector('.sucessMessage');
        if (form) form.classList.remove('Zero');
        if (msg) msg.style.display = 'none';
    });

    // Handle clicks (delegated)
    container.addEventListener('click', function (e) {
        const target = e.target;

        // 1) Edit button clicked
        if (target.matches('.openEditPermission')) {
            // Hide all forms / messages
            container.querySelectorAll('.editPermision').forEach(c => {
                const f = c.querySelector('form');
                const m = c.querySelector('.sucessMessage');
                if (f) f.classList.remove('Zero');
                if (m) m.style.display = 'none';
            });

            // Show the correct form in this card
            const card = target.closest('.card');
            if (!card) return;
            const permCard = card.querySelector('.editPermision');
            if (!permCard) return;

            const form = permCard.querySelector('form');
            const msg = permCard.querySelector('.sucessMessage');
            if (form) form.classList.add('Zero');
            if (msg) msg.style.display = 'none';

            return;
        }

        // 2) Close button clicked
        if (target.matches('.closeEditPermission')) {
            e.preventDefault();
            const card = target.closest('.card');
            if (!card) return;
            const permCard = card.querySelector('.editPermision');
            if (!permCard) return;

            const form = permCard.querySelector('form');
            const msg = permCard.querySelector('.sucessMessage');
            if (form) form.classList.remove('Zero');
            if (msg) msg.style.display = 'none';

            return;
        }

        // 3) Delete user button clicked
        if (target.matches('.deleteUserButton')) {
            let button = target;
            const card = button.closest('.card');
            if (!card) return;

            const form = card.querySelector('form');
            const deletedMsg = card.querySelector('.deletedMessage');
            const userId = button.dataset.id;

            // We'll use a custom attribute or closure to track whether deletion was confirmed
            if (!button.dataset.deleted) {
                // First click: confirm
                if (!confirm("Are you sure you want to delete this user?")) {
                    return;
                }

                fetch('dashboard/deleteUser.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'id=' + encodeURIComponent(userId)
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            if (form) form.remove();
                            if (deletedMsg) {
                                deletedMsg.style.display = 'block';
                                deletedMsg.textContent = data.message || 'User deleted successfully.';
                                deletedMsg.style.color = 'green';
                            }
                            button.textContent = 'Remove';
                            button.dataset.deleted = 'true';
                        } else {
                            alert("Error deleting user: " + (data.message || "Unknown error."));
                        }
                    })
                    .catch(err => {
                        console.error('AJAX error:', err);
                        alert("Failed to delete user.");
                    });
            } else {
                // Second click => remove entire card
                card.remove();
            }

            return;
        }
    });

    // Handle form submissions (delegated)
    container.addEventListener('submit', function (e) {
        const target = e.target;
        if (target.matches('.editPermision form')) {
            e.preventDefault();

            const form = target;
            const card = form.closest('.card');
            if (!card) return;
            const permCard = card.querySelector('.editPermision');
            if (!permCard) return;
            const successMessage = permCard.querySelector('.sucessMessage');

            const formData = new FormData(form);

            fetch('dashboard/updatePermission.php', {
                method: 'POST',
                body: formData
            })
                .then(r => r.json())
                .then(data => {
                    // Hide form, show message
                    form.classList.remove('Zero');
                    if (successMessage) {
                        successMessage.textContent = data.message || 'Updated';
                        successMessage.style.display = 'block';
                        successMessage.style.color = data.success ? 'green' : 'red';
                    }
                })
                .catch(err => {
                    console.error('AJAX Error:', err);
                    if (successMessage) {
                        successMessage.textContent = "Failed to update permissions.";
                        successMessage.style.display = 'block';
                        successMessage.style.color = 'red';
                    }
                });
        }
    });
});
