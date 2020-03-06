<?php
if (empty($_GET['id'])) {
    header("Location:" . YX_Link('404'));
    exit();
}



$page = (isset($_GET['page'])) ? (int) $_GET['page'] : 1;
$id   = 0;

if (!empty($_GET['publish'])) {
    if ($_GET['publish'] == 'true') {
        $id = @end(explode('-', $_GET['id']));
        $form_data_array = array('viewable' => 1);
        if (YX_IsAdmin() == true || $yx['config']['review_posts'] == 0) {
            $form_data_array['active'] = 1;
        }
        $update = YX_UpdatePost($id, $form_data_array, 'news');
    }
}

$news = $yx['news'] = YX_GetPost($_GET['id'], $page, 'news');

if (empty($news)) {
    header("Location:" . YX_Link('404'));
    exit();
}
$yx['is_post_owner'] = YX_IsPostOwner($news['id'], 'news');
if ($news['viewable'] == 0) {
    if ($yx['loggedin'] == false) {
        header("Location:" . YX_Link('404'));
        exit();
    } else if ($yx['is_post_owner'] === false) {
        header("Location:" . YX_Link('404'));
        exit();
    }
}
if ($news['active'] == 0) {
    if ($yx['loggedin'] == false) {
        header("Location:" . YX_Link('404'));
        exit();
    } else if ($yx['is_post_owner'] === false) {
        header("Location:" . YX_Link('404'));
        exit();
    }
}
if (empty($news['entries'][0])) {
    header("Location:" . YX_Link('404'));
    exit();
}

$update_views = YX_UpdateViews('news', $news['id']);

$next_link = "";
$back_link = "";

if ($news['entries_per_page'] > 0) {
    if ($page != $news['entries']['totalPages'] && $news['entries']['totalPages'] != 0) {
        $next_link = "<a class='btn btn-main' href='?page=" . ($page + 1) . "'><i class='fa fa-arrow-right'></i> " . $lang['next_page'] . "</a>";
    }
    if (($page != 1) || ($page == $news['entries']['totalPages'] && $page > 1)) {
        $back_link = "<a class='btn btn-main' href='?page=" . ($page - 1) . "'><i class='fa fa-arrow-left'></i> " . $lang['previous_page'] . "</a>";
    }
}

$yx['next_link']   = $next_link;
$yx['back_link']   = $back_link;
$yx['title']       = $news['title'];
$yx['description'] = $news['description'];
$yx['page']        = 'news';
$yx['keywords']    = $news['tags'];

$create_session    = YX_CreateSession();

$news_admin_option = '';
$publish_button = '';

if ($yx['is_post_owner'] == true) {
	$news_admin_option = YX_LoadPage("story/admin-options");
	if ($yx['news']['active'] == 0 && $yx['news']['viewable'] == 1) {
		$publish_button = '&nbsp; |<span class="btn approve"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22C6.47,22 2,17.5 2,12A10,10 0 0,1 12,2M12.5,7V12.25L17,14.92L16.25,16.15L11,13V7H12.5Z"></path></svg> ' . $lang['waiting_for_approval'] . '</span>';
	} else if ($yx['news']['viewable'] == 0 && $yx['news']['active'] == 0) {
        $publish_button = '&nbsp; |<a class="btn publish" href="?publish=true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M5,4V6H19V4H5M5,14H9V20H15V14H19L12,7L5,14Z"></path></svg> ' . $lang['publish'] . '</a>';
	}
}

$share_onclick = "YX_ShareLink(this.href, event,"  . $yx['news']['id'] . ", 'news');";
$share_twitter_onclick = "YX_ShareLink('none', event,"  . $yx['news']['id'] . ", 'news');";

$tags = '';
foreach (YX_GetPostTags($yx['news']['tags']) as $key => $tag) {
    $tags .= '<a class="tags news" href="{{LINK tags/' . $tag . '}}">' . $tag . '</a> ';
}
$yx['story']['is_active'] = 0;
if ($yx['news']['active'] == 1) {
   $yx['story']['is_active'] = 1;
}

if ($yx['loggedin'] === true) {
    
    $id = @end(explode('-', $_GET['id']));

    if (!is_numeric($id)) {
        preg_match('/^(?:.+)\-(?P<id>[0-9]+)(?:\.html|)$/',$id,$matches);
        if (!empty($matches['id']) && is_numeric($matches['id'])) {
            $id = $matches['id'];
        }
    }

    $yx['reported'] = false;
    $table          = T_REPORTS;
    $page           = $yx['page']; 
    $user_id        = $yx['user']['user_id'];
    $where          = array();
    $query_cols     = array('`post_id`' => $id,'`user_id`' => $user_id,'`type`' => $page);
    foreach ($query_cols as $col => $col_val) {
        $where[] = array(
            'column' => $col,
            'value'  => $col_val,
            'mark'   => '=',
        );
    }
    $user_reports       = YX_CountData($where,$table);
    if (is_numeric($user_reports) && $user_reports > 0) {
        $yx['reported'] = true;
    }
}


$comments = '';
foreach (YX_GetStoryComments(array('post_id' => $yx['news']['id'],'page' => $yx['page'])) as $key => $yx['comment']) {
    $replies            = '';
    foreach ($yx['comment']['replies'] as $yx['reply']) {
        $replies           .=  YX_LoadPage("comment/comment-reply", array(
        'REPLY_ID'           => $yx['reply']['id'],
        'COMM_ID'            => $yx['reply']['comment'],
        'POST_ID'            => $yx['reply']['news_id'],
        'REPLY_TEXT'         => $yx['reply']['text'],
        'REPLY_TIME'         => YX_Time_Elapsed_String($yx['reply']['time']),
        'REPLY_USER_NAME'    => $yx['reply']['user_data']['name'],
        'REPLY_USER_URL'     => $yx['reply']['user_data']['url'],
        'USER_VERIFIED'      => ($yx['reply']['user_data']['verified'] == 1) ? '<span class="verified-icon"><i class="fa fa-check-circle"></i></span>' : '',
        'REPLY_USER_AVATAR'  => $yx['reply']['user_data']['avatar'],
        ));
    }

    $comments          .=  YX_LoadPage("comment/comment-content", array(
    'COMM_ID'           => $yx['comment']['id'],
    'POST_ID'           => $yx['news']['id'],
    'OWNER'             => $yx['comment']['owner'],
    'STORY_PAGE'        => $yx['page'],
    'COMM_TEXT'         => $yx['comment']['text'],
    'COMM_TIME'         => YX_Time_Elapsed_String($yx['comment']['time']),
    'COMM_USER_NAME'    => $yx['comment']['user_data']['name'],
    'USER_VERIFIED'     => ($yx['comment']['user_data']['verified'] == 1) ? '<span class="verified-icon"><i class="fa fa-check-circle"></i></span>' : '',
    'COMM_USER_URL'     => $yx['comment']['user_data']['url'],
    'COMM_USER_AVATAR'  => $yx['comment']['user_data']['avatar'],
    'COMM_REPLIES'      => $replies,
    ));
}

$yx['user_reactions']  = YX_GetPercentageOfReactions($yx['news']['id'],$yx['page']);
$user_reactions        = YX_LoadPage('story/user-reactions',array(
    'STORY_ID' => $yx['news']['id'],
    'STORY_PAGE' => $yx['page']
));


$page_context = array(
    'STORY_ID' => $yx['news']['id'],
    'STORY_TITLE' => $yx['news']['title'],
    'STORY_THUMB' => YX_GetMedia($yx['news']['image']),
    'STORY_DESC' => $yx['news']['description'],
    'STORY_ADMIN_OPTIONS' => $news_admin_option,
    'STORY_VIEWS' => $yx['news']['views'],
    'STORY_ENTRIES' => YX_ForEachEntries($yx['news']['entries']),
    'STORY_AD' => YX_GetAd('between', false),
    'CATEGORY_NAME' => (!empty($yx['news_categories'][$yx['news']['category']])) ? $yx['news_categories'][$yx['news']['category']]: '',
    'CATEGORY_LINK' => '{{LINK latest-news/' . $yx['news']['category'] . '}}',
    'STORY_POSTED_TIME' => $yx['news']['posted'],
    'STORY_UPDATED_TIME' => $yx['news']['updated'],
    'STORY_LINK' => $yx['news']['url'],
    'STORY_ENCODED_URL' => urlencode($yx['news']['url']),
    'STORY_SHARES' => $yx['news']['shares'],

    'NEXT_LINK' => $next_link,
    'BACK_LINK' => $back_link,
    
    'EDIT_BUTTON' => YX_Link('edit-post/' . $yx['news']['id'] . '?type=news'),
    'DELETE_BUTTON' => YX_Link('delete-post/' . $yx['news']['id'] . '?type=news'),
    'PUBISH_BUTTON' => $publish_button,
    
    'PUBLISHER_NAME' => $yx['news']['publisher']['name'],
    'PUBLISHER_AVATAR' => $yx['news']['publisher']['avatar'],
    'PUBLISHER_VERIFIED' => ($yx['news']['publisher']['verified'] == 1) ? '<span class="verified-icon"><i class="fa fa-check-circle"></i></span>' : '',
    'PUBLISHER_URL' => $yx['news']['publisher']['url'],
    'PUBLISHER_GENDER' => ($yx['news']['publisher']['gender'] == 'male') ? $yx['lang']['male'] : $yx['lang']['female'],
    'PUBLISHER_COUNTRY' => (!empty($yx['news']['publisher']['country_id'])) ? ', Lives in ' . $yx['countries_name'][$yx['news']['publisher']['country_id']] : '',

    'NEW_SESSION_HASH' => $create_session,
    'SIDEBAR_HTML' => YX_LoadPage('sidebar/content', array('SIDEBAR_AD' => YX_GetAd('sidebar', false))),
    'MY_ID' => $yx['my_id'],
    'SHARE_BUTTON_ONE' => $share_onclick,
    'SHARE_BUTTON_TWITTER' => $share_twitter_onclick,

    'STORY_TAGS' => $tags,
    'STORY_COMMENT_TOTAL' => YX_CountPostComments($yx['news']['id'],$yx['page']),
    'STORY_COMMENTS' => $comments,
    'STORY_PAGE' => $yx['page'],
    'USER_REACTIONS' => $user_reactions,
    
    'SP_UAD_1' => '',
    'SP_UAD_2' => '',

    'SB_UAD_1' => '',
    'SB_UAD_2' => '',
    'SB_UAD_3' => '',
);


require_once('sources/ads/load_sp_ads.php');

$yx['content'] = YX_LoadPage("story/content",$page_context);

