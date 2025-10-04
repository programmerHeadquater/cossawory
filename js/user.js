document.addEventListener('DOMContentLoaded', function () {
    const editButtons = document.querySelectorAll('.openEditPermission');
    const closeButtons = document.querySelectorAll('.closeEditPermission');
    const permissionCards = document.querySelectorAll('.editPermision');

    // Hide all forms initially
    permissionCards.forEach(card => {
        card.querySelector('form').classList.remove('Zero');
        card.querySelector('.sucessMessage').style.display = 'none';
    });

    editButtons.forEach((button, index) => {
        button.addEventListener('click', function () {
            permissionCards.forEach((card, i) => {
                card.querySelector('form').classList.remove('Zero');
                card.querySelector('.sucessMessage').style.display = 'none';
            });

            const currentCard = permissionCards[index];
            currentCard.querySelector('form').classList.add('Zero');
            currentCard.querySelector('.sucessMessage').style.display = 'none'; // just in case
        });
    });

    closeButtons.forEach((button, index) => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            permissionCards[index].querySelector('form').classList.remove('Zero');
            permissionCards[index].querySelector('.sucessMessage').style.display = 'none';
        });
    });

    // AJAX form submission
    permissionCards.forEach((card, index) => {
        const form = card.querySelector('form');
        const successMessage = card.querySelector('.sucessMessage');

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(form);

            fetch('dashboard/updatePermission.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hide form, show success message
                    form.classList.remove('Zero');
                    successMessage.textContent = data.message;
                    successMessage.style.display = 'block';
                    successMessage.style.color = 'green';
                } else {
                    form.classList.remove('Zero');
                    successMessage.textContent = data.message;
                    successMessage.style.display = 'block';
                    successMessage.style.color = 'red';
                }
            })
            .catch(error => {
                console.error('AJAX Error:', error);
                alert("Failed to update permissions.");
            });
        });
    });
});


document.addEventListener('DOMContentLoaded', function () {
    const deleteButtons = document.querySelectorAll('.deleteUserButton');

    deleteButtons.forEach(button => {
        let deleted = false;

        button.addEventListener('click', function () {
            const card = button.closest('.card');
            const form = card.querySelector('form');
            const successMessage = card.querySelector('.deletedMessage');
            const userId = button.dataset.id;

            // ✅ First click: confirm + AJAX delete
            if (!deleted) {
                const confirmed = confirm("Are you sure you want to delete this user?");
                if (!confirmed) return; // User canceled

                fetch('dashboard/deleteUser.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'id=' + encodeURIComponent(userId)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (form) form.remove();
                        if (successMessage) successMessage.style.display = 'block';
                        button.textContent = 'Remove';
                        deleted = true;
                    } else {
                        alert("Error deleting user: " + (data.message || "Unknown error."));
                    }
                })
                .catch(error => {
                    console.error('AJAX error:', error);
                    alert("Failed to delete user.");
                });
            } 
            // ✅ Second click: remove the entire card
            else {
                card.remove();
            }
        });
    });
});
