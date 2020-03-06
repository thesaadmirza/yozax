<?php
error_reporting(0);
require_once('config.php');
require_once('assets/includes/phpMailer_config.php');
require 'assets/import/DB/vendor/autoload.php';
require_once("assets/import/php-rss/vendor/autoload.php");

$yx           = array();
// Connect to MySQL Server
$sqlConnect   = $yx['sqlConnect'] = mysqli_connect($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name);

$db = new MysqliDb($sqlConnect);

// Handling Server Errors
$ServerErrors = array();
if (mysqli_connect_errno()) {
    $ServerErrors[] = "Failed to connect to MySQL: " . mysqli_connect_error();
}
if (!function_exists('curl_init')) {
    $ServerErrors[] = "PHP CURL is NOT installed on your web server !";
}
if (!extension_loaded('gd') && !function_exists('gd_info')) {
    $ServerErrors[] = "PHP GD library is NOT installed on your web server !";
}
if (!extension_loaded('zip')) {
    $ServerErrors[] = "ZipArchive extension is NOT installed on your web server !";
}
if (!version_compare(PHP_VERSION, '5.4.0', '>=')) {
    $ServerErrors[] = "Required PHP_VERSION >= 5.4.0 , Your PHP_VERSION is : " . PHP_VERSION . "\n";
}
$query = mysqli_query($sqlConnect, "SET NAMES utf8mb4");
if (isset($ServerErrors) && !empty($ServerErrors)) {
    foreach ($ServerErrors as $Error) {
        echo "<h3>" . $Error . "</h3>";
    }
    die();
}


$fetch_banned_ip_data_array = array(
    'table'  => T_BANNED_IPS,
    'column' => '`ip_address`',
    'order'  => array(
        'type'   => 'DESC',
        'column' => 'id'
    )
);
    


$config                   = YX_GetConfig();
$time                     = $yx['time'] = time();
$yx['script_version']     = '1.5';
// Config Url
$config['theme_url']      = $site_url . '/themes/' . $config['theme'];
$config['site_url']       = $site_url;
$config['script_version'] = $yx['script_version'];
$yx['config']             = $config;
$yx['site_pages']         = array(
    'home'
);

$yx['banned_ips']         = YX_FetchDataFromDB($fetch_banned_ip_data_array);
$banned_ips               = array(); 
foreach ($yx['banned_ips']as $key => $ip) {
    $banned_ips[]         = $ip['ip_address'];
}

if (in_array($_SERVER["REMOTE_ADDR"], array_values($banned_ips))) {
    $banpage = YX_LoadPage('company/ban');
    echo $banpage;
    exit();
}

$http_header              = 'http://';

if (!empty($_SERVER['HTTPS'])) {
    $http_header = 'https://';
}

$yx['actual_link']       = $http_header . $_SERVER['HTTP_HOST'] . urlencode($_SERVER['REQUEST_URI']);
// Login With Url
$yx['facebookLoginUrl']  = $config['site_url'] . '/social_login.php?provider=Facebook';
$yx['twitterLoginUrl']   = $config['site_url'] . '/social_login.php?provider=Twitter';
$yx['googleLoginUrl']    = $config['site_url'] . '/social_login.php?provider=Google';
$yx['vkLoginUrl']        = $config['site_url'] . '/social_login.php?provider=Vkontakte';
$yx['login_with']        = ($yx['config']['facebook'] == 1 || $yx['config']['twitter'] == 1 || $yx['config']['google'] == 1 || $yx['config']['vkontakte'] == 1 || $yx['config']['wowonder'] == 1);
// Default User Pictures
$yx['userDefaultAvatar'] = 'upload/photos/avatar.jpg';
$yx['userDefaultCover']  = 'upload/photos/cover.jpg';
// Get LoggedIn User Data
$yx['loggedin']          = false;
$yx['page_home']         = false;
$yx['pages_home_array']  = array(
    'home',
    'create_new',
    'edit-post'
);
$yx['fav_post'] = false;
if (YX_IsLogged() == true) {
    $session_id         = (!empty($_SESSION['user_id'])) ? $_SESSION['user_id'] : $_COOKIE['user_id'];
    $yx['user_session'] = YX_GetUserFromSessionID($session_id);
    $yx['user']         = YX_UserData($yx['user_session']);
    if (!empty($yx['user']['language'])) {
        if (file_exists('assets/langs/' . $yx['user']['language'] . '.php')) {
            $_SESSION['lang'] = $yx['user']['language'];
        }
    }
    if ($yx['user']['user_id'] < 0 || empty($yx['user']['user_id']) || !is_numeric($yx['user']['user_id']) || YX_UserActive($yx['user']['username']) === false) {
        header("Location: " . YX_Link('logout'));
    }
    $yx['loggedin'] = true; 
}
if (!empty($_POST['user_id']) && !empty($_POST['s'])) {
    $application = 'phone';
    if (!empty($_GET['application'])) {
        if ($_GET['application'] == 'windows') {
            $application = YX_Secure($_GET['application']);
        }
    }
    if ($application == 'windows') {
        $_POST['s'] = md5($_POST['s']);
    }
    $s                = YX_Secure($_POST['s']);
    $user_id          = YX_Secure($_POST['user_id']);
    $check_if_session = YX_CheckUserSessionID($user_id, $s, $application);
    if ($check_if_session === true) {
        $yx['user']   = YX_UserData($user_id);

        if ($yx['user']['user_id'] < 0 || empty($yx['user']['user_id']) || !is_numeric($yx['user']['user_id']) || YX_UserActive($yx['user']['username']) === false) {
            $json_error_data = array(
                'api_status' => '400',
                'api_text' => 'failed',
                'errors' => array(
                    'error_id' => '7',
                    'error_text' => 'User id is wrong.'
                )
            );
            header("Content-type: application/json");
            echo json_encode($json_error_data, JSON_PRETTY_PRINT);
            exit();
        }
        $yx['loggedin'] = true;
    } else {
        $json_error_data = array(
            'api_status' => '400',
            'api_text' => 'failed',
            'errors' => array(
                'error_id' => '6',
                'error_text' => 'Session id is wrong.'
            )
        );
        header("Content-type: application/json");
        echo json_encode($json_error_data, JSON_PRETTY_PRINT);
        exit();
    }
}
if (isset($_GET['lang']) AND !empty($_GET['lang'])) {
    $lang_name = YX_Secure(strtolower($_GET['lang']));
    $lang_path = 'assets/langs/' . $lang_name . '.php';
    if (file_exists($lang_path)) {
        $_SESSION['lang'] = $lang_name;
        if ($yx['loggedin'] == true) {
            mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET `language` = '" . $lang_name . "' WHERE `user_id` = " . YX_Secure($yx['user']['user_id']));
        }
    }
}

//some simple switch statements to try and guess users language - this will need alot of work
//some countries have multiple languages which ruins the fun
//i.e canada speaks both english and french, belgium, ireland, south africa and switzerland are bilingual
//so we will only list opinionated - officially monolingual countries here
//France, Germany, Poland, Czech Republic, China (Mandarin), Russia, Italy, Portugal, Netherlands, 
//We also offer languages for multiple countries: arabic etc 
$country = base64_decode($_COOKIE['r']);

if ($country == "United Kingdom") {
    $_SESSION['lang'] = 'english';
}

else if ($country == "France") {
    $_SESSION['lang'] = 'french';
}
else if ($country == "Germany") {
    $_SESSION['lang'] = 'german';
}
else if (empty($_SESSION['lang'])) {
    $_SESSION['lang'] = $yx['config']['language'];
}
else{
    $_SESSION['lang'] = $yx['config']['language'];
}

$yx['language']      = $_SESSION['lang'];
$yx['language_type'] = 'ltr';
// Add rtl languages here.
$rtl_langs           = array(
    'arabic'
);
// checking if current language is rtl.
foreach ($rtl_langs as $lang) {
    if ($yx['language'] == strtolower($lang)) {
        $yx['language_type'] = 'rtl';
    }
}
// Icons Virables
$error_icon   = '<i class="fa fa-exclamation-circle"></i> ';
$success_icon = '<i class="fa fa-check"></i> ';
// Include Language File
$lang_file    = 'assets/langs/' . $yx['language'] . '.php';
if (file_exists($lang_file)) {
    require($lang_file);
}

$ccode               = YX_CustomCode('g');
$ccode               = (is_array($ccode))  ? $ccode    : array();
$config['header_cc'] = (!empty($ccode[0])) ? $ccode[0] : 0;
$config['footer_cc'] = (!empty($ccode[1])) ? $ccode[1] : 0;
$config['styles_cc'] = (!empty($ccode[2])) ? $ccode[2] : 0;

$yx['img_mime_types'] = array(
    'image/webp',
    'image/tiff',
    'image/svg+xml',
    'image/png',
    'image/jpeg',
    'image/jpg',
    'image/gif'
);

$yx['vid_mime_types'] = array(
    'video/mp4',
    'video/mov',
    'video/mpeg',
    'video/flv',
    'video/avi',
    'video/webm'
);

$yx['months'] = array(
    '1'  => 'January',
    '2'  => 'February',
    '3'  =>'March',
    '4'  =>'April',
    '5'  =>'May',
    '6'  =>'June',
    '7'  =>'July',
    '8'  =>'August',
    '9'  =>'September',
    '10' =>'October',
    '11' =>'November',
    '12' =>'December'
);

$yx['my_id']          = get_ip_address();
if ($yx['loggedin']) {
    $yx['my_id'] = $yx['user']['user_id'];
    if (empty($_SESSION['uploads'])) {
        $_SESSION['uploads'] = array();
    }
}

if ($yx['config']['usr_ads'] == 1) {

    if (!isset($_COOKIE['_uads'])) {
        setcookie('_uads', htmlentities(serialize(array(
            'date' => strtotime('+1 day'),
            'uaid_' => array()
        ))), time() + (10 * 365 * 24 * 60 * 60),'/');
    }

    $yx['user_ad_cons'] = array(
        'date' => strtotime('+1 day'),
        'uaid_' => array()
    );

    if (!empty($_COOKIE['_uads'])) {
        $yx['user_ad_cons'] = unserialize(html_entity_decode($_COOKIE['_uads']));
    }

    if (!is_array($yx['user_ad_cons']) || !isset($yx['user_ad_cons']['date']) || !isset($yx['user_ad_cons']['uaid_'])) {
        setcookie('_uads', htmlentities(serialize(array(
            'date' => strtotime('+1 day'),
            'uaid_' => array()
        ))), time() + (10 * 365 * 24 * 60 * 60),'/');
    }

    if (is_array($yx['user_ad_cons']) && isset($yx['user_ad_cons']['date']) && $yx['user_ad_cons']['date'] < time()) {
        setcookie('_uads', htmlentities(serialize(array(
            'date' => strtotime('+1 day'),
            'uaid_' => array()
        ))),time() + (10 * 365 * 24 * 60 * 60),'/');
    }
}

// setcookie('_uads', htmlentities(serialize(array(
//     'date' => strtotime('+1 day'),
//     'uaid_' => array()
// ))),time() + (10 * 365 * 24 * 60 * 60),'/');

// print_r($yx['user_ad_cons']);
// exit();
$url = YX_Link('');

require_once('assets/import/s3/aws-autoloader.php');
require_once('assets/includes/paypal_conf.php');
require_once('assets/includes/stripe_conf.php');
?>