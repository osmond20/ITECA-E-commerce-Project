// variables to store elements
const cartIcon = document.querySelector(".cart-icon");
const cart = document.querySelector(".cart");
const cartClose = document.getElementById("cart-close");
const emptyCart = document.querySelector(".empty-cart");
const totalContainer = document.querySelector(".total");
const checkOutBtn = document.querySelector(".btn-buy");
const main = document.querySelector(".main-el");
const footer = document.querySelector(".footer-el");
const cartContent = document.querySelector(".cart-content");
const shoppingComponent = document.querySelector(".shopping-component");
const contactContainer = document.querySelector(".contact-form-container");
const cartStorageKey = "shoppingCart";

// event listeners for the shopping cart
cartIcon.addEventListener("click", () =>{
    cart.classList.add("active");
        // for both the shoppingComponent and
        // contactContainer checking if the they exist
        // on the corresponding page
        if(shoppingComponent){
            shoppingComponent.classList.add("no-pointer");
        }

        if(contactContainer){
            contactContainer.classList.add("no-pointer");
        }

    // shows the dim in main and footer background
    main.classList.add("dim");
    footer.classList.add("dim");
} );

// listens for a click to close the cart drawer
cartClose.addEventListener("click", () =>{
        if(cart.classList.contains("active") === true){
            cart.classList.remove("active");
            main.classList.remove("dim");
            footer.classList.remove("dim");
        }
    }
);

// gets all buttons with the class "atc-btn"
const addCartButtons = document.querySelectorAll(".atc-btn, .atcBtn");
addCartButtons.forEach(button =>{
    button.addEventListener("click", event =>{
        const addButton = event.currentTarget;
        const productBox = addButton.closest(".product-container");
        addToCart(productBox, addButton); // calls function which adds a product to the cart
    });
});

// function for adding to the cart
function addToCart(productBox, addButton){
    // Shopping cards use their product container; the product page uses button data.
    const productImgSrc = productBox?.querySelector("img")?.src ?? addButton.dataset.itemImage;
    const productTitle = productBox?.querySelector(".product-text")?.textContent
        ?? addButton.dataset.itemName;
    const displayedPrice = productBox?.querySelector(".product-price")?.textContent;
    const rawPrice = displayedPrice ?? addButton.dataset.itemPrice;

    if(!productImgSrc || !productTitle || !rawPrice){
        console.error("Could not add product to cart: product details are incomplete.");
        return;
    }

    const productPrice = rawPrice.startsWith("R") ? rawPrice : `R${Number(rawPrice).toFixed(2).replace(".", ",")}`;
    
    const cartItems = cartContent.querySelectorAll(".cart-product-title");
    // ensures no duplication of adding the same item to the cart
    for(let item of cartItems){
        if(item.textContent === productTitle){
            alert("This item is already in the cart");
            return;
        }
    }

    // stores the product details so the cart can be restored on another page
    const product = {
        productTitle,
        productPrice,
        quantity: 1,
        productImgSrc
    };

    createCartBox(product); // create cart box
    saveCart(); // save to localStorage
    updateCartCount(1);
    updateTotalPrice(); // always reflects the carts latest state
    updateCartDisplay();
};

// function for creating the product elements in the shopping cart
function createCartBox(product){
    // used to store dynamically the product elements
    const cartBox = document.createElement("div");
    cartBox.classList.add("cart-box");
    // populating dynamically the product box with the product elements
    cartBox.innerHTML = `<img class="cart-img">
            <div class="cart-detail">
                <h2 class="cart-product-title"></h2>
                <span class="cart-price"></span>
                <div class="cart-quantity">
                    <button id="decrement">-</button>
                    <span class="number">${product.quantity}</span>
                    <button id="increment">+</button>
                </div>
            </div>
            <img src="../assets/images/Trash_Empty.png" class="cart-remove" height="20" width="20">
            `;

    cartBox.querySelector(".cart-img").src = product.productImgSrc;
    cartBox.querySelector(".cart-product-title").textContent = product.productTitle;
    cartBox.querySelector(".cart-price").textContent = product.productPrice;
            
    cartContent.appendChild(cartBox); // adds the cartBox to the cartContent to the HTML structure

    // clicking functionality to successfully remove products from the shopping cart
    cartBox.querySelector(".cart-remove").addEventListener("click",  ()=>{
        cartBox.remove();
        updateCartCount(-1); // keeps the count accurate when a product is removed
        saveCart(); // save cart
        updateTotalPrice();
        updateCartDisplay();
    });

    // functionality to implement quantity increment and decrement by capturing clicks
    // on those respective buttons
    cartBox.querySelector(".cart-quantity").addEventListener("click", event =>{
        const numberElement = cartBox.querySelector(".number");
        const decrementButton = cartBox.querySelector("#decrement");
        let quantity = parseInt(numberElement.textContent, 10); // store the quantity of products

        // changes the value of quantity based on the button click from the user either
        // "-" or "+"
        if(event.target.id === "decrement" && quantity > 1){
            quantity--;
            if(quantity === 1){
                decrementButton.style.color = "#999";
            }
        }else if(event.target.id === "increment"){
            quantity++;
            decrementButton.style.color = "#333";
        }

        numberElement.textContent = quantity;
        saveCart();
        updateTotalPrice(); // updates the total price when the cart chanages
    });
};

// function for saving the cart as JSON in localStorage
function saveCart(){
    const cartItems = []; // creating an empty array
    const cartBoxes = cartContent.querySelectorAll(".cart-box");

    cartBoxes.forEach(cartBox =>{
        cartItems.push({
            productTitle: cartBox.querySelector(".cart-product-title").textContent,
            productPrice: cartBox.querySelector(".cart-price").textContent,
            quantity: parseInt(cartBox.querySelector(".number").textContent, 10),
            productImgSrc: cartBox.querySelector(".cart-img").src
        });
    });

    // saving data in localStorage to ensure data persistence
    localStorage.setItem(cartStorageKey, JSON.stringify(cartItems));
};

// function for loading the saved cart from localStorage
function loadCart(){
    const savedCart = localStorage.getItem(cartStorageKey);
    if(savedCart === null){
        updateCartDisplay();
        return;
    }

    let cartItems;
    try{
        cartItems = JSON.parse(savedCart);
    }catch(error){
        console.error("Could not load the saved cart from localStorage:", error);
        updateCartDisplay();
        return;
    }

    // validating if the items retrieved from local
    // storage is returned as an array of array(s)
    if(!Array.isArray(cartItems)){
        console.error("The saved cart in localStorage is not a valid product list.");
        updateCartDisplay();
        return;
    }

    cartItems.forEach(product =>{
        // checking if the product is not empty
        if(product !== null){
            createCartBox(product);
        }else{
            console.error("Skipping an invalid product in the saved cart.");
        }
    });

    // restores the product count badge after loading the saved products
    cartItemCount = cartContent.querySelectorAll(".cart-box").length;
    updateCartCount(0);
    updateTotalPrice();
    updateCartDisplay();
};

// function for showing the cart total and empty-cart message when needed
function updateCartDisplay(){
    const cartIsEmpty = cartContent.querySelectorAll(".cart-box").length === 0;
    totalContainer.style.visibility = cartIsEmpty ? "hidden" : "visible";
    checkOutBtn.style.visibility = cartIsEmpty ? "hidden" : "visible";
    emptyCart.style.visibility = cartIsEmpty ? "visible" : "hidden";
};

// function defined to calculate the total price in the shopping cart
function updateTotalPrice(){
    const totalPriceElement = document.querySelector(".total-price");
    const cartBoxes = cartContent.querySelectorAll(".cart-box");
    let total = 0;
    cartBoxes.forEach(cartBox =>{
        const priceElement = cartBox.querySelector(".cart-price");
        const quantityElement = cartBox.querySelector(".number");
        const price = parseFloat(priceElement.textContent.replace("R", "").trim());
        const quantity = parseInt(quantityElement.textContent, 10);
        total += price * quantity;
    });
    totalPriceElement.textContent = `R${total}`;
};

// counter for the cart Count Badge = 0
let cartItemCount = 0;
// function for updating the cart count badge every a product is added to the cart
// which will store to the total number of product added to the cart
function updateCartCount(change){
    const cartItemCountBadge = document.querySelector(".cart-item-count");
    cartItemCount += change;
    if(cartItemCount > 0){
        cartItemCountBadge.style.visibility = "visible";
        cartItemCountBadge.textContent = cartItemCount;
    }else{
        cartItemCountBadge.style.visibility = "hidden";
        cartItemCountBadge.textContent = "";
    }
}

// loads the saved cart whenever this page is opened
loadCart();

// functionality to dim the main and footer element when the cart drawer opens
main.addEventListener("click", () => {
    if(cart.classList.contains("active") === true){
        cart.classList.remove("active");
        // for both the shoppingComponent and
        // contactContainer checking if the they exist
        // on the corresponding page
        if(shoppingComponent){
            shoppingComponent.classList.remove("no-pointer");
        }

        if(contactContainer){
            contactContainer.classList.remove("no-pointer");
        }
        main.classList.remove("dim");
        footer.classList.remove("dim");
    }
});