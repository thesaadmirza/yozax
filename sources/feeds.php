<?php
$yx['title']       = $yx['config']['title'];
$yx['description'] = $yx['config']['description'];
$yx['keywords']    = $yx['config']['keywords'];
$yx['page']        = 'rss';
header('Content-type: text/rss+xml');



use Bhaktaraz\RSSGenerator\Item;
use Bhaktaraz\RSSGenerator\Feed;
use Bhaktaraz\RSSGenerator\Channel;

$rss_feed_xml    = "";
$yx_rss_feed     = new Feed();
$yx_rss_channel  = new Channel();


$yx_rss_channel
	->title($yx['title'])
	->description($yx['description'])
	->url($yx['config']['site_url'])
	->appendTo($yx_rss_feed);







$rss_posts        = array( 
    T_LISTS       => 'YX_GetLists',
    T_NEWS        => 'YX_GetNews',
    T_POLLS_PAGES => 'YX_GetPolls',
    T_MUSIC       => 'YX_GetMusic',
    T_VIDEOS      => 'YX_GetVideos',
    T_QUIZZES     => 'YX_GetQuizzes'
);


foreach ($rss_posts as $table => $func) {
    $yx_rss_feed_type = '';
    switch ($table) {
        case T_LISTS:
            $yx_rss_feed_type = 'list';
            break;
        case T_NEWS:
            $yx_rss_feed_type = 'news';
            break;
        case T_POLLS_PAGES:
            $yx_rss_feed_type = 'polls';
            break;
        case T_MUSIC:
            $yx_rss_feed_type = 'music';
            break;
        case T_VIDEOS:
            $yx_rss_feed_type = 'video';
            break;
        case T_QUIZZES:
            $yx_rss_feed_type = 'quiz';
            break;
    }

    $fetch_rss_feed_data_array = array(
        'table' => $table,
        'column' => 'id',
        'limit' => 50,
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
                'function_name' => $func,
                'column' => 'id',
                'name'   => 'feed'
            )
        )
    ); 
    $rss_feed_data   = YX_FetchDataFromDB($fetch_rss_feed_data_array);
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

}
echo $yx_rss_feed;
exit();


