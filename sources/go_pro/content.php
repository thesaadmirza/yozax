<?php
if ($yx['loggedin'] == false || empty($yx['config']['go_pro']) || (YX_IsPRO() && empty($_SESSION['upgraded']))) {
	header("Location:" . YX_Link(''));
	exit();
}

$yx['title']       = 'Go Pro | ' . $yx['config']['title'];
$yx['description'] = $yx['config']['description'];
$yx['keywords']    = $yx['config']['keywords'];
$yx['page']        = 'go_pro';
$yx['content']     = YX_LoadPage('go_pro/content',array(

));
