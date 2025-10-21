
document.addEventListener("DOMContentLoaded", () => {
  const icon = document.getElementById('logout');
  const accountOption = document.getElementById('accountOption');

  icon.addEventListener('click', () => {
    accountOption.classList.toggle('open');
  });

  // Hide dropdown on outside click
  document.addEventListener('click', (e) => {
    if (!icon.contains(e.target) && !accountOption.contains(e.target)) {
      accountOption.classList.remove('open');
    }
  });

  const nav = document.getElementById('nav');
  const hamburger = document.getElementById('hamburger');

  hamburger.addEventListener('click', () => {
    nav.classList.toggle('navShow');
    hamburger.classList.toggle('closeBar');
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


// tooggle on review page update and delete

document.addEventListener('DOMContentLoaded', () => {
    const reviewTemplates = document.querySelectorAll('.reviewTemplate');

    reviewTemplates.forEach(template => {
        const updateBtn = template.querySelector('.updateBtn');
        const deleteBtn = template.querySelector('.deleteBtn');
        const updateBox = template.querySelector('.updatReview');
        const deleteBox = template.querySelector('.deleteReview');

        // Initialize collapsed state
        collapse(updateBox);
        collapse(deleteBox);

        updateBtn.addEventListener('click', () => {
            const isOpen = !updateBox.classList.contains('Zero');
            if (!isOpen) {
                expand(updateBox);
                collapse(deleteBox);
            } else {
                collapse(updateBox);
            }
        });

        deleteBtn.addEventListener('click', () => {
            const isOpen = !deleteBox.classList.contains('Zero');
            if (!isOpen) {
                expand(deleteBox);
                collapse(updateBox);
            } else {
                collapse(deleteBox);
            }
        });
    });

    function collapse(element) {
        // Set fixed height before collapsing to trigger animation
        element.style.height = element.scrollHeight + 'px';
        element.offsetHeight; // Force reflow
        element.style.transition = 'height 0.5s ease';
        element.style.height = '0px';
        element.classList.add('Zero');

        // Clean up styles after transition
        const onTransitionEnd = () => {
            element.style.transition = '';
            element.removeEventListener('transitionend', onTransitionEnd);
        };
        element.addEventListener('transitionend', onTransitionEnd);
    }

    function expand(element) {
        element.classList.remove('Zero');
        element.style.height = '0px'; // Reset to 0 before expanding
        element.offsetHeight; // Force reflow

        element.style.transition = 'height 0.5s ease';
        element.style.height = element.scrollHeight + 'px';

        const onTransitionEnd = () => {
            element.style.height = 'auto'; // Allow flexible height after animation
            element.style.transition = '';
            element.removeEventListener('transitionend', onTransitionEnd);
        };
        element.addEventListener('transitionend', onTransitionEnd);
    }
});



//adjust the text area height to auto increase but maintain 200px min
document.querySelectorAll('textarea').forEach(textarea => {
  textarea.style.overflow = 'hidden';
  textarea.style.resize = 'none';
  textarea.style.minHeight = '100px';

  function autoResize() {
    textarea.style.height = 'auto';
    textarea.style.height = Math.max(textarea.scrollHeight, 100) + 'px';
  }

  textarea.addEventListener('input', autoResize);
  autoResize();
});






