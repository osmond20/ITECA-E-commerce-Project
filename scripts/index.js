// Variables
const menuButton = document.querySelector(".menu-button");
const menu = document.querySelector(".menu");
const mainEl = document.querySelector(".main-el");
const footerEl = document.querySelector(".footer-el");
const videoBtn = document.querySelector(".video-btn");
const video = document.querySelector(".video");

// Menu functionality
menuButton.addEventListener("click", ()=>{
    // stores the boolean result of the toggle
    const isOpen = menu.classList.toggle("show");

    if(isOpen){
        menuButton.setAttribute("src", "assets/images/close.png")
        menu.setAttribute("aria-hidden", isOpen);
        menu.classList.add("show");
    }
    else{
        menuButton.setAttribute("src", "assets/images/Menu.png")
    }

    // switches opacity states
    mainEl.classList.toggle("dim");
    footerEl.classList.toggle("dim");
});

// Click functionality so that the menu closes without menu button event
mainEl.addEventListener("click", ()=>{
    if(menu.classList.contains("show") === true){
        menuButton.setAttribute("src", "assets/images/Menu.png")
        menu.setAttribute("aria-expanded", false);
        menu.classList.remove("show");
        mainEl.classList.remove("dim");
        footerEl.classList.remove("dim");
    }
});

videoBtn.addEventListener("click", ()=>{
    if(video.pause){
        videoBtn.setAttribute("src", "assets/images/pause.png");
        video.play();
    }
    else{
        video.pause();
        videoBtn.setAttribute("src", "assets/images/play.png");
    }
});