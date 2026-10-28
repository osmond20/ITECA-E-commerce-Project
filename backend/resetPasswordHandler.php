<?php 
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['reset-pw-btn'])) {
        header('Location: ../index.php');
        exit();
    }   

    // connecting to db
    require __DIR__ . "/../db/conn.php";

    // grabbing user credentials
    $selector = $_POST['selector'] ?? ''; // using for running a select stmt in the db
    $validator = $_POST['validator'] ?? ''; // using for validating the user
    $password = $_POST['pw'] ?? '';
    $passwordRepeat = $_POST['pw-repeat'] ?? '';

    // Both tokens must be hexadecimal before they are used anywhere
    // The reset link uses 16 hex chars for selector and 64 for validator.
    if (!preg_match('/^[a-f0-9]{16}$/i', $selector) || !preg_match('/^[a-f0-9]{64}$/i', $validator)) {
        http_response_code(400);
        exit('This password-reset link is invalid or expired. Request a new link.');
    }
    if ($password === '' || $passwordRepeat === '' || strlen($password) < 8 || strlen($password) > 4096) {
        header('Location: ../forgotpwd/create-new-password.php?newpwd=passwordinvalid&selector=' . urlencode($selector) . '&validator=' . urlencode($validator));
        exit();
    }
    if (!hash_equals($password, $passwordRepeat)) {
        header('Location: ../forgotpwd/create-new-password.php?newpwd=pwdnotsame&selector=' . urlencode($selector) . '&validator=' . urlencode($validator));
        exit();
    }

        // checking the token date if it has expired or not
        $currentDate = date("U");

        $stmt= $conn->prepare("SELECT * FROM pwdReset WHERE pwdResetSelector=? AND pwdResetExpires >=?");
        if (!$stmt) {
            error_log('Password reset token lookup preparation failed.');
            http_response_code(500);
            exit('A server error occurred. Please request a new reset link later.');
        }
            $stmt->bind_param("ss", $selector, $currentDate);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            
            /*  checking if there are no rows
                matching token from db and form in matching format
                The token is now stored hashed as a hex string so it is verified as-is
                checking if token is valid
            */
            if (!$row || !password_verify($validator, $row['pwdResetToken'])) {
                http_response_code(400);
                exit('This password-reset link is invalid or expired. Request a new link.');
            }
            
            $email = $row['pwdResetEmail'];
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            if ($newHash === false) {
                error_log('Password hashing failed during reset.');
                http_response_code(500);
                exit('A server error occurred. Please try again later.');
            }
    // Update the password and consume the token together. This assumes both tables
    $conn->begin_transaction();
    try {
        $update = $conn->prepare('UPDATE users SET password_hash = ? WHERE email_address = ?');
        if (!$update) {
            throw new RuntimeException('Could not prepare password update.');
        }
        $update->bind_param('ss', $newHash, $email);
        if (!$update->execute() || $update->affected_rows < 1) {
            $update->close();
            throw new RuntimeException('Password update failed.');
        }
        $update->close();

        $delete = $conn->prepare('DELETE FROM pwdReset WHERE pwdResetEmail = ?');
        if (!$delete) {
            throw new RuntimeException('Could not prepare reset token deletion.');
        }
        $delete->bind_param('s', $email);
        if (!$delete->execute()) {
            $delete->close();
            throw new RuntimeException('Reset token deletion failed.');
        }
        $delete->close();
        $conn->commit();
    } catch (Throwable $e) {
        $conn->rollback();
        error_log('Password reset transaction failed.');
        http_response_code(500);
        exit('A server error occurred. Please request a new reset link later.');
    }
    header('Location: ../pages/login.php?newpwd=passwordupdated');
    exit();
?>