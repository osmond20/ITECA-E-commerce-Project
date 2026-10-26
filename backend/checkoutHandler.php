<?php 
    require __DIR__ . "/session.php";

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
    $email = trim((string) ($_POST['email']));
    $country = trim((string) ($_POST['country']));
    $firstName = trim((string) ($_POST['fName']));
    $lastName = trim((string) ($_POST['lName']));
    $address = trim((string) ($_POST['address']));
    $city = trim((string) ($_POST['city']));
    $province = trim((string) ($_POST['province']));
    $postalCode = trim((string) ($_POST['postal-Code']));
    $tel = trim((string) ($_POST['phone-Number']));
    $note = trim((string) ($_POST['note']));
    $payment = $_POST['payment_method'];

    // delivery fee
    $deliveryfee = 100.00;

    // sending information inputted in the form
    $_SESSION['checkout_old'] = ['email' => $email, 'firstName'=> $firstName, 'lastName'=> $lastName,
        'address'=> $address,'city'=> $city, 'postal-code'=> $postalCode,
        'tel' => $tel,'note'=>$note];

    if(empty($email)|| !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($country)|| empty($firstName) 
        || empty($lastName) || empty($address) || empty($city) || empty($province) || empty($note)){
            header("Location: ../pages/checkout.php?error=incorrectfields");
            exit();
    }

    if($payment !== 'payfast'){
        header("Location: ../pages/checkout.php?error=payment");
        exit();
    }


    // Read the cart that the browser sent
    $cartFromBrowser = json_decode((string) ($_POST['cart'] ?? ''), true);
    $wanted = []; // 'title' => $productTitle, 'quantity' => $quantity
    if(is_array($cartFromBrowser)){
        foreach($cartFromBrowser as $selected){
            $productTitle = $selected['productTitle'] ?? '';
            $quantity = $selected['quantity'] ?? null;

            // checking if variables are valid
            if(!is_string($productTitle) || !is_int($quantity) || $productTitle === "" || $quantity < 1){
                continue; // ignore them and continue to the next line
            }

            // pushing to wanted array
            $wanted[] = ['title' => $productTitle, 'quantity' => $quantity];
        }
    }

    /// exit if greater than 4 because there are only 4 products that
    // can be added to the cart
    if($wanted === []){
        header("Location: ../pages/checkout.php?cartempty=empty");
        exit();
    }

    //looking up real price in the db
    $stmt = $conn->prepare("SELECT product_name, product_price, stock_quantity 
    FROM products WHERE product_name = ? LIMIT 1");
    if(!$stmt){
        error_log('Checkout: could not prepare product query.');
        header("Location: ../pages/checkout.php?error=serverError");
        exit();
    }

    $selections = []; // one entry per product, with the real price

    foreach($wanted as $productTitle => $quantity){
        // use the db record as the source of truth for availability and price
        $stmt->bind_param('s', $productTitle);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();

        // recheck stock at checkout with inventory
        $stock = (int) $product['stock_quantity'];
        if($quantity > $stock){
            $stmt->close();
            if($stock > 0){
                header("Location: ../pages/checkout.php?message=stock&stock=". urldecode($stock). "&product=" . urldecode($product['product_name']));
                exit();
            }
            else{
                header("Location: ../pages/checkout.php?message=outofstock");
                exit();
            }
        }

        //converting the price to double
        $price = number_format((float)$product['product_price'], 2);

        //summing the total of a selected product 
        $total = $price * (int)$quantity + $deliveryfee;

        //keeping verified product details and calculated total for the order
        $selections[] = [
            'name'=>$product['product_name'],
            'price'=>$price,
            'quantity'=>$quantity,
            'total'=>$total
        ];
    }
    $stmt->close();


    







?>