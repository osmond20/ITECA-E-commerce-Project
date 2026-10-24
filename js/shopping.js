async function loadShoppingPrices() {
    const buttons = document.querySelectorAll(".atc-btn");

    try {
        const response = await fetch(new URL("../api/products.json", window.location.href));
        if (!response.ok) {
            throw new Error(`Products API returned ${response.status}`);
        }

        const products = await response.json();
        if (!Array.isArray(products)) {
            throw new Error("Products API response is not a product list");
        }

        const productsById = new Map(
            products.map((product) => [String(product.product_id), product])
        );

        buttons.forEach((button) => {
            const product = productsById.get(button.dataset.itemId);
            const price = Number(product?.product_price);

            if (!product || !Number.isFinite(price) || price < 0) {
                console.error(`Could not find a valid price for product ${button.dataset.itemId}`);
                const priceDisplay = button.closest(".product-container")
                    ?.querySelector(".product-price");
                if (priceDisplay) {
                    priceDisplay.textContent = "Price unavailable";
                }
                return;
            }

            button.dataset.itemPrice = price.toFixed(2);
            const priceDisplay = button.closest(".product-container")
                ?.querySelector(".product-price");

            if (priceDisplay) {
                priceDisplay.textContent = `R${price.toFixed(2).replace(".", ",")}`;
            }

            button.disabled = false;
        });
    } catch (error) {
        console.error("Could not load shopping prices:", error);
        document.querySelectorAll(".product-price").forEach((priceDisplay) => {
            priceDisplay.textContent = "Price unavailable";
        });
    }
}

loadShoppingPrices();