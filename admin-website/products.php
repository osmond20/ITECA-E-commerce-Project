<?php
    require_once __DIR__ . '/../backend/session.php';

    if (($_SESSION['state'] ?? '') !== 'loggedIn' || ($_SESSION['role'] ?? '') !== 'admin') {
        header('Location: ../pages/login.php?error=forbidden');
        exit();
    }

        if (!isset($_SESSION['csrf_logout'])) {
        $_SESSION['csrf_logout'] = bin2hex(random_bytes(32));
    }

     // function to make text safe to print in HTML
    function safeText($text){
        return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link rel="preload" href="../assets/fonts/figtree-v9-latin-regular.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="../assets/fonts/figtree-v9-latin-500.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="../assets/fonts/figtree-v9-latin-600.woff2" as="font" type="font/woff2" crossorigin>
     <link rel="stylesheet" href="../css/products.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/popup.css">
</head>
<body>
    <?php require __DIR__ . "/header.php" ?>
    <main class="main-el">
      <!-- Products -->
      <section class="products-container" id="page-products">
        <div class="products">
          <article class="product">
            <div class="thumb">
              <img src="../assets/images/TGA-CSProduct.png" alt="Cedar Spice beard oil">
            </div>
            <h2>Cedar Spice</h2>
            <p class="meta">
              <span>R289,00</span>
              <span>Qty: 4</span>
            </p>
            <button class="btn" type="button">Edit</button>
          </article>
          <article class="product">
            <div class="thumb">
              <img src="../assets/images/TGA-FOProduct.png" alt="Fresh Oak beard oil">
            </div>
            <h2>Fresh Oak</h2>
            <p class="meta">
              <span>R249,00</span>
              <span>Qty: 7</span>
            </p>
            <button class="btn" type="button">Edit</button>
          </article>
          <article class="product">
            <div class="thumb">
              <img src="../assets/images/Fresh-Oak-BC.png" alt="Fresh Oak beard butter and beard oil combo">
            </div>
            <h2>Fresh Oak - Combo</h2>
            <p class="meta">
              <span>R409,00</span>
              <span>Qty: 5</span>
            </p>
            <button class="btn" type="button">Edit</button>
          </article>
          <article class="product">
            <div class="thumb">
              <img src="../assets/images/TGA-CS-Combo.png" alt="Cedar Spice beard butter and beard oil combo">
            </div>
            <h2>Cedar Spice - Combo</h2>
            <p class="meta">
              <span>R449,00</span>
              <span>Qty: 1</span>
            </p>
            <button class="btn" type="button">Edit</button>
          </article>
        </div>
      </section>
    </main>
    <?php require __DIR__ . "/popup.php" ?>
    <script defer src="../js/dashboard.js"></script>
</body>
</html>