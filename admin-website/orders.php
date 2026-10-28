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

    $sql = $conn->prepare('SELECT order_id, order_reference, customer_name, customer_email, full_address, paid_at,status, total FROM orders LIMIT 3');
    if(!$sql){
        error_log('Connection failed');
        http_response_code(500);
        exit('Server error occured. Please try again later.');
    }

    $sql->execute();
    // 2. make query and get result
    $result = $sql->get_result();

    // 3. fetch the resuting rows as an array (associative array)
    $orders = $result->fetch_all(MYSQLI_ASSOC);

    // free result from memory
    $result->free_result();

    // close connection
    $sql->close();

    print_r($orders);

    $sql = $conn->prepare('SELECT order_id FROM notify');
    if(!$sql){
        error_log('Connection failed');
        http_response_code(500);
        exit('Server error occured. Please try again later.');
    }

    $sql->execute();
    // get result
    $result = $sql->get_result();

    // fetch the resuting rows as an array (associative array)
    $notifiedRows = $result->fetch_all(MYSQLI_ASSOC);
    $notifiedOrderIds = array_fill_keys(
    array_map('strval', array_column($notifiedRows, 'order_id')),
    true);

    // free result from memory
    $result->free_result();

    // close connection
    $sql->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
    <link rel="preload" href="../assets/fonts/figtree-v9-latin-regular.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="../assets/fonts/figtree-v9-latin-500.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="../assets/fonts/figtree-v9-latin-600.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="../css/order.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/popup.css">
</head>
<body>
  <?php require __DIR__ . "/header.php" ?>
    <main class="main-el">
      <?php 
        $error = $_GET['error'] ?? '';
        $success = $_GET['delivery'] ?? '';

        if($error === 'serverError'){
          echo '<p class="checkout-alert-error">Server Error. Please try again</p>';
        }
        else if($success === 'success'){
          echo '<p class="checkout-alert>Successfully sent.</p>"';
        }
      ?>
          <!-- Orders -->
      <section class="orders" id="page-orders">
        <section class="card card-lg">
          <h2>Orders</h2>
          <div class="table-wrap">
           <?php if (count($orders) === 0) { ?>
            <p>No orders made.</p>
           <?php } else { ?>
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
                  <td>Actions</td>
                </tr>
                  <?php foreach($orders as $order) {?>
                    <tr>
                        <td><?php echo  safeText($order['order_reference']); ?></td>
                        <td><?php echo safeText($order['customer_name']); ?></td>
                        <td><?php echo safeText($order['customer_email']); ?></td>
                        <td><?php echo safeText($order['phone_number']); ?></td>
                        <td><?php echo safeText($order['full_address']); ?></td>
                        <td><?php echo safeText($order['paid_at']);?></td>
                        <td>
                            <?php if($orders['status'] === 'pending') {?>
                              <span class="badge-pending">
                                <?php echo safeText($order['status']);?>
                              </span>
                            <?php }else if($orders['status'] === 'cancelled'){ ?>
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
                            <?php } ?>  
                        </td>
                        <td><?php echo safeText($order['total']);?></td>
                        <td>
                                <?php if(!isset($notifiedOrderIds[(string)$order['order_id']])) {?>
                                <form action="../backend/deliveryHandler.php" method="post">
                                    <input type="text" name="delivery"  value="<?php echo safeText($order['order_id']); ?>" hidden>
                                    <input type="email" name="delivery-email" value="<?php echo safeText($order['customer_id'])  ?>"hidden>
                                    <button class="link-btn" name="delivery-btn" type="submit">Notify for Delivery</button>
                                </form>
                            <?php } ?>
                        </td>
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