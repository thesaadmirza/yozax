<?php
if ($yx['loggedin'] == false) {
	header("Location:" . $site_url);
	exit();
}

$yx['page'] = 'music-library';
$yx['title'] = $lang['music_library'] . ' | ' . $yx['config']['title'];
$yx['description'] = $yx['config']['description'];
$yx['keywords'] = $yx['config']['keywords'];

$yx['content'] = YX_LoadPage('music-library/content');