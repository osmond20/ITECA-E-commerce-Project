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
  <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <main class="main-el">
        <?php
            if(isset($_GET['error'])){
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
            else if($error == "incorrectcredentials"){
                echo "<p class='error'>Incorrect password or email</p>";
            }
            else if($error == "accountnotfound"){
                echo "<p class='error'>Account does not exist</p>";
            }
            }
            if(($_GET['newpwd'] ?? '') === "passwordupdated"){
                echo "<p class='success'>Password Updated</p>";
            }
        ?>
        <div class="login-container">
            <h1 class="title">Log In</h1>
             <form class="log-in-form" action="../backend/loginHandler.php" method="post" enctype="multipart/form-data">
                       <?php 
                      if(isset($_GET['email'])){
                        $email = $_GET['email'];
                        echo ' <input class="inputs" type="email" placeholder="Email" name="email" value="' .
                        htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '">';
                      }
                      else{
                       echo '<input class="inputs" type="email" placeholder="Email" name="email">';
                      }
                    ?>
                    <input class="inputs" type="password" placeholder="Password" name="pw"  minlength="8">
                    <button class="loginBtn" type="submit" name="login-btn">Log In</button>
                   <div class="link-content-container">
                    <p class="link-content">If you don't have an account? <a href="../pages/signin.php" class="login">Sign in</a></p>
                    <p class="link-content">Forgot password? <a href="../pages/resetpassword.php" class="login">Click here</a></p>
                   </div>
            </form>
        </div>
      </main>
</body>
</html>
