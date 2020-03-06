<?php
// @author Saad Mirza https://saadmirza.net

require 'assets/import/PayPal/vendor/autoload.php';
$paypal = new \PayPal\Rest\ApiContext(
  new \PayPal\Auth\OAuthTokenCredential(
    $yx['config']['paypal_id'],
    $yx['config']['paypal_secret']
  )
);

$paypal->setConfig(
  array(
    'mode' => $yx['config']['paypal_mode']
  )
);
