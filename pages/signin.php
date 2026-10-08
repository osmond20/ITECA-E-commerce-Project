<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
  <link rel="stylesheet" href="../css/signin.css">
    <title>Document</title>
</head>
<body>
      <main class="main-el">
        <div class="signin-container">
            <h1 class="title">Sign In</h1>
             <form class="sign-in-form" action="" method="post" enctype="multipart/form-data">
                    <input class="inputs" type="text" placeholder="First Name" name="firstname">
                    <p class="error">First Name cannot be empty</p>
                    <input class="inputs" type="text" placeholder="Last Name" name="lastname">
                    <p class="error">Last Name cannot be empty</p>
                    <input class="inputs" type="email" placeholder="Email" name="email">
                    <p class="error">Looks like this is not an email</p>
                    <input class="inputs" type="password" placeholder="Password" name="pw"  minlength="8">
                    <p class="error">Password cannot be empty</p>
                    <button class="signinBtn" type="submit">Sign In</button>
            </form>
            
            <p class="link-content">If you already have an account? <a href="../pages/login.php" class="login">Login</a></p>
        </div>
      </main>
</body>
<script defer src="../js/signin.js"></script>
</html>