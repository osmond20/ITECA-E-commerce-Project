<?php 
    require __DIR__  . "/session.php";

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['logout']) || !isset($_SESSION['csrf_logout']) || !is_string($_POST['csrf_token'] ?? null) || !hash_equals($_SESSION['csrf_logout'], $_POST['csrf_token'])) {
    header('Location: ../pages/index.php');
    exit();
}

    // Clear session data and expire the browser's session cookie.
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 42000,
            'path' => $params['path'],
            'domain' => $params['domain'],
            'secure' => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => $params['samesite'] ?? 'Lax',
        ]);
    }

    // destroying the session
    session_destroy();
    // redirecting the user back to the home page
    header('Location: ../pages/index.php');
    exit();
?>