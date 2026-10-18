<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
  <link rel="stylesheet" href="../css/reset.css">
</head>
<body>
    <main class="main-el">
        <?php 
              // FIX: resetRequest.php redirects with ?reset=success (or ?reset=invalid),
              // the old code was looking for ?error= and ?success= so nothing was ever shown
              $reset = $_GET['reset'] ?? '';
              if($reset === "success"){
                echo "<p class='success'>If that email is registered, a reset link is on its way. Check your inbox (and spam folder).</p>";
              }
              else if($reset === "invalid"){
                echo "<p class='error'>Please enter a valid email address.</p>";
              }
        ?>
        <div class="reset-container">
            <h1 class="title">Reset your password</h1>
             <form class="reset-form" action="../backend/resetRequest.php" method="post" enctype="multipart/form-data">
                    <input class="inputs" type="email" placeholder="Email" name="email">
                    <button class="resetBtn" type="submit" name="reset-request-btn">Request to Reset Password</button>
            </form>
        </div>
      </main>
</body>
</html>