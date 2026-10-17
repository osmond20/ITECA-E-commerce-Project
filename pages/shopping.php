<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
</head>
<body>
     <?php require "header.php" ?>
    <main class="main-el">
        <section class="shopping-component">
            <div class="product-container">
                <img class="product-img-1" src="../assets/images/TGA-FOProduct.png" alt="TGA Fresh Oak" height="50" width="50">
                <div class="text-container">
                    <p class="product-text">Fresh Oak</p>
                    <p class="product-text">R249,00</p>
                </div>
                <button class="atc-btn">Add to Cart</button>
            </div>
            <div class="product-container">
                <img class="product-img-1" src="../assets/images/TGA-CSProduct.png" alt="TGA Fresh Oak" height="50" width="50">
                <div class="text-container">
                    <p class="product-text">Cedar Spice</p>
                    <p class="product-text">R289,00</p>
                </div>
                <button class="atc-btn">Add to Cart</button>
            </div>
            <div class="product-container">
                <img class="product-img-2" src="../assets/images/Fresh-Oak-BC.png" alt="TGA Fresh Oak" height="50" width="50">
                <div class="text-container">
                    <p class="product-text">Fresh Oak - Combo</p>
                    <p class="product-text">R409,00</p>
                </div>
                <button class="atc-btn">Add to Cart</button>
            </div>
            <div class="product-container">
                <img class="product-img-3" src="../assets/images/TGA-CS-Combo.png" alt="TGA Fresh Oak" height="50" width="50">
                <div class="text-container">
                    <p class="product-text">Cedar Spice - Combo</p>
                    <p class="product-text">R449,00</p>
                </div>
                <button class="atc-btn">Add to Cart</button>
            </div>
        </section>
    </main>
    <?php require "footer.php" ?>
</body>
<script defer src="../js/index.js"></script>
</html>