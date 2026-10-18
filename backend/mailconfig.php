<?php
/**
 * mail-config.php - one place for your email + site settings.
 *
 * Save this next to resetRequest.php (your backend/ folder).
 * Add it to .gitignore, and on a live server keep it outside the public web folder.
 * It contains your Gmail App Password, so never share or commit it.
 */
return [
    // The Gmail account that SENDS the emails
    'username'     => 'your.account@gmail.com',

    // 16-character Google App Password (no spaces). NOT your normal Gmail password.
    'app_password' => '',

    // Name shown to the person receiving the email
    'from_name'    => '',

    // Address of your website, no trailing slash. This is what the reset link starts with.
    //   Local testing:  'http://localhost/yourproject'
    //   Live site:      'https://www.yourdomain.com'
    'base_url'     => 'http://localhost/yourproject',

    // true  = write the full Gmail conversation to your PHP error log (use while testing)
    // false = silent (use on the live site)
    'debug'        => true,
];
?>