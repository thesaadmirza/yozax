<?php 	
$fetch_latest_news_data_array = array(
    'table' => T_QUIZZES,
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
            'function_name' => 'YX_GetQuizzes',
            'column' => 'id',
            'name' => 'quiz'
        )
    )
);
$quizzes   = $yx['quizzes'] = YX_FetchDataFromDB($fetch_latest_news_data_array);
?>