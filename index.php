<?php
// @author Saad Mirza https://saadmirza.net

require_once('assets/init.php');

$page = '';
if (isset($_GET['link1'])) {
    $page = $_GET['link1'];
} 

else {
    $page = 'home';
} 

$og_meta_tags = '';

if ($yx['loggedin'] == true) {
    $update = YX_UpdateUserData($yx['user']['user_id'], array('last_active' => time()));
} 

else if (!empty($_SERVER['HTTP_HOST'])) {
    $server_scheme = @$_SERVER["HTTPS"];
    $pageURL = ($server_scheme == "on") ? "https://" : "http://";
    $http_url = $pageURL . $_SERVER['HTTP_HOST'];
    $url = parse_url($yx['config']['site_url']);
    if (!empty($url)) {
        if ($url['scheme'] == 'http') {
            if ($http_url != 'http://' . $url['host']) { 
             //empty
            }
        } 
        else {
            if ($http_url != 'https://' . $url['host']) { 
               //empty
            }
        }
    }
}




switch ($page) {
    case 'mobile': 
        include('sources/mobile/mobile.php');
        break;
    case 'mobile-store': 
            include('sources/mobile/mobile-store.php');
        break;
    case 'mobile-success': 
            include('sources/mobile/mobile-success.php');
        break;
    case 'brand-guidelines': 
        include('sources/brand-guidelines.php');
        break;
    case 'music-library': 
        include('sources/music-library.php');
        break;
    case 'notifications': 
        include('sources/notifications.php');
        break;
    case 'message': 
        include('sources/message.php');
        break;
    case 'home':
        include('sources/home.php');
        break;
    case 'activate':
        include('sources/activate.php');
        break;
    case 'login':
        include('sources/login.php');
        break;
    case 'register':
        include('sources/register.php');
        break;
    case 'timeline':
        include('sources/timeline.php');
        break;
    case 'logout':
        include('sources/logout.php');
        break;
    case 'forgot_password':
        include('sources/forgot_password.php');
        break;
    case 'reset-password':
        include('sources/reset_password.php');
        break;
    case 'profile':
        include('sources/profile.php');
        break;
    case 'settings':
        include('sources/settings.php');
        break;
    case 'create-new':
        include('sources/create_new.php');
        break;
    case 'news':
        include('sources/news.php');
        break;
    case 'lists':
        include('sources/lists.php');
        break;    
    case 'videos':
        include('sources/videos.php');
        break;   
    case 'music':
        include('sources/music.php');
        break;    
    case 'polls':
        include('sources/polls.php');
        break;
    case 'quiz':
        include('sources/quiz.php');
        break; 
    case 'delete-post':
        include('sources/delete_post.php');
        break;
    case 'edit-post':
        include('sources/edit_post.php');
        break;
    case 'admincp':
        header("Location: " . YX_Link('admin-cp'));
        exit();
        break;
    case 'search':
        include('sources/search.php');
        break;
    case 'latest-news':
        include('sources/latest-news.php');
        break;   
    case 'latest-lists':
        include('sources/latest-lists.php');
        break; 
    case 'latest-videos':
        include('sources/latest-videos.php');
        break; 
    case 'latest-music':
        include('sources/latest-music.php');
        break; 
    case 'latest-quizzes':
        include('sources/latest-quizzes.php');
        break;
    case 'latest-polls':
        include('sources/latest-polls.php');
        break; 
    case 'saved-drafts':
        include('sources/saved-drafts.php');
        break; 
    case 'saved-posts':
        include('sources/saved-posts.php');
        break; 
    case 'create-new-mobile':
        include('sources/create-new-mobile.php');
        break;
    case 'company':
        include('sources/company.php');
        break;
    case 'tags':
        include('sources/tags.php');
        break;
    case 'feeds':
        include('sources/feeds.php');
        break;
    case 'rss':
        include('sources/rss.php');
        break;
    case 'post_data':
        include('sources/post_data.php');
        break;
    case 'go_pro':
        include('sources/go_pro/content.php');
        break;
    case 'ads':
        include('sources/ads/ads.php');
        break;
    case 'create_ad':
        include('sources/ads/create.php');
        break;
    case 'edit_ad':
        include('sources/ads/edit_ad.php');
        break;
    
        
}


if (empty($yx['content'])) {
    include('sources/404.php');
}


$background        = '';
$extra_js          = '';
$over = '';
if ($yx['page'] == 'forgot_password' || $yx['page'] == 'register' || $yx['page'] == 'login') {
    $background = 'body{background: url(' . $config['theme_url'] . '/img/background.jpg) repeat fixed !important;}';
    $over = '<div class="overlay"></div>';
}
if ($yx['page'] == 'news' || $yx['page'] == 'lists' || $yx['page'] == 'polls' || $yx['page'] == 'videos' || $yx['page'] == 'music' || ($yx['page'] == 'quiz' && empty($yx['quiz-result']))) {
    switch ($yx['page']) {
        case 'news':
            $yx['og_meta'] = $yx['news'];
            break;
        case 'polls':
            $yx['og_meta'] = $yx['polls'];
            break;
        case 'lists':
            $yx['og_meta'] = $yx['lists'];
            break;
        case 'videos':
            $yx['og_meta'] = $yx['videos'];
            break;
        case 'music':
            $yx['og_meta'] = $yx['music'];
            break;
        case 'quiz':
            $yx['og_meta'] = $yx['quiz'];
            break;
    }
    $og_meta_tags = YX_Loadpage('header/og-meta', array(
        'OG_TITLE' => $yx['title'],
        'OG_DESC' =>  $yx['description'],
        'OG_IMAGE' => YX_GetMedia($yx['og_meta']['image'])
    ));
}
if ($yx['page'] == 'create_new' || $yx['page'] == 'edit-post') {
    $extra_js = YX_Loadpage('extra-js');
}

if ($yx['page'] == 'quiz' && !empty($yx['quiz-result'])) {
    $og_meta_tags = YX_Loadpage('header/og-meta', array(
        'OG_TITLE' => $yx['quiz-result']['title'],
        'OG_DESC' =>  $yx['quiz-result']['text'],
        'OG_IMAGE' => $yx['quiz-result']['image']
    ));
}

if ($yx['loggedin'] == true) {
    $header = YX_LoadPage('header/is-logged', array(
        'USER_DATA' => $yx['user'],
    ));
} 

else {
    $header = YX_LoadPage('header/not-logged');
}


/* Get active Breaking news */ 
$breaking_news_data   = "";
if ($yx['page'] != 'profile') {
    $fetch_brnews_data_array = array(
        'table' => T_BR_NEWS,
        'column' => 'id',
        'order' => array(
            'type' => 'DESC',
            'column' => 'id'
        ),
        'where' => array(
            array(
                'column' => 'expire',
                'value' =>  time(),
                'mark' => '>='
            ),
            array(
                'column' => 'active',
                'value' =>  1,
                'mark' => '='
            ),
        ),
        'final_data' => array(
            array(
                'function_name' => 'YX_GetBrNews',
                'column' => 'id'
            )
        )
    );
    
    $yx['breaking_news']  = YX_FetchDataFromDB($fetch_brnews_data_array);

    if (count($yx['breaking_news']) > 0) {
        $breaking_news_data = YX_LoadPage('br_news/content');
    }
}
 
/* Get active Announcements */ 
$announcement             = "";
if ($yx['loggedin'] === true && $yx['page'] != 'profile') {

    $yx['announcements']  = YX_GetAnnouncments();
    if(is_array($yx['announcements'])) {
        foreach ($yx['announcements'] as $yx['announcement']){
            $announcement   =  YX_LoadPage("announcement/content",array(
                'ANN_ID'    => $yx['announcement']['id'],
                'ANN_TEXT'  => YX_Decode($yx['announcement']['text']),
            ));
        }
    }
}

/* Get active Announcements */ 

$final_content = YX_LoadPage('container', array(
    'CONTAINER_TITLE' => $yx['title'],
    'CONTAINER_DESC' => $yx['description'],
    'CONTAINER_KEYWORDS' => $yx['keywords'],

    'OG_META_TAGS' => $og_meta_tags,

    'HEADER_HTML' => YX_LoadPage('header/content', array(
        'PAGE_IS_HOME' => ($yx['page_home'] == true || strpos($yx['page'], 'latest-') !== false) ? 'container-home' : '',
        'LOGGEDIN_HEADER' => $header,
		'ACTIVE_NAVBAR_HOME' => ($yx['page'] == 'home') ? 'active' : '',
        'ACTIVE_NAVBAR_NEWS' => ($yx['page'] == 'latest-news') ? 'active' : '',
        'ACTIVE_NAVBAR_LISTS' => ($yx['page'] == 'latest-lists') ? 'active' : '',
        'ACTIVE_NAVBAR_VIDEOS' => ($yx['page'] == 'latest-videos') ? 'active' : '',
        'ACTIVE_NAVBAR_MUSIC' => ($yx['page'] == 'latest-music') ? 'active' : '',
        'ACTIVE_NAVBAR_POLLS' => ($yx['page'] == 'latest-polls') ? 'active' : '',
        'ACTIVE_NAVBAR_QUZZES' => ($yx['page'] == 'latest-quizzes') ? 'active' : '',
    )),
    'FOOTER_HTML' => YX_LoadPage('footer/content'),

    'BACKGROUND_IMAGE' => $background,
    'OVER' => $over,
    'CONTAINER_SIZE' => ($yx['page_home'] == true || strpos($yx['page'], 'latest-') !== false) ? 'container-home' : '',
    'CONTENT_CONTAINER' => ($yx['page'] == 'profile') ? 'profile-content-container' : 'content-container',
    'LATEST_CONTAINER' => (strpos($yx['page'], 'latest-') !== false) ? 'latest-container' : '',
    'WHITE_BACKGROUND' => ($yx['page'] == 'news' || $yx['page'] == 'delete-post') ? 'class="news-page"' : '',

    'EXTRA_JS_CODE' => $extra_js,
    'BR_NEWS' => ($yx['page'] != 'login' && $yx['page'] != 'register' && $yx['page'] != 'reset_password' && $yx['page'] != 'forgot_password') ? $breaking_news_data: '',
    'ANNOUNCEMENT' => $announcement,

    'CONTAINER_CONTENT' => $yx['content'],
    'HEADER_AD' => YX_GetAd('header', false),
    'FOOTER_AD' => YX_GetAd('footer', false),
));

echo $final_content;
mysqli_close($sqlConnect);
unset($yx);
