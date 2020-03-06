<?php 
$yx['title']       = $lang['latest_music'] .' | ' . $yx['config']['title'];
$yx['description'] = $yx['config']['description'];
$yx['keywords']    = $yx['config']['keywords'];
$yx['page']        = 'latest-music';
$fetch_latest_news_data_array = array(
    'table' => T_MUSIC,
    'column' => 'id',
    'limit' => 4,
    'order' => array(
        'type' => 'rand',
        'column' => 'id'
    ),
    'where' => array(
        array(
            'column' => 'active',
            'value' => '1',
            'mark' => '='
        ),
        array(
            'column' => 'featured',
            'value' => '1',
            'mark' => '='
        ),
    ),
    'final_data' => array(
        array(
            'function_name' => 'YX_GetMusic',
            'column' => 'id',
            'name' => 'news'
        )
    )
);
$big_top_news_html = '';
$latest_news  = $yx['latest_news'] = (!empty($_GET['c_id'])) ? array() : YX_FetchDataFromDB($fetch_latest_news_data_array);
$top_news_html = '';
foreach ($latest_news as $key => $yx['top_news_data']) {
	$top_news_html1 = YX_Loadpage('latest/lists/top_4', array(
        'TOP_NEWS_URL' => $yx['top_news_data']['news']['url'],
        'TOP_NEWS_IMAGE' => $yx['top_news_data']['news']['small_image'],
        'TOP_NEWS_TITLE' => $yx['top_news_data']['news']['title'],
        'TOP_NEWS_SHORT_TITLE' => $yx['top_news_data']['news']['short_title'],
        'TOP_NEWS_DESC' => $yx['top_news_data']['news']['description'],
        'TOP_NEWS_POSTED' => $yx['top_news_data']['news']['posted'],
        'TOP_NEWS_PUBLISHER__NAME' => $yx['top_news_data']['news']['publisher']['name']
    ));
	if ($key < 3) {
		$top_news_html  .= $top_news_html1;
	} else {
		$top_news_html2 = YX_Loadpage('latest/lists/top_1', array(
	        'TOP_NEWS_URL' => $yx['top_news_data']['news']['url'],
	        'TOP_NEWS_IMAGE' => $yx['top_news_data']['news']['small_image'],
	        'TOP_NEWS_TITLE' => $yx['top_news_data']['news']['title'],
	        'TOP_NEWS_SHORT_TITLE' => $yx['top_news_data']['news']['short_title'],
	        'TOP_NEWS_DESC' => $yx['top_news_data']['news']['description'],
	        'TOP_NEWS_POSTED' => $yx['top_news_data']['news']['posted'],
	        'TOP_NEWS_PUBLISHER__NAME' => $yx['top_news_data']['news']['publisher']['name']
	    ));
		$big_top_news_html = $top_news_html2;
	}
}

$fetch_latest_news_page_data_array = array(
    'table' => T_MUSIC,
    'column' => 'id',
    'limit' => 20,
    'order' => array(
        'type' => 'desc',
        'column' => 'id'
    ),
    'where' => array(
        array(
            'column' => 'active',
            'value' => '1',
            'mark' => '='
        ),
    ),
    'final_data' => array(
        array(
            'function_name' => 'YX_GetMusic',
            'column' => 'id',
            'name' => 'news'
        )
    )
);
if (!empty($_GET['c_id'])) {
	if (in_array($_GET['c_id'], array_keys($yx['music_categories']))) {
		$fetch_latest_news_page_data_array['where'][] = array(
            'column' => 'category',
            'value' => $_GET['c_id'],
            'mark' => '='
        );
	}
}
$latest_page_music = $yx['latest_page_music'] = YX_FetchDataFromDB($fetch_latest_news_page_data_array);

$yx['content'] = YX_LoadPage('latest/music', array('TOP_4' => $top_news_html, 'TOP_BIG' => $big_top_news_html));
?>