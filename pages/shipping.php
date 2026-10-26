<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping</title>
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
  <link rel="stylesheet" href="../css/shipping.css">
  <link rel="stylesheet" href="../css/cart.css">
</head>
<body>
     <?php require "header.php" ?>
    <main class="main-el">
        <div class="shipping-content">
            <h1 class="shipping-title">Shipping, Returns and Refunds</h1>
            <p class="shipping-paragraph">Shipping takes 2-5 days depending on where you are located. We ship from Johannesburg using The Courier Guy.
            Shipping prices are displayed on the checkout page.
            Our refund and returns policy lasts 14 days. You can return your item if it is unopened and unused. Please contact us to arrange your return and refund.</p>
        </div>
    </main>
    <?php require __DIR__ . "//cart.php" ?>
    <?php require "footer.php" ?>
</body>
<script defer src="../js/index.js"></script>
</html>