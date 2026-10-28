const menuBtn = document.querySelector(".menu-button");
const menu = document.querySelector(".menu");
const mainEl = document.querySelector(".main-el");
const accountPopup = document.getElementById("account-popup");
const popup = document.querySelector('.modal');
const modalClose = document.querySelector('.modal-close');

menuBtn.addEventListener("click", ()=>{
    // stores the boolean result of the toggle
    const isOpen = menu.classList.toggle("show");

    // evaluates the result
    if(isOpen){
        menuBtn.setAttribute('src', "../assets/images/close.png");
        menu.setAttribute('aria-expanded', isOpen);
        mainEl.classList.add("dim");
    }
    else{
        menuBtn.setAttribute('src', "../assets/images/Menu.png");
        menu.setAttribute('aria-expanded', isOpen);
        mainEl.classList.remove('dim')
    }
});

// click functionality so that the menu closes without the menu click
// button event
mainEl.addEventListener("click", ()=>{
    if(menu.classList.contains("show")){
        menu.classList.remove("show");
        menuBtn.setAttribute("src", "../assets/images/Menu.png");
        mainEl.classList.remove("dim");
        menu.setAttribute('aria-expanded', false)
    }
});

// displaying an acount popover
accountPopup.addEventListener("click", ()=>{
    popup.classList.add('show');
    
    if(menu.classList.contains('show')){
        menu.classList.remove('show');
        menuBtn.setAttribute('src', "../assets/images/Menu.png");
        mainEl.classList.remove("dim");
        menu.setAttribute('aria-expanded', false)
    }
    mainEl.classList.add('dim');
});

modalClose.addEventListener("click", ()=>{
    if(popup.classList.contains("show")){
        popup.classList.remove('show');
        mainEl.classList.remove("dim");
    }
});