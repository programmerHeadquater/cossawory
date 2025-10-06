console.log("Js is working on main.js")



// this is a menu toggle 
const nav = document.getElementById('nav');
const hamburger = document.getElementById('hamburger');


hamburger.addEventListener('click', () => {
  nav.classList.toggle('navShow');
  hamburger.classList.toggle('closeBar');

  const isExpanded = hamburger.getAttribute('aria-expanded') === 'true';
  hamburger.setAttribute('aria-expanded', String(!isExpanded));
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