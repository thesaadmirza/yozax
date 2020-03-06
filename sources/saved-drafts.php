<?php
if ($yx['loggedin'] == false) {
	header("Location:" . $site_url);
	exit();
}
$yx['title'] = $lang['saved_drafts'] . ' | ' . $yx['config']['title'];
$yx['description'] = $yx['config']['description'];
$yx['page'] = 'saved-drafts';
$yx['keywords'] = $yx['config']['keywords'];

$order_by = array('type' => 'desc', 'column' => 'id');
$where_function = array(
    array(
        'column' => 'active',
        'value' => '0',
        'mark' => '='
    ),
    array(
        'column' => 'viewable',
        'value' => '0',
        'mark' => '='
    ),
    array(
        'column' => 'user_id',
        'value' => $yx['user']['user_id'],
        'mark' => '='
    ),
);

$count_user_darfts_news = array(
    'table' => T_NEWS,
    'column' => 'id',
    'count' => true,
    'where' => $where_function
);

$news = YX_FetchDataFromDB($count_user_darfts_news);

$count_user_darfts_lists = array(
    'table' => T_LISTS,
    'column' => 'id',
    'count' => true,
    'where' => $where_function
);

$lists = YX_FetchDataFromDB($count_user_darfts_lists);

$count_user_darfts_videos = array(
    'table' => T_VIDEOS,
    'column' => 'id',
    'count' => true,
    'where' => $where_function
);

$videos = YX_FetchDataFromDB($count_user_darfts_videos);

$count_user_darfts_music = array(
    'table' => T_MUSIC,
    'column' => 'id',
    'count' => true,
    'where' => $where_function
);

$music = YX_FetchDataFromDB($count_user_darfts_music);

$count_user_darfts_polls = array(
    'table' => T_POLLS_PAGES,
    'column' => 'id',
    'count' => true,
    'where' => $where_function
);

$polls = YX_FetchDataFromDB($count_user_darfts_polls);

///////

$get_user_darfts_news = array(
    'table' => T_NEWS,
    'column' => 'id',
    'order' => $order_by,
    'limit' => 50,
    'where' => $where_function,
    'final_data' => array(
        array(
            'function_name' => 'YX_GetNews',
            'column' => 'id',
            'name' => 'news'
        )
    )
);

$get_news = $yx['get_news_drafts'] = YX_FetchDataFromDB($get_user_darfts_news);

$get_user_darfts_lists = array(
    'table' => T_LISTS,
    'column' => 'id',
    'order' => $order_by,
    'limit' => 50,
    'where' => $where_function,
    'final_data' => array(
        array(
            'function_name' => 'YX_GetLists',
            'column' => 'id',
            'name' => 'news'
        )
    )
);

$get_lists = $yx['get_lists_drafts'] = YX_FetchDataFromDB($get_user_darfts_lists);

$get_user_darfts_videos = array(
    'table' => T_VIDEOS,
    'order' => $order_by,
    'column' => 'id',
    'limit' => 50,
    'where' => $where_function,
    'final_data' => array(
        array(
            'function_name' => 'YX_GetVideos',
            'column' => 'id',
            'name' => 'news'
        )
    )
);

$get_videos = $yx['get_videos_drafts'] = YX_FetchDataFromDB($get_user_darfts_videos);

$get_user_darfts_music = array(
    'table' => T_MUSIC,
    'order' => $order_by,
    'column' => 'id',
    'limit' => 50,
    'where' => $where_function,
    'final_data' => array(
        array(
            'function_name' => 'YX_GetMusic',
            'column' => 'id',
            'name' => 'news'
        )
    )
);

$get_music = $yx['get_music_drafts'] = YX_FetchDataFromDB($get_user_darfts_music);


$get_user_darfts_polls = array(
    'table' => T_POLLS_PAGES,
    'order' => $order_by,
    'column' => 'id',
    'limit' => 50,
    'where' => $where_function,
    'final_data' => array(
        array(
            'function_name' => 'YX_GetPolls',
            'column' => 'id',
            'name' => 'news'
        )
    )
);

$get_polls = $yx['get_polls_drafts'] = YX_FetchDataFromDB($get_user_darfts_polls);

$yx['content'] = YX_LoadPage('saved-drafts/content', array(
	'COUNT_NEWS_DRAFTS' => $news[0]['count'],
	'COUNT_LISTS_DRAFTS' => $lists[0]['count'],
	'COUNT_VIDEOS_DRAFTS' => $videos[0]['count'],
	'COUNT_MUSIC_DRAFTS' => $music[0]['count'],
	'COUNT_POLLS_DRAFTS' => $polls[0]['count'],
));