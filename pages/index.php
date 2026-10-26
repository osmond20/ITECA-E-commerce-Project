<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="../css/cart.css">
    <title>Document</title>
</head>
<body>
   <?php require "header.php" ?>
    <main class="main-el">
        <section class="hero-content">
         <div class="content">
            <h1 class="content-title">A routine built for restraint — so you don’t need ten products.</h1>
            <p class="content-paragraph">Condition and maintain your beard with a system designed to be chosen once.</p>
            <a href="shopping.php" class="content-btn">Shop Products</a>
         </div>  
         <img class="landing-page-image" src="../assets/images/truegentafrica-1.png" alt="" height="30" width="30"> 
        </section>
        <section class="statement-content">
            <h2 class="statement-title">Designed to remove excess, not add steps.</h2>
            <p class="statement-paragraph">True Gent products are built to work together as a complete grooming system. No overlapping functions. No experimental layering. Once your routine is set, your attention is free for better things</p>
        </section>
        <section class="product-content">
            <img class="landing-page-image" id="product-image" src="../assets/images/truegentafrica-2.png" alt="" height="30" width="30">
            <div class="content p-content">
            <h1 class="content-title">True Gent Beard Oil – Cedar Spice</h1>
            <p class="content-paragraph">A luxurious blend of pure, African-grown oils crafted for the True Gentleman. Smooth, hydrating, and intentional. With a grounded, evening-leaning scent that leaves a steady, unmistakable presence without ever overpowering.</p>
            <a href="shopping.php" class="content-btn">Shop Now</a>
         </div> 
        </section>
        <section class="values-content">
            <div class="video-wrapper">
                <video class="video" src="../assets/videos/snapsave-app_1163057655412849_hd.mp4" muted>
                </video>
                <div class="btn-wrapper">
                        <img class="video-btn" src="../assets/images/play.png" alt="" height="10" width="10">
                </div>
            </div>

            <div class="value-list">
                <div class="value">
                    <img class="value-icon" src="../assets/images/heart.png" alt="" height="20" width="20">
                    <div class="value-content">
                        <h3 class="value-title">Made to Strengthen</h3>
                        <p class="value-paragraph">Not just Shine. Infused with natural oils that nourish from root to tip for a fuller, stronger beard over time.</p>
                    </div>
                </div>
                <div class="value">
                    <img class="value-icon" src="../assets/images/water.png" alt="" height="20" width="20">
                    <div class="value-content">
                        <h3 class="value-title">Pure, Honest Ingredients</h3>
                        <p class="value-paragraph">Pure, high quality oils blended with purpose and integrity. No silicones. No parabens.</p>
                    </div>
                </div>
                <div class="value">
                    <img class="value-icon" src="../assets/images/earth.png" alt="" height="20" width="20">
                    <div class="value-content">
                        <h3 class="value-title">Born Here</h3>
                        <p class="value-paragraph">Formulated and bottled in South Africa – supporting local craftsmanship and sustainable sourcing.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php require __DIR__ . "//cart.php" ?>
    <?php require "footer.php" ?>
</body>
<script defer src="../js/index.js"></script>
</html>
<?php  ?>