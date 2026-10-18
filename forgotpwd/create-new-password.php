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
  <link rel="stylesheet" href="../css/resetNew.css">
</head>
<body>
    <main class="main-el">
        <?php 
              if(!isset($_GET['error'])){
                // html code runs
              }
              else{
                    $newPwd = $_GET['newpwd'];
                if($newPwd == "empty"){
                  echo "<p class='error'>Empty Password</p>";
                }
                else if($newPwd == "pwdnotsame"){
                    echo "<p class='error'>Password does not match</p>";
                }
              }
        ?>
        <div class="reset-container">
            <h1 class="title">Set New Password</h1>
            <?php 
                $selector = $_GET['selector'];
                $validator = $_GET['validator']; // token

                // checking if validators(tokens) exists inside the URL
                if(empty($selector) || empty($validator)){
                    echo "Could not validate your request!";
                }
                else{
                    // checking if tokens are in fact hexadecimal tokens
                    if(ctype_xdigit($selector) !== false && ctype_xdigit($validator)){
                      ?>
                      
                        <form class="reset-form" action="../backend/resetPassword.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="selector" value="<?php echo $selector?>">
                            <input type="hidden" name="selector" value="<?php echo $validator?>">
                            <input class="inputs" type="password" name="pw" placeholder="Enter new password">
                            <input class="inputs" type="password" name="pw-repeat" placeholder="Repeat new password">
                            <button class="resetBtn" type="submit" name="reset-pw-btn">Reset Password</button>
                        </form>

                      <?php
                    }
                }

            ?>
        </div>
      </main>
</body>
</html>