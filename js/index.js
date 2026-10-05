// Variables
const menuButton = document.querySelector(".menu-button");
const menu = document.querySelector(".menu");
const mainEl = document.querySelector(".main-el");
const footerEl = document.querySelector(".footer-el");
const videoBtn = document.querySelector(".video-btn");
const video = document.querySelector(".video");
const accIcon = document.getElementById("acc-icon");
const accMenu = document.querySelector(".account-menu");
const imagesPath = new URL("../assets/images/", document.currentScript.src);

// Menu functionality
menuButton.addEventListener("click", ()=>{
    // stores the boolean result of the toggle
    const isOpen = menu.classList.toggle("show");

    if(isOpen){
        menuButton.setAttribute("src", new URL("close.png", imagesPath))
        menu.setAttribute("aria-hidden", isOpen);
    }
    else{
        menuButton.setAttribute("src", new URL("Menu.png", imagesPath))
    }

    // switches opacity states
    mainEl.classList.toggle("dim");
    footerEl.classList.toggle("dim");
});

// Click functionality so that the menu closes without menu button event
mainEl.addEventListener("click", ()=>{
    if(menu.classList.contains("show") === true){
        menuButton.setAttribute("src", new URL("Menu.png", imagesPath))
        menu.setAttribute("aria-expanded", false);
        menu.classList.remove("show");
        mainEl.classList.remove("dim");
        footerEl.classList.remove("dim");
    }
});

accIcon.addEventListener("click", ()=>{
    const isOpen = accMenu.classList.toggle("show");
    accMenu.setAttribute("aria-expanded", isOpen);
});  

if(videoBtn && video){
    videoBtn.addEventListener("click", ()=>{
        if(video.paused === true){
            video.play();
            videoBtn.setAttribute("src", new URL("pause.png", imagesPath));
        }
        else{
            video.pause();
            videoBtn.setAttribute("src", new URL("play.png", imagesPath));
        }
    });
}