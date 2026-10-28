<?php
    require_once __DIR__ . '/../backend/session.php';

    require_once __DIR__ . '/../db/conn.php';

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

    $stmt = $conn->prepare('SELECT product_id, product_price, stock_quantity FROM products');
    if(!$stmt){
      error_log('Connection failed');
      http_response_code(500);
      exit('Server error occured. Please try again later.');
    }

    $stmt->execute();
    $result= $stmt->get_result();
    $products = $result->fetch_all(MYSQLI_ASSOC);
    $result->free_result();
    $stmt->close();

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
            <div class="image-container">
              <img src="../assets/images/TGA-FOProduct.png" alt="Cedar Spice beard oil">
            </div>
            <h2>Fresh Oak</h2>
            <p class="product-data">
              <span>R<?php echo safeText(number_format($products[0]['product_price'], 2)); ?></span>
              <span>Qty: <?php echo safeText($products[0]['stock_quantity']); ?></span>
            </p>
            <div class="product-data-update">
              <form class="updateForm" action="../backend/updateProductsHandler" method="post">
                <input class="updateInputs" type="text" name="price" value="">
                <input class="updateInputs" type="number" name="quantity" min="0" value="0">
              </form>
            </div>
            <button class="btn" type="button">Edit</button>
          </article>
          <article class="product">
            <div class="image-container">
              <img src="../assets/images/TGA-CSProduct.png" alt="Fresh Oak beard oil">
            </div>
            <h2>Cedar Spice</h2>
            <p class="product-data">
              <span>R<?php echo safeText(number_format($products[1]['product_price'], 2)); ?></span>
              <span>Qty: <?php echo safeText($products[1]['stock_quantity']); ?></span>
            </p>
            <div class="product-data-update">
              <form class="updateForm" action="../backend/updateProductsHandler" method="post">
                <input class="updateInputs" type="text" name="price" value="">
                <input class="updateInputs" type="number" name="quantity" min="0" value="0">
              </form>
            </div>
            <button class="btn" type="button">Edit</button>
          </article>
          <article class="product">
            <div class="image-container">
              <img src="../assets/images/Fresh-Oak-BC.png" alt="Fresh Oak beard butter and beard oil combo">
            </div>
            <h2>Fresh Oak - Combo</h2>
            <p class="product-data">
              <span>R<?php echo safeText($products[2]['product_price']); ?></span>
              <span>Qty: <?php echo safeText($products[2]['stock_quantity']); ?></span>
            </p>
            <div class="product-data-update">
              <form class="updateForm" action="../backend/updateProductsHandler" method="post">
                <input class="updateInputs" type="text" name="price" value="">
                <input class="updateInputs" type="number" name="quantity" min="0" value="0">
              </form>
            </div>
            <button class="btn" type="button">Edit</button>
          </article>
          <article class="product">
            <div class="image-container">
              <img src="../assets/images/TGA-CS-Combo.png" alt="Cedar Spice beard butter and beard oil combo">
            </div>
            <h2>Cedar Spice - Combo</h2>
            <p class="product-data">
              <span>R<?php echo safeText($products[3]['product_price']); ?></span>
              <span>Qty: <?php echo safeText($products[3]['stock_quantity']); ?></span>
            </p>
            <div class="product-data-update">
              <form class="updateForm" action="../backend/updateProductsHandler" method="post">
                <input class="updateInputs" type="text" name="price" value="">
                <input class="updateInputs" type="number" name="quantity" min="0" value="0">
              </form>
            </div>
            <button class="btn" type="button">Edit</button>
          </article>
        </div>
      </section>
    </main>
    <?php require __DIR__ . "/popup.php" ?>
    <script defer src="../js/dashboard.js"></script>
    <script defer src="../js/productUpdate.js"></script>
</body>
</html>