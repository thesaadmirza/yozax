<?php

if ($yx['loggedin'] == false || (!YX_IsAdmin() && $yx['config']['can_post'] != 1)) {
    header("Location:" . $site_url);
    exit();
}

if ($yx['loggedin'] == false) {
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
$types = array(
    'news',
    'lists',
    'music',
    'videos',
    'polls',
    'quiz',
);
if (!in_array($_GET['type'], $types)) {
    header("Location:" . YX_Link(''));
    exit();
}
$type          = YX_Secure($_GET['type']);
$id            = YX_Secure($_GET['id']);
$is_post_owner = YX_IsPostOwner($id, $type);
if ($is_post_owner === false) {
    header("Location:" . YX_Link(''));
    exit();
}
$yx['post_data'] = array();
if ($type == 'news') {
    $include_type    = 'news';
}
if ($type == 'lists') {
    $include_type    = 'list';
}
if ($type == 'polls') {
    $include_type    = 'poll';
}
if ($type == 'videos') {
    $include_type    = 'video';
}
if ($type == 'music') {
    $include_type    = 'music';
}

if ($type == 'quiz') {
    $include_type    = 'quiz';
}

$yx['post_data'] = YX_GetEditPost($id, 0, $type);
if (empty($yx['post_data'])) {
    header("Location:" . YX_Link(''));
    exit();
}



$yx['post_data']['type'] = $type;
$yx['post_data']['type'] = $type;

$yx['title']       = $lang['edit_post'] . ' | ' . $yx['config']['title'];
$yx['description'] = $yx['config']['description'];
$yx['page']        = 'edit-post';
$yx['keywords']    = $yx['config']['keywords'];
$entries           = '';

$categories_string = $include_type . '_' . 'categories';
$categories = $yx[$categories_string];
$category_html = '';
foreach ($categories as $key => $category) {
    $selected = ($key == $yx['post_data']['category']) ? 'selected' : '';
    $category_html .= '<option value=" ' . $key . ' " ' . $selected . '> ' . $category . ' </option>';
}

if ($type == 'quiz') {
    $quiz_resilts = YX_GetQuizResults($id);
    if (empty($quiz_resilts)) {
        header("Location:" . YX_Link(''));
        exit();
    }

    $yx['post_data']['entries'] = array_merge($quiz_resilts,$yx['post_data']['entries']);
    $resilts   = "";
    $questions = "";

    foreach ($yx['post_data']['entries'] as $yx['key'] => $yx['entry']) {
        $entry_type = $yx['entry']['entry_type'];
        if ($entry_type == "result") {
            $values_to_change = array(
                'ENTRY_ID'        => $yx['entry']['id'],
                'ENTRY_TITLE'     => $yx['entry']['title'],
                'ENTRY_TEXT'      => $yx['entry']['text'],
                'ENTRY_IMAGE'     => YX_GetMedia($yx['entry']['image']),
            );
            $resilts .= YX_LoadPage("edit-post/entries/{$entry_type}", $values_to_change);
        }
        else if($entry_type == "question"){
            $values_to_change = array(
                'ENTRY_ID'        => $yx['entry']['id'],
                'ENTRY_TITLE'     => $yx['entry']['title'],
                'ENTRY_TEXT'      => $yx['entry']['text'],
                'ENTRY_IMAGE'     => $yx['entry']['image'],
            );
            $questions .= YX_LoadPage("edit-post/entries/{$entry_type}", $values_to_change);
        }
    }

    $entries = YX_LoadPage("edit-post/entries/quiz", array(
        'QUIZ_RESULTS'   => $resilts,
        'QUIZ_QUESTIONS' => $questions
    ));

}
else{

    foreach ($yx['post_data']['entries'] as $yx['key'] => $yx['entry']) {
        $entry_type = $yx['entry']['entry_type'];

        if (!empty($entry_type)) {
            $video_html               = '';
            $video_src                = '';
            $yx['entry']['poll_type'] = null;
            if (!empty($yx['entry']['poll_answers'][0])) {
                $yx['entry']['poll_type'] = $yx['entry']['poll_answers'][0]['type'];
            }
            if ($yx['entry']['entry_type'] == 'video') {
                $entry_content = 'entries/video';
                if ($yx['entry']['video_type'] == 'youtube') {
                    $html = YX_LoadPage('iframe/youtube');
                } else if ($yx['entry']['video_type'] == 'vine') {
                    $html = YX_LoadPage('iframe/vine');
                } else if ($yx['entry']['video_type'] == 'vimeo') {
                    $html = YX_LoadPage('iframe/vimeo');
                } else if ($yx['entry']['video_type'] == 'dailymotion') {
                    $html = YX_LoadPage('iframe/dailymotion');
                } else if ($yx['entry']['video_type'] == 'facebook') {
                    $html = YX_LoadPage('iframe/facebook');
                } else if ($yx['entry']['video_type'] == 'source') {
                    $html = YX_LoadPage('players/video',array(
                        'VIDEO_SRC' => YX_GetMedia($yx['entry']['video_url'])
                    ));
                    $video_src                = $yx['entry']['video_url'];
                    $_SESSION['uploads'][]    = $video_src;
                    $yx['entry']['video_url'] = '';

                }
                if (!empty($html)) {
                    $video_html = str_replace('{video_id}', $yx['entry']['video'], $html);
                }
            }
            $values_to_change = array(
                'ENTRY_ID' => $yx['entry']['id'],
                'ENTRY_TITLE' => $yx['entry']['title'],
                'ENTRY_TEXT' => $yx['entry']['text'],
                'ENTRY_IMAGE' => $yx['entry']['image'],
                'ENTRY_TWEET_URL' => $yx['entry']['tweet_url'],
                'ENTRY_TWEET' => html_entity_decode($yx['entry']['tweet']),
                'ENTRY_VIDEO' => $yx['entry']['video'],
                'ENTRY_VIDEO_TYPE' => $yx['entry']['video_type'],
                'ENTRY_VIDEO_URL' => $yx['entry']['video_url'],
                'ENTRY_VIDEO_SRC' => $video_src,
                'ENTRY_SOUNDCLOUD_ID' => $yx['entry']['soundcloud_id'],
                'ENTRY_INSTAGRAM' => html_entity_decode($yx['entry']['instagram']),
                'ENTRY_INSTAGRAM_URL' => $yx['entry']['instagram_url'],
                'ENTRY_FACEBOOK' => $yx['entry']['facebook_post'],
                'ENTRY_VIDEO_HTML' => $video_html
            );
            $entries .= YX_LoadPage("edit-post/entries/{$entry_type}", $values_to_change);

        }
    } 
}


// print_r($_SESSION['uploads']);
// exit();
$add_entries = "";
$new_pages   = array('quiz');
if (!in_array($type, $new_pages)) {
    $add_entries = YX_LoadPage("edit-post/add-entries");
}


$yx['content'] = YX_LoadPage("edit-post/content", array(
    'POST_ID' => $yx['post_data']['id'],
    'POST_TITLE' => $yx['post_data']['title'],
    'POST_SHORT_TITLE' => $yx['post_data']['short_title'],
    'POST_DESC' => $yx['post_data']['description'],
    'POST_TAGS' => $yx['post_data']['tags'],
    'POST_CATEGORY' => $yx['post_data']['category'],
    'POST_IMAGE' => YX_GetMedia($yx['post_data']['image']),
    'POST_ENTRIES' => $entries,
    'ADD_ENTRIES' => $add_entries,
    'PAGE_TYPE' => $include_type,
    'Categories' => $category_html,
    'SELECT_PER_PAGE_0' => ($yx['post_data']['entries_per_page'] == 0) ? ' selected' : '',
    'SELECT_PER_PAGE_1' => ($yx['post_data']['entries_per_page'] == 1) ? ' selected' : '',
    'SELECT_PER_PAGE_2' => ($yx['post_data']['entries_per_page'] == 2) ? ' selected' : '',
    'SELECT_PER_PAGE_4' => ($yx['post_data']['entries_per_page'] == 4) ? ' selected' : '',
    'SELECT_PER_PAGE_6' => ($yx['post_data']['entries_per_page'] == 6) ? ' selected' : '',
    'SELECT_PER_PAGE_8' => ($yx['post_data']['entries_per_page'] == 8) ? ' selected' : '',
    'SELECT_PER_PAGE_10' => ($yx['post_data']['entries_per_page'] == 10) ? ' selected' : ''
));
if (in_array($yx['page'], $yx['pages_home_array'])) {
    $yx['page_home'] = true;
}