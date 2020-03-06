<?php
if ($yx['loggedin'] == false || $yx['config']['usr_ads'] != 1) {
	header("Location:" . YX_Link(''));
	exit();
}

$yx['title']       = 'Ads  | ' . $yx['config']['title'];
$yx['description'] = $yx['config']['description'];
$yx['keywords']    = $yx['config']['keywords'];
$yx['page']        = 'user_ads';

$user_ads          = $db->where("user_id",$yx['user']['id'])->get(T_USER_ADS);
$user_ads_html     = "";
$ad_index          = 1;

foreach ($user_ads as $yx['user_ad']) {
	$yx['user_ad']  = YX_ObjectToArray($yx['user_ad']);
	$stat           = $yx['lang']['active'];

	if ($yx['user_ad']['status'] == 2) {
		$stat       = $yx['lang']['inactive'];
	}

	$user_ads_html .= YX_LoadPage('ads/includes/list',array(
		'ID' => $yx['user_ad']['id'],
		'STATUS' => $stat,
		'INDEX' => $ad_index,
		'NAME' => $yx['user_ad']['title'],
		'NAME' => $yx['user_ad']['title'],
		'RESULTS' => $yx['user_ad']['results'],
		'SPENT' => $yx['user_ad']['spent'],
		'TIME' => date('n M, Y h:m A',$yx['user_ad']['time']),
		'EDIT' => YX_Link(sprintf('ads/edit/%u/',$yx['user_ad']['id'])),
	));

	$ad_index = ($ad_index + 1);
}

$yx['content'] = YX_LoadPage('ads/content',array(
	'USER_ADS' => $user_ads_html
));