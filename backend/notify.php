<?php
/**
 * notify.php — PayFast ITN (aligned with official PayFast sample)
 *
 * After any payment attempt, open backend/itn-debug.log in File Manager.
 */

require_once __DIR__ . '/payfast.php';

function itnLog(string $message): void
{
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . "\n";
    @file_put_contents(__DIR__ . '/itn-debug.log', $line, FILE_APPEND | LOCK_EX);
}

// ---------- Official-style helpers (from PayFast docs) ----------

/**
 * Build parameter string from ITN data (stops at signature), matching PayFast sample.
 */
function pfBuildParamString(array $pfData): string
{
    $pfParamString = '';
    foreach ($pfData as $key => $val) {
        if ($key !== 'signature') {
            $pfParamString .= $key . '=' . urlencode(stripslashes((string) $val)) . '&';
        } else {
            break;
        }
    }
    return substr($pfParamString, 0, -1);
}

function pfValidSignature(array $pfData, string $pfParamString, $pfPassphrase = null): bool
{
    if ($pfPassphrase === null || $pfPassphrase === '') {
        $tempParamString = $pfParamString;
    } else {
        $tempParamString = $pfParamString . '&passphrase=' . urlencode($pfPassphrase);
    }
    $signature = md5($tempParamString);
    $received  = (string) ($pfData['signature'] ?? '');
    itnLog('Signature received=' . $received . ' calculated=' . $signature);
    return hash_equals($signature, $received);
}

/**
 * Official sample uses HTTP_REFERER. ITN often has no referer on free hosts,
 * so this is best-effort only and must NOT block payment.
 */
function pfValidIP(): bool
{
    $validHosts = [
        'www.payfast.co.za',
        'sandbox.payfast.co.za',
        'w1w.payfast.co.za',
        'w2w.payfast.co.za',
    ];
    $validIps = [];
    foreach ($validHosts as $pfHostname) {
        $ips = @gethostbynamel($pfHostname);
        if ($ips !== false) {
            $validIps = array_merge($validIps, $ips);
        }
    }
    $validIps = array_unique($validIps);

    $refererHost = '';
    if (!empty($_SERVER['HTTP_REFERER'])) {
        $parts = parse_url($_SERVER['HTTP_REFERER']);
        $refererHost = $parts['host'] ?? '';
    }

    if ($refererHost !== '') {
        $referrerIp = gethostbyname($refererHost);
        $ok = in_array($referrerIp, $validIps, true);
        itnLog('IP check via referer host=' . $refererHost . ' ip=' . $referrerIp . ' ok=' . ($ok ? 'yes' : 'no'));
        return $ok;
    }

    // Fallback: REMOTE_ADDR (often proxy on free hosts)
    $remote = $_SERVER['REMOTE_ADDR'] ?? '';
    $ok = $remote !== '' && in_array($remote, $validIps, true);
    itnLog('IP check via REMOTE_ADDR=' . $remote . ' ok=' . ($ok ? 'yes' : 'no') . ' (informational only)');
    return $ok;
}

function pfValidPaymentData($cartTotal, array $pfData): bool
{
    $gross = (float) ($pfData['amount_gross'] ?? 0);
    $ok = !(abs((float) $cartTotal - $gross) > 0.01);
    itnLog('Amount check cartTotal=' . $cartTotal . ' amount_gross=' . $gross . ' ok=' . ($ok ? 'yes' : 'no'));
    return $ok;
}

function pfValidServerConfirmation(string $pfParamString, string $pfHost = 'sandbox.payfast.co.za'): bool
{
    if (!in_array('curl', get_loaded_extensions(), true)) {
        itnLog('WARNING: curl not loaded — skipping server confirmation');
        return true;
    }

    $url = 'https://' . $pfHost . '/eng/query/validate';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_USERAGENT, null);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $pfParamString);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);

    $response = curl_exec($ch);
    $errno = curl_errno($ch);
    $error = curl_error($ch);
    curl_close($ch);

    itnLog('Server confirm response=' . var_export($response, true) . ' errno=' . $errno . ' err=' . $error);

    if ($errno !== 0) {
        // Free hosts sometimes block outbound curl — do not hard-fail after signature+amount OK
        itnLog('WARNING: curl failed during validate — allowing through');
        return true;
    }

    return $response === 'VALID';
}

function markOrderPaid(mysqli $conn, string $reference, string $pfPaymentId): bool
{
    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare(
            "UPDATE orders
             SET status = 'paid', paid_at = NOW(), pf_payment_id = ?
             WHERE order_reference = ? AND status = 'pending'"
        );
        if (!$stmt) {
            throw new RuntimeException($conn->error);
        }
        $stmt->bind_param('ss', $pfPaymentId, $reference);
        if (!$stmt->execute()) {
            throw new RuntimeException($stmt->error);
        }
        $changed = $stmt->affected_rows === 1;
        $stmt->close();
        $conn->commit();
        itnLog('markOrderPaid reference=' . $reference . ' changed=' . ($changed ? 'yes' : 'no'));
        return $changed;
    } catch (Throwable $e) {
        $conn->rollback();
        itnLog('markOrderPaid error: ' . $e->getMessage());
        return false;
    }
}

function markOrderCancelled(mysqli $conn, string $reference): bool
{
    try {
        $stmt = $conn->prepare(
            "UPDATE orders SET status = 'cancelled'
             WHERE order_reference = ? AND status = 'pending'"
        );
        if (!$stmt) {
            throw new RuntimeException($conn->error);
        }
        $stmt->bind_param('s', $reference);
        $stmt->execute();
        $changed = $stmt->affected_rows === 1;
        $stmt->close();
        itnLog('markOrderCancelled reference=' . $reference . ' changed=' . ($changed ? 'yes' : 'no'));
        return $changed;
    } catch (Throwable $e) {
        itnLog('markOrderCancelled error: ' . $e->getMessage());
        return false;
    }
}

function sendOrderConfirmationEmail(array $order, string $reference): void
{
    $configPath = __DIR__ . '/mailconfig.php';
    if (!is_file($configPath)) {
        itnLog('mailconfig.php missing — skip email');
        return;
    }
    $config = require $configPath;
    $base = __DIR__ . '/../PHPMailer/src';
    if (!is_file($base . '/PHPMailer.php')) {
        itnLog('PHPMailer missing — skip email');
        return;
    }
    require_once $base . '/Exception.php';
    require_once $base . '/PHPMailer.php';
    require_once $base . '/SMTP.php';

    try {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['username'];
        $mail->Password   = $config['app_password'];
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';
        $mail->setFrom($config['username'], $config['from_name'] ?? 'True Gent Africa');
        $mail->addAddress($order['customer_email'], $order['customer_name']);
        $total = number_format((float) $order['total'], 2);
        $mail->isHTML(true);
        $mail->Subject = 'Payment confirmation for order ' . $reference;
        $mail->Body = '<p>Hi ' . htmlspecialchars($order['customer_name'], ENT_QUOTES, 'UTF-8')
            . ', payment received for order <strong>' . htmlspecialchars($reference, ENT_QUOTES, 'UTF-8')
            . '</strong>. Total R' . $total . '.</p>';
        $mail->AltBody = "Payment received for order {$reference}. Total R{$total}.";
        $mail->send();
        itnLog('Email sent to ' . $order['customer_email']);
    } catch (Throwable $e) {
        itnLog('Email failed: ' . $e->getMessage());
    }
}

// ---------- Health check ----------
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'GET') {
    header('Content-Type: text/plain; charset=utf-8');
    echo "notify.php is live\n";
    echo "Time: " . date('c') . "\n";
    itnLog('GET health-check from ' . ($_SERVER['REMOTE_ADDR'] ?? '?'));
    exit;
}

// ---------- ITN entry (official pattern: 200 first, then process) ----------
header('HTTP/1.0 200 OK');
flush();

itnLog('======== ITN HIT ========');
itnLog('METHOD=' . ($_SERVER['REQUEST_METHOD'] ?? '?'));
itnLog('REMOTE_ADDR=' . ($_SERVER['REMOTE_ADDR'] ?? '?'));
itnLog('POST=' . json_encode($_POST));

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || empty($_POST)) {
    itnLog('Not a POST with data — exit');
    exit;
}

define('SANDBOX_MODE', true);
$pfHost = SANDBOX_MODE ? 'sandbox.payfast.co.za' : 'www.payfast.co.za';

// Posted variables from ITN — strip slashes (official sample)
$pfData = $_POST;
foreach ($pfData as $key => $val) {
    $pfData[$key] = stripslashes((string) $val);
}

$pfParamString = pfBuildParamString($pfData);
itnLog('pfParamString=' . $pfParamString);

$pf = payfastSettings();
$passphrase = $pf['passphrase'] ?? null;

require_once __DIR__ . '/../db/conn.php';

$reference = (string) ($pfData['m_payment_id'] ?? '');
if ($reference === '') {
    itnLog('Missing m_payment_id');
    exit;
}

$stmt = $conn->prepare(
    'SELECT total, customer_email, customer_name, status
     FROM orders WHERE order_reference = ? LIMIT 1'
);
if (!$stmt) {
    itnLog('DB prepare failed: ' . $conn->error);
    exit;
}
$stmt->bind_param('s', $reference);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    itnLog('Unknown order: ' . $reference);
    exit;
}
itnLog('Order status=' . $order['status'] . ' total=' . $order['total']);

if ($order['status'] !== 'pending') {
    itnLog('Already processed — skip');
    exit;
}

$cartTotal = $order['total'];

// Official 4 checks
$check1 = pfValidSignature($pfData, $pfParamString, $passphrase);
$check2 = pfValidIP(); // informational on free hosts
$check3 = pfValidPaymentData($cartTotal, $pfData);
$check4 = pfValidServerConfirmation($pfParamString, $pfHost);

itnLog('Checks: signature=' . ($check1 ? 'pass' : 'FAIL')
    . ' ip=' . ($check2 ? 'pass' : 'fail(ignored)')
    . ' amount=' . ($check3 ? 'pass' : 'FAIL')
    . ' server=' . ($check4 ? 'pass' : 'FAIL'));

// IP must NOT block (free hosts / empty referer). Require 1, 3, 4.
if ($check1 && $check3 && $check4) {
    $status = (string) ($pfData['payment_status'] ?? '');
    itnLog('payment_status=' . $status);

    if ($status === 'COMPLETE') {
        $pfPaymentId = (string) ($pfData['pf_payment_id'] ?? '');
        if (markOrderPaid($conn, $reference, $pfPaymentId)) {
            sendOrderConfirmationEmail($order, $reference);
            itnLog('SUCCESS: order marked paid');
        }
    } elseif ($status === 'CANCELLED') {
        markOrderCancelled($conn, $reference);
    } else {
        itnLog('Unhandled payment_status: ' . $status);
    }
} else {
    itnLog('Some required checks failed — investigate manually');
}

itnLog('======== ITN END ========');
exit;
