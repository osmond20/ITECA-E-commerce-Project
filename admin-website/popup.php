    <?php 
      $username = $_SESSION['username'];
      $email = $_SESSION['user_email'];
    ?>
    <!-- Account pop-up -->
    <div class="modal" id="accountModal" role="dialog" aria-modal="true" aria-labelledby="accountTitle">
      <div class="modal-box">
        <img class="modal-close"src="../assets/images/close.png" alt="">
        <h2 id="accountTitle">Account</h2>
        <p class="acc-name"><?php echo safeText($username); ?></p>
        <p class="acc-email"><?php echo safeText($email); ?></p>
      </div>
    </div>