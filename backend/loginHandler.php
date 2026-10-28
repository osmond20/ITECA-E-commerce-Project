<?php 

require  __DIR__ . "/../backend/session.php";
    
    if($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['login-btn'])){
        // unidentified user is redirected to the sigin.php if tried accessing this page via URL
        header("Location: ../pages/login.php");
        exit();
    }

    require_once __DIR__ . '/../db/conn.php';

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['pw'] ?? '';

    if ($email === '' || $password === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: ../pages/login.php?error=incorrectcredentials');
        exit();
    }

    // verifying the email and password for logging in
    $stmt = $conn->prepare("SELECT * FROM users WHERE email_address=?");
    if (!$stmt) {
    error_log('Login query preparation failed.');
    http_response_code(500);
    exit('A server error occurred. Please try again later.');
    }
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result= $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    // Using the same response for unknown email addresses and incorrect passwords.
    if (!$user || !isset($user['password_hash']) || !password_verify($password, $user['password_hash'])) {
        header('Location: ../pages/login.php?error=incorrectcredentials');
        exit();
    }

    // Preventing session fixation and establishing the authenticated identity.
    session_regenerate_id(true);
    $_SESSION['state'] = 'loggedIn';
    $_SESSION['user_email'] = $user['email_address'];
    $_SESSION['username'] = $user['username'] ?? '';
    $_SESSION['role'] = ($user['role'] ?? '') === 'admin' ? 'admin' : 'user';

    if ($_SESSION['role'] === 'admin') {
        header('Location: ../admin-website/dashboard.php');
    } else {
        header('Location: ../index.php');
    }
    exit();

?>