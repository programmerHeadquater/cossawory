console.log("Js is working on main.js")
const nav = document.getElementById('nav');
const hamburger = document.getElementById('hamburger');
hamburger.addEventListener('click',()=>{
    nav.classList.toggle('navShow');
    hamburger.classList.toggle('closeBar');
    
})
const closeMenu = document.getElementById('closeMenu');
closeMenu.addEventListener('click',()=>{
    nav.classList.toggle('navShow');
    hamburger.classList.toggle('closeBar');


})


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
