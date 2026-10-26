<?php
/* =====================================================================
   notify.php  -  EXAMPLE: PayFast tells us a payment happened (ITN)

   PayFast calls this file in the background after the customer pays.
   The customer never sees it. This is the ONLY place that should mark an
   order "paid" - the return_url page can be opened by anyone, but only
   PayFast can call this (and we check that it really is PayFast).

   Put this file at backend/notify.php (its address is the notify_url in
   payfast.php). From the video: step 4, "Confirm payment".
   ===================================================================== */

    require_once __DIR__ . '/payfast.php';

    // 1) Tell PayFast "got it" straight away (it wants HTTP 200 quickly).
    http_response_code(200);
    header('Content-Length: 0');
    flush();

    // ---------- the four security checks from the video ----------

    // CHECK 1: is the signature correct? (proves nobody changed the data)
    // Here we use ALL fields exactly as PayFast sent them, except the signature itself.
    function itnSignatureIsValid(array $post, string $passphrase): bool
    {
        $received = (string) ($post['signature'] ?? '');
        unset($post['signature']);

        $pairs = [];
        foreach ($post as $name => $value) {
            $pairs[] = $name . '=' . urlencode(stripslashes((string) $value));
        }
        $text = implode('&', $pairs);
        if ($passphrase !== '') {
            $text .= '&passphrase=' . urlencode(trim($passphrase));
        }
        return hash_equals(md5($text), $received);
    }

    // CHECK 2: did the request come from a real PayFast server?
    function itnComesFromPayFast(): bool
    {
        $validHosts = ['www.payfast.co.za', 'sandbox.payfast.co.za', 'w1w.payfast.co.za', 'w2w.payfast.co.za'];
        $validIps = [];
        foreach ($validHosts as $host) {
            $ips = gethostbynamel($host);
            if ($ips !== false) {
                $validIps = array_merge($validIps, $ips);
            }
        }
        return in_array($_SERVER['REMOTE_ADDR'] ?? '', $validIps, true);
    }

    // CHECK 3: is the amount what we expected? (compare in cents, not decimals)
    function itnAmountMatches(float $amountPaid, string $ourTotal): bool
    {
        return (int) round($amountPaid * 100) === (int) round(((float) $ourTotal) * 100);
    }

    // CHECK 4: ask PayFast "did you really send this?" - it answers VALID or INVALID.
    function itnConfirmedByPayFast(array $post, string $host): bool
    {
        $pairs = [];
        foreach ($post as $name => $value) {
            if ($name !== 'signature') {
                $pairs[] = $name . '=' . urlencode(stripslashes((string) $value));
            }
        }

        $curl = curl_init('https://' . $host . '/eng/query/validate');
        curl_setopt_array($curl, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => implode('&', $pairs),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT        => 15,
        ]);
        $answer = curl_exec($curl);
        curl_close($curl);

        return $answer === 'VALID';
    }

    // ---------- update the order ----------

    // Marks a pending order as paid and takes the stock off the shelf.
    // Returns true if it changed the order. Safe to call twice (PayFast can
    // send the same notification again): only a "pending" order is changed.
    function markOrderPaid(mysqli $conn, string $reference, string $pfPaymentId): bool
    {
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("UPDATE orders SET status = 'paid', paid_at = NOW(), pf_payment_id = ?
                                    WHERE order_reference = ? AND status = 'pending'");
            $stmt->bind_param('ss', $pfPaymentId, $reference);
            $stmt->execute();
            $changed = $stmt->affected_rows === 1;
            $stmt->close();

            if ($changed) {
                // take the bought quantities off the stock
                $stock = $conn->prepare('UPDATE products p
                                        JOIN order_items i ON i.product_id = p.product_id
                                        JOIN orders o ON o.order_id = i.order_id
                                        SET p.stock_quantity = GREATEST(p.stock_quantity - i.quantity, 0)
                                        WHERE o.order_reference = ?');
                $stock->bind_param('s', $reference);
                $stock->execute();
                $stock->close();
            }
            $conn->commit();
            return $changed;
        } catch (Throwable $error) {
            $conn->rollback();
            error_log('markOrderPaid failed: ' . $error->getMessage());
            return false;
        }
    }

    // ---------- run (skipped when this file is only loaded for testing) ----------
    if (PHP_SAPI !== 'cli' && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
        require_once __DIR__ . '/../db/conn.php';

        $pf = payfastSettings();
        $post = $_POST;

        $reference = (string) ($post['m_payment_id'] ?? '');
        $stmt = $conn->prepare('SELECT total FROM orders WHERE order_reference = ? LIMIT 1');
        $stmt->bind_param('s', $reference);
        $stmt->execute();
        $order = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$order) {
            error_log('ITN: unknown order ' . $reference);
            exit();
        }
        if (!itnSignatureIsValid($post, $pf['passphrase'])) {
            error_log('ITN: bad signature for ' . $reference);
            exit();
        }
        if (!itnComesFromPayFast()) {
            error_log('ITN: request did not come from PayFast (' . ($_SERVER['REMOTE_ADDR'] ?? '?') . ')');
            exit();
        }
        if (!itnAmountMatches((float) ($post['amount_gross'] ?? 0), $order['total'])) {
            error_log('ITN: amount mismatch for ' . $reference);
            exit();
        }
        if (!itnConfirmedByPayFast($post, $pf['host'])) {
            error_log('ITN: PayFast did not confirm ' . $reference);
            exit();
        }

        // All four checks passed, so we can trust the payment status.
        $status = (string) ($post['payment_status'] ?? '');
        if ($status === 'COMPLETE') {
            markOrderPaid($conn, $reference, (string) ($post['pf_payment_id'] ?? ''));
            // TODO: send the customer their confirmation email here
        }
    }
?>