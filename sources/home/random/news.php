<?php 	
$fetch_random_news_data_array = array(
    'table' => T_NEWS,
    'column' => 'id',
    'limit' => 1,
    'order' => array(
        'type' => 'rand'
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
$random_news                  = $yx['random_news'] = YX_FetchDataFromDB($fetch_random_news_data_array); 
?>