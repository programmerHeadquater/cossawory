document.addEventListener("DOMContentLoaded", () => {
    const icon = document.getElementById('logoutIcon');
    const accountOption = document.getElementById('accountOption');

    icon.addEventListener('click', () => {
        const currentDisplay = getComputedStyle(accountOption).display;
        const isVisible = currentDisplay === 'block';

        console.log(isVisible); // true/false
        console.log(currentDisplay); // actual computed display value

        accountOption.style.display = isVisible ? 'none' : 'block';
    });

    // Hide dropdown on outside click
    document.addEventListener('click', (e) => {
        if (!icon.contains(e.target) && !accountOption.contains(e.target)) {
            accountOption.style.display = 'none';
        }
    });
});


// this is collision check for id
document.querySelectorAll('[id]').forEach(el => {
  const id = el.id;

  if (!id) return;

  const matches = document.querySelectorAll(`#${CSS.escape(id)}`);

  if (matches.length > 1) {
    console.warn(`⚠️ Duplicate ID found: #${id} (${matches.length} times)`);

    // Log all elements with that duplicate ID
    matches.forEach(matchEl => {
      console.log(matchEl);
    });

  } else {
    console.log(`✅ ID is unique: #${id}`);
  }
});



