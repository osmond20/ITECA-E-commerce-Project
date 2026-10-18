<?php
    require_once __DIR__ . '/../backend/session.php';

    if (($_SESSION['state'] ?? '') !== 'loggedIn' || ($_SESSION['role'] ?? '') !== 'admin') {
        header('Location: ../pages/login.php?error=forbidden');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers</title>
</head>
<body>
    <main>
    <h1>Customers</h1>
    <p>This admin route is protected. Its page content has not yet been implemented.</p>
    <form action="../backend/logoutHandler.php" method="post">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_logout'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
      <button type="submit" name="logout">Log out</button>
    </form>
  </main>
</body>
</html>