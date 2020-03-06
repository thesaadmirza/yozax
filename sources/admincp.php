<?php

if ($yx['loggedin'] == false) {
    header("Location: " . $yx['config']['site_url']);
    exit();
}
$is_admin = YX_IsAdmin();
if ($is_admin === false) {
    header("Location: " . $yx['config']['site_url']);
    exit();
}
$page        = 'dashboard';
$page_src    = 'content';
$pages_array = array(
    'dashboard',
    'general',
    'site',
    'design',
    'social',
    'news',
    'lists',
    'videos',
    'polls',
    'music',
    'quizzes',
    'email',
    'users',
    'company',
    'verifications_requests',
    'themes',
    'announcement',
    'breaking_news',
    'ads',
    'custom_fields',
    'create_field',
    'edit_field',
    'banned_ip',
    'post_reports',
    'back_up',
    'custom_code',
    's3',
    'apps_api',
);
$hr_keys     = array(
    'email',
    'quizzes',
    'verifications_requests',
    'users',
    'breaking_news',
    'custom_fields',
    'banned_ip',
    'post_reports',
    'back_up',
    's3',
    'apps_api',
);
$pages_urls_ = array(
    'dashboard' => array(
        'url' => 'admincp/dashboard',
        'name' => $lang['dashbaord']
    ),
    'general' => array(
        'url' => 'admincp/general',
        'name' => $lang['general_settings']
    ),
    'site' => array(
        'url' => 'admincp/site',
        'name' => $lang['site_settings']
    ),
    'design' => array(
        'url' => 'admincp/design',
        'name' => $lang['design']
    ),
    'themes' => array(
        'url' => 'admincp/themes',
        'name' => $lang['themes']
    ),
    'social' => array(
        'url' => 'admincp/social',
        'name' => $lang['social_login']
    ),
    'email' => array(
        'url' => 'admincp/email',
        'name' => $lang['email_settings']
    ),
    'users' => array(
        'url' => 'admincp/users',
        'name' => $lang['users']
    ),
    'news' => array(
        'url' => 'admincp/news',
        'name' => $lang['news']
    ),
    'lists' => array(
        'url' => 'admincp/lists',
        'name' => $lang['lists']
    ),
    'videos' => array(
        'url' => 'admincp/videos',
        'name' => $lang['videos']
    ),
    'music' => array(
        'url' => 'admincp/music',
        'name' => $lang['music']
    ),
    'polls' => array(
        'url' => 'admincp/polls',
        'name' => $lang['polls']
    ),
    'quizzes' => array(
        'url' => 'admincp/quizzes',
        'name' => 'Quizzes'
    ),
    'verifications_requests' => array(
        'url' => 'admincp/verifications_requests',
        'name' => $lang['verification']
    ),
    'breaking_news' => array(
        'url' => 'admincp/breaking_news',
        'name' => $lang['br_news']
    ),
    'announcement' => array(
        'url' => 'admincp/announcement',
        'name' => $lang['announcement']
    ),
    'custom_fields' => array(
        'url' => 'admincp/custom_fields',
        'name' => 'Custom Fields'
    ),
    'custom_code' => array(
        'url' => 'admincp/custom_code',
        'name' => 'Custom Css & Js'
    ),
    'banned_ip' => array(
        'url' => 'admincp/banned_ip',
        'name' => 'Ban User'
    ),
    'post_reports' => array(
        'url' => 'admincp/post_reports',
        'name' => 'Reports'
    ),
    'ads' => array(
        'url' => 'admincp/ads',
        'name' => $lang['advertisement']
    ),
    's3' => array(
        'url' => 'admincp/s3',
        'name' => 'Amazon S3'
    ),
    'apps_api' => array(
        'url' => 'admincp/apps_api',
        'name' => 'API Settings'
    ),
    'company' => array(
        'url' => 'admincp/company',
        'name' => $lang['company_pages']
    ),
    'terms' => array(
        'url' => 'admincp/back_up',
        'name' => 'Backups'
    ),
    
    
);
if (!empty($_GET['page'])) {
    if (in_array($_GET['page'], $pages_array)) {
        $page = $_GET['page'];
    }
}
$list_menu = '';
foreach ($pages_urls_ as $key => $page_) {
    if (in_array($key, $pages_array)) {
        $active = ($page == $key) ? 'active' : '';
        $list_menu .= '<li class="list-group-item ' . $active . '"><a href="{{LINK ' . $page_['url'] . '}}">' . $page_['name'] . '</a></li>';
        if (in_array($key, $hr_keys)) {
            $list_menu .= '<hr>';
        }
    }
}
if ($page == 'news') {
   $yx['all_news'] = array();
}
$final_array = array(
    'ADMINCP_PAGE' => YX_LoadPage("admincp/{$page}/$page_src"),
    'LIST_MENU' => $list_menu,
);

if ($page == 'news') {
    $fetch_latest_news_data_array = array(
        'table' => T_NEWS,
        'column' => 'id',
        'limit' => 500000,
        'final_data' => array(
            array(
                'function_name' => 'YX_GetNews',
                'column' => 'id',
                'name' => 'news'
            )
        )
    );

    $latest_news  = YX_FetchDataFromDB($fetch_latest_news_data_array); 
    $latest_news_html = '';
    foreach ($latest_news as $key => $yx['news']) {
        $latest_news_html .=  YX_LoadPage("admincp/news/list", array(
            'POST_ID' => $yx['news']['id'],
            'POST_LINK' => $yx['news']['news']['url'],
            'POST_TITLE' => $yx['news']['news']['title'],
            'POST_TITLE_CROPPED' => mb_substr($yx['news']['news']['title'], 0, 20, "UTF-8") . '...',
            'POST_CATEGORY' => (!empty($yx['news_categories'][$yx['news']['news']['category']])) ? $yx['news_categories'][$yx['news']['news']['category']] : 0,
            'POST_AUTHOR' => $yx['news']['news']['publisher']['name'],
            'POST_AUTHOR_URL' => $yx['news']['news']['publisher']['url'],
            'POST_STATUS' => ($yx['news']['news']['active'] == 0) ? $lang['pending'] : $lang['active'],
            'POST_ACTIVE' => $yx['news']['news']['active'],
            'POST_FEATURED_TEXT' => ($yx['news']['news']['featured'] == 1) ? $lang['yes'] : $lang['no'],
            'POST_FEATURED' => $yx['news']['news']['featured'],
        ));
    }
    $final_array['NEWS_DATA'] = $latest_news_html;
}
if ($page == 'lists') {
    $fetch_latest_news_data_array = array(
        'table' => T_LISTS,
        'column' => 'id',
        'limit' => 500000,
        'final_data' => array(
            array(
                'function_name' => 'YX_GetLists',
                'column' => 'id',
                'name' => 'news'
            )
        )
    );

    $latest_news  = YX_FetchDataFromDB($fetch_latest_news_data_array); 
    $latest_news_html = '';
    foreach ($latest_news as $key => $yx['news']) {
        $latest_news_html .=  YX_LoadPage("admincp/lists/list", array(
            'POST_ID' => $yx['news']['id'],
            'POST_LINK' => $yx['news']['news']['url'],
            'POST_TITLE' => $yx['news']['news']['title'],
            'POST_TITLE_CROPPED' => mb_substr($yx['news']['news']['title'], 0, 20, "UTF-8") . '...',
            'POST_CATEGORY' => (!empty($yx['list_categories'][$yx['news']['news']['category']])) ? $yx['list_categories'][$yx['news']['news']['category']] : 0,
            'POST_AUTHOR' => $yx['news']['news']['publisher']['name'],
            'POST_AUTHOR_URL' => $yx['news']['news']['publisher']['url'],
            'POST_STATUS' => ($yx['news']['news']['active'] == 0) ? $lang['pending'] : $lang['active'],
            'POST_ACTIVE' => $yx['news']['news']['active'],
            'POST_FEATURED_TEXT' => ($yx['news']['news']['featured'] == 1) ? $lang['yes'] : $lang['no'],
            'POST_FEATURED' => $yx['news']['news']['featured'],
        ));
    }
    $final_array['LISTS_DATA'] = $latest_news_html;
}

if ($page == 'videos') {
    $fetch_latest_news_data_array = array(
        'table' => T_VIDEOS,
        'column' => 'id',
        'limit' => 500000,
        'final_data' => array(
            array(
                'function_name' => 'YX_GetVideos',
                'column' => 'id',
                'name' => 'news'
            )
        )
    );

    $latest_news  = YX_FetchDataFromDB($fetch_latest_news_data_array); 
    $latest_news_html = '';
    foreach ($latest_news as $key => $yx['news']) {
        $latest_news_html .=  YX_LoadPage("admincp/videos/list", array(
            'POST_ID' => $yx['news']['id'],
            'POST_LINK' => $yx['news']['news']['url'],
            'POST_TITLE' => $yx['news']['news']['title'],
            'POST_TITLE_CROPPED' => mb_substr($yx['news']['news']['title'], 0, 20, "UTF-8") . '...',
            'POST_CATEGORY' => (!empty($yx['video_categories'][$yx['news']['news']['category']])) ? $yx['video_categories'][$yx['news']['news']['category']] : 0,
            'POST_AUTHOR' => $yx['news']['news']['publisher']['name'],
            'POST_AUTHOR_URL' => $yx['news']['news']['publisher']['url'],
            'POST_STATUS' => ($yx['news']['news']['active'] == 0) ? $lang['pending'] : $lang['active'],
            'POST_ACTIVE' => $yx['news']['news']['active'],
            'POST_FEATURED_TEXT' => ($yx['news']['news']['featured'] == 1) ? $lang['yes'] : $lang['no'],
            'POST_FEATURED' => $yx['news']['news']['featured'],
        ));
    }
    $final_array['VIDEOS_DATA'] = $latest_news_html;
}


if ($page == 'music') {
    $fetch_latest_news_data_array = array(
        'table' => T_MUSIC,
        'column' => 'id',
        'limit' => 500000,
        'final_data' => array(
            array(
                'function_name' => 'YX_GetMusic',
                'column' => 'id',
                'name' => 'news'
            )
        )
    );

    $latest_news  = YX_FetchDataFromDB($fetch_latest_news_data_array); 
    $latest_news_html = '';
    foreach ($latest_news as $key => $yx['news']) {
        $latest_news_html .=  YX_LoadPage("admincp/music/list", array(
            'POST_ID' => $yx['news']['id'],
            'POST_LINK' => $yx['news']['news']['url'],
            'POST_TITLE' => $yx['news']['news']['title'],
            'POST_TITLE_CROPPED' => mb_substr($yx['news']['news']['title'], 0, 20, "UTF-8") . '...',
            'POST_CATEGORY' => (!empty($yx['music_categories'][$yx['news']['news']['category']])) ? $yx['music_categories'][$yx['news']['news']['category']] : 0,
            'POST_AUTHOR' => $yx['news']['news']['publisher']['name'],
            'POST_AUTHOR_URL' => $yx['news']['news']['publisher']['url'],
            'POST_STATUS' => ($yx['news']['news']['active'] == 0) ? $lang['pending'] : $lang['active'],
            'POST_ACTIVE' => $yx['news']['news']['active'],
            'POST_FEATURED_TEXT' => ($yx['news']['news']['featured'] == 1) ? $lang['yes'] : $lang['no'],
            'POST_FEATURED' => $yx['news']['news']['featured'],
        ));
    }
    $final_array['MUSIC_DATA'] = $latest_news_html;
}

if ($page == 'polls') {
    $fetch_latest_news_data_array = array(
        'table' => T_POLLS_PAGES,
        'column' => 'id',
        'limit' => 500000,
        'final_data' => array(
            array(
                'function_name' => 'YX_GetPolls',
                'column' => 'id',
                'name' => 'news'
            )
        )
    );

    $latest_news  = YX_FetchDataFromDB($fetch_latest_news_data_array); 
    $latest_news_html = '';
    foreach ($latest_news as $key => $yx['news']) {
        $latest_news_html .=  YX_LoadPage("admincp/polls/list", array(
            'POST_ID' => $yx['news']['id'],
            'POST_LINK' => $yx['news']['news']['url'],
            'POST_TITLE' => $yx['news']['news']['title'],
            'POST_TITLE_CROPPED' => mb_substr($yx['news']['news']['title'], 0, 20, "UTF-8") . '...',
            'POST_CATEGORY' => (!empty($yx['poll_categories'][$yx['news']['news']['category']])) ? $yx['poll_categories'][$yx['news']['news']['category']] : 0,
            'POST_AUTHOR' => $yx['news']['news']['publisher']['name'],
            'POST_AUTHOR_URL' => $yx['news']['news']['publisher']['url'],
            'POST_STATUS' => ($yx['news']['news']['active'] == 0) ? $lang['pending'] : $lang['active'],
            'POST_ACTIVE' => $yx['news']['news']['active'],
            'POST_FEATURED_TEXT' => ($yx['news']['news']['featured'] == 1) ? $lang['yes'] : $lang['no'],
            'POST_FEATURED' => $yx['news']['news']['featured'],
        ));
    }
    $final_array['POLLS_DATA'] = $latest_news_html;
}

if ($page == 'quizzes') {
    $fetch_latest_news_data_array = array(
        'table' => T_QUIZZES,
        'column' => 'id',
        'limit' => 500000,
        'final_data' => array(
            array(
                'function_name' => 'YX_GetQuizzes',
                'column' => 'id',
                'name' => 'news'
            )
        )
    );

    $latest_news  = YX_FetchDataFromDB($fetch_latest_news_data_array); 
    $latest_news_html = '';
    foreach ($latest_news as $key => $yx['news']) {
        $latest_news_html .=  YX_LoadPage("admincp/quizzes/list", array(
            'POST_ID' => $yx['news']['id'],
            'POST_LINK' => $yx['news']['news']['url'],
            'POST_TITLE' => $yx['news']['news']['title'],
            'POST_TITLE_CROPPED' => mb_substr($yx['news']['news']['title'], 0, 20, "UTF-8") . '...',
            'POST_CATEGORY' => (!empty($yx['poll_categories'][$yx['news']['news']['category']])) ? $yx['poll_categories'][$yx['news']['news']['category']] : 0,
            'POST_AUTHOR' => $yx['news']['news']['publisher']['name'],
            'POST_AUTHOR_URL' => $yx['news']['news']['publisher']['url'],
            'POST_STATUS' => ($yx['news']['news']['active'] == 0) ? $lang['pending'] : $lang['active'],
            'POST_ACTIVE' => $yx['news']['news']['active'],
            'POST_FEATURED_TEXT' => ($yx['news']['news']['featured'] == 1) ? $lang['yes'] : $lang['no'],
            'POST_FEATURED' => $yx['news']['news']['featured'],
        ));
    }
    $final_array['QUIZ_DATA'] = $latest_news_html;
}

if ($page == 'users') {
    $fetch_latest_users_data_array = array(
        'table' => T_USERS,
        'column' => 'user_id',
        'limit' => 500000,
        'order' => array(
            'type' => 'rand',
            'column' => 'id'
        ),
        'final_data' => array(
            array(
                'function_name' => 'YX_UserData',
                'column' => 'user_id',
                'name' => 'users'
            )
        )
    );

    $latest_users  = YX_FetchDataFromDB($fetch_latest_users_data_array); 
    $latest_news_html = '';
    foreach ($latest_users as $key => $yx['users']) {
        $latest_news_html .=  YX_LoadPage("admincp/users/list", array(
           'USER_DATA' => $yx['users']['users'],
           'USER_STATUS' => ($yx['users']['users']['active'] == 0) ? $lang['pending'] : $lang['active'],
        ));
    }
    $final_array['USERS_DATA'] = $latest_news_html;
}

if ($page == 'verifications_requests') {
    $fetch_verif_requests_array = array(
        'table' => T_VER_REQUESTS,
        'column' => '*',
        'limit' => 500000,
        'order' => array(
            'type' => 'DESC',
            'column' => 'id'
        ),
    );

    $verification_users_req      = YX_FetchDataFromDB($fetch_verif_requests_array); 
    $verification_users_req_html = '';
    if (count($verification_users_req) > 0) {
        foreach ($verification_users_req as $yx['request']) {
            $verification_users_req_html .=  YX_LoadPage("admincp/verifications_requests/list", array(
                'REQUEST_ID'       => $yx['request']['id'],
                'REQUEST_USERNAME' => $yx['request']['name'],
                'REQUEST_TYPE'     => $yx['request']['type'],
                'REQUEST_AJAX'     => $yx['config']['site_url'] . '/ajax_requests.php?f=verification&s=load&id='.$yx['request']['id'],
            ));
        }
        $final_array['REQUESTS_DATA'] = $verification_users_req_html;
    }
    else{
        $final_array['REQUESTS_DATA'] = YX_LoadPage("admincp/verifications_requests/no-requests");
    }
    
    
}

if ($page == 'dashboard') {
    $count_users_array = array(
        'table' => T_USERS,
        'column' => 'user_id',
        'count' => true
    );
    $users = YX_FetchDataFromDB($count_users_array);
    $final_array['COUNT_USERS'] = $users[0]['count'];

    $count_news_array = array(
        'table' => T_NEWS,
        'column' => 'id',
        'count' => true
    );
    $news = YX_FetchDataFromDB($count_news_array);
    $final_array['COUNT_NEWS'] = $news[0]['count'];

    $count_lists_array = array(
        'table' => T_LISTS,
        'column' => 'id',
        'count' => true
    );
    $lists = YX_FetchDataFromDB($count_lists_array);
    $final_array['COUNT_LISTS'] = $lists[0]['count'];

    $count_videos_array = array(
        'table' => T_VIDEOS,
        'column' => 'id',
        'count' => true
    );
    $videos = YX_FetchDataFromDB($count_videos_array);
    $final_array['COUNT_VIDEOS'] = $videos[0]['count'];

    $count_music_array = array(
        'table' => T_MUSIC,
        'column' => 'id',
        'count' => true
    );
    $music = YX_FetchDataFromDB($count_music_array);
    $final_array['COUNT_MUSIC'] = $music[0]['count'];

    $count_polls_array = array(
        'table' => T_POLLS_PAGES,
        'column' => 'id',
        'count' => true
    );
    $polls = YX_FetchDataFromDB($count_polls_array);
    $final_array['COUNT_POLLS'] = $polls[0]['count'];



    $drafts = 0;

    $count_news_array = array(
        'table' => T_NEWS,
        'column' => 'id',
        'count' => true,
        'where' => array(
            array(
                'column' => 'active',
                'value' => '0',
                'mark' => '='
            ),
            array(
                'column' => 'viewable',
                'value' => '0',
                'mark' => '='
            ),
        )
    );
    $news = YX_FetchDataFromDB($count_news_array);
    $drafts += $news[0]['count'];

    $count_lists_array = array(
        'table' => T_LISTS,
        'column' => 'id',
        'count' => true,
        'where' => array(
            array(
                'column' => 'active',
                'value' => '0',
                'mark' => '='
            ),
            array(
                'column' => 'viewable',
                'value' => '0',
                'mark' => '='
            ),
        )
    );
    $lists = YX_FetchDataFromDB($count_lists_array);
    $drafts += $lists[0]['count'];

    $count_videos_array = array(
        'table' => T_VIDEOS,
        'column' => 'id',
        'count' => true,
        'where' => array(
            array(
                'column' => 'active',
                'value' => '0',
                'mark' => '='
            ),
            array(
                'column' => 'viewable',
                'value' => '0',
                'mark' => '='
            ),
        )
    );
    $videos = YX_FetchDataFromDB($count_videos_array);
    $drafts += $videos[0]['count'];

    $count_music_array = array(
        'table' => T_MUSIC,
        'column' => 'id',
        'count' => true,
        'where' => array(
            array(
                'column' => 'active',
                'value' => '0',
                'mark' => '='
            ),
            array(
                'column' => 'viewable',
                'value' => '0',
                'mark' => '='
            ),
        )
    );
    $music = YX_FetchDataFromDB($count_music_array);
    $drafts += $music[0]['count'];

    $count_polls_array = array(
        'table' => T_POLLS_PAGES,
        'column' => 'id',
        'count' => true,
        'where' => array(
            array(
                'column' => 'active',
                'value' => '0',
                'mark' => '='
            ),
            array(
                'column' => 'viewable',
                'value' => '0',
                'mark' => '='
            ),
        )
    );
    $polls = YX_FetchDataFromDB($count_polls_array);
    $drafts += $polls[0]['count'];

    $final_array['COUNT_DRAFTS'] = $drafts;

    $count_polls_array = array(
        'table' => T_POLLS_PAGES,
        'column' => 'id',
        'count' => true
    );
    $polls = YX_FetchDataFromDB($count_polls_array);
    $final_array['COUNT_POLLS'] = $polls[0]['count'];

    $online_users = 0;
    $count_online_array = array(
        'table' => T_USERS,
        'column' => 'user_id',
        'count' => true,
        'where' => array(
            array(
                'column' => 'active',
                'value' => '1',
                'mark' => '='
            ),
            array(
                'column' => 'last_active',
                'value' => time() - 60,
                'mark' => '>'
            ),
        )
    );
    $online_users = YX_FetchDataFromDB($count_online_array);
    $final_array['COUNT_ONLINE'] = $online_users[0]['count'];
}


if ($page == 'breaking_news') {
    $fetch_brnews_data_array = array(
        'table' => T_BR_NEWS,
        'column' => 'id',
        'limit' => 500000,
        'order' => array(
            'type' => 'DESC',
            'column' => 'id'
        ),
        'final_data' => array(
            array(
                'function_name' => 'YX_GetBrNews',
                'column' => 'id'
            )
        )
    );

    $breaking_news_list          = YX_FetchDataFromDB($fetch_brnews_data_array); 
    $breaking_news_list_html     = '';
    if (count($breaking_news_list) > 0) {
        foreach ($breaking_news_list as $yx['brnews']) {
            $breaking_news_list_html .=  YX_LoadPage("admincp/breaking_news/list", array(
                'BRNEWS_ID'       => $yx['brnews']['id'],
                'BRNEWS_EXPIRE'   => $yx['brnews']['expire'],
                'BRNEWS_URL'      => $yx['brnews']['url'],
                'BRNEWS_STATUS'   => $yx['brnews']['active'],
            ));
        }
        $final_array['BR_NEWS_DATA'] = $breaking_news_list_html;
    }
    else{
        $final_array['BR_NEWS_DATA'] = YX_LoadPage("admincp/breaking_news/no-news");
    }
    
    
}


if ($page == 'announcement') {
    $fetch_active_ann_data_array = array(
        'table' => T_ANNOUNCEMENT,
        'column' => '*',
        'limit' => 500000,
        'order' => array(
            'type' => 'DESC',
            'column' => 'id'
        ),
        'where' => array(
            array(
                'column' => 'active',
                'value' => '1',
                'mark' => '='
            )
        )
    );

    $active_announcement_list      = YX_FetchDataFromDB($fetch_active_ann_data_array); 
    $active_announcement_list_html = '';
    $table                         = T_ANNOUNCEMENT_VIEWS;
    foreach ($active_announcement_list as $yx['announcement']) {
        
        $yx['announcement']['time']  = YX_Time_Elapsed_String($yx['announcement']['time']);
        $yx['announcement']['views'] = YX_CountData(array( 0 => array(
            'column' => '`announcement_id`',
            'value' => $yx['announcement']['id'],
            'mark' => '='
        )),$table);

        $active_announcement_list_html .=  YX_LoadPage("admincp/announcement/active",array(
            'ANN_ID'    => $yx['announcement']['id'],
            'ANN_VIEWS' => $yx['announcement']['views'],
            'ANN_TEXT'  => YX_Decode($yx['announcement']['text']),
            'ANN_TIME'  => $yx['announcement']['time'],
        ));
    }

    $final_array['ANNOUNCEMENT_ACTIVE']   = $active_announcement_list_html;

    $fetch_inactive_ann_data_array = array(
        'table' => T_ANNOUNCEMENT,
        'column' => '*',
        'limit' => 500000,
        'order' => array(
            'type' => 'DESC',
            'column' => 'id'
        ),
        'where' => array(
            array(
                'column' => 'active',
                'value' => '0',
                'mark' => '='
            )
        )
    );

    $inactive_announcement_list      = YX_FetchDataFromDB($fetch_inactive_ann_data_array); 
    $inactive_announcement_list_html = '';
    foreach ($inactive_announcement_list as $yx['announcement']) {
        
        $yx['announcement']['time']  = YX_Time_Elapsed_String($yx['announcement']['time']);
        $yx['announcement']['views'] = YX_CountData(array( 0 => array(
            'column' => '`announcement_id`',
            'value' => $yx['announcement']['id'],
            'mark' => '='
        )),$table);
        $inactive_announcement_list_html .=  YX_LoadPage("admincp/announcement/inactive",array(
            'ANN_ID'    => $yx['announcement']['id'],
            'ANN_VIEWS' => $yx['announcement']['views'],
            'ANN_TEXT'  => YX_Decode($yx['announcement']['text']),
            'ANN_TIME'  => $yx['announcement']['time'],
        ));
    }

    $final_array['ANNOUNCEMENT_INACTIVE'] = $inactive_announcement_list_html;
  
}

if ($page == 'custom_fields') {
    $yx_profile_fields_list    = '';
    $fetch_prfields_data_array = array(
        'table' => T_PR_FIELDS,
        'column' => '*',
        'limit' => 500000,
        'order' => array(
            'type' => 'DESC',
            'column' => 'id'
        ),
        'where' => array(
            array(
                'column' => 'active',
                'value' => '1',
                'mark' => '='
            )
        )
    );
    
    $yx_profile_fields      = YX_FetchDataFromDB($fetch_prfields_data_array); 

    foreach ($yx_profile_fields as $yx['filed_data']) {
        if ($yx['filed_data']['select_type'] == 'yes') {
          $type  = 'Select box';
        }

        else if ($yx['filed_data']['type'] == 'textarea') {
          $type  = 'Text area';
        }

        else if ($yx['filed_data']['type'] == 'textbox') {
          $type  = 'Text box';
        } 

        else{
            $type  = 'Unknown';
        }  
          
        $yx_profile_fields_list .=  YX_LoadPage("admincp/custom_fields/list",array(
            'FIELD_ID'     => $yx['filed_data']['id'],
            'FIELD_NAME'   => $yx['filed_data']['name'],
            'FIELD_TYPE'   => $type,
            'FIELD_LEN'    => $yx['filed_data']['length'],
            'FIELD_PLACE'  => $yx['filed_data']['placement'],
            'EDIT'         => YX_Link('admincp/edit_field?id='.$yx['filed_data']['id']),
        ));
    }

    $final_array['YX_FILEDS_DATA'] = $yx_profile_fields_list;
  
}

if ($page == 'banned_ip') {
    $yx_banned_ip_list          = '';
    $fetch_banned_ip_data_array = array(
        'table' => T_BANNED_IPS,
        'column' => '*',
        'limit' => 500000,
        'order' => array(
            'type' => 'DESC',
            'column' => 'id'
        )
    );
    
    $yx['banned_ips']       = YX_FetchDataFromDB($fetch_banned_ip_data_array); 
    if (count($yx['banned_ips']) > 0) {
        foreach ($yx['banned_ips'] as $yx['banned_ip']) {     
            $yx_banned_ip_list .=  YX_LoadPage("admincp/banned_ip/list",array(
                'BANNEDIP_ID'     => $yx['banned_ip']['id'],
                'BANNEDIP_TIME'   => YX_Time_Elapsed_String($yx['banned_ip']['time']),
                'BANNEDIP_ADDR'   => $yx['banned_ip']['ip_address'],
            ));
        }
        $final_array['YX_BANNEDIP_DATA'] = $yx_banned_ip_list;
    }
    else{
        $final_array['YX_BANNEDIP_DATA'] = YX_LoadPage("admincp/banned_ip/no-bannedip");
    }

}

if ($page == 'post_reports') {
    $yx_post_reports_list       = '';
    $fetch_post_reports_data_array = array(
        'table' => T_REPORTS,
        'column' => '*',
        'limit' => 500000,
        'order' => array(
            'type' => 'DESC',
            'column' => 'id'
        )
    );
    
    $yx['post_reports']            = YX_FetchDataFromDB($fetch_post_reports_data_array); 
    if (count($yx['post_reports']) > 0) {
        foreach ($yx['post_reports'] as $yx['report']) {
            $reporter              = YX_UserData($yx['report']['user_id']);
            if (!empty($reporter)) {
                $yx_post_reports_list .=  YX_LoadPage("admincp/post_reports/list",array(
                    'REPORT_ID'       => $yx['report']['id'],
                    'REPORT_TIME'     => YX_Time_Elapsed_String($yx['report']['time']),
                    'REPORT_USER'     => $reporter['url'],
                    'REPORT_USERNAME' => $reporter['name'],
                    'REPORT_POST'     => YX_Link($yx['report']['type'] . "/" . $yx['report']['post_id']),
                ));
            }
            
        }

        $final_array['YX_REPORTS_DATA'] = $yx_post_reports_list;
    }
    else{
        $final_array['YX_REPORTS_DATA'] = YX_LoadPage("admincp/post_reports/no-reports");
    }
}

if ($page == 'custom_code') {
    $code                  = YX_CustomCode('g');
    $error                 = "/*\n\t{{ERROR}}\n*/";
    $error                 = str_replace("{{ERROR}}", $lang['error_found_while_loading'], $error);

    $final_array['HEADER'] = (
        (isset($code[0]) && trim(strlen($code[0])) > 0) ? $code[0] : ((isset($code[0])) ? $yx['config']['header_ccx'] : $error)
    );

    $final_array['FOOTER'] = (
        (isset($code[1]) && trim(strlen($code[1])) > 0) ? $code[1] : ((isset($code[1])) ? $yx['config']['footer_ccx'] : $error)
    );

    $final_array['STYLE']  = (
        (isset($code[2]) && trim(strlen($code[2])) > 0) ? $code[2] : ((isset($code[2])) ? $yx['config']['styles_ccx'] : $error)
    );

}


if ($page == 's3') {

    $final_array['S3_1']   = ($yx['config']['amazone_s3'] == 1)   ? ' checked' : '';
    $final_array['S3_0']   = ($yx['config']['amazone_s3'] == 0)   ? ' checked' : '';
    $final_array['ALERT']  = 'Amazon S3 require PHP 5.5 or higher to run, your php version is: ' . PHP_VERSION;
    
}



$yx['title']       = $yx['config']['name'] . ' | Admin Panel';
$yx['description'] = $yx['config']['description'];
$yx['keywords']    = $yx['config']['keywords'];
$yx['page']        = 'admincp';
$yx['content']     = YX_LoadPage('admincp/content', $final_array);
