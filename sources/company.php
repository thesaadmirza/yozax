<?php
if (empty($_GET['type']) || !isset($_GET['type'])) {
	header("Location: " . $yx['config']['site_url']);
	exit();
}
$pages = array('terms','privacy-policy','about-us', 'developers');
if (!in_array($_GET['type'], $pages)) {
	eader("Location: " . $yx['config']['site_url']);
	exit();
}
$yx['terms'] = YX_GetTerms();

$yx['description'] = $yx['config']['description'];
$yx['keywords']    = $yx['config']['keywords'];
$yx['page']        = 'terms';
$yx['title']       = '';

$type = YX_Secure($_GET['type']);
if ($type == 'company') {
	$yx['title']  = $lang['terms_of_use'];
} else if ($type == 'about-us') {
    $yx['title']  = $lang['about_us'];
} else if ($type == 'privacy-policy') {
    $yx['title']  = $lang['privacy_policy'];
} 
$page = 'company/' . $type;

$yx['type'] = $type;
$yx['title'] = $yx['config']['name'] . ' | ' . $yx['title'];
$yx['content']     = YX_LoadPage($page);