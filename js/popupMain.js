const backgroundMain = document.querySelector(".main-el");
const popup = document.querySelector('.modal');
const modalClose = document.querySelector('.modal-close');
const accountMenu = document.querySelector('.account-menu');
const accountIcon = document.getElementById("acc-icon");
const popupButton = document.getElementById("account-popup");
const backgroundFooter = document.querySelector(".footer-el");

// displaying an acount popover
popupButton.addEventListener("click", ()=>{
    const isOpen = accountMenu.classList.contains('show');
    popup.classList.add('show');
    
    if(isOpen){
        accountMenu.classList.remove('show');
        accountIcon.setAttribute("aria-expanded", isOpen);
    }
    backgroundMain.classList.add("dim");
    backgroundFooter.classList.add("dim");
});

// closing the account
modalClose.addEventListener("click", ()=>{
    if(popup.classList.contains("show")){
        popup.classList.remove('show');
        backgroundMain.classList.remove("dim");
        backgroundFooter.classList.remove("dim");
    }
});
