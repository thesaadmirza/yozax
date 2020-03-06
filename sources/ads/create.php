<?php
if ($yx['loggedin'] == false || $yx['config']['usr_ads'] != 1) {
	header("Location:" . YX_Link(''));
	exit();
}

$yx['title']       = 'Create Ads  | ' . $yx['config']['title'];
$yx['description'] = $yx['config']['description'];
$yx['keywords']    = $yx['config']['keywords'];
$yx['page']        = 'user_ads';

$yx['content']     = YX_LoadPage('ads/create-new',array(
	
));