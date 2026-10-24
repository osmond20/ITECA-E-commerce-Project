// variables to store elements
const productId = new URLSearchParams(window.location.search).get("product_id");
const productImage = document.getElementById("image_URL");
const productName = document.getElementById("product_name");
const productPrice = document.getElementById("product_price");
const productDescription = document.getElementById("description");
const ingredients = document.getElementById("ingredients");
const howToUse = document.getElementById("how_to_use");
const addToCartButton = document.querySelector(".atcBtn");

// function to display errors if fetch proves to be unsuccessful
function showProductError(message) {
    productName.textContent = message;
    productDescription.textContent = "";
    productPrice.textContent = "";
    productImage.removeAttribute("src");
    addToCartButton.disabled = true;
}

// function used to operate the product info toggles when a user clicks
function setupProductInfoToggles() {
    document.querySelectorAll(".component").forEach((component) => {
        const arrow = component.querySelector(".arrowBtn");
        const info = component.nextElementSibling?.querySelector(".container-info");

        if (!arrow || !info) {
            return;
        }

        arrow.addEventListener("click", () => {
            info.hidden = !info.hidden;
            arrow.classList.toggle("active", !info.hidden);
        });
    });
}

// asynchronous fethcing from "api/products.json" into product page elements
async function loadProduct() {
    if (!productId) {
        showProductError("Product not found");
        return;
    }

    try {
        // pauses execution until promise is returned
        const response = await fetch(new URL("../api/products.json", window.location.href));
        if (!response.ok) {
            throw new Error(`Products API returned ${response.status}`);
        }

        // pauses execution until promise is returned
        const products = await response.json();
        if (!Array.isArray(products)) {
            throw new Error("Products API response is not a product list");
        }

        const product = products.find((item) => String(item.product_id) === productId);
        if (!product) {
            showProductError("Product not found");
            return;
        }

        const price = Number(product.product_price);
        if(!Number.isFinite(price) || price < 0){
            throw new Error(`Product ${productId} has an invalid price`);
        }

        // loading data retrieved from the product.json api into
        // the corresponding elements dynamically
        productName.textContent = product.product_name;
        productPrice.textContent = `R${String(product.product_price).replace(".", ",")}`;
        productDescription.textContent = product.description;
        ingredients.textContent = product.ingredients;
        howToUse.textContent = product.how_to_use;
        productImage.src = new URL(product.image_URL, window.location.href);
        productImage.alt = product.product_name;
        document.title = product.product_name;

        // storing the product information in the button 
        // to facilitate cart.js to easily read from the 
        // product page's button
        addToCartButton.dataset.itemId = String(product.product_id);
        addToCartButton.dataset.itemName = product.product_name;
        addToCartButton.dataset.itemPrice = price.toFixed(2);
        addToCartButton.dataset.itemDescription = product.description;
        addToCartButton.dataset.itemUrl = window.location.href;
        addToCartButton.dataset.itemImage = productImage.src;
        addToCartButton.disabled = false;
    } catch (error) {
        console.error("Could not load product:", error);
        showProductError("Unable to load product. Please try again later.");
    }
}

setupProductInfoToggles(); // callling for ensuring clicking is available
loadProduct(); // calling for data fetching