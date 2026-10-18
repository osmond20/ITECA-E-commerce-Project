<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    if(isset($_POST['reset-request-btn'])){
        require __DIR__ . "/../db/conn.php";

        // loading PHPMailer (change the path if your PHPMailer folder lives somewhere else)
        require __DIR__ . "/../PHPMailer/src/Exception.php";
        require __DIR__ . "/../PHPMailer/src/PHPMailer.php";
        require __DIR__ . "/../PHPMailer/src/SMTP.php";

        // Gmail login details + website address (see mail-config.php)
        $configPath = __DIR__ . '/mailconfig.php';
        if (!is_file($configPath)) {
            error_log('Password reset mail configuration file is missing.');
            header('Location: ../pages/resetpassword.php?reset=success');
            exit();
        }
        $config = require $configPath;

        $userEmail = trim($_POST['email'] ?? '');

        // stopping early if this doesn't look like an email address
        if(!filter_var($userEmail, FILTER_VALIDATE_EMAIL)){
            header("Location: ../pages/resetpassword.php?reset=invalid");
            exit();
        }

        /* using tokens for --cryptographic security--
         - using 2 tokens (safety precautions):
            token 1 (validator) will authenticate the user
            token 2 (selector) will perform a database lookup to pinpoint
            what existing can be verified against the user with
            when they return back to the website
        */

        // using bytes that will converted to hexadecimal format
        $selector = bin2hex(random_bytes(8));
        // token for authenticating the user.
        $validator = bin2hex(random_bytes(32));

        // creating link to send to the user by email
        $url = rtrim($config['base_url'], '/')
            . "/forgotpwd/create-new-password.php?selector=" . $selector . "&validator=" . $validator;

        // creating an expiry date for the token (30 minutes)
        $expires = date("U") + 1800;

        // only creating a token + sending an email if the address belongs to a real user.
        // The user is shown the same message either way, so nobody can use this form
        // to find out which emails are registered (and we don't email strangers).
        $sql = $conn->prepare("SELECT 1 FROM users WHERE email_address=? LIMIT 1");
        if(!$sql){
            error_log('Password reset account lookup could not be prepared.');
            header('Location: ../pages/resetpassword.php?reset=success');
            exit();
        }
        $sql->bind_param("s", $userEmail);
        $sql->execute();
        $userExists = $sql->get_result()->num_rows > 0;
        $sql->close();

        if($userExists){
            // setting up logic to delete any existing tokens
            $sql = $conn->prepare("DELETE FROM pwdReset WHERE pwdResetEmail=?");

            if(!$sql){
                error_log('Password reset token deletion could not be prepared.');
                header('Location: ../pages/resetpassword.php?reset=success');
                exit();
            }
            else{
                $sql->bind_param("s", $userEmail);
                $sql->execute();
                $sql->close();
            }

            $sql = $conn->prepare("INSERT INTO 
            pwdReset (pwdResetEmail, pwdResetSelector, 
            pwdResetToken, pwdResetExpires) VALUES (?, ?, ?, ?)");
            if(!$sql){
                error_log('Password reset token insertion could not be prepared.');
                header('Location: ../pages/resetpassword.php?reset=success');
                exit();
            }else{
                // hashing the token for security
                $hashedToken = password_hash($validator, PASSWORD_DEFAULT);
                $sql->bind_param("ssss", $userEmail, $selector, $hashedToken, $expires);
                $sql->execute();
            }

            $sql->close();

            // ---------- sending the email with PHPMailer ----------
            $mail = new PHPMailer(true); // true = throw exceptions on errors

            try{
                // debug output goes to the PHP error log (never to the page,
                // because the page redirects below)
                $mail->SMTPDebug   = false; // Never enable SMTP debug output in production.
                $mail->Debugoutput = 'error_log';

                // server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = $config['username'];
                $mail->Password   = $config['app_password'];
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
                $mail->CharSet    = 'UTF-8';

                // sender / recipient
                $mail->setFrom($config['username'], $config['from_name']);
                $mail->addAddress($userEmail);
                $mail->addReplyTo($config['username'], $config['from_name']);

                // content
                $safeUrl = htmlspecialchars($url, ENT_QUOTES);

                $mail->isHTML(true);
                $mail->Subject = "Reset your password";
                $mail->Body    = '<h1>True Gent Africa</h1>
                    <p>We received a password reset request. The link to reset your password is below.
                    It expires in 30 minutes. If you did not make this request, you can ignore this email.</p>
                    <p><a href="' . $safeUrl . '">Reset your password</a></p>
                    <p>If the link does not work, copy this address into your browser:<br>' . $safeUrl . '</p>
                    <p class="copyright">&copy; ' . date("Y") . ' True Gent Africa. All Rights Reserved.</p>';
                $mail->AltBody = "We received a password reset request.\n"
                    . "Open this link to reset your password (it expires in 30 minutes):\n"
                    . $url . "\n\n"
                    . "If you did not make this request, you can ignore this email.";

                $mail->send();
            }
            catch(Exception $e){
                // not telling the visitor (that would reveal the account exists),
                // but recording it so you can see what went wrong
                error_log('Password reset email failed; check mail server configuration.');
            }
        }

        header("Location: ../pages/resetpassword.php?reset=success");
        exit();
    }
    else{
        //if user enters the page via the URL
        header("Location: ../pages/index.php");
        exit();
    }
?>