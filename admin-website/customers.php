<?php
    require_once __DIR__ . '/../backend/session.php';

    if (($_SESSION['state'] ?? '') !== 'loggedIn' || ($_SESSION['role'] ?? '') !== 'admin') {
        header('Location: ../pages/login.php?error=forbidden');
        exit();
    }

        if (!isset($_SESSION['csrf_logout'])) {
        $_SESSION['csrf_logout'] = bin2hex(random_bytes(32));
    }

     // function to make text safe to print in HTML
    function safeText($text){
        return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers</title>
    <link rel="preload" href="../assets/fonts/figtree-v9-latin-regular.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="../assets/fonts/figtree-v9-latin-500.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="../assets/fonts/figtree-v9-latin-600.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="../css/popup.css">
    <link rel="stylesheet" href="../css/customer.css">
<link rel="stylesheet" href="../css/header.css">
</head>
<body>
  <?php require __DIR__ . "/header.php" ?>
    <main class="main-el">
            <!-- Customers -->
      <section class="customers">
        <section class="card card-sm">
          <h2>Customers</h2>
          <div class="table-wrap">
            <table>
              <thead>
              <tr>
                  <th>Customer</th>
                  <th>Email</th>
                  <th>Telephone Numbers</th>
                  <th>Total Orders</th>
              </tr>
            </thead>
              <tbody>
                <tr>
                  <td>Sophia Clark</td>
                  <td>sophia.clark@example.com</td>
                  <td>0678900653</td>
                  <td>4</td>
                </tr>
                <tr>
                  <td>Ethan Carter</td>
                  <td>ethan.carter@example.com</td>
                  <td>0678900653</td>
                  <td>5</td>
                </tr>
                <tr>
                  <td>Liam Foster</td>
                  <td>liam.foster@example.com</td>
                  <td>0678900653</td>
                  <td>1</td>
                </tr>
                <tr>
                  <td>Ava Harper</td>
                  <td>ava.harper@example.com</td>
                  <td>0678900653</td>
                  <td>3</td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>
      </section>
    </main>
    <?php require __DIR__ . "/popup.php" ?>
    <script defer src="../js/dashboard.js"></script>
</body>
</html>