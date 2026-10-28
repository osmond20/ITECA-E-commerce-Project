    <?php 
      $username = htmlspecialchars((string) ($_SESSION['username'] ?? ''), ENT_QUOTES, 'UTF-8');
      $email = htmlspecialchars((string) ($_SESSION['user_email'] ?? ''), ENT_QUOTES, 'UTF-8');
    ?>
    <!-- Account pop-up -->
    <div class="modal" id="accountModal" role="dialog" aria-modal="true" aria-labelledby="accountTitle">
      <div class="modal-box">
        <img class="modal-close"src="../assets/images/close.png" alt="">
        <h2 id="accountTitle">Account</h2>
        <p class="acc-name"><?php echo $username; ?></p>
        <p class="acc-email"><?php echo $email; ?></p>
      </div>
    </div>