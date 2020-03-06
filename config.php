<?php
// @author Saad Mirza https://saadmirza.net
$env = 'local';

if ($env == 'local') {

    // MySQL Hostname
    $sql_db_host = "localhost";
    // MySQL Database User
    $sql_db_user = "root";
    // MySQL Database Password
    $sql_db_pass = "root";
    // MySQL Database Name
    $sql_db_name = "yozax_local";

    // Site URL
    $site_url = "http://localhost/yozax"; // e.g (http://example.com)

} else if ($env == 'prod') {

    // MySQL Hostname
    $sql_db_host = "aaitfn82xdoxur.ck0tvaiaa8xg.us-east-1.rds.amazonaws.com";
    // MySQL Database User
    $sql_db_user = "thedumbestdino";
    // MySQL Database Password
    $sql_db_pass = "Conky&99XbO";
    // MySQL Database Name
    $sql_db_name = "ebdb";

    // Site URL
    $site_url = "https://saadmirza.net/yozax"; // e.g (http://example.com)

} else {
    echo ('Connection Error');
}
