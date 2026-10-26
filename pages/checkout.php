<?php 
    require __DIR__ . "/../backend/session.php";

    // A secret to proves that user sent a form from checkout
    if(empty($_SESSION['csrf_checkout'])){
        $_SESSION['csrf_checkout'] = bin2hex(random_bytes(32));
    }

    // reading data from the checkoutHandler.php 
    // $old: what the customer had typed in the form
    $old = $_SESSION['checkout_old'] ?? [];

    // deleting any session stored messages sent from the 
    // checkoutHandler.php so that they don't show again
    unset($_SESSION['checkout_errors'], $_SESSION['checkout_old'], $_SESSION['checkout_notice']);

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
    <title>Checkout</title>
    <link rel="preload" href="../assets/fonts/figtree-v9-latin-regular.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="../assets/fonts/figtree-v9-latin-500.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="../assets/fonts/figtree-v9-latin-600.woff2" as="font" type="font/woff2" crossorigin>
    <!-- styles.css gives us the site header, menu and footer.
         checkout.css is only the checkout page. cart.css is the cart drawer. -->
    <link rel="stylesheet" href="../css/checkout.css">
    <link rel="stylesheet" href="../css/cart.css">
</head>
<body>
    <?php require __DIR__ . "/../pages/header.php" ?>
    <main class="main-el">
        <h1 class="visually-hidden">Checkout</h1>
        <div class="checkout-layout">
            <form action="../backend/checkoutHandler.php" class="checkout-form" id="checkout-form" method="post" novalidate>
                <?php
                    $error =$_GET['error'] ?? '';
                    $notice = $_GET['notice'] ?? '';
                    $session = $_GET['session'] ?? '';
                    $emptycart = $_GET['cartempty'] ?? '';
                    $message = $_GET['message'] ?? '';

                    if ($notice === 'paymentsuccess'){
                        echo '<p class="checkout-alert">Successful payment.</p>';
                    }
                    else if($notice === 'paymentfailed'){
                        echo '<p class="checkout-alert checkout-alert-error">Payment failed</p>';
                    }
                    else if ($error === 'incorrectfields'){ 
                    echo ' <p class="checkout-alert checkout-alert-error">Invalid submission.</p>';
                    } 
                    else if($error === 'emptycart'){
                        echo '<p class="checkout-alert checkout-alert-error">Your cart is empty. Please add a product the cart.</p>';
                    }
                    else if($error === 'payment'){
                        echo '<p class="checkout-alert checkout-alert-error">Please check the payment method.</p>';
                    }
                    else if($error === "serverError"){
                        echo '<p class="checkout-alert checkout-alert-error">Something went wrong on our side. Please try again later.</p>';
                    }
                    else if($error === "saveError"){
                        echo '<p class="checkout-alert checkout-alert-error">We could not save your order. Please try again.</p>';
                    }
                    else if($session === 'sessionexpired'){
                        echo '<p class="checkout-alert">Session expired.</p>';
                    }
                    else if($emptycart === 'empty'){
                        echo '<p class="checkout-alert">Your cart is empty. Please add a product.</p>';
                    }
                    else if($message === 'stock'){
                        $stock = $_GET['stock'];
                        $product = $_GET['product_name'];
                        echo '<p class="checkout-alert">Sorry we only have' . safeText($stock) .'of'. safeText($product) .'in stock.</p>';
                    }
                    else if($message === 'out of stock'){
                        $product = $_GET['product_name'];
                        echo '<p class="checkout-alert">Sorry, '. safeText($product). 'is out of stock.</p>';
                    }
                    ?>
               <!-- Hidden value sent along with the form -->
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_checkout'] ?>"> 
                <!-- js/checkout.js fills this with the cart, e.g. [{"id":1,"quantity":2}] -->
                <input type="hidden" name="cart" id="cart-field" value="[]">

                <!-- Contact -->
                <h2 class="checkout-heading">Contact</h2>
                <p class="checkout-intro">We'll only use your email to send your order confirmation and delivery updates</p>
                <input class="field-input" type="email" name="email" placeholder="Enter your email address" value="<?php safeText($old['email'] ?? ""); ?>">
                
                <!-- Delivery -->
                <h2 class="checkout-heading">Delivery</h2>
                <select class="field-select" id="countries" name="country">
                <!--use the option tags to list how many items you
                want in the dropdown menu-->
                    <option value="" disabled selected>Select Your Country</option>
                    <option value="South Africa">South Africa</option>
                </select>

                <div class="field-row">
                    <input class="field-input" type="text" name="fName" placeholder="First Name" value="<?php safeText($old['firstName'] ?? ""); ?>">
                    <input class="field-input" type="text" name="lName" placeholder="Last Name" value="<?php safeText($old['lastName'] ?? ""); ?>">
                </div>

                <input class="field-input" type="text" name="address" placeholder="Enter your address" value="<?php safeText($old['address'] ?? ""); ?>">

                <div class="field-row">
                    <input class="field-input" type="text" name="city" placeholder="City" value="<?php safeText($old['city'] ?? ""); ?>">
                    <select class="field-select" name="province">
                        <option value="Province" disabled selected>Province</option>
                        <option value="Eastern Cape" default>Eastern Cape</option>
                        <option value="Free State" default>Free State</option>
                        <option value="Gauteng" default>Gauteng</option>
                        <option value="KwaZulu-Natal" default>KwaZulu-Natal</option>
                        <option value="Limpopo" default>Limpopo</option>
                        <option value="Mpumalanga" default>Mpumalanga</option>
                        <option value="Northern Cape" default>Northern Cape</option>
                        <option value="North West" default>North West</option>
                        <option value="Western Cape" default>Western Cape</option>
                    </select>
                </div>
                <div class="field-row">
                    <input class="field-input" type="text" name="postal-Code" placeholder="Postal Code" value="<?php safeText($old['postal-code'] ?? ""); ?>">
                    <input class="field-input" type="tel" name="phone-Number" placeholder="Phone Number" value="<?php safeText($old['tel'] ?? ""); ?>">
                </div>
                <textarea class="field-textarea" name="note" placeholder="Notes about your order, .e.g. special delivery"><?php safeText($old['note'] ?? ""); ?></textarea>
                <!-- ---------- Payment ---------- -->
                <div class="payment-box">
                    <legend class="payment-legend">Payment Options</legend>
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="payfast" checked>
                        <img src="../assets/images/payfast.png" alt="PayFast" width="83" height="22">
                    </label>
                    <p class="payment-note">You'll be redirected to PayFast to complete your purchase</p>
                </div>
                
                <p class="checkout-terms">
                    By proceeding with your purchase you agree to our
                    <a href="../pages/privacypolicy.php">Terms and Conditions</a> and
                    <a href="../pages/privacypolicy.php">Privacy Policy</a>
                </p>

                <button type="submit" name="checkout-submit" class="checkout-submit" id="checkout-submit">Proceed to Checkout</button>
            </form>
             <!-- ORDER SUMMARY -->
            <aside class="checkout-summary" aria-label="Order summary">
                <h2 class="checkout-heading">Estimated Total</h2>

                <div class="summary-card">
                    <!-- js/checkout.js puts the cart items here -->
                    <ul class="summary-items" id="summary-items"></ul>

                    <div class="summary-totals">
                        <p class="summary-row"><span>Subtotal</span><span class="summary-price" id="summary-subtotal">R0,00</span></p>
                        <p class="summary-row"><span>Flat rate 2-4 days</span><span class="summary-price" id="summary-shipping">R0,00</span></p>
                    </div>
                    <p class="summary-row summary-total-row"><span>Total</span><span class="summary-price" id="summary-total">R0,00</span></p>
                </div>
            </aside>
        </div>
    </main>
    <?php require __DIR__ . "/../pages/cart.php"?>
    <?php require __DIR__ . "/../pages/footer.php"?>
</body>
<script defer src="../js/index.js"></script>
<script defer src="../js/checkout.js"></script>
</html>