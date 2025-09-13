console.log("Js is working on main.js")
const nav = document.getElementById('nav');
const hamburger = document.getElementById('hamburger');
hamburger.addEventListener('click',()=>{
    nav.classList.toggle('navShow');
    console.log(nav)
    hamburger.classList.toggle('closeBar');
    console.log(hamburger)
    
})
const closeMenu = document.getElementById('closeMenu');
closeMenu.addEventListener('click',()=>{
    nav.classList.toggle('navShow');
    hamburger.classList.toggle('closeBar');


})
