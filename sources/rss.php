<?php
if (empty($_GET['page'])) {
    header("Location: " . $yx['config']['site_url']);
    exit();
}
 
$yx['title']       = $yx['config']['title'];
$yx['description'] = $yx['config']['description'];
$yx['keywords']    = $yx['config']['keywords'];
$yx['page']        = YX_Secure($_GET['page']);
header('Content-type: text/rss+xml');



use Bhaktaraz\RSSGenerator\Item;
use Bhaktaraz\RSSGenerator\Feed;
use Bhaktaraz\RSSGenerator\Channel;

$rss_feed_xml      = "";
$yx_rss_feed       = new Feed();
$yx_rss_channel    = new Channel();


$yx_rss_channel
	->title($yx['title'])
	->description($yx['description'])
	->url($yx['config']['site_url'])
	->appendTo($yx_rss_feed);



$rss_feed_table    = T_NEWS;
$rss_feed_func     = 'YX_GetNews';


switch ($yx['page']) {
    case 'lists':
        $rss_feed_table = T_LISTS;
        $rss_feed_func  = 'YX_GetLists';
        break;
    case 'videos':
        $rss_feed_table = T_VIDEOS;
        $rss_feed_func  = 'YX_GetVideos';
        break;
    case 'music':
        $rss_feed_table = T_MUSIC;
        $rss_feed_func  = 'YX_GetMusic';
        break;
    case 'polls':
        $rss_feed_table = T_POLLS_PAGES;
        $rss_feed_func  = 'YX_GetPolls';
        break;
    case 'quizzes':
        $rss_feed_table = T_QUIZZES;
        $rss_feed_func  = 'YX_GetQuizzes';
        break;
}


$fetch_rss_feed_data_array = array(
    'table'  => $rss_feed_table,
    'column' => 'id',
    'limit'  => 50,
    'order'  => array(
        'type'   => 'desc',
        'column' => 'id'
    ),
    'where'          => array(
        array(
            'column' => 'active',
            'value'  => '1',
            'mark'   => '='
        )
    ),
    'final_data' => array(
        array(
            'function_name'  => $rss_feed_func,
            'column' => 'id',
            'name'   => 'feed'
        )
    )
); 

$rss_feed_data        = YX_FetchDataFromDB($fetch_rss_feed_data_array);
if (is_array($rss_feed_data)) {
    foreach ($rss_feed_data as  $feed_item_data) {
        $yx_rss_item  = new Item();
        $yx_rss_item
         ->title($feed_item_data['feed']['title'])
         ->description($feed_item_data['feed']['description'])
         ->url($feed_item_data['feed']['url'])
         ->pubDate($feed_item_data['feed']['time'])
         ->guid($feed_item_data['feed']['url'],true)
         ->media(array(
            'attr'  => 'url',
            'ns'    => 'thumbnail',
            'link'  => YX_GetMedia($feed_item_data['feed']['image'])))
         ->appendTo($yx_rss_channel);
    }
}

echo $yx_rss_feed;
exit();


