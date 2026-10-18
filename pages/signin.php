<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
  <link rel="stylesheet" href="../css/signin.css">
    <title>Document</title>
</head>
<body>
      <main class="main-el">
        <?php
              if(!isset($_GET['error'])){
                // html code runs
              }
              else{
                $error = $_GET['error'];
                if($error == "emptyfields"){
                  echo "<p class='error'>Empty Fields</p>";
                }
                else if($error == "emailinvalid"){
                    echo "<p class='error'>Email is invalid</p>";
                }
                else if($error == "signinerror"){
                   echo "<p class='error'>Email already exists</p>";
                }
                else if($error == "passwordinvalid"){
                   echo "<p class='error'>Password must be at least 8 characters long.</p>";
                }
              }
            ?>
        <div class="signin-container">
            <h1 class="title">Sign In</h1>
             <form class="sign-in-form" action="../backend/signinHandler.php" method="post" enctype="multipart/form-data">
                    <?php 
                      if(isset($_GET['username'])){
                        $username = $_GET['username'];
                        //htmlspecialchars handles characters in the submitted value safely
                        echo ' <input class="inputs" type="text" placeholder="Username" name="username" value="' .
                          htmlspecialchars($username, ENT_QUOTES, 'UTF-8') . '">';
                      }
                      else{
                       echo ' <input class="inputs" type="text" placeholder="Username" name="username">';
                      }

                      if(isset($_GET['email'])){
                        $email = $_GET['email'];
                        echo ' <input class="inputs" type="email" placeholder="Email" name="email" value="' .
                        htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '">';
                      }
                      else{
                       echo '<input class="inputs" type="email" placeholder="Email" name="email">';
                      }
                    ?>
                    <input class="inputs" type="password" placeholder="Password" name="pw"  minlength="8" maxlength="4096" required>
                    <button class="signinBtn" type="submit" name="signin-btn">Sign In</button>
            </form>
            
            <p class="link-content">If you already have an account? <a href="../pages/login.php" class="login">Login</a></p>
        </div>
      </main>
</body>
</html>