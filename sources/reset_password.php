<?php
if ($yx['loggedin'] == true || empty($_GET['code'])) {
	header("Location:" . $site_url);
	exit();
}
$check_code = YX_PasswordResetCode($_GET['code']);
if ($check_code) {
	$yx['content'] = YX_LoadPage('reset_password/content');
} else {
	$yx['content'] = YX_LoadPage('reset_password/error');
}
$yx['page'] = 'reset_password';
$yx['title'] = $lang['reset_your_password'] . ' | ' . $yx['config']['title'];
$yx['description'] = $yx['config']['description'];
$yx['keywords'] = $yx['config']['keywords'];
