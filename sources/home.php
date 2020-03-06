<?php
$yx['title']       = $lang['home'] . ' | ' . $yx['config']['title'];
$yx['description'] = $yx['config']['description'];
$yx['keywords']    = $yx['config']['keywords'];
$yx['page']        = 'home';
if (in_array($yx['page'], $yx['pages_home_array'])) {
    $yx['page_home'] = true;
}

// Get TOP content;
require 'sources/home/top/news.php';
require 'sources/home/top/lists.php';
require 'sources/home/top/polls.php';
require 'sources/home/top/videos.php';
require 'sources/home/top/music.php';


// Get Latest content;
require 'sources/home/latest/news.php';
require 'sources/home/latest/lists.php';
require 'sources/home/latest/videos.php';
require 'sources/home/latest/polls.php';
require 'sources/home/latest/music.php';
require 'sources/home/latest/quizzes.php';

// Get Random content;
require 'sources/home/random/news.php';

// Get trending
require 'sources/home/trending/content.php';

$fetch_most_watched_data_array = array(
    'table' => T_LISTS,
    'column' => 'id',
    'limit' => 2,
    'order' => array(
        'type' => 'desc',
        'column' => 'id'
    ),
    'where' => array(
        array(
            'column' => 'active',
            'value' => '1',
            'mark' => '='
        )
    ),
    'final_data' => array(
        array(
            'function_name' => 'YX_GetLists',
            'column' => 'id',
            'name' => 'list'
        )
    )
);
$most_watched      = $yx['most_watched'] = YX_FetchDataFromDB($fetch_most_watched_data_array);
$yx_rss_source     = false;
$yx['yx_rss_feed'] = array();

if (!empty($yx['config']['rss_feed']) && YX_IsUrl($yx['config']['rss_feed'])){    
    $yx['yx_rss_feed'] = YX_ImportRssFeed($yx['config']['rss_feed'],$yx['config']['rss_feed_limit']);
}

$page_context  = array(
    'TOP_NEWS_HTML' => $top_news_html,
    'TOP_LISTS_HTML' => $top_lists_html,
    'TOP_POLLS_HTML' => $top_polls_html,
    'TOP_VIDEOS_HTML' => $top_videos_html,
    'TOP_MUSIC_HTML' => $top_music_html,
    'TOP_QUIZ_HTML' => $top_music_html,

    'LATEST_NEWS_AD' => YX_GetAd('home_latest_news', false),
    'LATEST_LISTS_AD' => YX_GetAd('home_latest_lists', false),
    'LATEST_VIDEOS_AD' => YX_GetAd('home_latest_videos', false),
    'LATEST_MUSIC_AD' => YX_GetAd('home_latest_music', false),

    'HP_UAD_1' => '',
    'HP_UAD_2' => '',
    'HP_UAD_3' => '',

    'SB_UAD_1' => '',
    'SB_UAD_2' => '',
);

$home_page_ads = yx_get_user_ads(2,3);
$index         = 1;

foreach ($home_page_ads as $yx['ad']) {

    $yx['ad']   = YX_ObjectToArray($yx['ad']);
    $ad_context = array(
        'ID' => $yx['ad']['id'],
        'ATTR_ID' => '',
        'URL' => $yx['ad']['url'],
        'NAME' => $yx['ad']['title'],
        'IMAGE' => YX_GetMedia($yx['ad']['media_file']),
        'AD' => '',
    );

    if (!yx_adcon_exists($yx['ad']['id'])) {
        $ad_context['ATTR_ID'] = sprintf('data-id="%u"',$yx['ad']['id']);
        $ad_context['AD']      = ' ad';
    }

    $page_context["HP_UAD_$index"] = YX_LoadPage("ads/ad",$ad_context);

    $index++;
}

$sidebar_ads = yx_get_user_ads(1,2);
$index       = 1;

foreach ($sidebar_ads as $yx['ad']) {
    $yx['ad']   = YX_ObjectToArray($yx['ad']);
    $ad_context = array(
        'ID' => $yx['ad']['id'],
        'ATTR_ID' => '',
        'URL' => $yx['ad']['url'],
        'NAME' => $yx['ad']['title'],
        'IMAGE' => YX_GetMedia($yx['ad']['media_file']),
        'AD' => '',
    );

    if (!yx_adcon_exists($yx['ad']['id'])) {
        $ad_context['ATTR_ID'] = sprintf('data-id="%u"',$yx['ad']['id']);
        $ad_context['AD']      = ' ad';
    }

    $page_context["SB_UAD_$index"] = YX_LoadPage("ads/ad",$ad_context);

    $index++;
}


$yx['content'] = YX_LoadPage('home/content', $page_context);