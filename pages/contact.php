<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
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
  <link rel="stylesheet" href="../css/contact.css">
</head>
<body>
    <?php require "header.php" ?>
    <main class="main-el">

        <div class="contact-form-container">
        <h1 class="contact-title">Contact Us</h1>
            // paste your formspree endpoint url in action=""
        <form class="contact-us-form" action="Add your Formspree endpoint here" method="post" enctype="multipart/form-data">
                <input class="inputs" name="Full Name" type="text" placeholder="Full Name">
                <p class="error">Full Name cannot be empty</p>
                <input class="inputs" name="Email" type="email" placeholder="Email">
                <p class="error" id="email-error">Looks like this is not an email</p>
                <textarea class="message" name="Message" rows="20" placeholder="Message"></textarea>
                <p class="error">Message cannot be empty</p>
                <button class="contactBtn" type="submit">Send</button>
            </form>
        </div> 
    </main>
    <?php require "footer.php" ?>
</body>
<script defer src="../js/index.js"></script>
<script defer src="../js/contact.js"></script>
</html>
