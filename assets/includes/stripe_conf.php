<?php
// @author Saad Mirza https://saadmirza.net

require "assets/import/stripe/vendor/autoload.php";

\Stripe\Stripe::setApiKey($yx['config']['stripe_secret_key']);
