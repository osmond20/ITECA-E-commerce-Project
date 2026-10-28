<?php 
    $siteRoot = $siteRoot ?? '../';
    require __DIR__ . "/../backend/session.php";
    if (!isset($_SESSION['csrf_logout'])) {
        $_SESSION['csrf_logout'] = bin2hex(random_bytes(32));
    }
?>
 <nav class="site-nav">
      <div class="nav-menu-and-brand">
        <img class="icon menu-button" src="<?= $siteRoot ?? '../' ?>assets/images/Menu.png" alt="Menu Icon" height="20" width="20">
        <a href="<?= $siteRoot ?? '../' ?>index.php" class="brand-title">True Gent Africa</a>
      </div>  
      <div class="nav-links">
        <a class="nav-link" href="<?= $siteRoot ?? '../' ?>index.php">Home</a>
        <a class="nav-link" href="<?= $siteRoot ?? '../' ?>pages/shopping.php">Shop</a>
        <a class="nav-link" href="<?= $siteRoot ?? '../' ?>pages/contact.php">Contact Us</a>
      </div>
      <div class="nav-actions">
        <img class="icon-actions cart-icon" src="<?= $siteRoot ?? '../' ?>assets/images/cart-outline.svg" alt="" height="20" width="20">
        <span class="cart-item-count"></span>
        <img class="icon-actions" id="acc-icon" aria-expanded="false" src="<?= $siteRoot ?? '../' ?>assets/images/account-icon.png" alt="" height="20" width="20">
      </div>
        <div class="account-menu">
            <ul class="acc-menu-list">
            <?php 
            // checking whether the session state is empty
            if(empty($_SESSION['state'])){
               echo
                '<li class="acc-menu-list-link" id="log-in">
                    <a class="acc-link" href="' . $siteRoot . 'pages/login.php" id="">Log-In</a>
                </li>
                <li class="sign-in-link" id=sign-in>
                    <a class="acc-link" href="' . $siteRoot . 'pages/signin.php">Sign-In</a>
                </li>';
            } 
            // if the session state is not empty
            else if($_SESSION['state'] === 'signedIn' || $_SESSION['state'] === 'loggedIn'){
                echo 
                '
                <li class="profile-link" id="profile">
                    <a class="acc-link" href="#" id="account-popup">Profile</a>
                </li>
                <li class="sign-in-link" id=orders>
                    <a class="acc-link" href="' . $siteRoot . 'pages/orders.php">Orders</a>
                </li>
                <li class="logout-link" id="log-out">
                    <form class="logout-form" action="' . $siteRoot . 'backend/logoutHandler.php" method="post">
                        <input type="hidden" name="csrf_token" value="' . htmlspecialchars($_SESSION['csrf_logout'], ENT_QUOTES, 'UTF-8') . '">
                        <button type="submit" class="acc-link logout-btn" name="logout">Logout</button>
                    </form>
                </li>
                ';
            }
            ?>
            </ul>
        </div>
    </nav>
    <aside class="menu" aria-expanded="true">
            <ul class="menu-links">
                <li class="list-link">
                    <a class="links" href="<?= $siteRoot ?? '../' ?>index.php">Home</a>
                </li>
                <li class="list-link">
                    <a class="links" href="<?= $siteRoot ?? '../' ?>pages/shopping.php">Shop</a>
                </li>
                <li class="list-link">
                    <a class="links" href="<?= $siteRoot ?? '../' ?>pages/contact.php">Contact Us</a>
                </li>
            </ul>
    </aside>