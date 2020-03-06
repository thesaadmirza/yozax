<?php
if ($yx['loggedin'] == true) {
	header("Location:" . $site_url);
	exit();
}
$yx['page'] = 'forgot_password';
$yx['title'] = $lang['forgot_password'] . ' | ' . $yx['config']['title'];
$yx['description'] = $yx['config']['description'];
$yx['keywords'] = $yx['config']['keywords'];

$yx['content']  = YX_LoadPage('forgot_password/content', array(
    'LOGIN_LINK' => YX_Link('login'),
));