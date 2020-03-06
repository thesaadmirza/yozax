<?php
if ($yx['loggedin'] == true) {
	header("Location:" . $site_url);
	exit();
}
$yx['title']           = $lang['create_new_account'] . ' | ' . $yx['config']['title'];
$yx['description']     = $yx['config']['description'];
$yx['keywords']        = $yx['config']['keywords'];
$yx['page']            = 'register'; 
$yx['profile_fields']  = YX_GetProfileFields('registration');
$yx['content']         = YX_LoadPage('register/content', array('CREATE_SESSION' => YX_CreateSession()));