<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping</title>
        <link rel="preload" 
  href="../assets/fonts/figtree-v9-latin-regular.woff2"
  as="font"
  type="font/woff2"
  crossorigin>
  <link rel="preload" 
  href="../assets/fonts/figtree-v9-latin-500.woff2"
  as="font"
  type="font/woff2"
  crossorigin>
  <link rel="preload" 
  href="../assets/fonts/figtree-v9-latin-600.woff2"
  as="font"
  type="font/woff2"
  crossorigin>
  <link rel="preload" 
  href="../assets/fonts/figtree-v9-latin-700.woff2"
  as="font"
  type="font/woff2"
  crossorigin>
  <link rel="stylesheet" href="../css/shopping.css">
  <link rel="stylesheet" href="../css/cart.css">
  <link rel="stylesheet" href="../css/popup.css">
</head>
<body>
     <?php require "header.php";
        require_once __DIR__ . "/../backend/APIfetch.php";
     ?>
    <main class="main-el">
        <section class="shopping-component">
            <div class="product-container">
                <a href="../pages/productpage.php?product_id=1">
                    <img class="product-img-1" src="../assets/images/TGA-FOProduct.png" alt="TGA Fresh Oak" height="50" width="50" id="1">
                </a>
                <div class="text-container">
                    <p class="product-text">Fresh Oak</p>
                    <p class="product-text product-price">Loading price...</p>
                </div>
                <button class="atc-btn" data-item-id="1" disabled>Add to Cart</button>
            </div>
            <div class="product-container">
                <a href="../pages/productpage.php?product_id=2">
                    <img class="product-img-1" src="../assets/images/TGA-CSProduct.png" alt="TGA Cedar Spice" height="50" width="50" id="2">
                </a>
                <div class="text-container">
                    <p class="product-text">Cedar Spice</p>
                    <p class="product-text product-price">Loading price...</p>
                </div>
                <button class="atc-btn"data-item-id="2" disabled>Add to Cart</button>
            </div>
            <div class="product-container">
                <a href="../pages/productpage.php?product_id=3">
                    <img class="product-img-2" src="../assets/images/Fresh-Oak-BC.png" alt="TGA Fresh Oak Combo" height="50" width="50" id="3">
                </a>
                <div class="text-container">
                    <p class="product-text">Fresh Oak Combo</p>
                    <p class="product-text product-price">Loading price...</p>
                </div>
                <button class="atc-btn" data-item-id="3"disabled>Add to Cart</button>
            </div>
            <div class="product-container">
                <a href="../pages/productpage.php?product_id=4">
                    <img class="product-img-3" src="../assets/images/TGA-CS-Combo.png" alt="TGA Cedar Spice Combo" height="50" width="50" id="4">
                </a>
                <div class="text-container">
                    <p class="product-text">Cedar Spice Combo</p>
                    <p class="product-text product-price">Loading price...</p>
                </div>
                <button class="atc-btn" data-item-id="4" disabled>Add to Cart</button>
            </div>
        </section>
    </main>
    <?php require __DIR__ . "/cart.php" ?>
    <?php require "footer.php" ?>
    <?php require __DIR__ . "/popupMain.php" ?>
</body>
<script defer src="../js/index.js"></script>
<script defer src="../js/shopping.js"></script>
<script defer src="../js/popupMain.js"></script>
</html>