<?php
if ($yx['loggedin'] == false) {
	header("Location:" . $site_url);
	exit();
}

$yx['page'] = 'notifications';
$yx['title'] = $lang['notifications'] . ' | ' . $yx['config']['title'];
$yx['description'] = $yx['config']['description'];
$yx['keywords'] = $yx['config']['keywords'];

$uid = YX_GetUserFromSessionID($_SESSION['user_id']);
 
$userData = YX_UserFollowers($uid);  
$followers_array = @unserialize($userData);
$followerInfo = [];
foreach ($followers_array as $key => $value) {
    $followerInfo[] = YX_UserData($value);
    
}
$yx['followerInfo'] = $followerInfo;
//echo '<pre>';
//print_r($followerInfo);exit;
$yx['content'] = YX_LoadPage('notifications/content');