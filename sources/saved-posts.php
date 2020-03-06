<?php
if ($yx['loggedin'] == false) {
	header("Location:" . $site_url);
	exit();
}
$yx['title'] = $lang['saved_posts'] . ' | ' . $yx['config']['title'];
$yx['description'] = $yx['config']['description'];
$yx['page'] = 'saved-posts';
$yx['keywords'] = $yx['config']['keywords'];

$saved_news = [];
$saved_lists = [];
$saved_videos = [];
$saved_music = [];
$saved_polls = [];
$saved_quizzes = [];

foreach ($yx['user']['fav_post'] as $value) {
    if(strpos($value,'n')!==false){ 
        $saved_news[] = str_replace("n-","",$value);
    }
    if(strpos($value,'l')!==false){
        $saved_lists[] = str_replace("l-","",$value);
    }
    if(strpos($value,'v')!==false){
        $saved_videos[] = str_replace("v-","",$value);
    }
    if(strpos($value,'m')!==false){
        $saved_music[] = str_replace("m-","",$value);
    }
    if(strpos($value,'p')!==false){
        $saved_polls[] = str_replace("p-","",$value);
    }
    if(strpos($value,'q')!==false){
        $saved_quizzes[] = str_replace("q-","",$value);
    }
}

$news = [];
foreach ($saved_news as $key => $value) { 
        $dataz = YX_SavedGetNews($value);
        if(!empty($dataz)){
            $news[] =  $dataz;
        }
}

$lists = [];
foreach ($saved_lists as $key => $value) { 
        $dataz = YX_GetSavedLists($value);
        if(!empty($dataz)){
            $lists[] =  $dataz;
        }
}

$videos = [];
foreach ($saved_videos as $key => $value) { 
        $dataz = YX_SavedGetVideos($value);
        if(!empty($dataz)){
            $videos[] =  $dataz;
        }
}

$music = [];
foreach ($saved_music as $key => $value) { 
        $dataz = YX_GetSavedMusic($value);
        if(!empty($dataz)){
            $music[] =  $dataz;
        }
}

$polls = [];
foreach ($saved_polls as $key => $value) { 
        $dataz = YX_GetSavedPolls($value);
        if(!empty($dataz)){
            $polls[] =  $dataz;
        }
}

$yx['get_news_saved'] = $news;
$yx['get_lists_saved'] = $lists;
$yx['get_videos_saved'] = $videos;
$yx['get_music_saved'] = $music;
$yx['get_polls_saved'] = $polls;
$yx['get_quizzes_saved'] = $quizzes;
//echo '<pre>';
//print_r($lists);
//exit;
$yx['content'] = YX_LoadPage('saved-posts/content', array(
	'COUNT_NEWS_DRAFTS' => count($news),
	'COUNT_LISTS_DRAFTS' => count($lists),
	'COUNT_VIDEOS_DRAFTS' => count($videos),
	'COUNT_MUSIC_DRAFTS' => count($music),
	'COUNT_POLLS_DRAFTS' => count($polls)
));