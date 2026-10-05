const contactForm = document.querySelector(".contact-us-form");
const inputs = document.querySelectorAll(".inputs");
const message = document.querySelector(".message");
const errorMessage = document.querySelectorAll(".error");

// Email validation form function
function isValidEmail(email){
    if(typeof email !== "string"){
        return false;
    }

    email = email.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
    return emailRegex.test(email);
}


contactForm.addEventListener("submit", event =>{
    
    let isValid = true;

    inputs.forEach(input =>{
        const invalidEmail = input.type === 'email' && !isValidEmail(input.value);
        const errorInput = input.nextElementSibling;
        if(input.value.trim() === '' || invalidEmail){
            input.classList.add("on-error");
            errorInput.classList.add("show")
            isValid = false;
        } else {
            input.classList.remove("on-error");
            errorInput.classList.remove("show")
        }
    })

     if(message.value === ""){
            message.classList.add("on-error");
            const error = message.nextElementSibling;
            error.classList.add("show");
            isValid = false;
    }

    if(!isValid){
        event.preventDefault();
    }
})