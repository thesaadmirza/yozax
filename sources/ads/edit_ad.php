<?php 

if ($yx['loggedin'] == false || empty($_GET['ad_id']) || $yx['config']['usr_ads'] != 1) {
	header("Location:" . YX_Link(''));
	exit();
}

$id = YX_Secure($_GET['ad_id']);
$db = $db->where('user_id',$yx['user']['id']);
$ad = $db->where('id',$id)->getOne(T_USER_ADS);

if (empty($ad)) {
	header("Location:" . YX_Link('404'));
	exit();
}


$yx['title']       = 'Edit Ads  | ' . $yx['config']['title'];
$yx['description'] = $yx['config']['description'];
$yx['keywords']    = $yx['config']['keywords'];
$yx['page']        = 'user_ads';
$image             = explode('/', $ad->media_file);

$yx['content']     = YX_LoadPage('ads/edit-ad',array(
	'ID'           => $ad->id,
	'NAME'         => $ad->title,
	'URL'          => $ad->url,
	'PLM1'         => (($ad->placement == 1) ? 'selected' : ''),
	'PLM2'         => (($ad->placement == 2) ? 'selected' : ''),
	'PLM3'         => (($ad->placement == 3) ? 'selected' : ''),
	'STAT1'        => (($ad->status == 1) ? 'selected' : ''),
	'STAT2'        => (($ad->status == 2) ? 'selected' : ''),
	'IMG_NAME'     => end($image),
));