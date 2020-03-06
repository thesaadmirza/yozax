<?php
$pages  = array('news','polls','videos','lists','music','quiz');

if (empty($_GET['id']) || empty($_GET['post_type'])) {
    $e404 = YX_LoadPage("api/markup/html/404/content");
    echo $e404;
    exit();
}

else if (!in_array($_GET['post_type'], $pages)) {
	$e404 = YX_LoadPage("api/markup/html/404/content");
    echo $e404;
    exit();
}

$page        = 1;
$post_type   = YX_Secure($_GET['post_type']);
$post_id     = @end(explode('-', $_GET['id']));
$post_data   = YX_GetPost($post_id, $page, $post_type);

if (empty($post_data)) {
    $e404 = YX_LoadPage("api/markup/html/404/content");
    echo $e404;
    exit();
}


$yx['content']  = YX_LoadPage("api/markup/html/story/content", array(
    'STORY_ENTRIES' => YX_ForEachEntries($post_data['entries']),
    'STORY_AD' => YX_GetAd('between', false)
));

$yx['title']               = $post_data['title'];
$yx['keywords']               = '';
$yx['description']         = $post_data['description'];
$yx['page']                = 'post_data';