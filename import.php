<?php
// @author Saad Mirza https://saadmirza.net
error_reporting(0);
require_once("assets/import/hybridauth/hybridauth/Hybrid/Auth.php");
require_once("assets/import/hybridauth/hybridauth/Hybrid/Endpoint.php");

Hybrid_Endpoint::process();
