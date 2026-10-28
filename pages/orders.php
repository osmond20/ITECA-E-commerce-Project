<?php 
    require_once __DIR__ . '/../backend/session.php';

    require_once __DIR__ . '/../db/conn.php';
    
    if (($_SESSION['state'] ?? '') !== 'loggedIn' || ($_SESSION['role'] ?? '') !== 'user') {
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

    $email = $_SESSION['user_email'];

    // Fetching the most recent orders
    $sql = $conn->prepare('SELECT order_reference, customer_name, 
    customer_email, full_address, paid_at, status, total FROM orders WHERE customer_email = ?');
    if(!$sql){
        error_log('Connection failed');
        http_response_code(500);
        exit('Server error occured. Please try again later.');
    }
    $sql->bind_param('s', $email);
    $sql->execute();
    // get result
    $result = $sql->get_result();

    // fetch the resuting rows as an array (associative array)
    $orders = $result->fetch_all(MYSQLI_ASSOC);

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
    <link rel="stylesheet" href="../css/orderMain.css">
    <link rel="stylesheet" href="../css/cart.css">
    <link rel="stylesheet" href="../css/popup.css">
</head>
<body>
    <?php require "header.php" ?>
    <main class="main-el">
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
                  </tr> 
                  <?php }  ?>
              </table>
           <?php } ?>
          </div>
        </section>
    </main>
    <?php require "footer.php"?>
    <?php require __DIR__ . "/cart.php" ?>
    <?php require __DIR__ . "/popupMain.php" ?>
</body>
<script defer src="../js/index.js"></script>
<script defer src="../js/popupMain.js"></script>
</html>