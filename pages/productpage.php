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
    <link rel="stylesheet" href="../css/productpage.css">
</head>
<body>
    <?php require "header.php" ?>
    <main class="main-el">
        <section class="image-container">
            <img class="main-img" id="product-img" src="" alt="TGA product" height="50" width="50">
        </section>
        <section class="product-info">
            <h1 class="title" id="product-title">Loading...</h1>
            <p class="price" id="product-price"></p>
            <button class="atcBtn">Add to cart</button>
            <p class="description" id="product-description"></p>
            <div class="additionalInfo-container">
                <div class="component">
                    <h2 class="component-title">Ingredients</h2> <img class="arrowBtn" src="../assets/images/arrow-down.png" alt="">
                </div>
                <div class="ingredients-info">
                    <p class="container-info" id="ingredients" hidden></p>
                </div>
            </div>
            <div class="additionalInfo-container">
                <div class="component">
                    <h2 class="component-title">How to use</h2> <img class="arrowBtn" src="../assets/images/arrow-down.png" alt="">
                </div>
                <div class="HTU-info">
                    <p class="container-info" id="HTU" hidden></p>
                </div>
            </div>
        </section>
    </main>
   <?php require "footer.php" ?>
</body>
<script defer src="../js/index.js"></script>
<script defer src="../js/productpage.js"></script>
</html>