document.addEventListener('DOMContentLoaded', function () {
    const cards = document.querySelectorAll('.card');

    // Hide all forms initially
    cards.forEach(card => {
        const form = card.querySelector('form');
        const successMessage = card.querySelector('.sucessMessage');
        const deletedMessage = card.querySelector('.deletedMessage');
        if (form) form.classList.add('Zero');
        if (successMessage) successMessage.style.display = 'none';
        if (deletedMessage) deletedMessage.style.display = 'none';
    });

    // Click listeners
    document.addEventListener('click', function (e) {
        const target = e.target;
        console.log(target);
        // Open edit form
        if (target.classList.contains('openEditPermission')) {
            const card = target.closest('.card');

            // Close all other forms
            cards.forEach(c => {
                const f = c.querySelector('form');
                const msg = c.querySelector('.sucessMessage');
                if (f) f.classList.add('Zero');
                if (msg) msg.style.display = 'none';
            });

            // Open this one
            const form = card.querySelector('form');
            form.classList.remove('Zero');

            // Prevent outside click from closing it instantly
            e.stopPropagation();
            return;
        }

        // Close button clicked
        if (target.classList.contains('closeEditPermission')) {
            console.log("htis is on ")
            const card = target.closest('.card');
            const form = card.querySelector('form');
            if (form) form.classList.add('Zero');
            e.stopPropagation();
            return;
        }

        // Delete user
        if (target.classList.contains('deleteUserButton')) {
            const button = target;
            const card = button.closest('.card');
            const form = card.querySelector('form');
            const successMessage = card.querySelector('.sucessMessage');
            const deletedMessage = card.querySelector('.deletedMessage');
            const userId = button.dataset.id;

            if (!button.dataset.deleted) {
                if (!confirm("Are you sure you want to delete this user?")) return;

                fetch('dashboard/deleteUser.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'id=' + encodeURIComponent(userId)
                })
                    .then(async r => {
                        const text = await r.text(); // read raw text
                        console.log('Raw response:', text); // log what PHP really sends
                        try {
                            return JSON.parse(text); // attempt to parse manually
                        } catch (e) {
                            console.error('JSON parse error:', e);
                            throw e;
                        }
                    })
                    .then(data => {
                        if (data.success) {
                            if (form) form.classList.add('Zero');
                            if (successMessage) successMessage.style.display = 'none';
                            if (deletedMessage) {
                                deletedMessage.style.display = 'block';
                                deletedMessage.textContent = data.message || 'User deleted.';
                            }
                            button.textContent = 'Remove';
                            button.dataset.deleted = 'true';
                        } else {
                            alert(data.message || 'Delete failed.');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Failed to delete user.');
                    });

                e.stopPropagation();
                return;
            } else {
                card.remove();
            }
        }
    });

    // AJAX Form submission
    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (form.closest('.card')) {
            e.preventDefault();

            const card = form.closest('.card');
            const successMessage = card.querySelector('.sucessMessage');
            const deletedMessage = card.querySelector('.deletedMessage');
            const formData = new FormData(form);

            fetch('dashboard/updatePermission.php', {
                method: 'POST',
                body: formData
            })
                .then(async r => {
                    const text = await r.text(); // read raw text
                    console.log('Raw response:', text); // log what PHP really sends
                    try {
                        return JSON.parse(text); // attempt to parse manually
                    } catch (e) {
                        console.error('JSON parse error:', e);
                        throw e;
                    }
                })
                .then(data => {
                    form.classList.add('Zero');
                    if (successMessage) {
                        successMessage.textContent = data.message || 'Updated.';
                        successMessage.style.display = 'block';
                        successMessage.style.color = data.success ? 'green' : 'red';
                    }
                    if (deletedMessage) {
                        deletedMessage.style.display = 'none';
                    }
                })
                .catch(err => {
                    console.error(err);
                    if (successMessage) {
                        successMessage.textContent = 'Update failed.';
                        successMessage.style.display = 'block';
                        successMessage.style.color = 'red';
                    }
                });
        }
    });

    // Outside click closes forms
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.card')) {
            cards.forEach(card => {
                const form = card.querySelector('form');
                const msg = card.querySelector('.sucessMessage');
                if (form) form.classList.add('Zero');
                if (msg) msg.style.display = 'none';
            });
        }
    });
});
