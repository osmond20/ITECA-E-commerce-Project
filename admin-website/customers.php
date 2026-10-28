<?php
    require_once __DIR__ . '/../backend/session.php';

    require_once __DIR__ . '/../db/conn.php';

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

    /*
    This query retrieves the customer name, customer email, customer's phone number
    and has an additional column that shows the number of orders that customer has
    And this is done for each customer.
    */
    $sql = $conn->prepare('SELECT customer_name,
       customer_email,
       phone_number,
       COUNT(DISTINCT order_id) AS order_count
        FROM orders
        GROUP BY customer_name, customer_email, phone_number');

    if(!$sql){
      error_log('Connection failed');
      http_response_code(500);
      exit('Server error occured. Please try again later.');
    }

    $sql->execute();
    $result = $sql->get_result();
    $customers = $result->fetch_all(MYSQLI_ASSOC);
    $result->free_result();
    $sql->close();


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
            <?php if(count($customers) === 0){ ?>
                <p>No customers.</p>
            <?php } else { ?>
                 <table border="1">
                    <tr>
                        <th>Customer Name</th>
                        <th>Email Address</th>
                        <th>Telephone Number</th>
                        <th>Total Orders</th>
                    </tr>
                    <?php foreach($customers as $customer) {?>
                    <tr>
                        <td><?php echo $customer['customer_name']; ?></td>
                        <td><?php echo $customer['customer_email']; ?></td>
                        <td><?php echo $customer['phone_number']; ?></td>
                        <td><?php echo $customer['order_count']?></td>
                    </tr> 
                    <?php }  ?>
                </table>
            <?php } ?>
          </div>
        </section>
      </section>
    </main>
    <?php require __DIR__ . "/popup.php" ?>
    <script defer src="../js/dashboard.js"></script>
</body>
</html>