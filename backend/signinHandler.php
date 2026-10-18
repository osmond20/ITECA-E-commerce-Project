<?php 
require __DIR__ . "/session.php";

    if($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['signin-btn'])){
    // unidentified user is redirected to the sigin.php if tried accessing this page via URL
    header("Location: ../pages/signin.php");
    exit();
    }

    require_once __DIR__ . '/../db/conn.php';

    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['pw'] ?? '';


    if(empty($username) || empty($email) || empty($password)){
        header("Location: ../pages/signin.php?error=emptyfields&username=". urlencode($username) . '&email=' .urlencode($email));
        exit();
    }
    else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        header("Location: ../pages/signin.php?error=emailinvalid&username=" . urlencode($username) . '&email=' .urlencode($email));
        exit();
    }
    else if(strlen($password) < 8 || strlen($password) > 4096){
        header('Location: ../pages/signin.php?error=passwordinvalid&username=' . urlencode($username) . '&email=' . urlencode($email));
        exit();
    }

    // Check whether this email is already registered.
    $check = $conn->prepare(
        'SELECT email_address FROM users WHERE email_address = ?');
    if (!$check) {
    error_log('Registration lookup preparation failed.');
    http_response_code(500);
    exit('A server error occurred. Please try again later.');
    }
    $check->bind_param('s', $email);
    $check->execute();
    $check->store_result();
    $check->close();

    if ($check->num_rows > 0) {
        header('Location: ../pages/signin.php?error=signinerror&username=' . urlencode($username) . '&email=' .urlencode($email));
        exit();
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    if ($passwordHash === false) {
        error_log('Password hashing failed during registration.');
        http_response_code(500);
        exit('A server error occurred. Please try again later.');
    }

    // Save values using parameters, not string concatenation.
    $insert = $conn->prepare(
        "INSERT INTO users (username, email_address, password_hash, role)
        VALUES (?, ?, ?, 'user')"
    );
    if (!$insert) {
    error_log('Registration insert preparation failed.');
    http_response_code(500);
    exit('A server error occurred. Please try again later.');
    }
    $insert->bind_param('sss', $username, $email, $password);
    if (!$insert->execute()) {
    error_log('Registration insert failed.');
    $insert->close();
    header('Location: ../pages/signin.php?error=signinerror&username=' . urlencode($username) . '&email=' . urlencode($email));
    exit();
    }
    $insert->close();

    // delete old session data and replace with new session data
    session_regenerate_id(true);

    // store session credentials
    $_SESSION['state']= "signedIn";
    $_SESSION['user_email'] = $email;
    $_SESSION['username'] = $username;
    $_SESSION['role'] = 'user';

    // redirects to the home page
    header("Location: ../pages/index.php");
    exit();
?>
