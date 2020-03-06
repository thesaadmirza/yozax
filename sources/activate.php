<?php
$yx['title'] = $config['title'];
$yx['description'] = $yx['config']['description'];
$yx['keywords'] = $yx['config']['keywords'];
$yx['page'] = 'activate';
if ($yx['loggedin'] == true) {
	header("Location: " . $config['site_url']);
	exit();
}
if (empty($_GET['email']) || empty($_GET['code'])) {
	header("Location: " . $config['site_url']);
	exit();
}
$user_id = YX_UserIdFromEmail($_GET['email']);
if (empty($user_id)) {
	header("Location: " . $config['site_url']);
	exit();
}
$user_data = YX_UserData($user_id);
if (empty($user_data)) {
	header("Location: " . $config['site_url']);
	exit();
}
if (YX_UserActive($user_data['username'])) {
	header("Location: " . $config['site_url']);
	exit();
}
if (YX_EmailCode($_GET['email'], $_GET['code']) === false) {
	header("Location: " . $config['site_url']);
	exit();
}
$activate_account = YX_ActivateAccount($user_data['user_id'], $user_data['username']);
if ($activate_account) {
	$_SESSION['user_id'] = $user_data['user_id'];
	$yx['content'] = YX_LoadPage('activate/done');
} else {
	$yx['content'] = YX_LoadPage('activate/error');
}

