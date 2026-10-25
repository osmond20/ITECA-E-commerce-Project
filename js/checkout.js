// variables
const checkoutForm = document.getElementById("checkout-form");
const cartField = document.getElementById("cart-field");
const submitButton = document.getElementById("checkout-submit");
const summaryItems = document.getElementById("summary-items");
const summarySubtotal = document.getElementById("summary-subtotal");
const summaryShipping = document.getElementById("summary-shipping");
const summaryTotal = document.getElementById("summary-total");
// getting existing items added to the cart from localstorage
const items = JSON.parse(localStorage.getItem("shoppingCart") || "[]");
if (!Array.isArray(items)) throw new Error("Invalid cart data");


function isValidEmail(email){
    if(typeof email !== "string"){
        return false;
    }

    email = email.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
    return emailRegex.test(email);
}

function validateForm(){
    const inputs = document.querySelectorAll(".field-input");
    const selections = document.querySelectorAll(".field-select");
    const note = document.querySelector(".field-textarea");
    let isValid = true;


    inputs.forEach(input=>{
       const invalidEmail = input.type === 'email' && !isValidEmail(input.value);
       if(input.value.trim() === '' || invalidEmail || note.value.trim()){
            input.classList.add('error');
            note.classList.add('error');
            isValid = false;
       }
       else{
        input.classList.remove('error');
        note.classList.remove('error');
       }
    });

    selections.forEach(selection =>{
        if(selection.value.trim() === "Province"  || selection.value === ''){
            selection.classList.add("error");
            isValid = false;
        }
        else{
            selection.classList.remove('error');
        }
    });

    if(note.value === ''){
        note.classList.add('error');
        isValid = false;
    }
    else{
        note.classList.remove('error');
    }

    return isValid;
}

// fixed cost for delivery
const shippingFee = 100;

function formatPrice(amount) {
  return `R${amount.toFixed(2).replace(".", ",")}`;
}

// function to show an error should the rendering fail
function showSummaryLoadError() {
  summaryItems.innerHTML =
    '<li class="summary-empty">We could not load your order. Please refresh the page.</li>';
  submitButton.disabled = true;
}

function renderSummary() {
  try {
    summaryItems.replaceChildren();

    // if the cart is empty
    if (items.length === 0) {
      summaryItems.innerHTML = '<li class="summary-empty">Your cart is empty.</li>';
      summarySubtotal.textContent = 0;
      summaryShipping.textContent = 0;
      summaryTotal.textContent = 0;
      submitButton.disabled = true;
      return;
    }

    let subtotal = 0;

    items.forEach((item) => {
      const price = Number(
        String(item.productPrice).replace("R", "").trim().replace(",", ".")
      );

      const quantity = Number(item.quantity); // stores the quantity of a corresponding product

      if (!Number.isFinite(price) || !Number.isFinite(quantity)) {
        throw new Error("Invalid cart item");
      }

      const lineTotal = price * quantity; // sum up the total
      subtotal += lineTotal; // allocating to subtotal

      // creating list item to render product added to the cart
      const row = document.createElement("li"); 
      row.className = "summary-item"; // set the row's className to match css

      const image = document.createElement("img");
      image.src = item.productImgSrc; // retrieves the correponding image source
      image.alt = "";

      const name = document.createElement("span");
      name.className = "summary-item-name"; // set the span class to match css
      if(quantity >= 1){
        name.textContent = `${item.productTitle} x ${quantity}`;
      }
      else{
        name.textContent = "";
      }

      const linePrice = document.createElement("span");
      linePrice.className = "summary-price";
      linePrice.textContent = formatPrice(lineTotal);

      row.append(image, name, linePrice); // add corresponding product details to the row
      summaryItems.append(row); // display the row in the unordered list
    });

    summarySubtotal.textContent = formatPrice(subtotal);
    summaryShipping.textContent = formatPrice(shippingFee);
    summaryTotal.textContent = formatPrice(subtotal + shippingFee);
    submitButton.disabled = false;
  } catch (error) {
    console.error("Could not render checkout summary:", error);
    showSummaryLoadError();
  }
}

renderSummary();

// saving JSON items to the hidden cart field 
// so the server can receive it
checkoutForm.addEventListener("submit", event=>{
    if(items.length == 0){
        event.preventDefault();
        return;
    }

    if(!validateForm()){
        event.preventDefault();
        return;
    }

    // if form is filled in correctly. server gets the newest cart
    cartField.value = JSON.stringify(items);
    submitButton.disabled = true;
    submitButton.textContent = "Please wait...";
    // form sent to the backend

    // re-render if the user gets back via the back button in the browser
    window.addEventListener("pageshow", ()=>{
        submitButton.textContent = "Proceed to Checkout";
        if(items.length > 1){
            renderSummary();
        }
    })
})
