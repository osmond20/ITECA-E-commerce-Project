<?php 
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require __DIR__. "/session.php";

    if($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['delivery-btn'])){
    // unidentified user is redirected to the sigin.php if tried accessing this page via URL
    header("Location: ../pages/signin.php");
    exit();
    }

    // loading PHPMailer (change the path if your PHPMailer folder lives somewhere else)
        require __DIR__ . "/../PHPMailer/src/Exception.php";
        require __DIR__ . "/../PHPMailer/src/PHPMailer.php";
        require __DIR__ . "/../PHPMailer/src/SMTP.php";

        // Gmail login details + website address (see mail-config.php)
        $configPath = __DIR__ . '/mailconfig.php';
        if (!is_file($configPath)) {
            error_log('Mail configuration file is missing.');
            header('Location: ../admin-website/orders.php?error=serverError');
            exit();
        }
        $config = require $configPath;

    require_once __DIR__ . '/../db/conn.php';

    $orderID = (int) $_POST['delivery'];
    $orderEmail = (string) $_POST['delivery-email'];
    $url = rtrim($config['base_url'], '/').'/pages/contact.php';

    // sql stmt
    $insert = $conn->prepare(
        "INSERT INTO users (order_id)
        VALUES (?)"
    );
    if (!$insert) {
    error_log('Connection failed');
    http_response_code(500);
    exit('A server error occurred. Please try again later.');
    }
    $insert->bind_param('s', $orderID);
    if (!$insert->execute()) {
        error_log('Failed to save to db');
        $insert->close();
        header('Location: ../admin-website/orders.php?error=serverError');
        exit();
    }
    $insert->close();

    // send email to customer that delivery is out
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

                // contact url page
                $contactURL = 

                // sender / recipient
                $mail->setFrom($config['username'], $config['from_name']);
                $mail->addAddress($orderEmail);
                $mail->addReplyTo($config['username'], $config['from_name']);

                $mail->isHTML(true);
                $mail->Subject = "Reset your password";
                $mail->Body    = '<h1>True Gent Africa</h1>
                    <p>We have sent your order out for delivery. We use Courier Guy for deliveries as
                    they will be touch with you, when in the vicinity of your address to deliver your 
                    parcel. Thank you for purchasing from us!
                    </p>
                    <p>If you have review on products or any questions you may have about them. Please contact
                    us here at '.$contactURL .'</p>
                    <p></p>
                    <p class="copyright">&copy; ' . date("Y") . ' True Gent Africa. All Rights Reserved.</p>';
                $mail->AltBody = "We have sent your order out for delivery. We use Courier Guy for deliveries
                as they will be in touch with you to ensure you receive your order.\n"
                    . "To get in touch with us, if you have any questions or a review on our products. Contact us here at\n"
                    . $contactURL. "\n\n"
                    . "If you did not make an order, you can ignore this email.";

                $mail->send();
            }
            catch(Exception $e){
                // not telling the visitor (that would reveal the account exists),
                // but recording it so you can see what went wrong
                error_log('Delivery email failed; check mail server configuration.');
            }


            //redirecting back to the user
            header("Location: ../admin-website/orders.php?delivery=success");
            exit();

?>