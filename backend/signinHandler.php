<?php 
require __DIR__ . "/../backend/session.php";

if(isset($_POST['signin-btn'])){
    require_once __DIR__ . '/../db/conn.php';

    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = password_hash($_POST['pw'], PASSWORD_DEFAULT);


    if(empty($username) || empty($email) || empty($password)){
        header("Location: ../pages/signin.php?error=emptyfields&username=". urlencode($username) . '&email=' .urlencode($email));
        exit();
    }
    else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        header("Location: ../pages/signin.php?error=emailinvalid&username=" . urlencode($username) . '&email=' .urlencode($email));
        exit();
    }

    // Check whether this email is already registered.
    $check = $conn->prepare(
        'SELECT email_address FROM users WHERE email_address = ?'
    );
    $check->bind_param('s', $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $check->close();
        header('Location: ../pages/signin.php?error=signinerror&username=' . urlencode($username) . '&email=' .urlencode($email));
        exit();
    }
    $check->close();

    // Save values using parameters, not string concatenation.
    $insert = $conn->prepare(
        'INSERT INTO users (username, email_address, password_hash)
        VALUES (?, ?, ?)'
    );
    $insert->bind_param('sss', $username, $email, $password);
    $insert->execute();
    $insert->close();

    // redirects to the home page
    $_SESSION['state']= "signedIn";
    header("Location: ../pages/index.php");
    exit();
}
else{
    // unidentified user is redirected to the sigin.php if tried accessing this page via URL
    header("Location: ../pages/signin.php");
    exit();
}
?>
