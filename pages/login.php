<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
            <link rel="preload" 
  href="assets/fonts/figtree-v9-latin-regular.woff2"
  as="font"
  type="font/woff2"
  crossorigin>
  <link rel="preload" 
  href="assets/fonts/figtree-v9-latin-500.woff2"
  as="font"
  type="font/woff2"
  crossorigin>
  <link rel="preload" 
  href="assets/fonts/figtree-v9-latin-600.woff2"
  as="font"
  type="font/woff2"
  crossorigin>
  <link rel="preload" 
  href="assets/fonts/figtree-v9-latin-700.woff2"
  as="font"
  type="font/woff2"
  crossorigin>
  <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <main class="main-el">
        <div class="login-container">
            <h1 class="title">Log In</h1>
             <form class="log-in-form" action="" method="post" enctype="multipart/form-data">
                    <input class="inputs" type="email" placeholder="Email" name="email">
                    <p class="error">Looks like this is not an email</p>
                    <input class="inputs" type="password" placeholder="Password" name="pw"  minlength="8">
                    <p class="error">Password cannot be empty</p>
                    <button class="loginBtn" type="submit">Log In</button>
            </form>
            
            <p class="link-content">If you don't have an account? <a href="../pages/signin.php" class="login">Sign in</a></p>
        </div>
      </main>
</body>
<script defer src="../js/login.js"></script>
</html>