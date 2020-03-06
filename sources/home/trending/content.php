<?php 
$fetch_latest_lists_data_array = array(
    'table' => T_LISTS,
    'column' => 'id',
    'limit' => 3,
    'order' => array(
        'type' => 'desc',
        'column' => 'views'
    ),
    'where' => array(
        array(
            'column' => 'active',
            'value' => '1',
            'mark' => '='
        ),
        array(
            'column' => 'time',
            'value' => time() - 604800,
            'mark' => '>'
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
$trending_list = $yx['trending_list'] = YX_FetchDataFromDB($fetch_latest_lists_data_array);

///----------

$fetch_latest_news_data_array = array(
    'table' => T_NEWS,
    'column' => 'id',
    'limit' => 3,
    'order' => array(
        'type' => 'desc',
        'column' => 'views'
    ),
    'where' => array(
        array(
            'column' => 'active',
            'value' => '1',
            'mark' => '='
        ),
        array(
            'column' => 'time',
            'value' => time() - 604800,
            'mark' => '<'
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
$trending_news = $yx['trending_news'] = YX_FetchDataFromDB($fetch_latest_news_data_array);


///----------
$fetch_latest_polls_data_array = array(
    'table' => T_POLLS_PAGES,
    'column' => 'id',
    'limit' => 3,
    'order' => array(
        'type' => 'desc',
        'column' => 'views'
    ),
    'where' => array(
        array(
            'column' => 'active',
            'value' => '1',
            'mark' => '='
        ),
        array(
            'column' => 'time',
            'value' => time() - 604800,
            'mark' => '>'
        )
    ),
    'final_data' => array(
        array(
            'function_name' => 'YX_GetPolls',
            'column' => 'id',
            'name' => 'poll'
        )
    )
);
$trending_poll = $yx['trending_poll'] = YX_FetchDataFromDB($fetch_latest_polls_data_array);

$fetch_latest_quizzes_data_array = array(
    'table' => T_QUIZZES,
    'column' => 'id',
    'limit' => 3,
    'order' => array(
        'type' => 'desc',
        'column' => 'views'
    ),
    'where' => array(
        array(
            'column' => 'active',
            'value' => '1',
            'mark' => '='
        ),
        array(
            'column' => 'time',
            'value' => time() - 604800,
            'mark' => '>'
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

$trending_quiz = $yx['trending_quiz'] = YX_FetchDataFromDB($fetch_latest_quizzes_data_array);
?>