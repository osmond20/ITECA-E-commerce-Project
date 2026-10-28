
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

    // Fetching the most recent orders
    $sql = $conn->prepare('SELECT order_reference, customer_name, 
    customer_email, full_address, phone_number, paid_at, status, total FROM orders
    ORDER BY order_id DESC LIMIT 3');
    if(!$sql){
        error_log('Connection failed');
        http_response_code(500);
        exit('Server error occured. Please try again later.');
    }

    $sql->execute();
    // get result
    $result = $sql->get_result();

    // fetch the resuting rows as an array (associative array)
    $orders = $result->fetch_all(MYSQLI_ASSOC);

    // free result from memory
    $result->free_result();

    // close connection
    $sql->close();

    $sql = $conn->prepare('SELECT COALESCE(SUM(total), 0) AS total_sum
    FROM orders
    WHERE status = ?');
    if(!$sql){
        error_log('Connection failed');
        http_response_code(500);
        exit('Server error occured. Please try again later.');
    }
    $status = "paid";
    $sql->bind_param("s", $status);
    $sql->execute();
    // get result
    $result = $sql->get_result();

    // The aggregate query returns a single row.
    $total = $result->fetch_assoc();

    // free result from memory
    $result->free_result();

    // close connection
    $sql->close();

    $sql = $conn->prepare('SELECT COUNT(*) AS total_orders
      FROM orders');
    if(!$sql){
        error_log('Connection failed');
        http_response_code(500);
        exit('Server error occured. Please try again later.');
    }

    $sql->execute();
    // get result
    $result = $sql->get_result();

    // The aggregate query returns a single row.
    $totalOrders = $result->fetch_assoc();

    // free result from memory
    $result->free_result();

    // close connection
    $sql->close();

    $sql = $conn->prepare("SELECT COUNT(DISTINCT customer_email) AS total_customers
      FROM orders
      WHERE customer_email IS NOT NULL AND customer_email <> ''");
    if(!$sql){
        error_log('Connection failed');
        http_response_code(500);
        exit('Server error occured. Please try again later.');
    }

    $sql->execute();
    // get result
    $result = $sql->get_result();

    // The aggregate query returns a single row.
    $totalCustomers = $result->fetch_assoc();

    // free result from memory
    $result->free_result();

    // close connection
    $sql->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>True Gent Africa – Admin</title>
<link rel="preload" href="../assets/fonts/figtree-v9-latin-regular.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="../assets/fonts/figtree-v9-latin-500.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="../assets/fonts/figtree-v9-latin-600.woff2" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="../css/dashboard.css">
<link rel="stylesheet" href="../css/header.css">
<link rel="stylesheet" href="../css/popup.css">
</head>
<body>
  <?php require __DIR__ . "/header.php" ?>
    <main class="main-el">
      <!-- Dashboard -->
      <section class="dashboard">
        <div class="stats">
          <article class="stat">
            <h2>Total Sales</h2>
            <?php if(count($total) === 0){?>
              <p>R0</p>
            <?php } else{ ?>
              <p>R<?php echo number_format((int) safeText($total['total_sum']), 0); ?></p>
            <?php } ?>
        </article>
          <article class="stat">
            <h2>Total Orders</h2>
            <p><?php echo safeText($totalOrders['total_orders']); ?></p>
        </article>
          <article class="stat">
            <h2>Total Customers</h2>
            <p><?php echo safeText($totalCustomers['total_customers']); ?></p>
        </article>
        </div>
        <section class="card card-lg">
          <h2>Recent Orders</h2>
          <div class="table-wrap">
           <?php if(count($orders) === 0) {?>
           <p>No orders made.</p>
           <?php } else {?>
               <table border="1">
                  <tr>
                      <th>Order Reference</th>
                      <th>Customer Name</th>
                      <th>Email Address</th>
                      <th>Phone Number</th>
                      <th>Address</th>
                      <th>Date</th>
                      <th>Status</th>
                      <th>Total</th>
                  </tr>
                  <?php foreach($orders as $order) {?>
                  <tr>
                      <td><?php echo safeText($order['order_reference']); ?></td>
                      <td><?php echo safeText($order['customer_name']); ?></td>
                      <td><?php echo safeText($order['customer_email']); ?></td>
                      <td><?php echo safeText($order['phone_number']); ?></td>
                      <td><?php echo safeText($order['full_address']); ?></td>
                      <td><?php echo safeText($order['paid_at']);?></td>
                      <td>
                         <?php if($order['status'] === 'pending') {?>
                          <span class="badge-pending">
                            <?php echo safeText($order['status']);?>
                          </span>
                         <?php }else if($order['status'] === 'cancelled'){ ?>
                          <span class="badge-cancelled">
                            <?php echo safeText($order['status']);?>
                          </span>
                         <?php }else if($order['status'] === 'failed') {?>
                          <span class="badge-failed">
                            <?php echo safeText($order['status']);?>
                          </span>
                         <?php }else if($order['status'] === 'paid'){ ?>
                          <span class="badge">
                            <?php echo safeText($order['status']);?>
                          </span>
                         <?php } else { ?>
                          <span><?php echo safeText($order['status']);?></span>
                         <?php } ?>
                      </td>
                      <td><?php echo safeText($order['total']);?></td>
                  </tr> 
                  <?php }  ?>
              </table>
           <?php } ?>
          </div>
        </section>
      </section>
    </main>
    <?php require __DIR__ . "/popup.php" ?>
</body>
</html>
<script defer src="../js/dashboard.js"></script>
