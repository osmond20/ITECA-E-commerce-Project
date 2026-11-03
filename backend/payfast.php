<?php 
// using return type declarated function to return the settings for payfast
function payfastSettings(): array
{
    // your own payfast sandbox credentials
    $settings = [
        'merchant_id'  => 'YOUR MERCHANT ID',
        'merchant_key' => 'YOUR MERCHANT KEY',
        'passphrase'   => 'YOUR PASSPHRASE',
        'site_url'     => 'https://yourshop.co.za',
        'host'         => 'sandbox.payfast.co.za',
    ];

    if ($settings['merchant_id'] === '' || $settings['merchant_key'] === '' || $settings['site_url'] === '') {
        error_log('PayFast settings are missing (PAYFAST_MERCHANT_ID / PAYFAST_MERCHANT_KEY / SITE_URL).');
        http_response_code(500);
        exit('Payments are temporarily unavailable.');
    }
    return $settings;
}

// generates payfast signature
function payfastSignature(array $data, string $passphrase = ''): string
{
    $pairs = [];
    foreach ($data as $name => $value) {
        if ($value !== '' && $value !== null) {          // blank fields are left out
            $pairs[] = $name . '=' . urlencode(trim((string) $value));
        }
    }
    $text = implode('&', $pairs);
    if ($passphrase !== '') {
        $text .= '&passphrase=' . urlencode(trim($passphrase));
    }
    return md5($text);
}
?>
