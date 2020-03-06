<?php 	
$fetch_latest_videos_data_array = array(
    'table' => T_VIDEOS,
    'column' => 'id',
    'limit' => 4,
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
            'function_name' => 'YX_GetVideos',
            'column' => 'id',
            'name' => 'video'
        )
    )
);
$latest_videos                 = $yx['latest_videos'] = YX_FetchDataFromDB($fetch_latest_videos_data_array);
 ?>