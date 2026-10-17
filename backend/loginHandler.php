<?php 

require  __DIR__ . "/../backend/session.php";

if(isset($_POST['login-btn'])){
    require_once __DIR__ . '/../db/conn.php';

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['pw'] ?? '';

    if(empty($email) || empty($password)){
        header("Location: ../pages/login.php?error=emptyfields&email=". urlencode($email));
        exit();
    }
    else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        header("Location: ../pages/login.php?error=emailinvalid&email=" .urlencode($email));
        exit();
    }

    // verifying the email and password for logging in
    $check1 = $conn->prepare("SELECT * FROM users WHERE email_address=?");
    $check1->bind_param('s', $email);
    $check1->execute();
    $result= $check1->get_result();
    if($result->num_rows > 0){
        $user = $result->fetch_assoc();

        if(password_verify($password, $user['password_hash'])){
            if($user['role'] === 'admin'){
                header("Location: ../admin-website/dashboard.php");
            }
            else{
                $_SESSION['state'] = 'loggedIn'; 
                header("Location: ../pages/index.php");
            }
            $check1->close();
            exit();
        }
        else if(!password_verify($password, $user['password_hash'])){
            header("Location: ../pages/login.php?error=incorrectcredentials");
            $check1->close();
            exit();
        }
    }
    else{
        header("Location: ../pages/login.php?error=accountnotfound");
        $check1->close();
        exit();
    }
}
else{
    // unidentified user is redirected to the sigin.php if tried accessing this page via URL
    header("Location: ../pages/login.php");
    exit();
}
?>