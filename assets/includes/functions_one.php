<?php

// @author Saad Mirza https://saadmirza.net
require_once('app.php');

use Aws\S3\S3Client;

function YX_GetConfig()
{
    global $sqlConnect;
    $data  = array();
    $query = mysqli_query($sqlConnect, "SELECT * FROM " . T_CONFIG);
    while ($fetched_data = mysqli_fetch_assoc($query)) {
        $data[$fetched_data['name']] = $fetched_data['value'];
    }
    return $data;
}
function YX_GetSessions()
{
    global $sqlConnect;
    $data  = array();
    $query = mysqli_query($sqlConnect, "SELECT * FROM " . T_APP_SESSIONS);
    while ($fetched_data = mysqli_fetch_assoc($query)) {
        $data[$fetched_data['name']] = $fetched_data['value'];
    }
    return $data;
}
function time_ago_in_php($timestamp)
{

    $time_ago        = $timestamp;
    $current_time    = time();
    $time_difference = $current_time - $time_ago;
    $seconds         = $time_difference;
    $minutes = round($seconds / 60); // value 60 is seconds  
    $hours   = round($seconds / 3600); //value 3600 is 60 minutes * 60 sec  
    $days    = round($seconds / 86400); //86400 = 24 * 60 * 60;  
    $weeks   = round($seconds / 604800); // 7*24*60*60;  
    $months  = round($seconds / 2629440); //((365+365+365+365+366)/5/12)*24*60*60  
    $years   = round($seconds / 31553280); //(365+365+365+365+366)/5 * 24 * 60 * 60

    if ($seconds <= 60) {

        return "Just Now";
    } else if ($minutes <= 60) {

        if ($minutes == 1) {

            return "one minute ago";
        } else {

            return "$minutes minutes ago";
        }
    } else if ($hours <= 24) {

        if ($hours == 1) {

            return "an hour ago";
        } else {

            return "$hours hrs ago";
        }
    } else if ($days <= 7) {

        if ($days == 1) {

            return "yesterday";
        } else {

            return "$days days ago";
        }
    } else if ($weeks <= 4.3) {

        if ($weeks == 1) {

            return "a week ago";
        } else {

            return "$weeks weeks ago";
        }
    } else if ($months <= 12) {

        if ($months == 1) {

            return "a month ago";
        } else {

            return "$months months ago";
        }
    } else {

        if ($years == 1) {

            return "one year ago";
        } else {

            return "$years years ago";
        }
    }
}

function YX_SaveTerm($update_name, $value)
{
    global $yx, $config, $sqlConnect;
    if ($yx['loggedin'] == false) {
        return false;
    }
    $update_name = YX_Secure($update_name);
    $value       = mysqli_real_escape_string($sqlConnect, $value);
    $query_one   = " UPDATE " . T_TERMS . " SET `text` = '{$value}' WHERE `type` = '{$update_name}'";
    $query       = mysqli_query($sqlConnect, $query_one);
    if ($query) {
        return true;
    } else {
        return false;
    }
}
function YX_SaveConfig($update_name, $value)
{
    global $yx, $config, $sqlConnect;
    if ($yx['loggedin'] == false) {
        return false;
    }
    if (!array_key_exists($update_name, $config)) {
        return false;
    }
    $update_name = YX_Secure($update_name);
    $value       = mysqli_real_escape_string($sqlConnect, $value);
    $table       = T_CONFIG;
    $query_one   = "UPDATE `$table` SET `value` = '{$value}' WHERE `name` = '{$update_name}'";

    $query       = mysqli_query($sqlConnect, $query_one);
    if ($query) {
        return true;
    }
    return false;
}
function YX_UserData($user_id = 0, $options = array())
{
    global $yx, $sqlConnect;
    if (empty($user_id)) {
        return false;
    }
    if (!empty($options['data'])) {
        $fetched_data   = $user_id;
    } else {
        $user_id      = YX_Secure($user_id);
        $query_one    = "SELECT * FROM " . T_USERS . " WHERE `user_id` = {$user_id}";
        $sql          = mysqli_query($sqlConnect, $query_one);
        $fetched_data = mysqli_fetch_assoc($sql);
    }
    $data         = array();
    if (empty($fetched_data)) {
        return array();
    }
    $fetched_data['avatar']        = YX_GetMedia($fetched_data['avatar']);
    $fetched_data['cover']         = YX_GetMedia($fetched_data['cover']);
    $fetched_data['url']           = YX_Link('user/' . $fetched_data['username']);
    $fetched_data['id']            = $fetched_data['user_id'];
    $fetched_data['about_decoded'] = br2nl($fetched_data['about']);
    $fetched_data['name']          = '';
    if (!empty($fetched_data['first_name'])) {
        if (!empty($fetched_data['last_name'])) {
            $fetched_data['name'] = $fetched_data['first_name'] . ' ' . $fetched_data['last_name'];
        } else {
            $fetched_data['name'] = $fetched_data['first_name'];
        }
    } else {
        $fetched_data['name'] = $fetched_data['username'];
    }
    $fetched_data['fav_post']            = explode(',', $fetched_data['fav_post']);
    return $fetched_data;
}
function YX_Login($username, $password)
{
    global $sqlConnect;
    if (empty($username) || empty($password)) {
        return false;
    }
    $username = YX_Secure($username);
    $password = YX_Secure(sha1($password));
    $query    = mysqli_query($sqlConnect, "SELECT COUNT(`user_id`) FROM " . T_USERS . " WHERE (`username` = '{$username}' OR `email` = '{$username}') AND `password` = '{$password}'");
    return (YX_Sql_Result($query, 0) == 1) ? true : false;
}
function YX_SetLoginWithSession($user_email)
{
    if (empty($user_email)) {
        return false;
    }
    $user_email          = YX_Secure($user_email);
    $_SESSION['user_id'] = YX_UserIdFromEmail($user_email);
}
function YX_UserActive($username)
{
    global $sqlConnect;
    if (empty($username)) {
        return false;
    }
    $username = YX_Secure($username);
    $query    = mysqli_query($sqlConnect, "SELECT COUNT(`user_id`) FROM " . T_USERS . "  WHERE (`username` = '{$username}' OR `email` = '{$username}') AND `active` = '1'");
    return (YX_Sql_Result($query, 0) == 1) ? true : false;
}
function YX_UserInactive($username)
{
    global $sqlConnect;
    if (empty($username)) {
        return false;
    }
    $username = YX_Secure($username);
    $query    = mysqli_query($sqlConnect, "SELECT COUNT(`user_id`) FROM " . T_USERS . "  WHERE (`username` = '{$username}' OR `email` = '{$username}') AND `active` = '2'");
    return (YX_Sql_Result($query, 0) == 1) ? true : false;
}
function YX_UserExists($username)
{
    global $sqlConnect;
    if (empty($username)) {
        return false;
    }
    $username = YX_Secure($username);
    $query    = mysqli_query($sqlConnect, "SELECT COUNT(`user_id`) FROM " . T_USERS . " WHERE `username` = '{$username}'");
    return (YX_Sql_Result($query, 0) == 1) ? true : false;
}
function YX_UserIdFromUsername($username)
{
    global $sqlConnect;
    if (empty($username)) {
        return false;
    }
    $username = YX_Secure($username);
    $query    = mysqli_query($sqlConnect, "SELECT `user_id` FROM " . T_USERS . " WHERE `username` = '{$username}'");
    return YX_Sql_Result($query, 0, 'user_id');
}
function YX_UserFollowers($id)
{
    global $sqlConnect;
    if (empty($id)) {
        return false;
    }
    $data  = array();

    $query    = mysqli_query($sqlConnect, "SELECT * FROM " . T_FOLLOWS . " WHERE `user_id` = '{$id}'");
    while ($fetched_data = mysqli_fetch_assoc($query)) {
        $data = $fetched_data['relation_arr'];
    }
    return $data;
}

function YX_UserIdForLogin($username)
{
    global $sqlConnect;
    if (empty($username)) {
        return false;
    }
    $username = YX_Secure($username);
    $query    = mysqli_query($sqlConnect, "SELECT `user_id` FROM " . T_USERS . " WHERE `username` = '{$username}' OR `email` = '{$username}'");
    return YX_Sql_Result($query, 0, 'user_id');
}
function YX_UserIdFromEmail($email)
{
    global $sqlConnect;
    if (empty($email)) {
        return false;
    }
    $email = YX_Secure($email);
    $query = mysqli_query($sqlConnect, "SELECT `user_id` FROM " . T_USERS . " WHERE `email` = '{$email}'");
    return YX_Sql_Result($query, 0, 'user_id');
}
function YX_EmailExists($email)
{
    global $sqlConnect;
    if (empty($email)) {
        return false;
    }
    $email = YX_Secure($email);
    $query = mysqli_query($sqlConnect, "SELECT COUNT(`user_id`) FROM " . T_USERS . " WHERE `email` = '{$email}'");
    return (YX_Sql_Result($query, 0) == 1) ? true : false;
}
function YX_RegisterUser($registration_data)
{
    global $yx, $sqlConnect;
    if (empty($registration_data)) {
        return false;
    }
    if ($yx['config']['registration'] == 0) {
        return false;
    }
    $ip     = '0.0.0.0';
    $get_ip = get_ip_address();
    if (!empty($get_ip)) {
        $ip = $get_ip;
    }
    $registration_data['registered'] = date('n') . '/' . date("Y");
    $registration_data['password']   = sha1($registration_data['password']);
    $registration_data['ip_address'] = YX_Secure($ip);
    $registration_data['language']   = $yx['config']['language'];
    if (!empty($_SESSION['lang'])) {
        $lang_name = strtolower($_SESSION['lang']);
        $lang_path = 'assets/langs/' . $lang_name . '.php';
        if (file_exists($lang_path)) {
            $registration_data['language'] = YX_Secure($lang_name);
        }
    }
    $fields = '`' . implode('`, `', array_keys($registration_data)) . '`';
    $data   = '\'' . implode('\', \'', $registration_data) . '\'';
    $query = mysqli_query($sqlConnect, "INSERT INTO " . T_USERS . " ({$fields}) VALUES ({$data})") or die(mysqli_error($sqlConnect));
    if ($query) {
        return true;
    } else {
        return false;
    }
}
function YX_SendMessage($data = array())
{
    global $yx, $sqlConnect, $mail;
    $email_from      = $data['from_email'] = YX_Secure($data['from_email']);
    $to_email        = $data['to_email'] = YX_Secure($data['to_email']);
    $subject         = $data['subject'];
    $message_body    = mysqli_real_escape_string($sqlConnect, $data['message_body']);
    $data['charSet'] = YX_Secure($data['charSet']);
    if ($yx['config']['smtp_or_mail'] == 'mail') {
        $mail->IsMail();
    } else if ($yx['config']['smtp_or_mail'] == 'smtp') {
        $mail->isSMTP();
        $mail->Host        = $yx['config']['smtp_host']; // Specify main and backup SMTP servers
        $mail->SMTPAuth    = true; // Enable SMTP authentication
        $mail->Username    = $yx['config']['smtp_username']; // SMTP username
        $mail->Password    = $yx['config']['smtp_password']; // SMTP password
        $mail->SMTPSecure  = $yx['config']['smtp_encryption']; // Enable TLS encryption, `ssl` also accepted
        $mail->Port        = $yx['config']['smtp_port'];
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
    } else {
        return false;
    }
    $mail->IsHTML($data['is_html']);
    $mail->setFrom($data['from_email'], $data['from_name']);
    $mail->addAddress($data['to_email'], $data['to_name']); // Add a recipient
    $mail->Subject = $data['subject'];
    $mail->CharSet = $data['charSet'];
    $mail->MsgHTML($data['message_body']);
    if ($mail->send()) {
        $mail->ClearAddresses();
        return true;
    } else {
        return false;
    }
}
function YX_EmailCode($email = '', $code = '')
{
    global $sqlConnect;
    if (empty($email) || empty($code)) {
        return false;
    }
    $code  = YX_Secure($code);
    $email = YX_Secure($email);
    $query = mysqli_query($sqlConnect, "SELECT COUNT(`user_id`) FROM " . T_USERS . " WHERE `email` = '{$email}' AND `email_code` = '{$code}'");
    return (YX_Sql_Result($query, 0) == 1) ? true : false;
}
function YX_PasswordResetCode($code = '')
{
    global $sqlConnect;
    if (empty($code)) {
        return false;
    }
    $code  = YX_Secure($code);
    $parts = @explode('_', $code);
    if (empty($parts)) {
        return false;
    }
    $user_id   = YX_Secure($parts[0]);
    $pass_code = YX_Secure($parts[1]);
    $query     = mysqli_query($sqlConnect, "SELECT COUNT(`user_id`) FROM " . T_USERS . " WHERE `password` = '{$pass_code}' AND `user_id` = '{$user_id}'");
    return (YX_Sql_Result($query, 0) == 1) ? true : false;
}
function YX_ActivateAccount($user_id = 0, $username = '')
{
    global $yx, $sqlConnect;
    if (empty($username)) {
        return false;
    }
    if (empty($user_id)) {
        return false;
    }
    if (YX_UserActive($username)) {
        return false;
    }
    $query = mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET `active` = '1' WHERE `user_id` = '{$user_id}'");
    if ($query) {
        return true;
    }
}
function YX_UpdateUserData($user_id = 0, $update_data = array())
{
    global $yx, $sqlConnect;
    if (empty($user_id) || empty($update_data)) {
        return false;
    }
    if (!is_numeric($user_id)) {
        return false;
    }
    $user_id = YX_Secure($user_id);
    if ($user_id != $yx['user']['user_id']) {
        if (YX_IsAdmin() === false) {
            return false;
        }
    }
    $update = array();
    foreach ($update_data as $field => $data) {
        $update[] = '`' . $field . '` = \'' . YX_Secure($data, 0) . '\'';
    }
    $impload   = implode(', ', $update);
    $query_one = " UPDATE " . T_USERS . " SET {$impload} WHERE `user_id` = {$user_id}";
    $query     = mysqli_query($sqlConnect, $query_one);
    if ($query) {
        return true;
    } else {
        return false;
    }
}
function YX_ImportImageFromLogin($media, $crop = 1)
{
    global $yx;
    if (!file_exists('upload/photos/' . date('Y'))) {
        mkdir('upload/photos/' . date('Y'), 0777, true);
    }
    if (!file_exists('upload/photos/' . date('Y') . '/' . date('m'))) {
        mkdir('upload/photos/' . date('Y') . '/' . date('m'), 0777, true);
    }
    $dir               = 'upload/photos/' . date('Y') . '/' . date('m');
    $file_dir          = $dir . '/' . YX_GenerateKey() . '_avatar.jpg';
    $arrContextOptions = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false
        )
    );
    $getImage          = fetchDataFromURL($media);
    if (!empty($getImage)) {
        $importImage = file_put_contents($file_dir, $getImage);
        if ($importImage && $crop == 1) {
            YX_Resize_Crop_Image(400, 400, $file_dir, $file_dir, 100);
        }
    }
    if (file_exists($file_dir)) {
        return $file_dir;
    } else {
        return false;
    }
}

function YX_ImportImageFromUrl($media, $id = 0, $type = '')
{
    global $yx;
    if (empty($media)) {
        return false;
    }
    $folder = 'upload';
    if (!empty($id)) {
        $folder = 'tmp';
    }
    if (!file_exists($folder . '/photos/' . date('Y'))) {
        mkdir($folder . '/photos/' . date('Y'), 0777, true);
    }
    if (!file_exists($folder . '/photos/' . date('Y') . '/' . date('m'))) {
        mkdir($folder . '/photos/' . date('Y') . '/' . date('m'), 0777, true);
    }
    $size      = @getimagesize($media);
    $extension = @image_type_to_extension($size[2]);
    if (empty($extension)) {
        $extension = '.jpg';
    }
    $dir       = "$folder/photos/" . date('Y') . '/' . date('m');
    $file_dir  = $dir . '/' . YX_GenerateKey() . '_url_image' . $extension;
    $get_media = @file_get_contents($media);
    if (!empty($get_media)) {
        $importImage = @file_put_contents($file_dir, $get_media);
        $types       = array(
            'news',
            'lists',
            'video',
            'music',
            'quiz',
            'poll'
        );
        if ($importImage && in_array($type, $types)) {
            @YX_Resize_Crop_Image(600, 320, $file_dir, $file_dir, 100);
        }
        if ($yx['config']['amazone_s3'] == 1) {
            $upload = YX_UploadToS3($file_dir);
            return $file_dir;
        }
    }
    if (file_exists($file_dir)) {
        return $file_dir;
    } else {
        return false;
    }
}


function YX_ShareTmpFile($data = array())
{
    global $yx, $sqlConnect;
    $allowed = '';
    if (!file_exists('tmp/photos/' . date('Y'))) {
        @mkdir('tmp/photos/' . date('Y'), 0777, true);
    }
    if (!file_exists('tmp/photos/' . date('Y') . '/' . date('m'))) {
        @mkdir('tmp/photos/' . date('Y') . '/' . date('m'), 0777, true);
    }
    if (isset($data['file']) && !empty($data['file'])) {
        $data['file'] = YX_Secure($data['file']);
    }
    if (isset($data['name']) && !empty($data['name'])) {
        $data['name'] = YX_Secure($data['name']);
    }
    if (isset($data['name']) && !empty($data['name'])) {
        $data['name'] = YX_Secure($data['name']);
    }
    if (empty($data)) {
        return false;
    }
    $allowed           = 'jpg,png,jpeg,gif';
    $new_string        = pathinfo($data['name'], PATHINFO_FILENAME) . '.' . strtolower(pathinfo($data['name'], PATHINFO_EXTENSION));
    $extension_allowed = explode(',', $allowed);
    $file_extension    = pathinfo($new_string, PATHINFO_EXTENSION);
    if (!in_array($file_extension, $extension_allowed)) {
        return false;
    }
    if ($file_extension == 'jpg' || $file_extension == 'jpeg' || $file_extension == 'png' || $file_extension == 'gif') {
        $folder   = 'photos';
        $fileType = 'image';
    }
    if (empty($folder) || empty($fileType)) {
        return false;
    }
    $ar = array(
        'image/png',
        'image/jpeg',
        'image/gif'
    );
    if (!in_array($data['type'], $ar)) {
        return false;
    }
    $dir         = "tmp/{$folder}/" . date('Y') . '/' . date('m');
    $filename    = $dir . '/' . YX_GenerateKey() . '_' . date('d') . '_' . md5(time()) . "_{$fileType}.{$file_extension}";
    $second_file = pathinfo($filename, PATHINFO_EXTENSION);
    if (move_uploaded_file($data['file'], $filename)) {
        $last_data             = array();
        $last_data['filename'] = $filename;
        $last_data['name']     = $data['name'];
        return $last_data;
    }
}

function YX_ShareFile($data = array(), $type = 0)
{
    global $yx, $sqlConnect;
    $allowed = '';
    if (!file_exists('upload/photos/' . date('Y'))) {
        @mkdir('upload/photos/' . date('Y'), 0777, true);
    }
    if (!file_exists('upload/photos/' . date('Y') . '/' . date('m'))) {
        @mkdir('upload/photos/' . date('Y') . '/' . date('m'), 0777, true);
    }
    if (!file_exists('upload/videos/' . date('Y'))) {
        @mkdir('upload/videos/' . date('Y'), 0777, true);
    }
    if (!file_exists('upload/videos/' . date('Y') . '/' . date('m'))) {
        @mkdir('upload/videos/' . date('Y') . '/' . date('m'), 0777, true);
    }
    if (!file_exists('upload/sounds/' . date('Y'))) {
        @mkdir('upload/sounds/' . date('Y'), 0777, true);
    }
    if (!file_exists('upload/sounds/' . date('Y') . '/' . date('m'))) {
        @mkdir('upload/sounds/' . date('Y') . '/' . date('m'), 0777, true);
    }
    if (isset($data['file']) && !empty($data['file'])) {
        $data['file'] = YX_Secure($data['file']);
    }
    if (isset($data['name']) && !empty($data['name'])) {
        $data['name'] = YX_Secure($data['name']);
    }
    if (isset($data['name']) && !empty($data['name'])) {
        $data['name'] = YX_Secure($data['name']);
    }
    if (empty($data)) {
        return false;
    }
    $allowed           = 'jpg,png,jpeg,gif,mp4,mp3';
    $new_string        = pathinfo($data['name'], PATHINFO_FILENAME) . '.' . strtolower(pathinfo($data['name'], PATHINFO_EXTENSION));
    $extension_allowed = explode(',', $allowed);
    $file_extension    = pathinfo($new_string, PATHINFO_EXTENSION);
    if (!in_array($file_extension, $extension_allowed)) {
        return false;
    }
    if ($file_extension == 'jpg' || $file_extension == 'jpeg' || $file_extension == 'png' || $file_extension == 'gif') {
        $folder   = 'photos';
        $fileType = 'image';
    } else if ($file_extension == 'mp4' || $file_extension == 'webm' || $file_extension == 'flv') {
        $folder   = 'videos';
        $fileType = 'video';
    } else if ($file_extension == 'mp3' || $file_extension == 'wav') {
        $folder   = 'sounds';
        $fileType = 'soundFile';
    }
    if (empty($folder) || empty($fileType)) {
        return false;
    }
    $ar = array(
        'video/mp4',
        'video/mov',
        'video/mpeg',
        'video/flv',
        'video/avi',
        'video/webm',
        'audio/wav',
        'audio/mpeg',
        'video/quicktime',
        'audio/mp3',
        'image/png',
        'image/jpeg',
        'image/gif'
    );
    if (!in_array($data['type'], $ar)) {
        return false;
    }
    $dir         = "upload/{$folder}/" . date('Y') . '/' . date('m');
    $filename    = $dir . '/' . YX_GenerateKey() . '_' . date('d') . '_' . md5(time()) . "_{$fileType}.{$file_extension}";
    $second_file = pathinfo($filename, PATHINFO_EXTENSION);
    if (move_uploaded_file($data['file'], $filename)) {
        if ($second_file == 'jpg' || $second_file == 'jpeg' || $second_file == 'png' || $second_file == 'gif') {

            if ($type == 1) {
                @YX_CompressImage($filename, $filename, 50);
                $explode2  = @end(explode('.', $filename));
                $explode3  = @explode('.', $filename);
                $last_file = $explode3[0] . '_small.' . $explode2;
                @YX_Resize_Crop_Image(800, 480, $filename, $last_file, 60);
                if ($yx['config']['amazone_s3'] == 1 && !empty($last_file)) {
                    $upload_s3 = YX_UploadToS3($last_file);
                }
            } else {
                if ($second_file != 'gif') {
                    if (!empty($data['crop'])) {
                        $crop_image = YX_Resize_Crop_Image($data['crop']['width'], $data['crop']['height'], $filename, $filename, 60);
                    }
                    @YX_CompressImage($filename, $filename, 90);
                }

                if ($yx['config']['amazone_s3'] == 1 && !empty($last_file)) {
                    $upload_s3 = YX_UploadToS3($last_file);
                }
            }
        }

        if ($yx['config']['amazone_s3'] == 1 && !empty($filename)) {
            $upload_s3 = YX_UploadToS3($filename);
        }

        $last_data             = array();
        $last_data['filename'] = $filename;
        $last_data['name']     = $data['name'];
        return $last_data;
    }
}
function YX_FetchTweet($tweet_url = '')
{
    global $yx, $sqlConnect;
    if (empty($tweet_url)) {
        return false;
    }
    $tweet_url = YX_Secure($tweet_url);
    $tweeturl  = 'https://publish.twitter.com/oembed?url=' . $tweet_url;
    $get_tweet = @file_get_contents($tweeturl);
    if (!empty($get_tweet)) {
        $json = json_decode($get_tweet, true);
        return $json;
    }
    return false;
}
function YX_FetchInestegramPost($instagram_url = '')
{
    global $yx, $sqlConnect;
    if (empty($instagram_url)) {
        return false;
    }
    $instagram_url      = YX_Secure($instagram_url);
    $instagram_link_url = 'https://api.instagram.com/oembed/?url=' . urlencode($instagram_url);
    $get_instagram      = file_get_contents($instagram_link_url);
    if (!empty($get_instagram)) {
        $json = json_decode($get_instagram, true);
        return $json;
    }
    return false;
}
function YX_FetchSoundCloud($sound_link = '')
{
    global $yx, $sqlConnect;
    if (empty($sound_link)) {
        return false;
    }
    $arrContextOptions = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false
        )
    );
    $url               = "https://api.soundcloud.com/resolve.json?url=" . urlencode($sound_link) . "&client_id=d4f8636b1b1d07e4461dcdc1db226a53";
    try {
        $track_json = file_get_contents($url, false, stream_context_create($arrContextOptions));
    } catch (Exception $e) {
        return false;
    }
    $track = json_decode($track_json, true);
    if (!empty($track[0]['tracks'][0]['id'])) {
        return $track[0]['tracks'][0]['id'];
    } else if (!empty($track['id'])) {
        return $track['id'];
    } else {
        return false;
    }
}
function YX_ValidateEntries($id = 0, $data = array())
{
    global $yx, $sqlConnect, $error_icon, $lang;
    if (empty($id)) {
        return false;
    }
    if (empty($data)) {
        return false;
    }
    if (!empty($data['data_inputs'])) {
        if ($data['type'] == 'text') {
            if (empty($data['data_inputs']['entry_text'])) {
                $errors = array(
                    'id' => $id,
                    'message' => $error_icon . $lang['text_content_is_required']
                );
            } else if (mb_strlen($data['data_inputs']['entry_text']) < 50) {
                $errors = array(
                    'id' => $id,
                    'message' => $error_icon . $lang['text_content_must_be_more_than_50']
                );
            }
        } else if ($data['type'] == 'video') {
            $video_array = array(
                'youtube',
                'dailymotion',
                'vine',
                'vimeo',
                'facebook',
                'source'
            );
            if (empty($data['data_inputs']['entry_video_type'])) {
                $errors = array(
                    'id' => $id,
                    'message' => $error_icon . $lang['video_link_is_required']
                );
            } else if (!in_array($data['data_inputs']['entry_video_type'], $video_array)) {
                $errors = array(
                    'id' => $id,
                    'message' => $error_icon . $lang['video_type_is_required']
                );
            } else if (empty($data['data_inputs']['entry_video_id']) && empty($data['data_inputs']['entry_video_src'])) {
                $errors = array(
                    'id' => $id,
                    'message' => $error_icon . $lang['video_url_is_required']
                );
            } else if ($data['data_inputs']['entry_video_type'] == 'source') {
                if (empty($_SESSION['uploads']) || !in_array($data['data_inputs']['entry_video_src'], $_SESSION['uploads'])) {
                    $errors = array(
                        'id' => $id,
                        'message' => $error_icon . $lang['please_check_details']
                    );
                }
            }
        } else if ($data['type'] == 'image') {
            if (empty($data['data_inputs']['entry_image'])) {
                $errors = array(
                    'id' => $id,
                    'message' => $error_icon . $lang['image_is_required']
                );
            } else if (!preg_match('/[\w\-]+\.(jpg|png|gif|jpeg)/', $data['data_inputs']['entry_image'])) {
                $errors = array(
                    'id' => $id,
                    'message' => $error_icon . $lang['invalid_image_url']
                );
            }
        } else if ($data['type'] == 'tweet') {
            if (empty($data['data_inputs']['entry_tweet_url'])) {
                $errors = array(
                    'id' => $id,
                    'message' => $error_icon . $lang['tweet_url_is_required']
                );
            } else if (!preg_match("/(http|https):\/\/twitter\.com\/[a-zA-Z0-9_]+\/status\/[0-9]+/", $data['data_inputs']['entry_tweet_url'])) {
                $errors = array(
                    'id' => $id,
                    'message' => $error_icon . $lang['wrong_tweet_url']
                );
            }
        } else if ($data['type'] == 'facebook') {
            if (empty($data['data_inputs']['entry_facebook'])) {
                $errors = array(
                    'id' => $id,
                    'message' => $error_icon . $lang['post_url_is_required']
                );
            } else if (!preg_match('/(http|https):\/\/(www\.)facebook\.com\/(.*)\/(posts|videos|photos)\/(.*)/', $data['data_inputs']['entry_facebook'])) {
                $errors = array(
                    'id' => $id,
                    'message' => $error_icon . $lang['invalid_fb_url']
                );
            }
        } else if ($data['type'] == 'instagram') {
            if (empty($data['data_inputs']['entry_instagram_url'])) {
                $errors = array(
                    'id' => $id,
                    'message' => $error_icon . $lang['post_url_is_required']
                );
            } else if (!preg_match('/(http|https):\/\/(www\.)instagram\.com\/p\/(.*)+/', $data['data_inputs']['entry_instagram_url'])) {
                $errors = array(
                    'id' => $id,
                    'message' => $error_icon . $lang['invalid_ig_url']
                );
            }
        } else if ($data['type'] == 'soundcloud') {
            if (empty($data['data_inputs']['entry_soundcloud_id'])) {
                $errors = array(
                    'id' => $id,
                    'message' => $error_icon . $lang['soundcloud_is_required']
                );
            } else if (!is_numeric($data['data_inputs']['entry_soundcloud_id'])) {
                $errors = array(
                    'id' => $id,
                    'message' => $error_icon . $lang['wrong_sound_cloud_id']
                );
            }
        }
        if ($data['type'] == 'option') {
            if (empty($data['data_inputs']['entry_option_name'])) {
                $errors = array(
                    'id' => $id,
                    'message' => $error_icon . $lang['option_title_is_required']
                );
            } else if (empty($data['data_inputs']['entry_image'])) {
                $errors = array(
                    'id' => $id,
                    'message' => $error_icon . $lang['image_is_required']
                );
            } else if (!preg_match('/[\w\-]+\.(jpg|png|gif|jpeg)/', $data['data_inputs']['entry_image'])) {
                $errors = array(
                    'id' => $id,
                    'message' => $error_icon . $lang['invalid_image_url']
                );
            } else {
                if (!empty($data['data_inputs']['answers'])) {
                    foreach ($data['data_inputs']['answers'] as $key => $value) {
                        if (empty($value)) {
                            $errors = array(
                                'id' => $id,
                                'message' => $error_icon . $lang['answer'] . ' #' . ($key + 1) . ' ' . $lang['is_empty']
                            );
                        }
                    }
                }
            }
        }
        if ($data['type'] == 'result') {
            if (empty($data['data_inputs']['entry_result_titile'])) {
                $errors = array(
                    'id' => $id,
                    'message' => $error_icon . 'Result title is required'
                );
            } else if (empty($data['data_inputs']['entry_image'])) {
                $errors = array(
                    'id' => $id,
                    'message' => $error_icon . $lang['image_is_required']
                );
            } else if (empty($data['data_inputs']['entry_result_description'])) {
                $errors = array(
                    'id' => $id,
                    'message' => $error_icon . $lang['desc_is_required']
                );
            } else if (!preg_match('/[\w\-]+\.(jpg|png|gif|jpeg)/', $data['data_inputs']['entry_image'])) {
                $errors = array(
                    'id' => $id,
                    'message' => $error_icon . $lang['invalid_image_url']
                );
            }
        }
        if ($data['type'] == 'question') {
            if (empty($data['data_inputs']['entry_image'])) {
                $errors = array(
                    'id' => $id,
                    'message' => $error_icon . $lang['image_is_required']
                );
            } else if (!preg_match('/[\w\-]+\.(jpg|png|gif|jpeg)/', $data['data_inputs']['entry_image'])) {
                $errors = array(
                    'id' => $id,
                    'message' => $error_icon . $lang['invalid_image_url']
                );
            } else {
                if (!empty($data['data_inputs']['answers'])) {
                    foreach ($data['data_inputs']['answers'] as $key => $question_ans_data) {
                        foreach ($question_ans_data as $value) {
                            if (empty($value)) {
                                $errors = array(
                                    'id' => $id,
                                    'message' => $error_icon . ' #' . ($key + 1) . ' ' . $lang['please_fill_info']
                                );
                            }
                        }
                    }
                }
            }
        }
    }
    if (!empty($errors)) {
        return $errors;
    }
    return array();
}
function YX_InsertPost($news_data = array(), $post_type = '')
{
    global $yx, $sqlConnect, $db;
    if (empty($news_data)) {
        return false;
    }
    $post_type               = YX_Secure($post_type);
    $post_types              = array(
        'news',
        'list',
        'poll',
        'video',
        'music'
    );
    $news_data['registered'] = date('n') . '/' . date("Y");
    $fields                  = '`' . implode('`, `', array_keys($news_data)) . '`';
    $data                    = '\'' . implode('\', \'', $news_data) . '\'';
    $table                   = '';
    switch ($post_type) {
        case 'news':
            $table = T_NEWS;
            break;
        case 'list':
            $table = T_LISTS;
            break;
        case 'poll':
            $table = T_POLLS_PAGES;
            break;
        case 'video':
            $table = T_VIDEOS;
            break;
        case 'music':
            $table = T_MUSIC;
            break;
        case 'quiz':
            $table = T_QUIZZES;
            break;
    }
    $query = mysqli_query($sqlConnect, "INSERT INTO {$table} ({$fields}) VALUES ({$data})") or die(mysqli_error($sqlConnect));
    $insert_id = mysqli_insert_id($sqlConnect);
    if ($query) {

        if (!YX_IsPRO()) {
            $db->where('user_id', $yx['user']['id'])->update(T_USERS, array(
                'posts' => ($yx['user']['posts'] += 1)
            ));
        }

        return $insert_id;
    }
    return false;
}
function YX_UpdatePost($post_id, $update_data = array(), $post_type = '')
{
    global $yx, $sqlConnect;
    if (empty($post_id) || empty($update_data) || empty($post_type)) {
        return false;
    }
    if (!is_numeric($post_id)) {
        return false;
    }
    $post_type  = YX_Secure($post_type);
    $post_types = array(
        'news',
        'list',
        'poll',
        'video',
        'music',
        'quiz'
    );
    if (!in_array($post_type, $post_types)) {
        return false;
    }
    $post_id = YX_Secure($post_id);
    $update  = array();
    foreach ($update_data as $field => $data) {
        $update[] = '`' . $field . '` = \'' . YX_Secure($data, 0) . '\'';
    }
    $table = '';
    switch ($post_type) {
        case 'news':
            $table = T_NEWS;
            break;
        case 'list':
            $table = T_LISTS;
            break;
        case 'poll':
            $table = T_POLLS_PAGES;
            break;
        case 'video':
            $table = T_VIDEOS;
            break;
        case 'music':
            $table = T_MUSIC;
            break;
        case 'quiz':
            $table = T_QUIZZES;
            break;
    }
    $impload   = implode(', ', $update);
    $query_one = " UPDATE {$table} SET {$impload} WHERE `id` = {$post_id}";
    $query     = mysqli_query($sqlConnect, $query_one);
    if ($query) {
        return true;
    } else {
        return false;
    }
}
function YX_UpdateEntries($id = 0, $post_id = 0, $data = array())
{
    global $yx, $sqlConnect, $error_icon;
    if (empty($id)) {
        return false;
    }
    if (empty($data)) {
        return false;
    }
    $entry_page        = 'news';
    $entry_pages_array = array(
        'news',
        'lists',
        'videos',
        'music',
        'poll',
        'quiz'
    );
    if (!empty($data['entry_page'])) {
        if (in_array($data['entry_page'], $entry_pages_array)) {
            $entry_page = YX_Secure($data['entry_page']);
        }
    }
    if (!empty($data['data_inputs'])) {
        $post_id  = YX_Secure($post_id);
        $table    = T_ENTRIES;
        $time     = time();
        $options  = false;
        $user_id  = YX_Secure($yx['user']['user_id']);
        $index_id = YX_Secure($data['index_id']);
        if ($data['type'] == 'text') {
            $text_title  = YX_Secure($data['data_inputs']['entry_title']);
            $text_text   = YX_Secure($data['data_inputs']['entry_text']);
            $update_keys = "`title` = '$text_title', `text` = '$text_text'";
        } else if ($data['type'] == 'video') {
            $video_title = YX_Secure($data['data_inputs']['entry_title']);
            $video_type  = YX_Secure($data['data_inputs']['entry_video_type']);
            $video_id    = YX_Secure($data['data_inputs']['entry_video_id']);
            $video_url   = YX_Secure($data['data_inputs']['entry_video_url']);
            $video_src   = YX_GetMediaSource(YX_Secure($data['data_inputs']['entry_video_src']));
            if (empty($video_url) && empty($video_src)) {
                return false;
            } else if (empty($video_url)) {
                $video_url = $video_src;
            }
            $update_keys = "`video_type` = '$video_type', `video` = '$video_id', `title` = '$video_title', `video_url` = '$video_url'";
        } else if ($data['type'] == 'image') {
            $image_title = YX_Secure($data['data_inputs']['entry_title']);
            $image_url   = YX_GetMediaSource(YX_Secure($data['data_inputs']['entry_image']));

            $update_keys = "`title` = '$image_title', `image` = '$image_url'";
        } else if ($data['type'] == 'tweet') {
            $tweet_title    = YX_Secure($data['data_inputs']['entry_title']);
            $tweet_url      = YX_Secure($data['data_inputs']['entry_tweet_url']);
            $get_Tweet      = YX_FetchTweet($tweet_url);
            $tweet_full_url = $tweet_url;
            if (!empty($get_Tweet['html'])) {
                $tweet_url   = YX_Secure($get_Tweet['html']);
                $update_keys = "`title` = '$tweet_title', `tweet_url` = '$tweet_full_url', `tweet` = '$tweet_url'";
            }
        } else if ($data['type'] == 'facebook') {
            $facebook_title = YX_Secure($data['data_inputs']['entry_title']);
            $facebook_url   = YX_Secure($data['data_inputs']['entry_facebook']);
            $update_keys    = "`title` = '$facebook_title', `facebook_post` = '$facebook_url'";
        } else if ($data['type'] == 'instagram') {
            $instagram_title    = YX_Secure($data['data_inputs']['entry_title']);
            $instagram_full_url = $data['data_inputs']['entry_instagram_url'];
            $instagram_url      = YX_FetchInestegramPost($instagram_full_url);
            if (!empty($instagram_url)) {
                $instagram_url = YX_Secure($instagram_url['html']);
                $update_keys   = "`title` = '$instagram_title', `instagram_url` = '$instagram_full_url', `instagram` = '$instagram_url'";
            }
        } else if ($data['type'] == 'soundcloud') {
            $soundcloud_title = YX_Secure($data['data_inputs']['entry_title']);
            $soundcloud_id    = YX_Secure($data['data_inputs']['entry_soundcloud_id']);
            $update_keys      = "`title` = '$soundcloud_title', `soundcloud_id` = '$soundcloud_id'";
        } else if ($data['type'] == 'option') {
            $entry_option_name = YX_Secure($data['data_inputs']['entry_option_name']);
            $entry_image       = YX_GetMediaSource(YX_Secure($data['data_inputs']['entry_image']));
            $update_keys       = "`image` = '$entry_image', `title` = '$entry_option_name'";
            $query             = "UPDATE {$table} SET {$update_keys} WHERE `id` = {$id}";
            $query             = mysqli_query($sqlConnect, $query);
            if ($query) {
                $query   = "UPDATE {$table} SET `index_id` = '{$index_id}' WHERE `id` = {$id}";
                $query   = mysqli_query($sqlConnect, $query);
                $options = true;
                if (!empty($data['data_inputs']['answers'])) {
                    foreach ($data['data_inputs']['answers'] as $key => $value) {
                        $value['text']  = (!YX_IsUrl($value['text'])) ? $value['text'] : YX_GetMediaSource($value['text']);
                        $insert_options = YX_InsertOptions($id, $value['text'], $data['data_inputs']['answer']);
                    }
                }
                return true;
            }
        } else if ($data['type'] == 'result') {
            $quiz_title  = YX_Secure($data['data_inputs']['entry_result_titile']);
            $quiz_text   = YX_Secure($data['data_inputs']['entry_result_description']);
            $entry_image = YX_Secure(YX_GetMediaSource($data['data_inputs']['entry_image']));
            $update_keys = "`image` = '$entry_image', `title` = '$quiz_title', `text` = '{$quiz_text}'";
            $query       = "UPDATE {$table} SET {$update_keys} WHERE `id` = {$id}";
            $query       = mysqli_query($sqlConnect, $query);
        } else if ($data['type'] == 'question') {
            $question_title = YX_Secure($data['data_inputs']['entry_question_name']);
            $entry_image    = YX_GetMediaSource(YX_Secure($data['data_inputs']['entry_image']));
            $update_keys    = "`image` = '{$entry_image}',`title` = '{$question_title}'";
            $sql            = "UPDATE {$table} SET {$update_keys} WHERE `id` = {$id}";
            $query          = mysqli_query($sqlConnect, $sql);
            if ($query && !empty($data['data_inputs']['answers'])) {
                $options   = true;
                $sql_id    = $id;
                $t_answers = T_ANSWERS;
                foreach ($data['data_inputs']['answers'] as $key => $value) {
                    $registration_data = array(
                        'entry_id' => $sql_id,
                        'result_index' => $value['entry_answer_result'],
                        'text'  => $value['entry_answer_text'],
                        'image' => YX_GetMediaSource($value['entry_answer_img']),
                        'time'  => $time,
                        'type'  => ''
                    );
                    YX_InsertData($registration_data, $t_answers);
                }
            }
        }
        if ($options == false) {
            $query = "UPDATE {$table} SET {$update_keys} WHERE `id` = {$id}";
            $query = mysqli_query($sqlConnect, $query);
            if ($query) {
                $query = "UPDATE {$table} SET `index_id` = '{$index_id}' WHERE `id` = {$id}";
                $query = mysqli_query($sqlConnect, $query);
                return true;
            }
        }
    }
    return false;
}

function YX_InsertEntries($id = 0, $post_id = 0, $data = array())
{
    global $yx, $sqlConnect, $error_icon;
    if (empty($id)) {
        return false;
    }
    if (empty($data)) {
        return false;
    }
    $entry_page        = 'news';
    $entry_pages_array = array(
        'news',
        'lists',
        'videos',
        'music',
        'polls',
        'quiz'
    );
    if (!empty($data['entry_page'])) {
        if (in_array($data['entry_page'], $entry_pages_array)) {
            $entry_page = YX_Secure($data['entry_page']);
        }
    }
    if (!empty($data['data_inputs'])) {
        $post_id  = YX_Secure($post_id);
        $table    = T_ENTRIES;
        $time     = time();
        $options  = false;
        $user_id  = YX_Secure($yx['user']['user_id']);
        $index_id = YX_Secure($data['index_id']);
        if ($data['type'] == 'text') {
            $text_title   = YX_Secure($data['data_inputs']['entry_title']);
            $text_text    = YX_Secure($data['data_inputs']['entry_text']);
            $insert_keys  = '(`user_id`, `post_id`, `entry_type`, `text`, `time`, `title`, `index_id`)';
            $insert_value = "('{$user_id}', '{$post_id}', 'text', '{$text_text}', '{$time}', '{$text_title}', '{$index_id}')";
        } else if ($data['type'] == 'video') {
            $video_title = YX_Secure($data['data_inputs']['entry_title']);
            $video_type  = YX_Secure($data['data_inputs']['entry_video_type']);
            $video_id    = YX_Secure($data['data_inputs']['entry_video_id']);
            $video_url   = YX_Secure($data['data_inputs']['entry_video_url']);
            $video_src   = YX_GetMediaSource(YX_Secure($data['data_inputs']['entry_video_src']));
            if (empty($video_src) && empty($video_url)) {
                return false;
            } else if (empty($video_url)) {
                $video_url = $video_src;
            }
            $insert_keys  = '(`user_id`, `post_id`, `entry_type`, `video_type`, `video_url`, `video`, `time`, `title`, `index_id`)';
            $insert_value = "('{$user_id}', '{$post_id}', 'video', '{$video_type}', '{$video_url}', '{$video_id}', '{$time}', '{$video_title}', '{$index_id}')";
        } else if ($data['type'] == 'image') {
            $image_title = YX_Secure($data['data_inputs']['entry_title']);
            $image_url   = YX_Secure($data['data_inputs']['entry_image']);
            if (!empty($image_url)) {
                $image_url = YX_GetMediaSource($image_url);
            }
            $insert_keys  = '(`user_id`, `post_id`, `entry_type`, `image`, `time`, `title`, `index_id`)';
            $insert_value = "('{$user_id}', '{$post_id}', 'image', '{$image_url}', '{$time}', '{$image_title}', '{$index_id}')";
        } else if ($data['type'] == 'tweet') {
            $tweet_title    = YX_Secure($data['data_inputs']['entry_title']);
            $tweet_url      = YX_Secure($data['data_inputs']['entry_tweet_url']);
            $get_Tweet      = YX_FetchTweet($tweet_url);
            $tweet_full_url = $tweet_url;
            if (!empty($get_Tweet['html'])) {
                $tweet_url    = YX_Secure($get_Tweet['html']);
                $insert_keys  = '(`user_id`, `post_id`, `entry_type`, `tweet`, `tweet_url`, `time`, `title`, `index_id`)';
                $insert_value = "('{$user_id}', '{$post_id}', 'tweet', '{$tweet_url}', '{$tweet_full_url}', '{$time}', '{$tweet_title}', '{$index_id}')";
            }
        } else if ($data['type'] == 'facebook') {
            $facebook_title = YX_Secure($data['data_inputs']['entry_title']);
            $facebook_url   = YX_Secure($data['data_inputs']['entry_facebook']);
            $insert_keys    = '(`user_id`, `post_id`, `entry_type`, `facebook_post`, `time`, `title`, `index_id`)';
            $insert_value   = "('{$user_id}', '{$post_id}', 'facebook', '{$facebook_url}', '{$time}', '{$facebook_title}', '{$index_id}')";
        } else if ($data['type'] == 'instagram') {
            $instagram_title    = YX_Secure($data['data_inputs']['entry_title']);
            $instagram_full_url = $data['data_inputs']['entry_instagram_url'];
            $instagram_url      = YX_FetchInestegramPost($instagram_full_url);
            if (!empty($instagram_url)) {
                $instagram_url = YX_Secure($instagram_url['html']);
                $insert_keys   = '(`user_id`, `post_id`, `entry_type`, `instagram`, `instagram_url`, `time`, `title`, `index_id`)';
                $insert_value  = "('{$user_id}', '{$post_id}', 'instagram', '{$instagram_url}', '{$instagram_full_url}', '{$time}', '{$instagram_title}', '{$index_id}')";
            }
        } else if ($data['type'] == 'soundcloud') {
            $soundcloud_title = YX_Secure($data['data_inputs']['entry_title']);
            $soundcloud_id    = YX_Secure($data['data_inputs']['entry_soundcloud_id']);
            $insert_keys      = '(`user_id`, `post_id`, `entry_type`, `soundcloud_id`, `time`, `title`, `index_id`)';
            $insert_value     = "('{$user_id}', '{$post_id}', 'soundcloud', '{$soundcloud_id}', '{$time}', '{$soundcloud_title}', '{$index_id}')";
        } else if ($data['type'] == 'result') {
            $quiz_title   = YX_Secure($data['data_inputs']['entry_result_titile']);
            $quiz_text    = YX_Secure($data['data_inputs']['entry_result_description']);
            $entry_image  = YX_GetMediaSource($data['data_inputs']['entry_image']);
            $insert_keys  = '(`user_id`,`post_id`,`index_id`, `entry_type`, `text`, `image`,`time`,`title`, `entry_page`)';
            $insert_value = "('{$user_id}','{$post_id}','{$index_id}','result','{$quiz_text}','{$entry_image}','{$time}','{$quiz_title}','result')";
            $options      = true;
            $sql          = "INSERT INTO {$table} {$insert_keys} VALUES {$insert_value}";
            $query        = mysqli_query($sqlConnect, $sql);
        } else if ($data['type'] == 'question') {
            $question_title = YX_Secure($data['data_inputs']['entry_question_name']);
            $entry_image    = YX_GetMediaSource($data['data_inputs']['entry_image']);
            $insert_keys    = '(`user_id`,`post_id`,`index_id`, `entry_type`,`image`,`time`,`title`)';
            $insert_value   = "('{$user_id}','{$post_id}','{$index_id}','question','{$entry_image}','{$time}','{$question_title}')";
            $sql            = "INSERT INTO {$table} {$insert_keys} VALUES {$insert_value}";
            $query          = mysqli_query($sqlConnect, $sql);
            if ($query) {
                $options   = true;
                $sql_id    = mysqli_insert_id($sqlConnect);
                $t_answers = T_ANSWERS;
                foreach ($data['data_inputs']['answers'] as $key => $value) {
                    $registration_data = array(
                        'entry_id' => $sql_id,
                        'result_index' => $value['entry_answer_result'],
                        'text' => $value['entry_answer_text'],
                        'image' => YX_GetMediaSource($value['entry_answer_img']),
                        'time' => $time,
                        'type' => ''
                    );

                    (YX_InsertData($registration_data, $t_answers));
                }
                $query_two = mysqli_query($sqlConnect, "UPDATE {$table} SET `entry_page` = '{$entry_page}' WHERE `id`= '$sql_id'");
                if ($query_two) {
                    return true;
                }
            }
        } else if ($data['type'] == 'option') {
            $entry_option_name = YX_Secure($data['data_inputs']['entry_option_name']);
            $entry_image       = YX_GetMediaSource($data['data_inputs']['entry_image']);
            $insert_keys       = '(`user_id`, `post_id`, `entry_type`, `image`, `time`, `title`, `index_id`)';
            $insert_value      = "('{$user_id}', '{$post_id}', 'option', '{$entry_image}', '{$time}', '{$entry_option_name}', '{$index_id}')";
            $query             = "INSERT INTO {$table} {$insert_keys} VALUES {$insert_value}";
            $query             = mysqli_query($sqlConnect, $query);
            if ($query) {
                $options = true;
                $sql_id  = mysqli_insert_id($sqlConnect);
                foreach ($data['data_inputs']['answers'] as $key => $value) {
                    if (is_array($value)) {
                        $value = $value['text'];
                    }
                    $value          = (!YX_IsUrl($value)) ? $value : YX_GetMediaSource($value);
                    $insert_options = YX_InsertOptions($sql_id, $value, $data['data_inputs']['answer']);
                }
                $query_two = mysqli_query($sqlConnect, "UPDATE $table SET `entry_page` = '{$entry_page}' WHERE `id`= '$sql_id'");
                if ($query_two) {
                    return true;
                }
            }
        }
        if ($options == false) {
            $query     = "INSERT INTO {$table} {$insert_keys} VALUES {$insert_value}";
            $query     = mysqli_query($sqlConnect, $query);
            $insert_id = mysqli_insert_id($sqlConnect);
            if (is_numeric($insert_id)) {
                $query_two = mysqli_query($sqlConnect, "UPDATE $table SET `entry_page` = '{$entry_page}' WHERE `id`= '$insert_id'");
                if ($query_two) {
                    return true;
                }
            }
        }
    }
    return false;
}
function YX_InsertOptions($id, $value, $type = false)
{
    global $yx, $sqlConnect;
    if (empty($id)) {
        return false;
    }
    if (empty($value)) {
        return false;
    }
    if (!$type || ($type != 'i' && $type != 't')) {
        return false;
    }
    $value = YX_Secure($value);
    $id    = YX_Secure($id);
    $time  = time();
    $field = ($type == 't') ? 'text' : 'image';
    $sql   = "INSERT INTO " . T_POLLS . " (`entry_id`,`$field`, `time`,`type`) VALUES ('{$id}', '{$value}', '{$time}','{$field}')";
    $query = mysqli_query($sqlConnect, $sql);
    if ($query) {
        return true;
    }
}
function YX_UpdateOptions($id, $value)
{
    global $yx, $sqlConnect;
    if (empty($id)) {
        return false;
    }
    if (empty($value)) {
        return false;
    }
    $value = YX_Secure($value);
    $id    = YX_Secure($id);
    $time  = time();
    $query = mysqli_query($sqlConnect, "UPDATE " . T_POLLS . " SET `text` = '$value' WHERE `id` = '{$id}'");
    if ($query) {
        return true;
    }
}
function YX_GetEditPost($id = '', $page = 0, $post_type = '')
{
    global $yx, $sqlConnect;
    if (empty($id)) {
        return false;
    }
    switch ($post_type) {
        case 'news':
            $table = T_NEWS;
            break;
        case 'lists':
            $table = T_LISTS;
            break;
        case 'polls':
            $table = T_POLLS_PAGES;
            break;
        case 'videos':
            $table = T_VIDEOS;
            break;
        case 'music':
            $table = T_MUSIC;
            break;
        case 'quiz':
            $table = T_QUIZZES;
            break;
    }
    $id = @end(explode('-', $id));
    if (empty($id) || !is_numeric($id)) {
        return false;
    }
    $data         = array();
    $id           = YX_Secure($id);
    $query_one    = "SELECT * FROM $table WHERE `id` = {$id}";
    $sql          = mysqli_query($sqlConnect, $query_one);
    $fetched_data = mysqli_fetch_assoc($sql);
    if (empty($fetched_data)) {
        return array();
    }
    $fetched_data['small_image'] = YX_GetMedia($fetched_data['image']);
    $fetched_data['big_image']   = YX_GetMedia($fetched_data['image']);
    $fetched_data['big_image']   = YX_GetMedia($fetched_data['image']);
    $fetched_data['url']         = YX_Link($post_type . '/' . $fetched_data['slug'] . '-' . $id);
    $fetched_data['posted']      = YX_Time_Elapsed_String($fetched_data['time']);
    $fetched_data['entries']     = YX_GetEntries($id, $post_type, 0, $page);
    $fetched_data['publisher']   = YX_UserData($fetched_data['user_id']);
    $fetched_data['views']       = number_format($fetched_data['views']);
    return $fetched_data;
}
function YX_GetQuizResults($id = false)
{
    global $yx, $sqlConnect;
    if (empty($id)) {
        return false;
    }
    $id = @end(explode('-', $id));
    if (empty($id) || !is_numeric($id)) {
        return false;
    }
    $data      = array();
    $id        = YX_Secure($id);
    $table     = T_ENTRIES;
    $query_one = "SELECT * FROM $table WHERE `post_id` = {$id} AND `entry_page` = 'result' ";
    $query     = mysqli_query($sqlConnect, $query_one);
    while ($fetched_data = mysqli_fetch_assoc($query)) {
        $data[] = $fetched_data;
    }
    return $data;
}
function YX_GetPost($id = '', $page = 0, $post_type = '')
{
    global $yx, $sqlConnect;
    if (empty($id)) {
        return false;
    }
    switch ($post_type) {
        case 'news':
            $table = T_NEWS;
            break;
        case 'lists':
            $table = T_LISTS;
            break;
        case 'polls':
            $table = T_POLLS_PAGES;
            break;
        case 'videos':
            $table = T_VIDEOS;
            break;
        case 'music':
            $table = T_MUSIC;
            break;
        case 'quiz':
            $table = T_QUIZZES;
            break;
    }
    $id = YX_Secure($id);
    if (!is_numeric($id)) {
        $id = @end(explode('-', $id));
        if (empty($id) || !is_numeric($id)) {
            return false;
        }
    }
    $data         = array();
    $query_one    = "SELECT * FROM $table WHERE `id` = {$id}";
    $sql          = mysqli_query($sqlConnect, $query_one);
    $fetched_data = mysqli_fetch_assoc($sql);
    if (empty($fetched_data)) {
        return array();
    }
    $fetched_data['small_image'] = YX_GetMedia($fetched_data['image']);
    $fetched_data['big_image']   = YX_GetMedia($fetched_data['image']);
    $fetched_data['url']         = YX_Link($post_type . '/' . $fetched_data['slug'] . '-' . $id);
    $fetched_data['posted']      = YX_Time_Elapsed_String($fetched_data['time']);
    $fetched_data['updated']     = YX_Time_Elapsed_String($fetched_data['last_update']);
    $fetched_data['entries']     = YX_GetEntries($id, $post_type, $fetched_data['entries_per_page'], $page);
    $fetched_data['publisher']   = YX_UserData($fetched_data['user_id']);
    $fetched_data['views']       = number_format($fetched_data['views']);
    return $fetched_data;
}
function YX_GetMusic($id = '', $page = 0)
{
    global $yx, $sqlConnect;
    if (empty($id)) {
        return false;
    }
    $id = @end(explode('-', $id));
    if (empty($id) || !is_numeric($id)) {
        return false;
    }
    $data         = array();
    $id           = YX_Secure($id);
    $query_one    = "SELECT * FROM " . T_MUSIC . " WHERE `id` = {$id}";
    $sql          = mysqli_query($sqlConnect, $query_one);
    $fetched_data = mysqli_fetch_assoc($sql);
    if (empty($fetched_data)) {
        return array();
    }
    $fetched_data['small_image'] = YX_GetMedia($fetched_data['image']);
    $fetched_data['big_image']   = YX_GetMedia($fetched_data['image']);
    $fetched_data['url']         = YX_Link('music/' . $fetched_data['slug'] . '-' . $id);
    $fetched_data['posted']      = YX_Time_Elapsed_String($fetched_data['time']);
    $fetched_data['updated']     = YX_Time_Elapsed_String($fetched_data['last_update']);
    $fetched_data['entries']     = YX_GetEntries($id, 'music', $fetched_data['entries_per_page'], $page);
    $fetched_data['publisher']   = YX_UserData($fetched_data['user_id']);
    $fetched_data['views']       = number_format($fetched_data['views']);
    $fetched_data['cat_name']    = $yx['music_categories'][$fetched_data['category']];
    return $fetched_data;
}
function YX_GetSavedMusic($id = '', $page = 0)
{
    global $yx, $sqlConnect;
    if (empty($id)) {
        return false;
    }
    $id = @end(explode('-', $id));
    if (empty($id) || !is_numeric($id)) {
        return false;
    }
    $data         = array();
    $id           = YX_Secure($id);
    $query_one    = "SELECT * FROM " . T_MUSIC . " WHERE `id` = {$id} and active= '1' ";
    $sql          = mysqli_query($sqlConnect, $query_one);
    $fetched_data = mysqli_fetch_assoc($sql);
    if (empty($fetched_data)) {
        return array();
    }
    $fetched_data['small_image'] = YX_GetMedia($fetched_data['image']);
    $fetched_data['big_image']   = YX_GetMedia($fetched_data['image']);
    $fetched_data['url']         = YX_Link('music/' . $fetched_data['slug'] . '-' . $id);
    $fetched_data['posted']      = YX_Time_Elapsed_String($fetched_data['time']);
    $fetched_data['updated']     = YX_Time_Elapsed_String($fetched_data['last_update']);
    $fetched_data['entries']     = YX_GetEntries($id, 'music', $fetched_data['entries_per_page'], $page);
    $fetched_data['publisher']   = YX_UserData($fetched_data['user_id']);
    $fetched_data['views']       = number_format($fetched_data['views']);
    $fetched_data['cat_name']    = $yx['music_categories'][$fetched_data['category']];
    return $fetched_data;
}
function YX_GetLists($id = '', $page = 0)
{
    global $yx, $sqlConnect;
    if (empty($id)) {
        return false;
    }
    $id = @end(explode('-', $id));
    if (empty($id) || !is_numeric($id)) {
        return false;
    }
    $data         = array();
    $id           = YX_Secure($id);
    $query_one    = "SELECT * FROM " . T_LISTS . " WHERE `id` = {$id}";
    $sql          = mysqli_query($sqlConnect, $query_one);
    $fetched_data = mysqli_fetch_assoc($sql);
    if (empty($fetched_data)) {
        return array();
    }
    $fetched_data['small_image'] = YX_GetMedia($fetched_data['image']);
    $fetched_data['big_image']   = YX_GetMedia($fetched_data['image']);
    $fetched_data['url']         = YX_Link('lists/' . $fetched_data['slug'] . '-' . $id);
    $fetched_data['posted']      = YX_Time_Elapsed_String($fetched_data['time']);
    $fetched_data['updated']     = YX_Time_Elapsed_String($fetched_data['last_update']);
    $fetched_data['entries']     = YX_GetEntries($id, 'lists', $fetched_data['entries_per_page'], $page);
    $fetched_data['publisher']   = YX_UserData($fetched_data['user_id']);
    $fetched_data['views']       = number_format($fetched_data['views']);
    $fetched_data['cat_name']    = $yx['list_categories'][$fetched_data['category']];
    return $fetched_data;
}
function YX_GetSavedLists($id = '', $page = 0)
{
    global $yx, $sqlConnect;
    if (empty($id)) {
        return false;
    }
    $id = @end(explode('-', $id));
    if (empty($id) || !is_numeric($id)) {
        return false;
    }
    $data         = array();
    $id           = YX_Secure($id);
    $query_one    = "SELECT * FROM " . T_LISTS . " WHERE `id` = {$id} and active='1' ";
    $sql          = mysqli_query($sqlConnect, $query_one);
    $fetched_data = mysqli_fetch_assoc($sql);
    if (empty($fetched_data)) {
        return array();
    }
    $fetched_data['small_image'] = YX_GetMedia($fetched_data['image']);
    $fetched_data['big_image']   = YX_GetMedia($fetched_data['image']);
    $fetched_data['url']         = YX_Link('lists/' . $fetched_data['slug'] . '-' . $id);
    $fetched_data['posted']      = YX_Time_Elapsed_String($fetched_data['time']);
    $fetched_data['updated']     = YX_Time_Elapsed_String($fetched_data['last_update']);
    $fetched_data['entries']     = YX_GetEntries($id, 'lists', $fetched_data['entries_per_page'], $page);
    $fetched_data['publisher']   = YX_UserData($fetched_data['user_id']);
    $fetched_data['views']       = number_format($fetched_data['views']);
    $fetched_data['cat_name']    = $yx['list_categories'][$fetched_data['category']];
    return $fetched_data;
}
function YX_GetQuizzes($id = '', $page = 0)
{
    global $yx, $sqlConnect;
    if (empty($id)) {
        return false;
    }
    $id = @end(explode('-', $id));
    if (empty($id) || !is_numeric($id)) {
        return false;
    }
    $data         = array();
    $id           = YX_Secure($id);
    $table        = T_QUIZZES;
    $query_one    = "SELECT * FROM `$table` WHERE `id` = {$id}";
    $sql          = mysqli_query($sqlConnect, $query_one);
    $fetched_data = mysqli_fetch_assoc($sql);
    if (empty($fetched_data)) {
        return array();
    }
    $fetched_data['small_image'] = YX_GetMedia($fetched_data['image']);
    $fetched_data['big_image']   = YX_GetMedia($fetched_data['image']);
    $fetched_data['url']         = YX_Link('quiz/' . $fetched_data['slug'] . '-' . $id);
    $fetched_data['posted']      = YX_Time_Elapsed_String($fetched_data['time']);
    $fetched_data['updated']     = YX_Time_Elapsed_String($fetched_data['last_update']);
    $fetched_data['entries']     = YX_GetEntries($id, 'lists', $fetched_data['entries_per_page'], $page);
    $fetched_data['publisher']   = YX_UserData($fetched_data['user_id']);
    $fetched_data['views']       = number_format($fetched_data['views']);
    $fetched_data['cat_name']    = $yx['quiz_categories'][$fetched_data['category']];
    return $fetched_data;
}
function YX_GetNews($id = '', $page = 0)
{
    global $yx, $sqlConnect;
    if (empty($id)) {
        return false;
    }
    $id = @end(explode('-', $id));
    if (empty($id) || !is_numeric($id)) {
        return false;
    }
    $data         = array();
    $id           = YX_Secure($id);
    $query_one    = "SELECT * FROM " . T_NEWS . " WHERE `id` = {$id}";
    $sql          = mysqli_query($sqlConnect, $query_one);
    $fetched_data = mysqli_fetch_assoc($sql);
    if (empty($fetched_data)) {
        return array();
    }
    $fetched_data['small_image'] = YX_GetMedia($fetched_data['image']);
    $fetched_data['big_image']   = YX_GetMedia($fetched_data['image']);
    $fetched_data['url']         = YX_Link('news/' . $fetched_data['slug'] . '-' . $id);
    $fetched_data['posted']      = YX_Time_Elapsed_String($fetched_data['time']);
    $fetched_data['updated']     = YX_Time_Elapsed_String($fetched_data['last_update']);
    $fetched_data['entries']     = YX_GetEntries($id, 'news', $fetched_data['entries_per_page'], $page);
    $fetched_data['publisher']   = YX_UserData($fetched_data['user_id']);
    $fetched_data['views']       = number_format($fetched_data['views']);
    $fetched_data['cat_name']    = $yx['news_categories'][$fetched_data['category']];
    return $fetched_data;
}
function YX_SavedGetNews($id = '', $page = 0)
{
    global $yx, $sqlConnect;
    if (empty($id)) {
        return false;
    }
    $id = @end(explode('-', $id));
    if (empty($id) || !is_numeric($id)) {
        return false;
    }
    $data         = array();
    $id           = YX_Secure($id);
    $query_one    = "SELECT * FROM " . T_NEWS . " WHERE `id` = {$id} and active='1' ";
    $sql          = mysqli_query($sqlConnect, $query_one);
    $fetched_data = mysqli_fetch_assoc($sql);
    if (empty($fetched_data)) {
        return array();
    }
    $fetched_data['small_image'] = YX_GetMedia($fetched_data['image']);
    $fetched_data['big_image']   = YX_GetMedia($fetched_data['image']);
    $fetched_data['url']         = YX_Link('news/' . $fetched_data['slug'] . '-' . $id);
    $fetched_data['posted']      = YX_Time_Elapsed_String($fetched_data['time']);
    $fetched_data['updated']     = YX_Time_Elapsed_String($fetched_data['last_update']);
    $fetched_data['entries']     = YX_GetEntries($id, 'news', $fetched_data['entries_per_page'], $page);
    $fetched_data['publisher']   = YX_UserData($fetched_data['user_id']);
    $fetched_data['views']       = number_format($fetched_data['views']);
    $fetched_data['cat_name']    = $yx['news_categories'][$fetched_data['category']];
    return $fetched_data;
}
function YX_GetVideos($id = '', $page = 0)
{
    global $yx, $sqlConnect;
    if (empty($id)) {
        return false;
    }
    $id = @end(explode('-', $id));
    if (empty($id) || !is_numeric($id)) {
        return false;
    }
    $data         = array();
    $id           = YX_Secure($id);
    $query_one    = "SELECT * FROM " . T_VIDEOS . " WHERE `id` = {$id}";
    $sql          = mysqli_query($sqlConnect, $query_one);
    $fetched_data = mysqli_fetch_assoc($sql);
    if (empty($fetched_data)) {
        return array();
    }
    $fetched_data['small_image'] = YX_GetMedia($fetched_data['image']);
    $fetched_data['big_image']   = YX_GetMedia($fetched_data['image']);
    $fetched_data['url']         = YX_Link('videos/' . $fetched_data['slug'] . '-' . $id);
    $fetched_data['posted']      = YX_Time_Elapsed_String($fetched_data['time']);
    $fetched_data['updated']     = YX_Time_Elapsed_String($fetched_data['last_update']);
    $fetched_data['entries']     = YX_GetEntries($id, 'videos', $fetched_data['entries_per_page'], $page);
    $fetched_data['publisher']   = YX_UserData($fetched_data['user_id']);
    $fetched_data['views']       = number_format($fetched_data['views']);
    $fetched_data['cat_name']    = $yx['video_categories'][$fetched_data['category']];
    return $fetched_data;
}
function YX_SavedGetVideos($id = '', $page = 0)
{
    global $yx, $sqlConnect;
    if (empty($id)) {
        return false;
    }
    $id = @end(explode('-', $id));
    if (empty($id) || !is_numeric($id)) {
        return false;
    }
    $data         = array();
    $id           = YX_Secure($id);
    $query_one    = "SELECT * FROM " . T_VIDEOS . " WHERE `id` = {$id} and active='1'";
    $sql          = mysqli_query($sqlConnect, $query_one);
    $fetched_data = mysqli_fetch_assoc($sql);
    if (empty($fetched_data)) {
        return array();
    }
    $fetched_data['small_image'] = YX_GetMedia($fetched_data['image']);
    $fetched_data['big_image']   = YX_GetMedia($fetched_data['image']);
    $fetched_data['url']         = YX_Link('videos/' . $fetched_data['slug'] . '-' . $id);
    $fetched_data['posted']      = YX_Time_Elapsed_String($fetched_data['time']);
    $fetched_data['updated']     = YX_Time_Elapsed_String($fetched_data['last_update']);
    $fetched_data['entries']     = YX_GetEntries($id, 'videos', $fetched_data['entries_per_page'], $page);
    $fetched_data['publisher']   = YX_UserData($fetched_data['user_id']);
    $fetched_data['views']       = number_format($fetched_data['views']);
    $fetched_data['cat_name']    = $yx['video_categories'][$fetched_data['category']];
    return $fetched_data;
}
function YX_GetPolls($id = '', $page = 0)
{
    global $yx, $sqlConnect;
    if (empty($id)) {
        return false;
    }
    $id = @end(explode('-', $id));
    if (empty($id) || !is_numeric($id)) {
        return false;
    }
    $data         = array();
    $id           = YX_Secure($id);
    $query_one    = "SELECT * FROM " . T_POLLS_PAGES . " WHERE `id` = {$id}";
    $sql          = mysqli_query($sqlConnect, $query_one);
    $fetched_data = mysqli_fetch_assoc($sql);
    if (empty($fetched_data)) {
        return array();
    }
    $fetched_data['small_image'] = YX_GetMedia($fetched_data['image']);
    $fetched_data['big_image']   = YX_GetMedia($fetched_data['image']);
    $fetched_data['url']         = YX_Link('polls/' . $fetched_data['slug'] . '-' . $id);
    $fetched_data['posted']      = YX_Time_Elapsed_String($fetched_data['time']);
    $fetched_data['updated']     = YX_Time_Elapsed_String($fetched_data['last_update']);
    $fetched_data['entries']     = YX_GetEntries($id, 'polls', $fetched_data['entries_per_page'], $page);
    $fetched_data['publisher']   = YX_UserData($fetched_data['user_id']);
    $fetched_data['views']       = number_format($fetched_data['views']);
    $fetched_data['cat_name']    = $yx['poll_categories'][$fetched_data['category']];
    return $fetched_data;
}
function YX_GetSavedPolls($id = '', $page = 0)
{
    global $yx, $sqlConnect;
    if (empty($id)) {
        return false;
    }
    $id = @end(explode('-', $id));
    if (empty($id) || !is_numeric($id)) {
        return false;
    }
    $data         = array();
    $id           = YX_Secure($id);
    $query_one    = "SELECT * FROM " . T_POLLS_PAGES . " WHERE `id` = {$id} and active = '1' ";
    $sql          = mysqli_query($sqlConnect, $query_one);
    $fetched_data = mysqli_fetch_assoc($sql);
    if (empty($fetched_data)) {
        return array();
    }
    $fetched_data['small_image'] = YX_GetMedia($fetched_data['image']);
    $fetched_data['big_image']   = YX_GetMedia($fetched_data['image']);
    $fetched_data['url']         = YX_Link('polls/' . $fetched_data['slug'] . '-' . $id);
    $fetched_data['posted']      = YX_Time_Elapsed_String($fetched_data['time']);
    $fetched_data['updated']     = YX_Time_Elapsed_String($fetched_data['last_update']);
    $fetched_data['entries']     = YX_GetEntries($id, 'polls', $fetched_data['entries_per_page'], $page);
    $fetched_data['publisher']   = YX_UserData($fetched_data['user_id']);
    $fetched_data['views']       = number_format($fetched_data['views']);
    $fetched_data['cat_name']    = $yx['poll_categories'][$fetched_data['category']];
    return $fetched_data;
}
function YX_FetchDataFromDB($data_array = array())
{
    global $yx, $sqlConnect;
    if (empty($data_array)) {
        return false;
    }
    if (empty($data_array['table'])) {
        return false;
    }
    if (empty($data_array['column'])) {
        return false;
    }
    $column     = $data_array['column'];
    $limit      = 0;
    $table      = $data_array['table'];
    $where      = '';
    $order      = '';
    $count      = false;
    $where_type = 'AND';
    if (!empty($data_array['where_type'])) {
        $where_type = $data_array['where_type'];
    }
    if (!empty($data_array['count'])) {
        $count = true;
    }
    if (!empty($data_array['order'])) {
        if (is_array($data_array['order'])) {
            if ($data_array['order']['type'] == 'rand') {
                $order = 'ORDER BY RAND()';
            } else if ($data_array['order']['type'] == 'desc' || $data_array['order']['type'] == 'DESC') {
                $order = 'ORDER BY ' . $data_array['order']['column'] . ' DESC';
            } else if ($data_array['order']['type'] == 'asc' || $data_array['order']['type'] == 'ASC') {
                $order = 'ORDER BY ' . $data_array['order']['column'] . ' ASC';
            } else if ($data_array['order']['type'] == 'text') {
                $order = 'ORDER BY ' . $data_array['order']['column'] . ' ';
            }
        }
    }
    if (!empty($data_array['limit'])) {
        if (is_numeric($data_array['limit'])) {
            $limit = $data_array['limit'];
        }
    }
    if (!empty($data_array['where'])) {
        if (is_array($data_array['where'])) {
            $where = 'WHERE ';
            if (count($data_array['where']) > 1) {
                $where_array = array();
                foreach ($data_array['where'] as $key => $value) {
                    $string = ' "' . YX_Secure($value['value']) . '" ';
                    if (isset($value['string'])) {
                        if ($value['string'] == false) {
                            $string = ' ' . YX_Secure($value['value']) . ' ';
                        }
                    }
                    $where_array[] = YX_Secure($value['column']) . ' ' . $value['mark'] . $string;
                }
                $where .= implode(' ' . $where_type . ' ', $where_array);
            } else {
                $string = ' "' . YX_Secure($data_array['where'][0]['value']) . '" ';
                if (isset($data_array['where'][0]['string'])) {
                    if ($data_array['where'][0]['string'] == false) {
                        $string = ' ' . YX_Secure($data_array['where'][0]['value']) . ' ';
                    }
                }
                $where .= YX_Secure($data_array['where'][0]['column']) . ' ' . $data_array['where'][0]['mark'] . $string;
            }
        }
    }
    $data          = array();
    $column_string = ($count == true) ? 'COUNT(' . $column . ') as count' : $column;
    $query_one     = "SELECT {$column_string} FROM {$table} {$where} {$order}";
    if (!empty($limit)) {
        $query_one .= " LIMIT {$limit}";
    }
    $sql = mysqli_query($sqlConnect, $query_one);
    while ($fetched_data = mysqli_fetch_assoc($sql)) {
        if (!empty($data_array['final_data'])) {
            foreach ($data_array['final_data'] as $key => $value) {
                $function_name = '';
                if (!empty($value['function_name'])) {
                    $function_name = $value['function_name'];
                }
                if (!empty($value['name']) && !empty($function_name)) {
                    $fetched_data[$value['name']] = $function_name($fetched_data[$value['column']]);
                } else if (!empty($value['simple_value_name'])) {
                    $fetched_data[$value['simple_value_name']] = $value['simple_value_text'];
                } else if (!empty($function_name)) {
                    $fetched_data = $function_name($fetched_data[$value['column']]);
                }
            }
        }
        $data[] = $fetched_data;
    }
    return $data;
}
function YX_GetEntries($id = 0, $type = '', $entries_per_page = 0, $page = 1)
{
    global $yx, $sqlConnect;
    if (empty($id) || empty($type)) {
        return false;
    }
    $types = array(
        'news',
        'polls',
        'lists',
        'videos',
        'music',
        'quiz'
    );
    if (!in_array($type, $types)) {
        return false;
    }
    $data       = array();
    $id         = YX_Secure($id);
    $totalPages = 0;
    $startAt    = 0;
    if (!empty($entries_per_page) && !empty($page)) {
        $startAt        = $entries_per_page * ($page - 1);
        $query_one_c    = "SELECT COUNT(*) as total FROM " . T_ENTRIES . " WHERE `post_id` = '{$id}' AND `entry_page` = '{$type}'";
        $sql_c          = mysqli_query($sqlConnect, $query_one_c);
        $fetched_data_c = mysqli_fetch_assoc($sql_c);
        $totalPages     = ceil($fetched_data_c['total'] / $entries_per_page);
    }
    $query_one = "SELECT * FROM " . T_ENTRIES . " WHERE `post_id` = '{$id}' AND `entry_page` = '$type'";
    $query_one .= " ORDER BY `index_id` ASC";
    if (!empty($entries_per_page)) {
        $query_one .= " LIMIT $startAt, $entries_per_page";
    }
    $sql = mysqli_query($sqlConnect, $query_one);
    while ($fetched_data = mysqli_fetch_assoc($sql)) {
        if ($fetched_data['entry_type'] == 'option') {
            $fetched_data['poll_answers'] = YX_GetPercentageOfOptionEntry($fetched_data['id']);
        }
        if ($fetched_data['entry_type'] == 'question') {
            $fetched_data['question_answers'] = YX_GetAnswers($fetched_data['id']);
        }
        $fetched_data['image'] = YX_GetMedia($fetched_data['image']);
        $data[]                = $fetched_data;
    }
    $data['totalPages'] = $totalPages;
    return $data;
}
function YX_GetVotes($option_id)
{
    global $yx, $sqlConnect;
    if (empty($option_id) || !is_numeric($option_id)) {
        return false;
    }
    $data         = array();
    $option_id    = YX_Secure($option_id);
    $query_one    = "SELECT COUNT(id) as count FROM " . T_VOTES . " WHERE `option_id` = {$option_id}";
    $sql          = mysqli_query($sqlConnect, $query_one);
    $fetched_data = mysqli_fetch_assoc($sql);
    if (empty($fetched_data)) {
        return array();
    }
    return $fetched_data['count'];
}
function YX_GetAnswers($question_id = false)
{
    global $yx, $sqlConnect;
    if (empty($question_id) || !is_numeric($question_id)) {
        return false;
    }
    $data        = array();
    $question_id = YX_Secure($question_id);
    $table       = T_ANSWERS;
    $query_one   = "SELECT * FROM `$table` WHERE `entry_id` = {$question_id}";
    $query       = mysqli_query($sqlConnect, $query_one);
    while ($fetched_data = mysqli_fetch_assoc($query)) {
        $fetched_data['image'] = YX_GetMedia($fetched_data['image']);
        $fetched_data['text']  = YX_ShortText($fetched_data['text'], 40);
        $data[]                = $fetched_data;
    }
    return $data;
}
function YX_UpdateViews($type = '', $id = 0)
{
    global $yx, $sqlConnect;
    if (empty($id) || empty($type)) {
        return false;
    }
    $table = '';
    switch ($type) {
        case 'news':
            $table = T_NEWS;
            break;
        case 'lists':
            $table = T_LISTS;
            break;
        case 'polls':
            $table = T_POLLS_PAGES;
            break;
        case 'videos':
            $table = T_VIDEOS;
            break;
        case 'music':
            $table = T_MUSIC;
            break;
        case 'quiz':
            $table = T_QUIZZES;
            break;
    }
    $num = rand(50, 3999); //change + $num to simply + 1 
    $id        = YX_Secure($id);
    $query_one = "UPDATE $table SET `views` = `views` + $num WHERE `id` = '{$id}'";
    $sql       = mysqli_query($sqlConnect, $query_one);
    if ($sql) {
        return true;
    }
    return false;
}
function YX_VoteOption($option_id, $type, $id, $entry_id)
{
    global $yx, $sqlConnect;
    if (empty($id) || empty($type) || empty($option_id)) {
        return false;
    }
    if (!is_numeric($option_id)) {
        return false;
    }
    $option_id = YX_Secure($option_id);
    $id        = YX_Secure($id);
    $type      = YX_Secure($type);
    $entry_id  = YX_Secure($entry_id);
    if (YX_IsEntryVoted($entry_id, $id)) {
        return false;
    }
    if (YX_IsOptionVoted($option_id, $id)) {
        return false;
    }
    $fields = '(`option_id`, `user_id`, `time`, `entry_id`)';
    if ($type == 'non_logged_user') {
        $fields = '(`option_id`, `ip_address`, `time`, `entry_id`)';
    }
    $query_one = "INSERT INTO " . T_VOTES . " {$fields} VALUES ('$option_id', '{$id}', '" . time() . "', '{$entry_id}')";
    $sql       = mysqli_query($sqlConnect, $query_one);
    if ($sql) {
        return true;
    }
}
function YX_IsOptionVoted($option_id, $id)
{
    global $yx, $sqlConnect;
    if (empty($id) || empty($option_id)) {
        return false;
    }
    if (!is_numeric($option_id)) {
        return false;
    }
    $id        = YX_Secure($id);
    $option_id = YX_Secure($option_id);
    if (is_numeric($id)) {
        $type = 'user_id';
    } else {
        $type = 'ip_address';
    }
    $query_one = "SELECT COUNT(id) as count FROM " . T_VOTES . " WHERE `option_id` = '{$option_id}' AND `{$type}` = '{$id}'";
    $sql       = mysqli_query($sqlConnect, $query_one);
    $sql_fetch = mysqli_fetch_assoc($sql);
    if ($sql_fetch['count'] > 0) {
        return true;
    }
    return false;
}
function YX_IsEntryVoted($entry_id, $id)
{
    global $yx, $sqlConnect;
    if (empty($id) || empty($entry_id)) {
        return false;
    }
    if (!is_numeric($entry_id)) {
        return false;
    }
    $id       = YX_Secure($id);
    $entry_id = YX_Secure($entry_id);
    if (is_numeric($id)) {
        $type = 'user_id';
    } else {
        $type = 'ip_address';
    }
    $query_one = "SELECT COUNT(id) as count FROM " . T_VOTES . " WHERE `entry_id` = '{$entry_id}' AND `{$type}` = '{$id}'";
    $sql       = mysqli_query($sqlConnect, $query_one);
    $sql_fetch = mysqli_fetch_assoc($sql);
    if ($sql_fetch['count'] > 0) {
        return true;
    }
    return false;
}
function YX_GetPercentageOfOptionEntry($entry_id)
{
    global $yx, $sqlConnect;
    if (empty($entry_id)) {
        return false;
    }
    if (!is_numeric($entry_id)) {
        return false;
    }
    $fetch_data_array = array(
        'table' => T_POLLS,
        'column' => '*',
        'where' => array(
            array(
                'column' => 'entry_id',
                'value' => $entry_id,
                'mark' => '='
            )
        ),
        'final_data' => array(
            array(
                'function_name' => 'YX_GetVotes',
                'column' => 'id',
                'name' => 'option_votes'
            )
        )
    );
    $data             = YX_FetchDataFromDB($fetch_data_array);
    $all              = 0;
    foreach ($data as $key => $value) {
        $all += $value['option_votes'];
    }
    $percentage_total = $all;
    foreach ($data as $key => $value) {
        $percentage                   = 0;
        $data[$key]['percentage']     = '0%';
        $data[$key]['percentage_num'] = 0;
        $data[$key]['all']            = $all;
        if ($percentage_total > 0) {
            $data[$key]['percentage']     = number_format(($value['option_votes'] / $percentage_total) * 100) . '%';
            $data[$key]['percentage_num'] = number_format(($value['option_votes'] / $percentage_total) * 100);
            $data[$key]['all']            = $all;
        }
    }
    return $data;
}
function YX_UpdateShares($id, $type)
{
    global $yx, $sqlConnect;
    if (empty($id) || empty($type)) {
        return false;
    }
    $table = '';
    switch ($type) {
        case 'news':
            $table = T_NEWS;
            break;
        case 'lists':
            $table = T_LISTS;
            break;
        case 'videos':
            $table = T_VIDEOS;
            break;
        case 'polls':
            $table = T_POLLS_PAGES;
            break;
        case 'music':
            $table = T_MUSIC;
            break;
    }
    $num = rand(1, 50); //change + $num to simply + 1 
    $id        = YX_Secure($id);
    $query_one = "UPDATE $table SET `shares` = `shares` + $num WHERE `id` = '{$id}'";
    $sql       = mysqli_query($sqlConnect, $query_one);
    if ($sql) {
        return true;
    }
    return false;
}
function YX_IsAdmin($user_id = 0)
{
    global $yx, $sqlConnect;
    if ($yx['loggedin'] == false) {
        return false;
    }
    $user_id = YX_Secure($user_id);
    if (empty($user_id)) {
        $user_id = YX_Secure($yx['user']['user_id']);
    }
    $user_data = YX_UserData($user_id);
    if ($user_data['admin'] == 1) {
        return true;
    }
    return false;
}
function YX_IsPostOwner($id, $type)
{
    global $yx, $sqlConnect;
    if ($yx['loggedin'] == false) {
        return false;
    }
    if (empty($id) || empty($type)) {
        return false;
    }
    if (YX_IsAdmin()) {
        return true;
    }
    $table = '';
    switch ($type) {
        case 'news':
            $table = T_NEWS;
            break;
        case 'lists':
            $table = T_LISTS;
            break;
        case 'polls':
            $table = T_POLLS_PAGES;
            break;
        case 'videos':
            $table = T_VIDEOS;
            break;
        case 'music':
            $table = T_MUSIC;
            break;
        case 'quiz':
            $table = T_QUIZZES;
            break;
    }
    $id        = YX_Secure($id);
    $user_id   = YX_Secure($yx['user']['user_id']);
    $query_one = "SELECT COUNT(`id`) as count FROM {$table} WHERE `user_id` = '{$user_id}' AND `id` = '{$id}'";
    $sql       = mysqli_query($sqlConnect, $query_one);
    $sql_fetch = mysqli_fetch_assoc($sql);
    if ($sql_fetch['count'] == 1) {
        return true;
    }
    return false;
}
function YX_DeletePost($id, $type)
{
    global $yx, $sqlConnect;
    if ($yx['loggedin'] == false) {
        return false;
    }
    if (empty($id) || empty($type)) {
        return false;
    }
    $types = array(
        'news',
        'lists',
        'music',
        'videos',
        'polls',
        'quiz'
    );
    if (!in_array($type, $types)) {
        return false;
    }
    if (YX_IsAdmin() === false) {
        if (YX_IsPostOwner($id, $type) === false) {
            return false;
        }
    }
    $delete_post = false;
    if ($type == 'news') {
        $table = T_NEWS;
    } else if ($type == 'lists') {
        $table = T_LISTS;
    } else if ($type == 'polls') {
        $table = T_POLLS_PAGES;
    } else if ($type == 'videos') {
        $table = T_VIDEOS;
    } else if ($type == 'music') {
        $table = T_MUSIC;
    } else if ($type == 'quiz') {
        $table = T_QUIZZES;
    }
    $delete_data = YX_GetPost($id, 0, $type);
    if (empty($delete_data)) {
        return false;
    }
    $fetch_data_array = array(
        'table' => $table,
        'column' => '*',
        'where' => array(
            array(
                'column' => 'id',
                'value' => $id,
                'mark' => '='
            )
        )
    );
    $data_field       = YX_FetchDataFromDB($fetch_data_array);
    foreach ($data_field as $key => $value) {
        if (!empty($value['image'])) {
            $url2_explode = explode('.', $value['image']);
            $url2 = $url2_explode[0] . '_hd.' . $url2_explode[1];
            @unlink($value['image']);
            @unlink($url2);
            $delete_from_s3 = YX_DeleteFromToS3($value['image']);
            $delete_from_s3 = YX_DeleteFromToS3($url2);
        }
    }
    $delete_post_query = "DELETE FROM {$table} WHERE `id` = '{$id}'";
    $delete_post       = mysqli_query($sqlConnect, $delete_post_query);
    if ($delete_post) {
        $fetch_data_array = array(
            'table' => T_ENTRIES,
            'column' => 'id',
            'order' => array(
                'type' => 'desc',
                'column' => 'id'
            ),
            'where' => array(
                array(
                    'column' => 'post_id',
                    'value' => $id,
                    'mark' => '='
                ),
                array(
                    'column' => 'entry_page',
                    'value' => $type,
                    'mark' => '='
                )
            )
        );
        $delete_entry     = false;
        $delete_entries   = YX_FetchDataFromDB($fetch_data_array);
        foreach ($delete_entries as $key => $value) {
            $delete_entry = YX_DeleteEntry($value['id']);
        }
        if ($delete_entry) {
            return true;
        }
    }
}
function YX_DeleteEntry($id)
{
    global $yx, $sqlConnect;
    if ($yx['loggedin'] == false) {
        return false;
    }
    if (empty($id)) {
        return false;
    }
    $id               = YX_Secure($id);
    $fetch_data_array = array(
        'table' => T_ENTRIES,
        'column' => '*',
        'where' => array(
            array(
                'column' => 'id',
                'value' => $id,
                'mark' => '='
            )
        )
    );
    $data_entry       = YX_FetchDataFromDB($fetch_data_array);
    if (!empty($data_entry)) {
        foreach ($data_entry as $key => $value) {
            if (!empty($value['image'])) {
                @unlink($value['image']);
                $delete_from_s3 = YX_DeleteFromToS3($value['image']);
            }
            if ($value['entry_type'] == 'option') {
                $entry_id = $value['id'];
                $mysqli   = mysqli_query($sqlConnect, "DELETE FROM " . T_POLLS . " WHERE `entry_id` = '{$entry_id}'");
                $mysqli   = mysqli_query($sqlConnect, "DELETE FROM " . T_VOTES . " WHERE `entry_id` = '{$entry_id}'");
            }
            if ($value['entry_type'] == 'question') {
                $entry_id = $value['id'];
                $table    = T_ANSWERS;
                $mysqli   = mysqli_query($sqlConnect, "DELETE FROM `$table` WHERE `entry_id` = '{$entry_id}'");
            }
        }
        $delete_entry_query = "DELETE FROM " . T_ENTRIES . " WHERE `id` = '{$id}'";
        $delete_entry       = mysqli_query($sqlConnect, $delete_entry_query);
        if ($delete_entry) {
            return true;
        }
    }
    return false;
}

function YX_DeleteSession($id)
{
    $delete_entry_query = "DELETE FROM " . T_APP_SESSIONS . " WHERE `id` = '{$id}'";
    $delete_entry       = mysqli_query($sqlConnect, $delete_entry_query);
    if ($delete_entry) {
        return true;
    }
}

function YX_ForEachEntries($entries_array = array())
{
    global $yx, $sqlConnect;
    if (empty($entries_array)) {
        return false;
    }
    $entries = '';
    foreach ($entries_array as $yx['key'] => $yx['entry']) {
        if ($yx['entry']['entry_type'] == 'text') {
            $entry_content = 'entries/text';
        }
        if ($yx['entry']['entry_type'] == 'image') {
            $entry_content = 'entries/image';
        }
        if ($yx['entry']['entry_type'] == 'tweet') {
            $entry_content = 'entries/tweet';
        }
        if ($yx['entry']['entry_type'] == 'facebook') {
            $entry_content = 'entries/facebook';
        }
        if ($yx['entry']['entry_type'] == 'soundcloud') {
            $entry_content = 'entries/soundcloud';
        }
        if ($yx['entry']['entry_type'] == 'instagram') {
            $entry_content = 'entries/instagram';
        }
        if ($yx['entry']['entry_type'] == 'tweet') {
            $entry_content = 'entries/tweet';
        }
        if ($yx['entry']['entry_type'] == 'question') {
            $entry_content = 'entries/question';
        }
        $video_html = '';
        if ($yx['entry']['entry_type'] == 'video') {
            $entry_content = 'entries/video';
            if ($yx['entry']['video_type'] == 'youtube') {
                $html = YX_LoadPage('iframe/youtube');
            } else if ($yx['entry']['video_type'] == 'vine') {
                $html = YX_LoadPage('iframe/vine');
            } else if ($yx['entry']['video_type'] == 'vimeo') {
                $html = YX_LoadPage('iframe/vimeo');
            } else if ($yx['entry']['video_type'] == 'dailymotion') {
                $html = YX_LoadPage('iframe/dailymotion');
            } else if ($yx['entry']['video_type'] == 'facebook') {
                $html = YX_LoadPage('iframe/facebook');
            } else if ($yx['entry']['video_type'] == 'source') {
                $html = YX_LoadPage('players/video', array(
                    'VIDEO_SRC' => YX_GetMedia($yx['entry']['video_url'])
                ));
            }
            if (!empty($html)) {
                $video_html = str_replace('{video_id}', $yx['entry']['video'], $html);
            }
        }
        $is_voted  = false;
        $votes     = '';
        $all_votes = 0;
        if ($yx['entry']['entry_type'] == 'option') {
            $entry_content = 'entries/option';
            $is_voted      = YX_IsEntryVoted($yx['entry']['id'], $yx['my_id']);
            foreach ($yx['entry']['poll_answers'] as $key => $value) {
                $is_option_voted = YX_IsOptionVoted($value['id'], $yx['my_id']);
                $yx['vote_type'] = $value['type'];
                $votes .= YX_LoadPage('entries/votes', array(
                    'OPTION_ID' => $value['id'],
                    'ENTRY_ID' => $yx['entry']['id'],
                    'OPTION_TEXT' => $value['text'],
                    'OPTION_IMAGE' => YX_GetMedia($value['image']),
                    'OPTION_PRERCENTAGE' => $value['percentage'],
                    'OPTION_OPACITY' => (!$is_option_voted && $is_voted) ? '0.6' : '1',
                    'IS_OPTION_VOTED_ACTIVE' => ($is_option_voted) ? 'active' : '',
                    'IS_OPTION_VOTED_ICON' => ($is_option_voted) ? '<i class="fa fa-check-circle-o"></i>' : '<i class="fa fa-circle-o"></i>',
                    'IS_OPTION_VOTED_PRERCENTAGE' => ($is_voted) ? $value['percentage'] : '',
                    'IS_OPTION_VOTED_PRERCENTAGE_BIGGER_THAN_ZERO' => ($value['percentage_num'] > 0 && $is_voted) ? ' ' : ''
                ));
                $all_votes = $value['all'];
            }
        }
        if ($yx['entry']['entry_type'] == 'question') {
            $entry_content = 'entries/question';
            foreach ($yx['entry']['question_answers'] as $key => $yx['answer']) {
                $votes .= YX_LoadPage('entries/answers', array(
                    'ENTRY_ID' => $yx['entry']['id'],
                    'POST_ID' => $yx['entry']['post_id'],
                    'ANS_IMAGE' => $yx['answer']['image'],
                    'ANS_TEXT' => $yx['answer']['text'],
                    'ANS_ID' => $yx['answer']['id'],
                    'INDEX_ID' => $yx['answer']['result_index']
                ));
            }
        }
        $dot = ($yx['entry']['entry_page'] == 'lists') ? $yx['entry']['index_id'] . '. ' : '';
        if (!empty($yx['entry']['id'])) {
            $entries .= YX_LoadPage($entry_content, array(
                'ENTRY_ID' => $yx['entry']['id'],
                'OPTION_TOTAL_VOTES' => $all_votes,
                'ENTRY_TITLE' => (!empty($yx['entry']['title'])) ? '<h3>' . $dot . $yx['entry']['title'] . '</h3>' : '',
                'ENTRY_FACEBOOK_POST' => $yx['entry']['facebook_post'],
                'ENTRY_IMAGE' => $yx['entry']['image'],
                'ENTRY_IS_VOTED' => $is_voted,
                'ENTRY_DATA_CLICKED' => ($is_voted) ? 'data-clicked="true"' : '',
                'ENTRY_OPTION_IS_VOTED' => ($is_voted) ? '' : 'hidden',
                'ENTRY_VOTE_OPTIONS' => $votes,
                'ENTRY_SOUNDCLOUD_ID' => $yx['entry']['soundcloud_id'],
                'ENTRY_DECODED_TEXT' => html_entity_decode($yx['entry']['text']),
                'ENTRY_DECODED_TWEET' => html_entity_decode($yx['entry']['tweet']),
                'ENTRY_VIDEO_HTML' => $video_html,
                'ENTRY_INSTAGRAM' => html_entity_decode($yx['entry']['instagram'])
            ), false);
        }
    }
    return $entries;
}
function YX_DeleteUser($user_id = 0)
{
    global $yx, $sqlConnect;
    if (empty($user_id)) {
        return false;
    }
    if ($yx['loggedin'] == false) {
        return false;
    }
    $user_data = YX_UserData($user_id);
    if (empty($user_data)) {
        return false;
    }
    if ($yx['user']['user_id'] != $user_data['user_id']) {
        if (YX_IsAdmin() == false) {
            return false;
        }
    }
    $query_one_delete_photos = mysqli_query($sqlConnect, " SELECT `avatar`, `cover` FROM " . T_USERS . " WHERE `user_id` = {$user_id}");
    $fetched_data            = mysqli_fetch_assoc($query_one_delete_photos);
    if (isset($fetched_data['avatar']) && !empty($fetched_data['avatar']) && $fetched_data['avatar'] != $yx['userDefaultAvatar']) {
        @unlink($fetched_data['avatar']);
        $delete_from_s3 = YX_DeleteFromToS3($fetched_data['avatar']);
    }
    if (isset($fetched_data['cover']) && !empty($fetched_data['cover']) && $fetched_data['cover'] != $yx['userDefaultCover']) {
        @unlink($fetched_data['cover']);
        $delete_from_s3 = YX_DeleteFromToS3($fetched_data['avatar']);
    }
    $query_one_delete_lists = mysqli_query($sqlConnect, " SELECT `id` FROM " . T_LISTS . " WHERE `user_id` = {$user_id}");
    if (mysqli_num_rows($query_one_delete_lists) > 0) {
        while ($fetched_data = mysqli_fetch_assoc($query_one_delete_lists)) {
            $delete_lists = YX_DeletePost($fetched_data['id'], 'lists');
        }
    }
    $query_one_delete_news = mysqli_query($sqlConnect, " SELECT `id` FROM " . T_NEWS . " WHERE `user_id` = {$user_id}");
    if (mysqli_num_rows($query_one_delete_news) > 0) {
        while ($fetched_data = mysqli_fetch_assoc($query_one_delete_news)) {
            $delete_news = YX_DeletePost($fetched_data['id'], 'news');
        }
    }
    $query_one_delete_videos = mysqli_query($sqlConnect, " SELECT `id` FROM " . T_VIDEOS . " WHERE `user_id` = {$user_id}");
    if (mysqli_num_rows($query_one_delete_videos) > 0) {
        while ($fetched_data = mysqli_fetch_assoc($query_one_delete_videos)) {
            $delete_videos = YX_DeletePost($fetched_data['id'], 'videos');
        }
    }
    $query_one_delete_music = mysqli_query($sqlConnect, " SELECT `id` FROM " . T_MUSIC . " WHERE `user_id` = {$user_id}");
    if (mysqli_num_rows($query_one_delete_music) > 0) {
        while ($fetched_data = mysqli_fetch_assoc($query_one_delete_music)) {
            $delete_music = YX_DeletePost($fetched_data['id'], 'music');
        }
    }
    $query_one_delete_polls = mysqli_query($sqlConnect, " SELECT `id` FROM " . T_POLLS_PAGES . " WHERE `user_id` = {$user_id}");
    if (mysqli_num_rows($query_one_delete_polls) > 0) {
        while ($fetched_data = mysqli_fetch_assoc($query_one_delete_polls)) {
            $delete_polls = YX_DeletePost($fetched_data['id'], 'polls');
        }
    }
    $query_one = mysqli_query($sqlConnect, "DELETE FROM " . T_SESSIONS . " WHERE `user_id` = {$user_id}");
    $query_one .= mysqli_query($sqlConnect, "DELETE FROM " . T_USERS . " WHERE `user_id` = {$user_id}");
    if ($query_one) {
        return true;
    }
    return false;
}
function YX_Search($search_data = array())
{
    global $yx, $sqlConnect;
    if (empty($search_data['keyword'])) {
        return false;
    }
    $data           = array();
    $data['news']   = array();
    $data['lists']  = array();
    $data['videos'] = array();
    $data['music']  = array();
    $data['polls']  = array();
    $data['quiz']   = array();
    $keyword        = YX_Secure($search_data['keyword']);
    $limit          = 20;
    if (!empty($search_data['limit'])) {
        $limit = $search_data['limit'];
    }
    $query_one = mysqli_query($sqlConnect, "SELECT `id` FROM " . T_NEWS . " WHERE `title` LIKE '%{$keyword}%' AND `active` = '1' ORDER BY `id` DESC LIMIT {$limit}") or die(mysqli_error($sqlConnect));
    while ($row = mysqli_fetch_assoc($query_one)) {
        $data['news'][] = YX_GetNews($row['id']);
    }
    $query_one = mysqli_query($sqlConnect, "SELECT `id` FROM " . T_LISTS . " WHERE `title` LIKE '%{$keyword}%' AND `active` = '1' ORDER BY `id` DESC LIMIT {$limit}") or die(mysqli_error($sqlConnect));
    while ($row = mysqli_fetch_assoc($query_one)) {
        $data['lists'][] = YX_GetLists($row['id']);
    }
    $query_one = mysqli_query($sqlConnect, "SELECT `id` FROM " . T_VIDEOS . " WHERE `title` LIKE '%{$keyword}%' AND `active` = '1' ORDER BY `id` DESC LIMIT {$limit}") or die(mysqli_error($sqlConnect));
    while ($row = mysqli_fetch_assoc($query_one)) {
        $data['videos'][] = YX_GetVideos($row['id']);
    }
    $query_one = mysqli_query($sqlConnect, "SELECT `id` FROM " . T_MUSIC . " WHERE `title` LIKE '%{$keyword}%' AND `active` = '1' ORDER BY `id` DESC LIMIT {$limit}") or die(mysqli_error($sqlConnect));
    while ($row = mysqli_fetch_assoc($query_one)) {
        $data['music'][] = YX_GetMusic($row['id']);
    }
    $query_one = mysqli_query($sqlConnect, "SELECT `id` FROM " . T_POLLS_PAGES . " WHERE `title` LIKE '%{$keyword}%' AND `active` = '1' ORDER BY `id` DESC LIMIT {$limit}") or die(mysqli_error($sqlConnect));
    while ($row = mysqli_fetch_assoc($query_one)) {
        $data['polls'][] = YX_GetPolls($row['id']);
    }
    $query_one = mysqli_query($sqlConnect, "SELECT `id` FROM " . T_QUIZZES . " WHERE `title` LIKE '%{$keyword}%' AND `active` = '1' ORDER BY `id` DESC LIMIT {$limit}") or die(mysqli_error($sqlConnect));
    while ($row = mysqli_fetch_assoc($query_one)) {
        $data['quiz'][] = YX_GetQuizzes($row['id']);
    }
    return $data;
}
function YX_CountUserPosts($user_id = 0, $type = '')
{
    global $l, $sqlConnect;
    if (empty($user_id) || empty($type)) {
        return false;
    }
    $table = '';
    switch ($type) {
        case 'news':
            $table = T_NEWS;
            break;
        case 'list':
            $table = T_LISTS;
            break;
        case 'poll':
            $table = T_POLLS_PAGES;
            break;
        case 'video':
            $table = T_VIDEOS;
            break;
        case 'music':
            $table = T_MUSIC;
            break;
        case 'quiz':
            $table = T_QUIZZES;
            break;
    }
    if (empty($table)) {
        return false;
    }
    $user_id       = YX_Secure($user_id);
    $query_one     = mysqli_query($sqlConnect, "SELECT COUNT(id) as count FROM {$table} WHERE user_id = '{$user_id}'");
    $query_one_sql = mysqli_fetch_assoc($query_one);
    return $query_one_sql['count'];
}
function YX_FeaturedPost($id = 0, $type = '', $featured = '')
{
    global $yx, $sqlConnect;
    if ($yx['loggedin'] == false) {
        return false;
    }
    if (empty($id) || empty($type)) {
        return false;
    }
    $types = array(
        'news',
        'lists',
        'music',
        'videos',
        'polls',
        'quiz'
    );
    if (!in_array($type, $types)) {
        return false;
    }
    if (YX_IsAdmin() === false) {
        return false;
    }
    $activate_post = false;
    if ($type == 'news') {
        $table = T_NEWS;
    } else if ($type == 'lists') {
        $table = T_LISTS;
    } else if ($type == 'polls') {
        $table = T_POLLS_PAGES;
    } else if ($type == 'videos') {
        $table = T_VIDEOS;
    } else if ($type == 'music') {
        $table = T_MUSIC;
    } else if ($type == 'quiz') {
        $table = T_QUIZZES;
    }
    $post_data = YX_GetPost($id, 0, $type);
    if (empty($post_data)) {
        return false;
    }
    $active_num = 0;
    if ($featured == 'featured') {
        $active_num = 1;
    }
    $post_query    = "UPDATE {$table} SET `featured` = '{$active_num}' WHERE `id` = '{$id}'";
    $activate_post = mysqli_query($sqlConnect, $post_query);
    if ($activate_post) {
        return true;
    }
}
function YX_ActivatePost($id = 0, $type = '', $activation = '')
{
    global $yx, $sqlConnect;
    if ($yx['loggedin'] == false) {
        return false;
    }
    if (empty($id) || empty($type)) {
        return false;
    }
    $types = array(
        'news',
        'lists',
        'music',
        'videos',
        'polls',
        'quiz'
    );
    if (!in_array($type, $types)) {
        return false;
    }
    if (YX_IsAdmin() === false) {
        return false;
    }
    $activate_post = false;
    if ($type == 'news') {
        $table = T_NEWS;
    } else if ($type == 'lists') {
        $table = T_LISTS;
    } else if ($type == 'polls') {
        $table = T_POLLS_PAGES;
    } else if ($type == 'videos') {
        $table = T_VIDEOS;
    } else if ($type == 'music') {
        $table = T_MUSIC;
    } else if ($type == 'quiz') {
        $table = T_QUIZZES;
    }
    $post_data = YX_GetPost($id, 0, $type);
    if (empty($post_data)) {
        return false;
    }
    $active_num = 0;
    if ($activation == 'activate') {
        $active_num = 1;
    }
    $post_query    = "UPDATE {$table} SET `active` = '{$active_num}' WHERE `id` = '{$id}'";
    $activate_post = mysqli_query($sqlConnect, $post_query);
    if ($activate_post) {
        return true;
    }
}
function YX_GetTerms()
{
    global $sqlConnect;
    $data  = array();
    $query = mysqli_query($sqlConnect, "SELECT * FROM " . T_TERMS);
    while ($fetched_data = mysqli_fetch_assoc($query)) {
        $data[$fetched_data['type']] = $fetched_data['text'];
    }
    return $data;
}
function YX_GetRegisteredDataStatics($month, $type = 'user')
{
    global $yx, $sqlConnect;
    $year       = date("Y");
    $type_table = T_USERS;
    $type_id    = 'user_id';
    if ($type == 'user') {
        $type_table = T_USERS;
        $type_id    = 'user_id';
    } else if ($type == 'news') {
        $type_table = T_NEWS;
        $type_id    = 'id';
    } else if ($type == 'lists') {
        $type_table = T_LISTS;
        $type_id    = 'id';
    } else if ($type == 'videos') {
        $type_table = T_VIDEOS;
        $type_id    = 'id';
    } else if ($type == 'music') {
        $type_table = T_MUSIC;
        $type_id    = 'id';
    } else if ($type == 'polls') {
        $type_table = T_POLLS_PAGES;
        $type_id    = 'id';
    } else if ($type == 'quizzes') {
        $type_table = T_QUIZZES;
        $type_id    = 'id';
    }
    $type_id      = YX_Secure($type_id);
    $query_one    = mysqli_query($sqlConnect, "SELECT COUNT($type_id) as count FROM {$type_table} WHERE `registered` = '{$month}/{$year}'");
    $fetched_data = mysqli_fetch_assoc($query_one);
    return $fetched_data['count'];
}
function YX_IsAdActive($type = '')
{
    global $sqlConnect;
    $query_one     = "SELECT COUNT(`id`) AS `count` FROM " . T_ADS . " WHERE `type` = '{$type}' AND `active` = '1' ";
    $sql_query_one = mysqli_query($sqlConnect, $query_one);
    $fetched_data  = mysqli_fetch_assoc($sql_query_one);
    return $fetched_data['count'];
}
function YX_GetAd($type = '', $admin = true)
{
    global $sqlConnect;
    $type      = YX_Secure($type);
    $query_one = "SELECT `code` FROM " . T_ADS . " WHERE `type` = '{$type}'";
    if ($admin === false) {
        $query_one .= " AND `active` = '1'";
    }
    $sql_query_one = mysqli_query($sqlConnect, $query_one);
    $fetched_data  = mysqli_fetch_assoc($sql_query_one);
    return $fetched_data['code'];
}
function YX_UpdateAdActivation($type)
{
    global $sqlConnect, $yx;
    if ($yx['loggedin'] == false) {
        return false;
    }
    if (YX_IsAdmin() === false) {
        return false;
    }
    if (YX_IsAdActive($type)) {
        $query_one = mysqli_query($sqlConnect, "UPDATE " . T_ADS . " SET `active` = '0' WHERE `type` = '{$type}'");
        return 'inactive';
    } else {
        $query_one = mysqli_query($sqlConnect, "UPDATE " . T_ADS . " SET `active` = '1' WHERE `type` = '{$type}'");
        return 'active';
    }
}
function YX_UpdateAdsCode($update_data = array())
{
    global $sqlConnect, $yx;
    if ($yx['loggedin'] == false) {
        return false;
    }
    if (YX_IsAdmin() === false) {
        return false;
    }
    if (empty($update_data)) {
        return false;
    }
    if (empty($update_data['type'])) {
        return false;
    }
    $type   = YX_Secure($update_data['type']);
    $update = array();
    foreach ($update_data as $field => $data) {
        $update[] = '`' . $field . '` = \'' . mysqli_real_escape_string($sqlConnect, $data) . '\'';
    }
    $query_text    = implode(', ', $update);
    $query_one     = " UPDATE " . T_ADS . " SET {$query_text} WHERE `type` = '{$type}'";
    $sql_query_one = mysqli_query($sqlConnect, $query_one);
    if ($sql_query_one) {
        return true;
    }
}
function YX_GetPostTags($tags = array())
{
    global $yx;
    if (empty($tags)) {
        return false;
    }
    $tags_final = array();
    $tags       = explode(',', $tags);
    foreach ($tags as $key => $tag) {
        $tags_final[] = trim($tag);
    }
    return $tags_final;
}
function YX_GetLanguages()
{
    $data           = array();
    $dir            = scandir('assets/langs');
    $languages_name = array_diff($dir, array(
        ".",
        "..",
        "error_log",
        "index.html",
        ".htaccess",
        "_notes",
        "extra"
    ));
    return $languages_name;
}
function YX_CheckUserSessionID($user_id = 0, $session_id = '', $platform = 'phone')
{
    global $yx, $sqlConnect;
    if (empty($user_id) || !is_numeric($user_id) || $user_id < 0) {
        return false;
    }
    if (empty($session_id)) {
        return false;
    }
    $platform  = YX_Secure($platform);
    $query     = mysqli_query($sqlConnect, "SELECT COUNT(`id`) as `session` FROM " . T_APP_SESSIONS . " WHERE `user_id` = '{$user_id}' AND `session_id` = '{$session_id}' AND `platform` = '{$platform}'");
    $query_sql = mysqli_fetch_assoc($query);
    if ($query_sql['session'] > 0) {
        return true;
    }
    return false;
}
function YX_RegisterComment($registration_data = array())
{
    global $yx, $sqlConnect;
    if (empty($registration_data)) {
        return false;
    }
    if (empty($registration_data['text']) || empty($registration_data['news_id'])) {
        return false;
    }
    $fields = '`' . implode('`, `', array_keys($registration_data)) . '`';
    $data   = '\'' . implode('\', \'', $registration_data) . '\'';
    $query = mysqli_query($sqlConnect, "INSERT INTO " . T_COMMENTS . " ({$fields}) VALUES ({$data})") or die(mysqli_error($sqlConnect));
    if ($query) {
        return mysqli_insert_id($sqlConnect);
    } else {
        return false;
    }
}
function YX_RegisterReply($registration_data = array())
{
    global $yx, $sqlConnect;
    if (empty($registration_data)) {
        return false;
    }
    if (empty($registration_data['text']) || empty($registration_data['news_id'])) {
        return false;
    }
    $fields = '`' . implode('`, `', array_keys($registration_data)) . '`';
    $data   = '\'' . implode('\', \'', $registration_data) . '\'';
    $sql    = "INSERT INTO " . T_COMM_REPLIES . " ({$fields}) VALUES ({$data})";
    $query = mysqli_query($sqlConnect, $sql) or die(mysqli_error($sqlConnect));
    if ($query) {
        return mysqli_insert_id($sqlConnect);
    } else {
        return false;
    }
}
function YX_CommentData($id = false)
{
    global $yx, $sqlConnect;
    if (empty($id) || !is_numeric($id) || $id < 1) {
        return false;
    }
    $table        = T_COMMENTS;
    $sql          = " SELECT * FROM `$table` WHERE `id` = {$id} ";
    $query        = mysqli_query($sqlConnect, $sql);
    $fetched_data = mysqli_fetch_assoc($query);
    $data         = false;
    if (!empty($fetched_data)) {
        $fetched_data['user_data'] = YX_UserData($fetched_data['user_id']);
        $fetched_data['replies']   = YX_GetCommReplies($fetched_data['id']);
        $fetched_data['owner']     = YX_IsCommentOwner($fetched_data['id']);
        $data                      = $fetched_data;
    }
    return $data;
}
function YX_IsCommentOwner($id = false, $user_id = false)
{
    global $yx, $sqlConnect;
    if ($yx['loggedin'] === false || empty($id) || !is_numeric($id) || $id < 1) {
        return false;
    }
    if (!$user_id) {
        $user_id = $yx['user']['user_id'];
    }
    $table = T_COMMENTS;
    $sql   = "SELECT `id` FROM `$table` WHERE `id` = '{$id}' AND `user_id` = '{$user_id}'";
    $query = mysqli_query($sqlConnect, $sql);
    return (mysqli_num_rows($query) > 0);
}
function YX_IsReplyOwner($id = false, $user_id = false)
{
    global $yx, $sqlConnect;
    if ($yx['loggedin'] === false || empty($id) || !is_numeric($id) || $id < 1) {
        return false;
    }
    if (!$user_id) {
        $user_id = $yx['user']['user_id'];
    }
    $table = T_COMM_REPLIES;
    $sql   = "SELECT `id` FROM `$table` WHERE `id` = '{$id}' AND `user_id` = '{$user_id}'";
    $query = mysqli_query($sqlConnect, $sql);
    return (mysqli_num_rows($query) > 0);
}
function YX_CommentReplyData($id = false)
{
    global $yx, $sqlConnect;
    if (empty($id) || !is_numeric($id) || $id < 1) {
        return false;
    }
    $table        = T_COMM_REPLIES;
    $sql          = " SELECT * FROM `$table` WHERE `id` = {$id} ";
    $query        = mysqli_query($sqlConnect, $sql);
    $fetched_data = mysqli_fetch_assoc($query);
    $data         = false;
    if (!empty($fetched_data)) {
        $fetched_data['user_data'] = YX_UserData($fetched_data['user_id']);
        $fetched_data['owner']     = YX_IsReplyOwner($fetched_data['id']);
        $data                      = $fetched_data;
    }
    return $data;
}
function YX_CountPostComments($post_id = false, $page = '')
{
    global $yx, $sqlConnect;
    if (empty($post_id) || !is_numeric($post_id) || $post_id < 1 || !$page) {
        return false;
    }
    $table_1      = T_COMMENTS;
    $table_2      = T_COMM_REPLIES;
    $page         = YX_Secure($page);
    $sql          = "SELECT COUNT(*) as replies ,(SELECT COUNT(*) FROM `$table_1` WHERE `page` = '{$page}' AND `news_id` = '{$post_id}') as comms FROM `$table_2` WHERE `page` = '{$page}' AND `news_id` = '{$post_id}'";
    $query        = mysqli_query($sqlConnect, $sql);
    $fetched_data = mysqli_fetch_assoc($query);
    return ($fetched_data['replies'] + $fetched_data['comms']);
}
function YX_GetStoryComments($args = array())
{
    global $yx, $sqlConnect;
    if (empty($args)) {
        return false;
    }
    $options = array(
        "id" => false,
        "offset" => 0,
        "post_id" => false,
        "page" => false
    );
    $args    = array_merge($options, $args);
    $offset  = YX_Secure($args['offset']);
    $id      = YX_Secure($args['id']);
    $post    = YX_Secure($args['post_id']);
    $page    = YX_Secure($args['page']);
    $table   = T_COMMENTS;
    $sub_sql = "";
    if ($page) {
        $sub_sql = " AND `page` = '{$page}' ";
    }
    if ($offset && is_numeric($offset) && $offset > 0) {
        $sub_sql .= " AND `id` < '{$offset}' AND `id` <> '{$offset}' ";
    }
    $sql   = "SELECT `id` FROM $table WHERE `news_id` = '{$post}' {$sub_sql} ORDER BY `id` DESC LIMIT 10";
    $data  = array();
    $query = mysqli_query($sqlConnect, $sql);
    while ($fetched_data = mysqli_fetch_assoc($query)) {
        $data[] = YX_CommentData($fetched_data['id']);
    }
    return $data;
}
function YX_GetCommReplies($comm_id = false)
{
    global $yx, $sqlConnect;
    if (empty($comm_id) || !is_numeric($comm_id) || $comm_id < 1) {
        return false;
    }
    $table = T_COMM_REPLIES;
    $sql   = "SELECT `id` FROM $table WHERE `comment` = '{$comm_id}' ORDER BY `id` DESC";
    $data  = array();
    $query = mysqli_query($sqlConnect, $sql);
    while ($fetched_data = mysqli_fetch_assoc($query)) {
        $data[] = YX_CommentReplyData($fetched_data['id']);
    }
    return $data;
}
function YX_DeleteComment($id = false, $page = false)
{
    global $yx, $sqlConnect;
    if (empty($id) || !is_numeric($id) || $id < 1) {
        return false;
    }
    if (!YX_IsCommentOwner($id)) {
        return false;
    }
    $c_table = T_COMMENTS;
    $r_table = T_COMM_REPLIES;
    $page    = YX_Secure($page);
    $sql     = " DELETE FROM `$c_table` WHERE `id` = '{$id}' ";
    @mysqli_query($sqlConnect, "DELETE FROM `$r_table` WHERE `comment` = '{$id}' AND `page` = '{$page}'");
    return mysqli_query($sqlConnect, $sql);
}
function YX_DeleteReply($id = false)
{
    global $yx, $sqlConnect;
    if (empty($id) || !is_numeric($id) || $id < 1) {
        return false;
    }
    if (!YX_IsReplyOwner($id)) {
        return false;
    }
    $table = T_COMM_REPLIES;
    $id    = YX_Secure($id);
    $sql   = " DELETE FROM `$table` WHERE `id` = '{$id}' ";
    return mysqli_query($sqlConnect, $sql);
}
function YX_IsReactionExists($post_id = false, $option_id = false)
{
    global $sqlConnect, $yx;
    if (empty($post_id) || empty($option_id)) {
        return false;
    }
    if (!is_numeric($post_id) || !is_numeric($option_id) || $option_id < 1 || $post_id < 1) {
        return false;
    }
    $post_id   = YX_Secure($post_id);
    $option_id = YX_Secure($option_id);
    $table     = T_REACTIONS;
    $ip        = $_SERVER['REMOTE_ADDR'];
    $where     = " `id` > 0 ";
    $user_id   = ($yx['loggedin'] == true) ? $yx['user']['user_id'] : 0;
    if (!empty($user_id)) {
        $where = "(`user_id`  = '{$user_id}')";
    } else {
        $where = "(`ip_address` = '{$ip}' AND `user_id`  = '0')";
    }
    $sql   = "SELECT COUNT(`user_id`) 
                  FROM  `$table` 
                  WHERE {$where}
                  AND   `post_id`   = '{$post_id}' 
                  AND   `option_id` = '{$option_id}'";
    $query = mysqli_query($sqlConnect, $sql);
    return (YX_Sql_Result($query, 0) > 0) ? true : false;
}
function YX_RegisterReaction($registration_data = array())
{
    global $yx, $sqlConnect;
    if (empty($registration_data)) {
        return false;
    }
    $fields = '`' . implode('`, `', array_keys($registration_data)) . '`';
    $data   = '\'' . implode('\', \'', $registration_data) . '\'';
    $table  = T_REACTIONS;
    $sql    = "INSERT INTO `$table` ({$fields}) VALUES ({$data})";
    $query = mysqli_query($sqlConnect, $sql) or die(mysqli_error($sqlConnect));
    if ($query) {
        return mysqli_insert_id($sqlConnect);
    } else {
        return false;
    }
}
function YX_GetReactions($option_id = false, $post_id = false, $page = false)
{
    global $yx, $sqlConnect;
    if (empty($option_id) || !is_numeric($option_id) || empty($post_id) || !is_numeric($post_id)) {
        return false;
    }
    $data         = array();
    $option_id    = YX_Secure($option_id);
    $post_id      = YX_Secure($post_id);
    $page         = YX_Secure($page);
    $table        = T_REACTIONS;
    $query_one    = "SELECT COUNT(id) as count FROM `$table` WHERE `option_id` = {$option_id} AND `post_id` = '{$post_id}' AND `page` = '{$page}' ";
    $sql          = mysqli_query($sqlConnect, $query_one);
    $fetched_data = mysqli_fetch_assoc($sql);
    if (empty($fetched_data)) {
        return array();
    }
    return $fetched_data['count'];
}
function YX_GetPercentageOfReactions($post_id = false, $page = false)
{
    global $yx, $sqlConnect;
    if (empty($post_id) || empty($page)) {
        return false;
    }
    if (!is_numeric($post_id)) {
        return false;
    }
    $all  = 0;
    $data = array();
    for ($i = 1; $i < 10; $i++) {
        $data[$i]           = array();
        $data[$i]['num']    = YX_GetReactions($i, $post_id, $page);
        $data[$i]['status'] = (YX_IsReactionExists($post_id, $i)) ? 'rgb(216, 216, 216)' : '#f1f1f1';
    }
    return $data;
}
function YX_GetMoreNews($offset = false, $before_id = false)
{
    global $yx, $sqlConnect;
    if (empty($offset) || empty($before_id)) {
        return false;
    }
    $data  = array();
    $table = T_NEWS;
    $news  = implode(',', range($offset, $before_id));
    $sql   = "SELECT `id` FROM `$table` WHERE `active` = '1' AND `id` NOT IN ({$news}) ORDER BY `id` DESC LIMIT 15";
    $query = mysqli_query($sqlConnect, $sql);
    while ($fetched_data = mysqli_fetch_assoc($query)) {
        $news_data = YX_GetNews($fetched_data['id']);
        if (!empty($news_data)) {
            $data[]['news'] = $news_data;
        }
    }
    return $data;
}
function YX_RegisterVerificationRequest($registration_data = array())
{
    global $yx, $sqlConnect;
    if ($yx['loggedin'] === false || empty($registration_data)) {
        return false;
    }
    $fields = '`' . implode('`, `', array_keys($registration_data)) . '`';
    $data   = '\'' . implode('\', \'', $registration_data) . '\'';
    $table  = T_VER_REQUESTS;
    $query = mysqli_query($sqlConnect, "INSERT INTO `$table` ({$fields}) VALUES ({$data})") or die(mysqli_error($sqlConnect));
    if ($query) {
        return mysqli_insert_id($sqlConnect);
    } else {
        return false;
    }
}
function YX_IsVerificationRequestExists($user_id = false)
{
    global $sqlConnect, $yx;
    if ($yx['loggedin'] === false) {
        return false;
    }
    if (!is_numeric($user_id) || $user_id < 1) {
        $user_id = $yx['user']['user_id'];
    }
    $table   = T_VER_REQUESTS;
    $user_id = $yx['user']['user_id'];
    $sql     = "SELECT COUNT(`user_id`) FROM `$table` WHERE `user_id` = '{$user_id}'";
    $query   = mysqli_query($sqlConnect, $sql);
    return (YX_Sql_Result($query, 0) > 0) ? true : false;
}
function YX_UpdateVerificationRequest($id = false, $update_data = array())
{
    global $yx, $sqlConnect;
    if ($yx['loggedin'] === false || empty($update_data) || !$id) {
        return false;
    }
    $id = YX_Secure($id);
    foreach ($update_data as $field => $data) {
        $update[] = '`' . $field . '` = \'' . $data . '\'';
    }
    $impload   = implode(', ', $update);
    $query_one = "UPDATE " . T_VER_REQUESTS . " SET {$impload} WHERE `id` = {$id} ";
    $query     = mysqli_query($sqlConnect, $query_one);
    return $query;
}
function YX_VerifyUser($id = false)
{
    global $yx, $sqlConnect;
    if ($yx['loggedin'] === false || !$id) {
        return false;
    }
    $table    = T_VER_REQUESTS;
    $sql      = "SELECT `user_id` FROM `$table` WHERE `id` = '{$id}' ";
    $query    = mysqli_query($sqlConnect, $sql);
    $verified = false;
    if (!empty($query) && YX_Sql_Result($query, 0) > 0) {
        $fetched_data = mysqli_fetch_assoc($query);
        $up_date      = array(
            'verified' => 1
        );
        $user_id      = $fetched_data['user_id'];
        $users_table  = T_USERS;
        $verified     = mysqli_query($sqlConnect, "UPDATE `$users_table` SET `verified` = 1");
        if ($verified) {
            @mysqli_query($sqlConnect, "DELETE FROM `$table` WHERE `id` = '{$id}'");
        }
    }
    return $verified;
}
function YX_DeleteVerificationRequest($id = false)
{
    global $yx, $sqlConnect;
    if ($yx['loggedin'] === false || !$id) {
        return false;
    }
    $table   = T_VER_REQUESTS;
    $sql     = "SELECT `user_id` FROM `$table` WHERE `id` = '{$id}' ";
    $query   = mysqli_query($sqlConnect, $sql);
    $ignored = false;
    if (!empty($query) && YX_Sql_Result($query, 0) > 0) {
        $fetched_data = mysqli_fetch_assoc($query);
        $user_id      = $fetched_data['user_id'];
        $users_table  = T_USERS;
        $ignored      = mysqli_query($sqlConnect, "UPDATE `$users_table` SET `verified` = 0");
        if ($ignored) {
            @mysqli_query($sqlConnect, "DELETE FROM `$table` WHERE `id` = '{$id}'");
        }
    }
    return $ignored;
}
function YX_VerificationRequestData($id = false)
{
    global $yx, $sqlConnect;
    if ($yx['loggedin'] === false || !$id) {
        return false;
    }
    $table = T_VER_REQUESTS;
    $sql   = "SELECT * FROM `$table` WHERE `id` = '{$id}' ";
    $query = mysqli_query($sqlConnect, $sql);
    $data  = false;
    if (!empty($query) && mysqli_num_rows($query) > 0) {
        $fetched_data              = mysqli_fetch_assoc($query);
        $fetched_data['user_data'] = YX_UserData($fetched_data['user_id']);
        $data                      = $fetched_data;
    }
    return $data;
}
function YX_InsertData($re_data = array(), $table = false)
{
    global $yx, $sqlConnect;
    if ($yx['loggedin'] === false || empty($re_data)) {
        return false;
    }
    $fields = '`' . implode('`, `', array_keys($re_data)) . '`';
    $data   = '\'' . implode('\', \'', $re_data) . '\'';
    $sql    = "INSERT INTO `$table` ({$fields}) VALUES ({$data})";
    $query = mysqli_query($sqlConnect, $sql) or die(mysqli_error($sqlConnect));
    if ($query) {
        return mysqli_insert_id($sqlConnect);
    } else {
        return false;
    }
}
function YX_UpdateData($id = false, $up_data = array(), $table = false)
{
    global $yx, $sqlConnect;
    if ($yx['loggedin'] === false || empty($up_data) || empty($id)) {
        return false;
    }
    foreach ($up_data as $field => $data) {
        $update[] = '`' . $field . '` = \'' . $data . '\'';
    }
    $impload   = implode(', ', $update);
    $query_one = "UPDATE `$table` SET {$impload} WHERE `id` = {$id} ";
    $query     = mysqli_query($sqlConnect, $query_one);
    return $query;
}

function YX_DeleteData($where = array(), $table = false)
{
    global $yx, $sqlConnect;
    if ($yx['loggedin'] === false || empty($where) || !is_array($where)) {
        return false;
    }
    $__where__   = "";
    $where_array = array();
    $where_type  = "AND";
    if (count($where) > 1) {
        foreach ($where as $key => $value) {
            $string = ' "' . YX_Secure($value['value']) . '" ';
            if (isset($value['string'])) {
                if ($value['string'] == false) {
                    $string = ' ' . YX_Secure($value['value']) . ' ';
                }
            }
            $where_array[] = YX_Secure($value['column']) . ' ' . $value['mark'] . $string;
        }
        $__where__ .= implode(' ' . $where_type . ' ', $where_array);
    } else {
        $string = ' "' . YX_Secure($where[0]['value']) . '" ';
        if (isset($where[0]['string'])) {
            if ($where[0]['string'] == false) {
                $string = ' ' . YX_Secure($where[0]['value']) . ' ';
            }
        }
        $__where__ .= YX_Secure($where[0]['column']) . ' ' . $where[0]['mark'] . $string;
    }
    $query_one = "DELETE FROM `$table` WHERE {$__where__}";
    $query     = mysqli_query($sqlConnect, $query_one);
    return $query;
}

function YX_CountData($where = array(), $table = false, $col_name = false)
{
    global $yx, $sqlConnect;
    $__where__   = "";
    $where_array = array();
    $where_type  = 'AND';
    $where_string = 'WHERE';
    if (is_array($where) && !empty($where)) {
        $col_name = 'id';
        if (count($where) > 1) {
            foreach ($where as $key => $value) {
                $string = ' "' . YX_Secure($value['value']) . '" ';
                if (isset($value['string'])) {
                    if ($value['string'] == false) {
                        $string = ' ' . YX_Secure($value['value']) . ' ';
                    }
                }

                $where_array[] = YX_Secure($value['column']) . ' ' . $value['mark'] . $string;
            }
            $__where__ .= implode(' ' . $where_type . ' ', $where_array);
        } else {
            $string = ' "' . YX_Secure($where[0]['value']) . '" ';
            if (isset($where[0]['string'])) {
                if ($where[0]['string'] == false) {
                    $string = ' ' . YX_Secure($where[0]['value']) . ' ';
                }
            }
            $__where__ .= YX_Secure($where[0]['column']) . ' ' . $where[0]['mark'] . $string;
        }
    } else {
        $where_string = '';
    }
    $query_one    = "SELECT COUNT(`$col_name`) AS 'count' FROM `$table` $where_string {$__where__}";
    $query        = mysqli_query($sqlConnect, $query_one);
    $fetched_data = mysqli_fetch_assoc($query);
    return $fetched_data['count'];
}
function YX_ShortText($text = "", $len = 100)
{
    if (empty($text) || !is_string($text) || !is_numeric($len) || $len < 1) {
        return "****";
    }
    if (strlen($text) > $len) {
        $text = mb_substr($text, 0, $len, "UTF-8") . "..";
    }
    return $text;
}
function YX_GetQuizResult($post_id = false, $index = false)
{
    global $yx, $sqlConnect;
    if (empty($index) || empty($post_id)) {
        return false;
    }
    $index   = YX_Secure($index);
    $post_id = YX_Secure($post_id);
    $table   = T_ENTRIES;
    $sql     = "SELECT * FROM `$table` WHERE `post_id` = '{$post_id}' AND `index_id` = '{$index}' AND `entry_type` = 'result'";
    $query = mysqli_query($sqlConnect, $sql) or die(mysqli_error($sqlConnect));
    $data = false;
    if ($query && mysqli_num_rows($query) > 0) {
        $fetched_data          = mysqli_fetch_assoc($query);
        $fetched_data['image'] = YX_GetMedia($fetched_data['image']);
        $data                  = $fetched_data;
    }
    return $data;
}
function YX_GetBrNews($id = false)
{
    global $yx, $sqlConnect;
    if (empty($id) || empty($id)) {
        return false;
    }
    $id    = YX_Secure($id);
    $table = T_BR_NEWS;
    $sql   = "SELECT * FROM `$table` WHERE `id` = '{$id}'";
    $query = mysqli_query($sqlConnect, $sql) or die(mysqli_error($sqlConnect));
    $data = false;
    if ($query && mysqli_num_rows($query) > 0) {
        $fetched_data           = mysqli_fetch_assoc($query);
        $fetched_data['expire'] = date("Y-M-d H:m", $fetched_data['expire']);
        $fetched_data['active_or'] = $fetched_data['active'];
        $fetched_data['active'] = ($fetched_data['active'] == 1) ? 'Active' : 'Not Active';
        $data                   = $fetched_data;
    }
    return $data;
}
function YX_GetAnnouncments()
{
    global $yx, $sqlConnect;
    if ($yx['loggedin'] === false) {
        return false;
    }
    $views_table = T_ANNOUNCEMENT_VIEWS;
    $table       = T_ANNOUNCEMENT;
    $user        = $yx['user']['user_id'];
    $sql         = "SELECT * FROM `$table` WHERE `active` = '1' 
                    AND `id` NOT IN (SELECT `announcement_id` FROM `$views_table` WHERE `user_id` = '{$user}' )";
    $data        = array();
    $query = mysqli_query($sqlConnect, $sql) or die(mysqli_error($sqlConnect));
    while ($fetched_data = mysqli_fetch_assoc($query)) {
        $data[] = $fetched_data;
    }
    return $data;
}

function YX_ImportRssFeed($url = false, $limit = false)
{
    global $yx, $sqlConnect;
    if (!$url || !YX_IsUrl($url)) {
        return false;
    }
    $feed      = @file_get_contents($url);
    $data      = array();
    $max_items = (is_numeric($limit) && $limit > 0) ? $limit : 10;
    if ($feed) {
        $yx_xml_feed = @simplexml_load_string($feed);
        $i           = 0;
        if ($yx_xml_feed) {
            foreach ($yx_xml_feed->channel->item as $val) {
                if ($i < $max_items) {
                    $f_data                = array();
                    $f_data['title']       = @$val->title;
                    $f_data['description'] = @$val->description;
                    $f_data['link']        = @$val->link;
                    $f_data['date']        = @$val->pubDate;
                    $f_data['image']       = null;
                    $child                 = @$val->children('media', true)->thumbnail->attributes();
                    $thumbnail             = @$child['url'];
                    $image                 = @$val->image->url;
                    $enclosure             = @$val->enclosure;
                    if (YX_IsUrl($thumbnail)) {
                        $f_data['image'] = $thumbnail;
                    } else if (YX_IsUrl($image)) {
                        $f_data['image'] = $image;
                    } else if (!empty($enclosure)) {
                        $enclosure_attrs = $enclosure->attributes();
                        $enclosure_type  = $enclosure_attrs['type'];
                        if (in_array($enclosure_type, $yx['img_mime_types'])) {
                            $f_data['image'] = $enclosure_attrs['url'];
                        }
                    } else {
                        $f_data['image'] = $yx['config']['theme_url'] . '/img/no-img.jpg';
                    }
                    if (!empty($f_data['title']) && !empty($f_data['description']) && !empty($f_data['link'])) {
                        $f_data['title']       = strip_tags($f_data['title']);
                        $f_data['description'] = strip_tags($f_data['description']);
                        $data[]                = $f_data;
                    }
                    $i++;
                } else {
                    break;
                }
            }
        }
    }
    return $data;
}

function YX_RegisterNewField($registration_data = array())
{
    global $yx, $sqlConnect;
    if (empty($registration_data)) {
        return false;
    }
    $fields = '`' . implode('`, `', array_keys($registration_data)) . '`';
    $data   = '\'' . implode('\', \'', $registration_data) . '\'';
    $table  = T_PR_FIELDS;
    $query  = mysqli_query($sqlConnect, "INSERT INTO  `$table` ({$fields}) VALUES ({$data})");
    if ($query) {
        $sql_id  = mysqli_insert_id($sqlConnect);
        $column  = 'fid_' . $sql_id;
        $table   = T_UC_FIELDS;
        $length  = $registration_data['length'];
        $query_2 = mysqli_query($sqlConnect, "ALTER TABLE `$table` ADD COLUMN `{$column}` varchar({$length}) NOT NULL DEFAULT ''");
        return true;
    }
    return false;
}

function YX_GetProfileFields($type = 'all')
{
    global $yx, $sqlConnect;
    $data       = array();
    $where      = '';
    $placements = array(
        'profile',
        'general'
    );

    if ($type != 'all' && in_array($type, $placements)) {
        $where = "WHERE `placement` = '{$type}' AND `placement` <> 'none' AND `active` = '1'";
    } else if ($type == 'none') {
        $where = "WHERE `profile_page` = '1' AND `active` = '1'";
    } else if ($type = 'registration') {
        $where = "WHERE `registration_page` = '1'";
    } else if ($type != 'admin') {
        $where = "WHERE `active` = '1'";
    }

    $type      = YX_Secure($type);
    $table     = T_PR_FIELDS;
    $query_one = "SELECT * FROM `$table` {$where} ORDER BY `id` ASC";
    $sql       = mysqli_query($sqlConnect, $query_one);
    while ($fetched_data = mysqli_fetch_assoc($sql)) {
        $fetched_data['fid'] = 'fid_' . $fetched_data['id'];

        $fetched_data['name'] = preg_replace_callback("/{{LANG (.*?)}}/", function ($m) use ($yx) {
            return (isset($yx['lang'][$m[1]])) ? $yx['lang'][$m[1]] : '';
        }, $fetched_data['name']);

        $fetched_data['description'] = preg_replace_callback("/{{LANG (.*?)}}/", function ($m) use ($yx) {
            return (isset($yx['lang'][$m[1]])) ? $yx['lang'][$m[1]] : '';
        }, $fetched_data['description']);

        $fetched_data['type'] = preg_replace_callback("/{{LANG (.*?)}}/", function ($m) use ($yx) {
            return (isset($yx['lang'][$m[1]])) ? $yx['lang'][$m[1]] : '';
        }, $fetched_data['type']);

        $data[]               = $fetched_data;
    }
    return $data;
}

function YX_UserFieldsData($user_id = false)
{
    global $yx, $sqlConnect;
    if (empty($user_id) || !is_numeric($user_id) || $user_id < 0) {
        return false;
    }
    $data         = array();
    $user_id      = YX_Secure($user_id);
    $table        = T_UC_FIELDS;
    $query_one    = "SELECT * FROM  `$table` WHERE `user_id` = {$user_id}";
    $sql          = mysqli_query($sqlConnect, $query_one);
    $fetched_data = mysqli_fetch_assoc($sql);
    if (empty($fetched_data)) {
        return array();
    }
    return $fetched_data;
}

function YX_UpdateUserCustomData($user_id, $update_data, $loggedin = true)
{
    global $yx, $sqlConnect;
    if ($loggedin == true) {
        if ($yx['loggedin'] == false) {
            return false;
        }
    }
    if (empty($user_id) || !is_numeric($user_id) || $user_id < 0) {
        return false;
    }
    if (empty($update_data)) {
        return false;
    }
    $user_id = YX_Secure($user_id);
    if ($loggedin == true) {
        if (YX_IsAdmin() === false) {
            if ($yx['user']['user_id'] != $user_id) {
                return false;
            }
        }
    }
    $update = array();
    foreach ($update_data as $field => $data) {
        foreach ($data as $key => $value) {
            $update[] = '`' . $key . '` = \'' . YX_Secure($value, 0) . '\'';
        }
    }
    $impload     = implode(', ', $update);
    $table       = T_UC_FIELDS;
    $query_one   = "UPDATE `$table` SET {$impload} WHERE `user_id` = {$user_id}";
    $query_1     = mysqli_query($sqlConnect, "SELECT COUNT(`id`) as count FROM `$table` WHERE `user_id` = {$user_id}") or die(mysqli_error($sqlConnect));
    $query_1_sql = mysqli_fetch_assoc($query_1);
    $query       = false;
    if ($query_1_sql['count'] == 1) {
        $query = mysqli_query($sqlConnect, $query_one);
    } else {
        $query_2 = mysqli_query($sqlConnect, "INSERT INTO `$table` (`user_id`) VALUES ({$user_id})") or die(mysqli_error($sqlConnect));
        if ($query_2) {
            $query = mysqli_query($sqlConnect, $query_one) or die(mysqli_error($sqlConnect));
        }
    }
    if ($query) {
        return true;
    }
    return false;
}

function YX_GetDataById($id = 0, $table = false)
{
    global $yx, $sqlConnect;
    if (empty($id) || !is_numeric($id) || $id < 0 || !$table) {
        return false;
    }

    $data         = array();
    $id           = YX_Secure($id);
    $sql          = "SELECT * FROM `$table` WHERE `id` = {$id}";
    $query        = mysqli_query($sqlConnect, $sql) or die(mysqli_error($sqlConnect));
    if ($query && mysqli_num_rows($query) > 0) {
        $fetched_data = mysqli_fetch_assoc($query);
        $data         = $fetched_data;
    }

    return $data;
}


function YX_DeleteField($id = false)
{
    global $yx, $sqlConnect;
    if ($yx['loggedin'] == false || !YX_IsAdmin()) {
        return false;
    }
    $id    = YX_Secure($id);
    $table = T_PR_FIELDS;
    $query = mysqli_query($sqlConnect, "DELETE FROM `$table` WHERE `id` = {$id}");
    if ($query) {
        $table  = T_UC_FIELDS;
        $query2 = mysqli_query($sqlConnect, "ALTER TABLE `$table` DROP `fid_{$id}`;");
        if ($query2) {
            return true;
        }
    }
    return false;
}

function YX_GetBanned($type = '')
{
    global $sqlConnect;
    $data  = array();
    $table = T_BANNED_IPS;
    $query = mysqli_query($sqlConnect, "SELECT * FROM `$table` ORDER BY id DESC");
    if ($type == 'user') {
        while ($fetched_data = mysqli_fetch_assoc($query)) {
            $data[] = $fetched_data['ip_address'];
        }
    } else {
        while ($fetched_data = mysqli_fetch_assoc($query)) {
            $data[] = $fetched_data;
        }
    }
    return $data;
}


function YX_UploadToS3($filename, $config = array())
{
    global $yx;
    if ($yx['config']['amazone_s3'] == 0) {
        return false;
    }

    $s3Config = empty($yx['config']['amazone_s3_key']) || empty($yx['config']['amazone_s3_s_key']) || empty($yx['config']['region'])           || empty($yx['config']['bucket_name']);


    if ($s3Config) {
        return false;
    }



    $s3 = new S3Client([
        'version'     => 'latest',
        'region'      => $yx['config']['region'],
        'credentials' => [
            'key'    => $yx['config']['amazone_s3_key'],
            'secret' => $yx['config']['amazone_s3_s_key'],
        ]
    ]);


    $s3->putObject([
        'Bucket' => $yx['config']['bucket_name'],
        'Key'    => $filename,
        'Body'   => fopen($filename, 'r+'),
        'ACL'    => 'public-read',
    ]);

    if (empty($config['delete'])) {
        if ($s3->doesObjectExist($yx['config']['bucket_name'], $filename)) {
            if (empty($config['amazon'])) {
                @unlink($filename);
            }
            return true;
        }
    } else {
        return true;
    }
}


function YX_DeleteFromToS3($filename, $config = array())
{
    global $yx;
    if ($yx['config']['amazone_s3'] == 0) {
        return false;
    }

    $s3Config = (empty($yx['config']['amazone_s3_key'])   ||
        empty($yx['config']['amazone_s3_s_key']) ||
        empty($yx['config']['region'])           ||
        empty($yx['config']['bucket_name']));

    if (!$s3Config) {
        return false;
    }

    $s3 = new S3Client([
        'version'     => 'latest',
        'region'      => $yx['config']['region'],
        'credentials' => [
            'key'    => $yx['config']['amazone_s3_key'],
            'secret' => $yx['config']['amazone_s3_s_key'],
        ]
    ]);
    $s3->deleteObject([
        'Bucket' => $yx['config']['bucket_name'],
        'Key'    => $filename,
    ]);
    if (!$s3->doesObjectExist($yx['config']['bucket_name'], $filename)) {
        return true;
    }
}

function YX_GetMediaSource($url = false, $crop = 0)
{
    global $yx;
    if (!$url || !YX_IsUrl($url)) {
        return false;
    }
    $url_full = $url;
    $url = substr($url, strpos($url, "upload"), 120);
    if (strpos($url, "upload") === false) {
        $url = YX_ImportImageFromLogin($url, 0);
        $url = substr($url, strpos($url, "upload"), 120);
    }
    if (!file_exists($url) && !empty($crop)) {
        $url = YX_ImportImageFromLogin($url_full, 0);
        $url = substr($url, strpos($url, "upload"), 120);
    }
    $url2_explode = explode('.', $url);
    $url2 = $url2_explode[0] . '_hd.' . $url2_explode[1];
    if (!empty($crop)) {
        $resize = YX_Resize_Crop_Image(1920, 1080, $url, $url2, 60);
        $resize = YX_Resize_Crop_Image(600, 320, $url, $url, 60);
        if ($yx['config']['amazone_s3'] == 1) {
            $upload = YX_UploadToS3($url);
            $upload = YX_UploadToS3($url2);
        }
    }
    return trim($url);
}

function YX_DeleteUserAD($id = false)
{
    global $yx, $db;

    $db->where('user_id', $yx['user']['id']);
    $db->where('id', $id);
    $ad  = $db->getOne(T_USER_ADS);
    $del = false;

    if (!empty($ad) || YX_IsAdmin()) {
        $del = $db->where('id', $id)->delete(T_USER_ADS);

        if (file_exists($ad->media_file)) {
            @unlink($ad->media_file);
        } else {
            @YX_DeleteFromToS3($ad->media_file);
        }
    }

    return $del;
}


function GetUserSession()
{
    global $sqlConnect;
    $query = mysqli_query($sqlConnect, "SELECT * FROM " .  T_SESSIONS . " WHERE `session_id` = '" . YX_Secure($_SESSION['user_id']) . "'");
    $fetched_data = mysqli_fetch_assoc($query);
    return $fetched_data['user_id'];
}


function DoFollow($id)
{
    if (empty($id)) {
        return false;
    }

    $MyID = GetUserSession();
    if ($MyID === $id) {
        return false;
    }

    global $sqlConnect;
    $query = mysqli_query($sqlConnect, "SELECT id FROM " .  T_FOLLOWS . " WHERE `user_id` = '" . $MyID . "'");
    $fetched_data = mysqli_fetch_assoc($query);


    if (isset($fetched_data['id'])) {

        $query = mysqli_query($sqlConnect, "SELECT relation_arr FROM " .  T_FOLLOWS . " WHERE `user_id` = '" . $MyID . "'");
        $fetched_data = mysqli_fetch_assoc($query);

        $arr = $fetched_data['relation_arr'];
        $arr = unserialize($arr);
        if (!in_array($id, $arr, TRUE)) {
            array_push($arr, $id);
            $arr = serialize($arr);
            $query_one   = " UPDATE " . T_FOLLOWS . " SET `relation_arr` = '{$arr}' WHERE `user_id` = '{$MyID}'";
            mysqli_query($sqlConnect, $query_one);
        } else {
            if (($key = array_search($id, $arr)) !== false) {
                unset($arr[$key]);
                $arr = serialize($arr);
                $query_one   = " UPDATE " . T_FOLLOWS . " SET `relation_arr` = '{$arr}' WHERE `user_id` = '{$MyID}'";
                mysqli_query($sqlConnect, $query_one);
            }
        }

        //

        $query = mysqli_query($sqlConnect, "SELECT relation_arr FROM " .  T_FOLLOWS . " WHERE `user_id` = '" . $id . "'");
        $fetched_data = mysqli_fetch_assoc($query);

        $arr = $fetched_data['relation_arr'];
        $arr = unserialize($arr);
        if (!in_array($id, $arr, TRUE)) {
            array_push($arr, $id);
            $arr = serialize($arr);
            $query_one   = " UPDATE " . T_FOLLOWS . " SET `relation_arr` = '{$arr}' WHERE `user_id` = '{$id}'";
            mysqli_query($sqlConnect, $query_one);
        } else {
            if (($key = array_search($id, $arr)) !== false) {
                unset($arr[$key]);
                $arr = serialize($arr);
                $query_one   = " UPDATE " . T_FOLLOWS . " SET `relation_arr` = '{$arr}' WHERE `user_id` = '{$id}'";
                mysqli_query($sqlConnect, $query_one);
            }
        }
    } else {

        $fields = 'user_id,	relation_arr';
        $arr = array();
        array_push($arr, $id);
        $arr = serialize($arr);
        $query = mysqli_query($sqlConnect, "INSERT INTO " . T_FOLLOWS . " ({$fields}) VALUES ('{$MyID}','{$arr}')") or die(mysqli_error($sqlConnect));

        $arr = array();
        array_push($arr, $MyID);
        $arr = serialize($arr);
        $query = mysqli_query($sqlConnect, "INSERT INTO " . T_FOLLOWS . " ({$fields}) VALUES ('{$id}','{$arr}')") or die(mysqli_error($sqlConnect));
    }


    return true;
}


function CheckFollow($id)
{
    global $sqlConnect;
    $MyID = GetUserSession();
    if ($MyID === $id) {
        return false;
    }




    $query = mysqli_query($sqlConnect, "SELECT relation_arr FROM " .  T_FOLLOWS . " WHERE `user_id` = '" . $MyID . "'");
    $fetched_data = mysqli_fetch_assoc($query);

    $arr = $fetched_data['relation_arr'];
    $arr = unserialize($arr);

    if (!in_array($id, $arr, TRUE)) {
        return 'no';
    } else {
        return 'yes';
    }
}




function ListFollowers()
{
    global $sqlConnect;
    global $url;
    $MyID = GetUserSession();


    $query = mysqli_query($sqlConnect, "SELECT relation_arr FROM " .  T_FOLLOWS . " WHERE `user_id` = '" . $MyID . "'");
    $fetched_data = mysqli_fetch_assoc($query);

    $arr = $fetched_data['relation_arr'];
    $arr = unserialize($arr);

    $content = '';

    foreach ($arr as $key => $v) {

        $data = mysqli_query($sqlConnect, "SELECT * FROM " .  T_USERS . " WHERE `user_id` = '" . $v . "'");
        $fetched_user = mysqli_fetch_assoc($data);
        $content .= '<li class="Tochat" data-id="' . $fetched_user['user_id'] . '">';
        $content .= '<img  class="shotimgonlist" src="' . $url . $fetched_user['avatar'] . '">';
        $content .= '<div>';
        $content .= '<h2 class="onlistName"><a href="' . YX_Link('user/' . $fetched_user['username']) . '">' . $fetched_user['username'] . '</a></h2>';
        $content .= '</div>';
        $content .= '</li>';
    }

    return $content;
}


function ChatHeader($id)
{

    global $sqlConnect;
    global $url;
    $MyID = GetUserSession();
    $query = mysqli_query($sqlConnect, "SELECT relation_arr FROM " .  T_FOLLOWS . " WHERE `user_id` = '" . $MyID . "'");
    $fetched_data = mysqli_fetch_assoc($query);

    $arr = $fetched_data['relation_arr'];
    $arr = unserialize($arr);
    $content = '';

    if (in_array($id, $arr, TRUE)) {
        $data = mysqli_query($sqlConnect, "SELECT * FROM " .  T_USERS . " WHERE `user_id` = '" . $id . "'");
        $fetched_user = mysqli_fetch_assoc($data);
        $content .= '<img class="shotimgonlistHeader"  src="' . $url . $fetched_user['avatar'] . '">';
        $content .= '<div>';
        $content .= '<h2 class="onlistName">' . $fetched_user['username'] . '</h2>';
        $content .= '</div>';
    }

    return $content;
}


function AddMessage($toid, $msg)
{
    global $sqlConnect;
    $mYiD = GetUserSession();

    if (empty($toid) || empty($msg)) {
        return false;
    }

    $query = mysqli_query($sqlConnect, "SELECT user_id FROM " .  T_USERS . " WHERE `user_id` = '" . $toid . "'");
    $fetched_data = mysqli_fetch_assoc($query);

    if (empty($fetched_data['user_id'])) {
        return false;
    }

    if ($mYiD == $toid) {
        return false;
    }

    $fields = $fields = 'from_id,to_id,msg,date';
    $now = date("Y-m-d H:i:s");

    $query = mysqli_query($sqlConnect, "INSERT INTO " . T_MSG . " ({$fields}) VALUES ('{$mYiD}','{$toid}','{$msg}','{$now}')") or die(mysqli_error($sqlConnect));

    return true;
}


function LoadChat($id)
{


    global $sqlConnect;
    $MyID = GetUserSession();
    $query = mysqli_query($sqlConnect, "SELECT relation_arr FROM " .  T_FOLLOWS . " WHERE `user_id` = '" . $MyID . "'");
    $fetched_data = mysqli_fetch_assoc($query);

    $arr = $fetched_data['relation_arr'];
    $arr = unserialize($arr);
    $content = '';

    if (in_array($id, $arr, TRUE)) {

        $query = mysqli_query($sqlConnect, "SELECT * FROM " .  T_MSG . " WHERE `from_id` = '" . $MyID . "' and `to_id` = '" . $id . "' OR  `from_id` = '" . $id . "' and `to_id` = '" . $MyID . "'");

        while ($v = mysqli_fetch_assoc($query)) {
            if ($v['from_id'] == $MyID) {
                $content .= '<li class="me">';
                $content .= '<div class="entete">';
                $content .= '<h3>' . $v['date'] . '</h3>';
                $content .= '<span class="status blue"</span>';
                $content .= '</div>';
                $content .= '<div class="triangle"></div>';
                $content .= '<div class="message">';
                $content .= $v['msg'];
                $content .= '</div>';
                $content .= '</li>';
            } else {
                $content .= '<li class="you">';
                $content .= '<div class="entete">';
                $content .= '<h3>' . $v['date'] . '</h3>';
                $content .= '<span class="status green"</span>';
                $content .= '</div>';
                $content .= '<div class="triangle"></div>';
                $content .= '<div class="message">';
                $content .= $v['msg'];
                $content .= '</div>';
                $content .= '</li>';
            }
        }
    }

    return $content;
}
