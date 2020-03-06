<?php 	
$fetch_latest_news_data_array = array(
    'table' => T_NEWS,
    'column' => 'id',
    'limit' => 6,
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
            'function_name' => 'YX_GetNews',
            'column' => 'id',
            'name' => 'news'
        )
    )
);
$latest_news                  = $yx['latest_news'] = YX_FetchDataFromDB($fetch_latest_news_data_array);
?>