<?php 
    require __DIR__ . "/session.php";

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['checkout-submit'])) {
        header('Location: ../pages/checkout.php');
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
    $note = trim((string) ($_POST['note']));

    if(empty($email)|| !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($country)|| empty($firstName) 
        || empty($lastName) || empty($address) || empty($city) || empty($province) || $note){
            header("Location: ../pages/checkout.php?error=incorrectfields");
            exit();
    }

    







?>