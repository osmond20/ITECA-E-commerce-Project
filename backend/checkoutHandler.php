<?php 
    require __DIR__ . "/session.php";

    require __DIR__ . "/payfast.php";

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['checkout-submit'])) {
        header('Location: ../pages/checkout.php');
        exit();
    }

    // The secret code from the form must match the one saved in the session.
    $sentToken = (string) ($_POST['csrf_token'] ?? '');
    $realToken = (string) ($_SESSION['csrf_checkout'] ?? '');
    if ($realToken === '' || !hash_equals($realToken, $sentToken)) {
        header('Location: ../pages/checkout.php?session="sessionexpired"');
        exit();
    }

    require_once __DIR__ . "/../db/conn.php";

    // getting input values and storing them
    $email = trim((string) ($_POST['email'] ?? ''));
    $country = trim((string) ($_POST['country'] ?? ''));
    $firstName = trim((string) ($_POST['fName'] ?? ''));
    $lastName = trim((string) ($_POST['lName'] ?? ''));
    $address = trim((string) ($_POST['address'] ?? ''));
    $city = trim((string) ($_POST['city'] ?? ''));
    $province = trim((string) ($_POST['province'] ?? ''));
    $postalCode = trim((string) ($_POST['postal-Code'] ?? ''));
    $tel = trim((string) ($_POST['phone-Number'] ?? ''));
    $note = trim((string) ($_POST['note'] ?? ''));
    $payment = (string) ($_POST['payment_method'] ?? '');

    // shipping fee
    $flatRate = 100.00;

    // sending information inputted in the form
    $_SESSION['checkout_old'] = ['email' => $email, 'firstName'=> $firstName, 'lastName'=> $lastName,
        'address'=> $address,'city'=> $city, 'postal-code'=> $postalCode,
        'tel' => $tel,'note'=>$note];

    if(empty($email)|| !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($country)|| empty($firstName) 
        || empty($lastName) || empty($address) || empty($city) || empty($province)){
            header("Location: ../pages/checkout.php?error=incorrectfields");
            exit();
    }

    if($payment !== 'payfast'){
        header("Location: ../pages/checkout.php?error=payment");
        exit();
    }


    // Read and validate every item in the cart sent by the browser.
    $cartFromBrowser = json_decode((string) ($_POST['cart'] ?? ''), true);
    if(!is_array($cartFromBrowser)){
        header("Location: ../pages/checkout.php?error=invalidcart");
        exit();
    }
    if($cartFromBrowser === []){
        header("Location: ../pages/checkout.php?cartempty=empty");
        exit();
    }

    $parsePositiveInt = static function ($value): ?int {
        if(is_int($value)){
            return $value > 0 ? $value : null;
        }
        if(!is_string($value) || !preg_match('/^[1-9][0-9]*$/D', $value)){
            return null;
        }

        $parsed = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        return $parsed === false ? null : $parsed;
    };

    $wanted = [];
    foreach($cartFromBrowser as $selected){
        if(!is_array($selected)){
            header("Location: ../pages/checkout.php?error=invalidcart");
            exit();
        }

        $productID = $parsePositiveInt($selected['productID'] ?? null);
        $quantity = $parsePositiveInt($selected['quantity'] ?? null);
        if($productID === null || $quantity === null
            || (isset($wanted[$productID]) && $wanted[$productID] > PHP_INT_MAX - $quantity)){
            header("Location: ../pages/checkout.php?error=invalidcart");
            exit();
        }

        $wanted[$productID] = ($wanted[$productID] ?? 0) + $quantity;
    }
    ksort($wanted, SORT_NUMERIC);

    //looking up real price in the db
    $stmt = $conn->prepare("SELECT product_id, product_name, product_price, stock_quantity 
    FROM products WHERE product_id = ? LIMIT 1");
    if(!$stmt){
        error_log('Checkout: could not prepare product query.');
        header("Location: ../pages/checkout.php?error=serverError");
        exit();
    }

    $selections = []; // one entry per product, with the real price

    foreach($wanted as $productID => $quantity){

        // Use the database record as the source of truth for product details.
        $stmt->bind_param('i', $productID);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();

        if(!$product){
            $stmt->close();
            header("Location: ../pages/checkout.php?error=invalidproduct");
            exit();
        }

        //converting the price to double
        $price = number_format((float)$product['product_price'], 2, '.', '');

        // getting the subtotal which is the sum of all products only
        $subtotal = $price * (int)$quantity;

        //keeping verified product details and calculated total for the order
        $selections[] = [
            'product_id'=> (int) $product['product_id'],
            'name'=>$product['product_name'],
            'price'=>$price,
            'quantity'=>$quantity,
            'subtotal'=>$subtotal
        ];
    }
    $stmt->close();

    //saving to db
    // order details must be according to database columns
    /*
        $email
        $fAddress = $address . ", ". $city . ", " 
        . $province . ", " . $postalCode . ", " . $country;
        $payment
        $subtotal  
        $shippingFee
        $total
        $userEmail = $SESSION['user_email'] ?? null;
        */

    $reference = 'TGA='. strtoupper(bin2hex(random_bytes(5)));
    $_SESSION['reference'] = $reference;

    $customerName = $firstName . " " . $lastName;
    $deliveryAddress = $address . ", " . $city . ", " . $province
    . ", " . $postalCode . ", " . $country;
    $total = 0;
    $subtotal = 0;
    foreach($selections as $selection){
        $subtotal += $selection['subtotal'];
    }
    $total+= $subtotal + $flatRate;
    $total = number_format((float) $total, 2, '.', '');
    $userEmail = $_SESSION['user_email'] ?? null;

    // saving everything all at once or nothing at all
    // because if saving an item fails, the half-saved order is undone
    $conn->begin_transaction();
    try{
        // Reserve each product atomically so concurrent checkouts cannot oversell.
        $reserveStmt = $conn->prepare('UPDATE products
            SET stock_quantity = stock_quantity - ?
            WHERE product_id = ? AND stock_quantity >= ?');
        if(!$reserveStmt){
            throw new Exception($conn->error);
        }

        foreach($selections as $selection){
            $productID = $selection['product_id'];
            $quantity = $selection['quantity'];
            $reserveStmt->bind_param('iii', $quantity, $productID, $quantity);
            if(!$reserveStmt->execute()){
                throw new Exception($reserveStmt->error);
            }
            if($reserveStmt->affected_rows !== 1){
                $reserveStmt->close();
                $conn->rollback();
                header("Location: ../pages/checkout.php?error=stock");
                exit();
            }
        }
        $reserveStmt->close();

        // saving the order itself to the order table
        $stmt = $conn->prepare('
            INSERT INTO orders (order_reference, user_email, customer_email, customer_name, full_address, phone_number, payment_method, 
            subtotal, shipping, total, order_note) VALUES (?,?,?,?,?, ?, ?, ?, ?, ?, ?)
        ');
        if(!$stmt){
            throw new Exception($conn->error);
        }

        $stmt->bind_param('sssssssssss', $reference, $userEmail, $email, $customerName, $deliveryAddress, $tel, $payment, $subtotal, 
        $flatRate, $total, $note);

        if(!$stmt->execute()){
            throw new Exception($stmt->error);
        }

        $orderId = $conn->insert_id; // gets the id recently inserted by mySQL
        $stmt->close();

        // adding a record to order_items
        $itemStmt = $conn->prepare(
            'INSERT INTO order_items (order_id, product_id, product_name, 
            unit_price, quantity, selection_total) VALUES (?, ?, ?, ?, ?, ?)');
        
        if(!$itemStmt){
            throw new Exception($conn->error);
        }

        foreach($selections as $selection){
            $productID = $selection['product_id'];
            $name = $selection['name'];
            $price = $selection['price'];
            $quantity = $selection['quantity'];
            $selectionTotal = $selection['subtotal'];

            $itemStmt->bind_param("iisdid", $orderId, $productID, $name, $price, $quantity, $selectionTotal);
            // executing query
            if(!$itemStmt->execute()){
                throw new Exception($itemStmt->error);
            }
        }
        $itemStmt->close();

        $conn->commit(); // after everything work all at once, make it permanent

    }
    catch(Throwable $error){
        $conn->rollback(); // saving fails, undo it all
        error_log('savedOrder failed' . $error->getMessage());
        header("Location: ../pages/checkout.php?error=saveError");
        exit();
    }

    // Builds the signed form and posts it to PayFast. Never returns.
    $pf = payfastSettings();

    // The fields, in PayFast's required order.
    $data = [
        // --- merchant details ---
        'merchant_id'   => $pf['merchant_id'],
        'merchant_key'  => $pf['merchant_key'],
        'return_url'    => $pf['site_url'] . '/pages/checkout.php?notice=paymentsuccess',
        'cancel_url'    => $pf['site_url'] . '/pages/checkout.php?notice=paymentfailed',
        'notify_url'    => $pf['site_url'] . '/backend/notify.php',
        // --- buyer details ---
        'name_first'    => $firstName,
        'name_last'     => $lastName,
        'email_address' => $email,
        // --- transaction details ---
        'm_payment_id'  => $reference,                                        // comes back to us in notify.php
        'amount'        => $total,  // e.g. 389.00
        'item_name'     => 'True Gent Africa order ' . $reference,
    ];
    $data['signature'] = payfastSignature($data, $pf['passphrase']);

    // A page with a hidden form that submits itself (like the video's "Pay Now" button).
    $action = 'https://' . $pf['host'] . '/eng/process';
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Redirecting to PayFast...</title></head><body>';
    echo '<p>Taking you to PayFast...</p>';
    echo '<form id="payfast-form" action="' . htmlspecialchars($action, ENT_QUOTES) . '" method="post">';
    foreach ($data as $name => $value) {
        echo '<input type="hidden" name="' . htmlspecialchars($name, ENT_QUOTES) . '" value="' . htmlspecialchars((string) $value, ENT_QUOTES) . '">';
    }
    echo '<noscript><button type="submit">Continue to PayFast</button></noscript>';
    echo '</form><script>document.getElementById("payfast-form").submit();</script></body></html>';
    exit();
?>