<?php
if ($yx['loggedin'] == false || (!YX_IsAdmin() && $yx['config']['can_post'] != 1)) {
	header("Location:" . YX_Link(''));
	exit();
}
if (empty($_GET['id'])) {
	header("Location:" . YX_Link(''));
	exit();
}
if (empty($_GET['type'])) {
	header("Location:" . YX_Link(''));
	exit();
}
$types = array('news', 'lists', 'music', 'videos', 'polls','quiz');
if (!in_array($_GET['type'], $types)) {
	header("Location:" . YX_Link(''));
	exit();
}
$type = YX_Secure($_GET['type']);
$id = YX_Secure($_GET['id']);
$is_post_owner = YX_IsPostOwner($id, $type);

if ($is_post_owner === false) {
	header("Location:" . YX_Link(''));
	exit();
}
$yx['delete_data'] = array();
$yx['delete_data'] = YX_GetPost($id, 0, $type);
if (empty($yx['delete_data'])) {
	header("Location:" . YX_Link(''));
	exit();
}
$yx['delete_data']['type'] = $type;


$yx['title'] = $lang['delete_post'] . ' | ' . $yx['config']['title'];
$yx['description'] = $yx['config']['description'];
$yx['page'] = 'delete-post';
$yx['keywords'] = $yx['config']['keywords'];

$time           = time() . rand(1111, 9999);
$create_session = YX_CreateSession();

$yx['content']  = YX_LoadPage("delete-post/content", array(
    'NEW_SESSION_HASH' => $create_session,
    'DELETE_DATA__TITLE' => $yx['delete_data']['title'],
    'DELETE_DATA__ID' => $yx['delete_data']['id'],
    'DELETE_DATA__TYPE' => $yx['delete_data']['type'],
    'DELETE_DATA__URL' => $yx['delete_data']['url'],
    'SIDEBAR_HTML' => YX_LoadPage('sidebar/content', array())
));