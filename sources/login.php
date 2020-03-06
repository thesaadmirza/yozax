<?php
if ($yx['loggedin'] == true) {
	header("Location:" . $site_url);
	exit();
}
$yx['title'] = $lang['login']  . ' | ' . $yx['config']['title'];
$yx['description'] = $yx['config']['description'];
$yx['page'] = 'login';
$yx['keywords'] = $yx['config']['keywords'];
$yx['content'] = YX_LoadPage('login/content');