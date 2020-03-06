<?php
if (empty($_GET['u'])) {
	header("Location: " . YX_Link('404'));
	exit();
}
if (YX_UserExists($_GET['u']) === false) {
	header("Location: " . YX_Link('404'));
	exit();
}
$user_id = YX_UserIdFromUsername($_GET['u']);
$profile_data = $yx['profile_data'] = YX_UserData($user_id);
if (empty($user_id) || empty($profile_data)) {
	header("Location: " . YX_Link('404'));
	exit();
}

$url = YX_Link('');


$yx['profile_data']['news_count']   = YX_CountUserPosts($profile_data['user_id'], 'news');
$yx['profile_data']['polls_count']  = YX_CountUserPosts($profile_data['user_id'], 'poll');
$yx['profile_data']['lists_count']  = YX_CountUserPosts($profile_data['user_id'], 'list');
$yx['profile_data']['videos_count'] = YX_CountUserPosts($profile_data['user_id'], 'video');
$yx['profile_data']['music_count']  = YX_CountUserPosts($profile_data['user_id'], 'music');
$yx['profile_data']['quiz_count']   = YX_CountUserPosts($profile_data['user_id'], 'quiz');

$fetch_latest_news_page_data_array = array(
    'table' => T_NEWS,
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
         array(
            'column' => 'user_id',
            'value' => $profile_data['user_id'],
            'mark' => '='
        ),
    ),
    'final_data' => array(
        array(
            'function_name' => 'YX_GetNews',
            'column' => 'id',
            'name' => 'news'
        )
    )
);

$yx['latest_page_news'] = YX_FetchDataFromDB($fetch_latest_news_page_data_array);

$fetch_latest_news_page_data_array = array(
    'table' => T_LISTS,
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
         array(
            'column' => 'user_id',
            'value' => $profile_data['user_id'],
            'mark' => '='
        ),
    ),
    'final_data' => array(
        array(
            'function_name' => 'YX_GetLists',
            'column' => 'id',
            'name' => 'news'
        )
    )
);

$yx['latest_page_lists'] = YX_FetchDataFromDB($fetch_latest_news_page_data_array);

$fetch_latest_news_page_data_array = array(
    'table' => T_VIDEOS,
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
         array(
            'column' => 'user_id',
            'value' => $profile_data['user_id'],
            'mark' => '='
        ),
    ),
    'final_data' => array(
        array(
            'function_name' => 'YX_GetVideos',
            'column' => 'id',
            'name' => 'news'
        )
    )
);

$yx['latest_page_videos'] = YX_FetchDataFromDB($fetch_latest_news_page_data_array);


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
         array(
            'column' => 'user_id',
            'value' => $profile_data['user_id'],
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

$yx['latest_page_music'] = YX_FetchDataFromDB($fetch_latest_news_page_data_array);

$fetch_latest_news_page_data_array = array(
    'table' => T_POLLS_PAGES,
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
         array(
            'column' => 'user_id',
            'value' => $profile_data['user_id'],
            'mark' => '='
        ),
    ),
    'final_data' => array(
        array(
            'function_name' => 'YX_GetPolls',
            'column' => 'id',
            'name' => 'news'
        )
    )
);

$yx['latest_page_polls'] = YX_FetchDataFromDB($fetch_latest_news_page_data_array);



$fetch_latest_news_page_data_array = array(
    'table' => T_QUIZZES,
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
         array(
            'column' => 'user_id',
            'value' => $profile_data['user_id'],
            'mark' => '='
        ),
    ),
    'final_data' => array(
        array(
            'function_name' => 'YX_GetQuizzes',
            'column' => 'id',
            'name' => 'news'
        )
    )
);
$yx['fields']              = array();
$yx['latest_page_quizzes'] = YX_FetchDataFromDB($fetch_latest_news_page_data_array);
$yx['pr_fields']           = YX_GetProfileFields('none');
$yx['pr_fields']           = (is_array($yx['pr_fields'])) ? $yx['pr_fields'] : array();
$yx['user_fields']         = YX_UserFieldsData($user_id);
$yx['title']               = $yx['profile_data']['name'];
$yx['description']         = $yx['profile_data']['about'];
$yx['page']                = 'profile';

foreach ($yx['pr_fields'] as $key => $yx['field']) {
    if (!empty($yx['user_fields'][$yx['field']['fid']])) {
        $yx['fields'][]    = $yx['field'];
    }
}

$btnFollow = '<a href="'.YX_Link('login').'"><button class="btn btn-main"  role="button" disabled><span>Follow</span></button></a>';
$messagebtn = '';
if(!empty($_SESSION['user_id'])){
    
    if(GetUserSession() === $yx['profile_data']['user_id']){
        $btnFollow = '<button class="btn btn-main"  role="button" disabled><span>Follow</span></button>';
        $messagebtn = '<a href="'.$url.'user/message"><button type="button" class="btn btn-main"  role="button"><span>Message</span></button></a>';
    }else{

        $val = CheckFollow($yx['profile_data']['user_id']);
        global $url;


        if($val === 'yes'){
            $messagebtn = '<a href="'.$url.'user/message?to='.$yx['profile_data']['user_id'].'"><button type="button" class="btn btn-main"  role="button"><span>Message</span></button></a>';
            $btnFollow = '<button type="submit" class="btn btn-main"  role="button" id="follow" data-id="'.$yx['profile_data']['user_id'].'"><span>Unfollow</span></button>';
        }
        if($val === 'no'){
            $btnFollow = '<button type="submit" class="btn btn-main"  role="button" id="follow" data-id="'.$yx['profile_data']['user_id'].'"><span>Follow</span></button>';
        }
        
    }

}



$yx['keywords']            = $yx['config']['keywords'];
$yx['profile_data']['verified'] = ($yx['profile_data']['verified'] == 1) ? '<span class="verified-icon"><i class="fa fa-check-circle"></i></span>' : '';
$yx['content']             = YX_LoadPage("profile/content", array(
    'USER_DATA'            => $yx['profile_data'],
    'btnFollow' => $btnFollow,
    'msgbtn' => $messagebtn
));
