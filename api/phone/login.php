<?php
// @author Saad Mirza https://saadmirza.net

/* 

   http://www.site.com/app_api.php?type=user_login
   POST
       1. username  = < username >
       2. password  = < user password >
       3. s         = < session >


   ERROR CODES: 
       1. Bad request, no type specified.
       2. Please write your username.
       3. Please write your password.
       4. No session sent.
       5. Username is not exists.
       6. Incorrect username or password.

   JSON REPLY:
       {
        'api_status'     => '200',
        'api_text'       => 'success',
        'api_version'    => 'api_version',
        'user_id'        => 'loggedin_user_id',
        'messages'       => [array (respond_message_1,respond_message_2, ...) ],
        'timezone'       => user_timezone,
        'cookie'         => session
        
       }


*/

$json_error_data   = array();
$json_success_data = array();
if (empty($_GET['type']) || !isset($_GET['type'])) {
    $json_error_data = array(
        'api_status' => '400',
        'api_text' => 'failed',
        'api_version' => $api_version,
        'errors' => array(
            'error_id' => '1',
            'error_text' => 'Bad request, no type specified.'
        )
    );
    header("Content-type: application/json");
    echo json_encode($json_error_data, JSON_PRETTY_PRINT);
    exit();
}
$type = YX_Secure($_GET['type'], 0);
if ($type == 'user_login') {
    if (empty($_POST['username'])) {
        $json_error_data = array(
            'api_status' => '400',
            'api_text' => 'failed',
            'api_version' => $api_version,
            'errors' => array(
                'error_id' => '2',
                'error_text' => 'Please write your username.'
            )
        );
    } else if (empty($_POST['password'])) {
        $json_error_data = array(
            'api_status' => '400',
            'api_text' => 'failed',
            'api_version' => $api_version,
            'errors' => array(
                'error_id' => '3',
                'error_text' => 'Please write your password.'
            )
        );
    } else if (empty($_POST['s'])) {
        $json_error_data = array(
            'api_status' => '400',
            'api_text' => 'failed',
            'api_version' => $api_version,
            'errors' => array(
                'error_id' => '4',
                'error_text' => 'No session sent.'
            )
        );
    }
    if (empty($json_error_data)) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $user_id  = YX_UserIdFromUsername($username);
        if (empty($user_id)) {
            $user_id = YX_UserIdFromEmail($username);
        }
        $s               = md5(YX_Secure($_POST['s']));
        $user_login_data = YX_UserData($user_id);
        if (empty($user_login_data)) {
            $json_error_data = array(
                'api_status' => '400',
                'api_text' => 'failed',
                'api_version' => $api_version,
                'errors' => array(
                    'error_id' => '5',
                    'error_text' => 'Username is not exists.'
                )
            );
            header("Content-type: application/json");
            echo json_encode($json_error_data, JSON_PRETTY_PRINT);
            exit();
        } else {
            $login = YX_Login($username, $password);
            if (!$login) {
                $json_error_data = array(
                    'api_status' => '400',
                    'api_text' => 'failed',
                    'api_version' => $api_version,
                    'errors' => array(
                        'error_id' => '6',
                        'error_text' => 'Incorrect username or password.'
                    )
                );
                header("Content-type: application/json");
                echo json_encode($json_error_data, JSON_PRETTY_PRINT);
                exit();
            } else {

                $time        = time();
                $platform_info = $_SERVER['HTTP_USER_AGENT'];
                $add_session = mysqli_query($sqlConnect, "INSERT INTO " . T_APP_SESSIONS . " (`user_id`, `session_id`, `platform`,`platform_info`, `time`) VALUES ('{$user_id}', '{$s}', 'phone', '{$platform_info}','{$time}')");

                if ($add_session) {
                    $session = '';
                    if (!empty($_POST['timezone'])) {
                        $timezone = YX_Secure($_POST['timezone']);
                    } else {
                        $timezone = 'UTC';
                    }
                    $add_timezone = mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET `timezone` = '{$timezone}' WHERE `user_id` = {$user_id}");
                    if (!empty($_GET['cookie'])) {
                        $userid  = YX_UserIdForLogin($username);
                        $ip      = YX_Secure(get_ip_address());
                        $update  = mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET `ip_address` = '{$ip}' WHERE `user_id` = '{$userid}'");
                        $session = YX_CreateLoginSession(YX_UserIdForLogin($username));
                    }
                    if (!empty($_POST['device_id'])) {
                        $userid    = YX_UserIdForLogin($username);
                        $device_id = YX_Secure($_POST['device_id']);
                        $update    = mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET `device_id` = '{$device_id}' WHERE `user_id` = '{$userid}'");
                    }
                    

                    $json_success_data = array(
                        'api_status' => '200',
                        'api_text' => 'success',
                        'api_version' => $api_version,
                        'user_id' => YX_UserIdFromUsername($username),
                        'messages' => array(
                            'respond_message_1' => 'Successfully logged in, Please wait..'
                        ),
                        'timezone' => $timezone,
                        'cookie' => $session,
                        'session' => $s
                    );
                    header("Content-type: application/json");
                    echo json_encode($json_success_data, JSON_PRETTY_PRINT);
                    exit();
                } else {
                    $json_error_data = array(
                        'api_status' => '400',
                        'api_text' => 'failed',
                        'api_version' => $api_version,
                        'errors' => array(
                            'error_id' => '7',
                            'error_text' => 'Error found, please try again later.'
                        )
                    );
                    header("Content-type: application/json");
                    echo json_encode($json_error_data, JSON_PRETTY_PRINT);
                    exit();
                }
            }
        }
    } else {
        header("Content-type: application/json");
        echo json_encode($json_error_data, JSON_PRETTY_PRINT);
        exit();
    }
}
header("Content-type: application/json");
echo json_encode($json_success_data);
exit();
