<div class="cart">
    <div class="cart-content"></div>
    <div class="total">
        <div class="total-title">Estimated Total</div>
        <div class="total-price">R0</div>
    </div>
    <div class="empty-cart">
        <img src="<?= $siteRoot ?? '../' ?>assets/images/cart-outline.svg" class="cart-empty" alt="" height="80" width="80">
        <p class="empty-cart-text">Your Cart is Empty</p>
        <a href="<?= $siteRoot ?? '../' ?>pages/shopping.php" class="redirect-btn">Get Shopping</a>
    </div>
    <a href="<?= $siteRoot ?? '../' ?>pages/checkout.php" class="btn-buy">Checkout</a>
    <img src="<?= $siteRoot ?? '../' ?>assets/images/close.png" id="cart-close">
</div>