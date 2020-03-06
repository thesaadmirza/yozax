<?php 

// @author Saad Mirza https://saadmirza.net

/* 

   /app_api.php?type=latest_posts
   GET:
       & page  = < news page should be in this array [('news','poll','video','list','music','quiz')] >
       & limit = < default 20>


   ERROR CODES: 
       1. Bad request, no type specified.
       2. Bad request, no page specified.

   JSON REPLY:
       {
        'api_status'     => '200',
        'api_text'       => 'success',
        'api_version'    => 'api_version',
        'page_data'      => [array]
        
       }

    

*/



$json_error_data         = array();
$json_success_data       = array();
$latest_news_pages       = array('news','poll','video','list','music','quiz');
if (empty($_GET['type']) || !isset($_GET['type'])) {
    $json_error_data     = array(
        'api_status'     => '400',
        'api_text'       => 'failed',
        'api_version'    => $api_version,
        'errors'         => array(
            'error_id'   => '1',
            'error_text' => 'Bad request, no type specified.'
        )
    );
}
else if (!isset($_GET['page']) || !in_array($_GET['page'], $latest_news_pages)) {
    $json_error_data     = array(
        'api_status'     => '400',
        'api_text'       => 'failed',
        'api_version'    => $api_version,
        'errors'         => array(
            'error_id'   => '2',
            'error_text' => 'Bad request, no page specified.'
        )
    );
}

if (!empty($json_error_data)) { 
    header("Content-type: application/json");
    echo json_encode($json_error_data);
    exit();
}

$type                    = YX_Secure($_GET['type'], 0);
$limit                   = (!empty($_GET['limit']) && is_numeric($_GET['limit'])) ? $_GET['limit'] : 20;


if ($type == 'latest_posts') {
 
    $function_name           = '';
    $page                    = YX_Secure($_GET['page'], 0);
    $table                   = T_NEWS;
    switch ($page) {
        case 'news':
            $function_name   = 'YX_GetNews';
            break;
        case 'list':
            $function_name   = 'YX_GetLists';
            $table           = T_LISTS;
            break;
        case 'poll':
            $function_name   = 'YX_GetPolls';
            $table           = T_POLLS_PAGES;
            break;
        case 'video':
            $function_name   = 'YX_GetVideos';
            $table           = T_VIDEOS;
            break;
        case 'music':
            $function_name   = 'YX_GetMusic';
            $table           = T_MUSIC;
            break;
        case 'quiz':
            $function_name   = 'YX_GetQuizzes';
            $table           = T_QUIZZES;
            break;
    }
    

    $fetch_latest_data_array = array(
        'table'              => $table,
        'column'             => 'id',
        'limit'              => $limit,
        'user_data'          => 'public',
        'order'              => array(
            'type'           => 'desc',
            'column'         => 'id'
        ),
        'where'              => array(
            array(
                'column'     => 'active',
                'value'      => '1',
                'mark'       => '='
            ),
        ),
        'final_data'         => array(
            array(
             'function_name' => $function_name,
             'column'        => 'id'
            )
        )
    );
    $posts                   = YX_FetchDataFromDB($fetch_latest_data_array);
    $user_data               = array('name','username','email','avatar','gender','facebook','instagram','verified');
    $posts_data              = array();

    foreach ($posts as $value) {
        $value['publisher']  = array_intersect_key($value['publisher'], array_flip($user_data));
        $posts_data[]        = $value;
    }

    $json_success_data                 = array(
             'api_status'              => '200',
             'api_text'                => 'success',
             'api_version'             => $api_version,
             $page.'_data'             => $posts_data
    );
    header("Content-type: application/json");
    echo json_encode($json_success_data, JSON_PRETTY_PRINT);
    exit();
    
}
