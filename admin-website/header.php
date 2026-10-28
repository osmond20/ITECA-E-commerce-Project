
<header>
    <nav class="nav-container">
  <div class="nav-title-container">
    <div class="nav-menu-container"><img class="menu-button" src="../assets/images/Menu.png" alt="" height="20" width="20"></div>
    <h1 class="nav-title">True Gent Africa</h1>
  </div>
  <aside class="menu" aria-expanded="false">
    <a class="menu-links" href="../admin-website/dashboard.php">Dashboard</a>
    <a class="menu-links" href="../admin-website/orders.php">Orders</a>
    <a class="menu-links" href="../admin-website/products.php">Products</a>
    <a class="menu-links" href="../admin-website/customers.php">Customers</a>
    <a class="menu-links" id="account-popup" href="#">Account</a>
    <form action="../backend/logoutHandler.php" method="post">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_logout'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <button class="menu-links" type="submit" name="logout" value="1">Log out</button>
    </form>
  </aside>
</nav>
</header>