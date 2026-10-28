<?php 
    require __DIR__. "/session.php";

    require_once __DIR__ .  '/../db/conn.php';

      if (($_SESSION['state'] ?? '') !== 'loggedIn' || ($_SESSION['role'] ?? '') !== 'admin') {
        header('Location: ../pages/login.php?error=forbidden');
        exit();
    }

    if($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['updateBtn'])){
        header("Location: ../pages/index.php");
        exit();
    }

    $productID = $_POST['id'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    if(empty($price) || $quantity < 0){
        header("Location: ../admin-website/products.php?error=inputError");
        exit();
    }

    $productID = (int) $productID;
    $price = number_format((float) $price, 2);
    $quantity = (int) $quantity;

    $stmt = $conn->prepare('UPDATE products SET product_price = ?, stock_quantity = stock_quantity + ? WHERE product_id = ?');
    if(!$stmt){
        error_log('Connection failed');
        http_response_code(500);
        exit('A server error occurred. Please try again later.');
    }

    $stmt->bind_param('dii', $price, $quantity, $productID);
    if(!$stmt->execute()){
        error_log('Failed to save to db.');
        $stmt->close();
        header('Location: ../admin-website/products.php?error=serverError');
        exit();
    }
    $stmt->close();

    header("Location: ../admin-website/products.php?success=saved");
    exit();
?>