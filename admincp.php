<?php
// @author Saad Mirza https://saadmirza.net


require 'assets/init.php';

if (YX_IsAdmin() == false) {
    header("Location: " . YX_Link(''));
    exit();
}

// autoload admin panel files
require 'admin-panel/autoload.php';
