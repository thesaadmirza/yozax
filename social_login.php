<?php
// @author Saad Mirza https://saadmirza.net
require_once('assets/init.php');

$types = array(
    'Google',
    'Facebook',
    'Twitter',
    'Vkontakte'
);

if (isset($_GET['provider']) && in_array($_GET['provider'], $types)) {
    $provider = YX_Secure($_GET['provider']);
}


require_once('./assets/import/social-login/config.php');
require_once('./assets/import/social-login/autoload.php');

use Hybridauth\Hybridauth;
use Hybridauth\HttpClient;

if (isset($_GET['provider']) && in_array($_GET['provider'], $types)) {
    try {
        $hybridauth   = new Hybridauth($LoginWithConfig);
        $authProvider = $hybridauth->authenticate($provider);
        $tokens = $authProvider->getAccessToken();
        $user_profile = $authProvider->getUserProfile();
        if ($user_profile && isset($user_profile->identifier)) {
            $name = $user_profile->firstName;
            if ($provider == 'Google') {
                $notfound_email     = 'go_';
                $notfound_email_com = '@google.com';
            } else if ($provider == 'Facebook') {
                $notfound_email     = 'fa_';
                $notfound_email_com = '@facebook.com';
            } else if ($provider == 'Twitter') {
                $notfound_email     = 'tw_';
                $notfound_email_com = '@twitter.com';
            } else if ($provider == 'Vkontakte') {
                $notfound_email     = 'vk_';
                $notfound_email_com = '@vkontakte.com';
            }
            $user_name  = $notfound_email . $user_profile->identifier;
            $user_email = $user_name . $notfound_email_com;
            if (!empty($user_profile->email)) {
                $user_email = $user_profile->email;
            }
            if (YX_EmailExists($user_email) === true) {
                $user_id = YX_UserIdForLogin($user_email);
                $session = YX_CreateLoginSession($user_id);
                $_SESSION['user_id'] = $session;
                setcookie(
                    "user_id",
                    $session,
                    time() + (10 * 365 * 24 * 60 * 60)
                );
                header("Location: " . $config['site_url']);
                exit();
            } else {
                $str          = md5(microtime());
                $id           = substr($str, 0, 9);
                $user_uniq_id = (YX_UserExists($id) === false) ? $id : 'u_' . $id;
                $social_url   = substr($user_profile->profileURL, strrpos($user_profile->profileURL, '/') + 1);
                $image = YX_ImportImageFromLogin($user_profile->photoURL);
                $re_data      = array(
                    'username' => YX_Secure($user_uniq_id, 0),
                    'email' => YX_Secure($user_email, 0),
                    'password' => YX_Secure($user_email, 0),
                    'email_code' => YX_Secure(md5($user_uniq_id), 0),
                    'first_name' => YX_Secure($name),
                    'last_name' => YX_Secure($user_profile->lastName),
                    'avatar' => YX_Secure($image),
                    'src' => YX_Secure($provider),
                    'active' => '1'
                );
                if ($yx['config']['amazone_s3'] == 1) {
                    $upload = YX_UploadToS3($image);
                }
                if ($provider == 'Google') {
                    $re_data['about']  = YX_Secure($user_profile->description);
                    $re_data['google'] = YX_Secure($social_url);
                }
                if ($provider == 'Facebook') {
                    $fa_social_url       = @explode('/', $user_profile->profileURL);
                    $re_data['facebook'] = YX_Secure($fa_social_url[4]);
                    $re_data['gender'] = 'male';
                    if (!empty($user_profile->gender)) {
                        if ($user_profile->gender == 'male') {
                            $re_data['gender'] = 'male';
                        } else if ($user_profile->gender == 'female') {
                            $re_data['gender'] = 'female';
                        }
                    }
                }
                if ($provider == 'Twitter') {
                    $re_data['twitter'] = YX_Secure($social_url);
                }
                if (YX_RegisterUser($re_data) === true) {
                    $user_id = YX_UserIdForLogin($user_email);
                    $session = YX_CreateLoginSession($user_id);
                    $_SESSION['user_id'] = $session;
                    setcookie(
                        "user_id",
                        $session,
                        time() + (10 * 365 * 24 * 60 * 60)
                    );
                    /*$wo['user'] = $re_data;
                    $body       = YX_LoadPage('emails/login-with');
                    $headers    = "From: " . $config['siteName'] . " <" . $config['siteEmail'] . ">\r\n";
                    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                    @mail($re_data['email'], 'Thank you for your registration.', $body, $headers);*/
                    header("Location: " . YX_Link(''));
                    exit('Done');
                }
            }
        }
    } catch (Exception $e) {
        switch ($e->getCode()) {
            case 0:
                echo "Unspecified error.";
                break;
            case 1:
                echo "Hybridauth configuration error.";
                break;
            case 2:
                echo "Provider not properly configured.";
                break;
            case 3:
                echo "Unknown or disabled provider.";
                break;
            case 4:
                echo "Missing provider application credentials.";
                break;
            case 5:
                echo "Authentication failed The user has canceled the authentication or the provider refused the connection.";
                break;
            case 6:
                echo "User profile request failed. Most likely the user is not connected to the provider and he should to authenticate again.";
                break;
            case 7:
                echo "User not connected to the provider.";
                break;
            case 8:
                echo "Provider does not support this feature.";
                break;
        }
        echo " an error found while processing your request!";
        echo " <b><a href='" . YX_Link('') . "'>Try again<a></b>";
    }
} else {
    header("Location: " . YX_Link(''));
    exit();
}
