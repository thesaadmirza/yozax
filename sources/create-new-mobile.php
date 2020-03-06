<?php
if ($yx['loggedin'] == false) {
	header("Location:" . $site_url);
	exit();
}

$yx['page'] = 'create-new-mobile';
$yx['title'] = $yx['config']['title'] . ' | ' . $lang['reset_your_password'];
$yx['description'] = $yx['config']['description'];
$yx['keywords'] = $yx['config']['keywords'];
$yx['content'] = YX_LoadPage('create-new/mobile');
