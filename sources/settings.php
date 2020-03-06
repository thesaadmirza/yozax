<?php
if ($yx['loggedin'] == false) {
	header("Location:" . $site_url);
	exit();
}

$user_id            = $yx['user']['user_id'];

$yx['is_admin']     = YX_IsAdmin();
$yx['setting']['admin'] = false;
if (isset($_GET['user']) && !empty($_GET['user']) && ($yx['is_admin'] === true)) {
    if (YX_UserExists($_GET['user']) === false) {
        header("Location: " . $yx['config']['site_url']);
        exit();
    }
    $user_id                = YX_UserIdFromUsername($_GET['user']);
    $yx['setting']['admin'] = true;
}
$yx['setting'] = YX_UserData($user_id);
$yx['setting_page'] = 'general';
$pages_array = array(
	   'general',
	   'profile',
	   'password',
	   'privacy',
	   'change',
	   'social',
	   'avatar',
	   'email',
	   'wallet',
	   'verification',
	   'delete'
);
if ($yx['setting']['user_id'] == $yx['user']['user_id']) {
  $pages_array = array(
	   'general',
	   'profile',
	   'password',
	   'privacy',
	   'change',
	   'social',
	   'avatar',
	   'email',
	   'wallet',
	   'verification',
	   'delete'
   );
}

if (!empty($_GET['page'])) {
   if (in_array($_GET['page'], $pages_array)) {
      $yx['setting_page'] = $_GET['page'];
   }
}
$yx['user_setting'] = '';
if (!empty($_GET['user'])) {
    $yx['user_setting'] = 'user=' . $_GET['user']. '&';
}
$COUNTRIES_LAYOUT = '';
foreach ($yx['countries_name'] as $key => $value) {
	$selected = ($key == $yx['setting']['country_id']) ? 'selected' : '';
	$COUNTRIES_LAYOUT .= '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
}

if (!empty($_SESSION['refilled_balance'])) {
	$yx['refilled_balance'] = $_SESSION['refilled_balance'];
	unset($_SESSION['refilled_balance']);
}

$yx['profile_fields']     = null;

$yx['user']['fields']     = YX_UserFieldsData($yx['user']['user_id']);

if ($yx['setting_page'] == 'general') {
	$yx['profile_fields'] = YX_GetProfileFields('general');
}

elseif ($yx['setting_page'] == 'profile') {
	$yx['profile_fields'] = YX_GetProfileFields('profile');
}



$yx['content'] = YX_LoadPage('settings/content', array( 
    'SETTINGS_PAGE' => YX_LoadPage("settings/" . $yx['setting_page'], array(
    	'USER_DATA' => $yx['setting'], 
    	'COUNTRIES_LAYOUT' => $COUNTRIES_LAYOUT, 
    	'ADMIN_LAYOUT' => YX_LoadPage('settings/admin', array('USER_DATA' => $yx['setting']))))
));



$yx['title'] = $lang['settings'] . ' | ' . $yx['config']['title'];
$yx['description'] = $yx['config']['description'];
$yx['page'] = 'settings';
$yx['keywords'] = $yx['config']['keywords'];