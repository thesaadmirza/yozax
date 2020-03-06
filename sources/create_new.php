<?php
if ($yx['loggedin'] == false || (!YX_IsAdmin() && $yx['config']['can_post'] != 1)) {
    header("Location:" . $site_url);
    exit();
}

else if($yx['config']['go_pro'] == 1 && !YX_IsAdmin() && !YX_IsPRO() && $yx['user']['posts'] >= $yx['config']['user_max_posts']){
    header("Location:" . YX_Link('go_pro'));
    exit();
}

$yx['title']          = $lang['create_new'] . ' | ' . $yx['config']['title'];
$yx['description']    = $yx['config']['description'];
$yx['page']           = 'create_new';
$yx['keywords']       = $yx['config']['keywords'];
$type                 = 'news';
$allowed_create_pages = array(
    'news',
    'video',
    'list',
    'music',
    'poll',
    'quiz'
);
if (!empty($_GET['type'])) {
    $_GET['type'] = YX_Secure($_GET['type']);
    if (in_array($_GET['type'], $allowed_create_pages)) {
        $type = $_GET['type'];
    }
    else{
        header("Location:" . $site_url);
        exit();
    }
}
if (in_array($yx['page'], $yx['pages_home_array'])) {
    $yx['page_home'] = true;
}
$time              = time() . rand(1111, 9999);
$create_session    = YX_CreateSession();
$include_type      = '';
$include_entry     = 'text';
$allow_create_post = true;
if ($type == 'news') {
    $include_type      = 'news';
    if($yx['config']['news'] == 0) $allow_create_post = false;
}

if ($type == 'list') {
    $include_type    = 'list';
    if($yx['config']['lists'] == 0) $allow_create_post = false;
}
if ($type == 'poll') {
    $include_type    = 'poll';
    if($yx['config']['polls'] == 0) $allow_create_post = false;
}
if ($type == 'quiz') {
    $include_type    = 'quiz';
    if($yx['config']['quizzes'] == 0) $allow_create_post = false;
}
if ($type == 'video') {
    $include_type    = 'video';
    if($yx['config']['videos'] == 0) $allow_create_post = false;
}
if ($type == 'music') {
    $include_type    = 'music';
    if($yx['config']['music'] == 0) $allow_create_post = false;
}

if (!$allow_create_post) {
    header("Location:" . $site_url);
    exit();
}

$categories_string = $type . '_' . 'categories';
$categories = $yx[$categories_string];
$category_html = '';
foreach ($categories as $key => $category) {
    $category_html .= '<option value=" ' . $key . ' "> ' . $category . ' </option>';
}

$entry_type = '';
$page_type  = '';
switch ($include_type) {
    case 'video':
        $entry_type = YX_LoadPage("create-new/entries/video", array(
           'ENTRY_TIME' => $time,

        ));
        $page_type = YX_LoadPage('create-new/add-entries');
        break;
    case 'music':
        $entry_type = YX_LoadPage("create-new/entries/soundcloud", array(
           'ENTRY_TIME' => $time
        ));
        $page_type = YX_LoadPage('create-new/add-entries');
        break;
    case 'poll':
        $entry_type = YX_LoadPage("create-new/entries/options", array(
           'ENTRY_TIME' => $time
        ));
        $page_type = YX_LoadPage('create-new/add-entries');
        break;
    case 'quiz':
        $entry_type = YX_LoadPage("create-new/entries/quiz", array(
           'ENTRY_TIME' => $time,
           'QUESTION'   => YX_LoadPage("create-new/entries/question",array(
                'ENTRY_TIME' => time() . rand(1111, 9999),
            ))
        ));
        break;
    default:
        $entry_type = YX_LoadPage("create-new/entries/text", array(
           'ENTRY_TIME' => $time
        ));
        $page_type = YX_LoadPage('create-new/add-entries');
        break;
}
$yx['content']  = YX_LoadPage("create-new/content", array(
    'ENTRY_TIME' => $time,
    'NEW_SESSION_HASH' => $create_session,
    'NEW_ENTRY' => $entry_type,
    'PAGE_TYPE' => $include_type,
    'ADD_ENTRIES' => $page_type,
    'Categories' => $category_html
));