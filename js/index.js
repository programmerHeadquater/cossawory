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




//adjust the text area height to auto increase but maintain 200px min
document.querySelectorAll('textarea').forEach(textarea => {
  textarea.style.overflow = 'hidden';
  textarea.style.resize = 'none';
  textarea.style.minHeight = '50px';

  function autoResize() {
    textarea.style.height = 'auto';
    textarea.style.height = Math.max(textarea.scrollHeight, 30) + 'px';
  }

  textarea.addEventListener('input', autoResize);
  autoResize();
});