<?php

if (empty($_GET['id'])) {
    header("Location:" . YX_Link('404'));
    exit();
}


$page = (isset($_GET['page'])) ? (int) $_GET['page'] : 1;
if (!empty($_GET['publish'])) {
    if ($_GET['publish'] == 'true') {
         $id = @end(explode('-', $_GET['id']));
        $form_data_array = array('viewable' => 1);
        if (YX_IsAdmin() == true || $yx['config']['review_posts'] == 0) {
            $form_data_array['active'] = 1;
        }
        $update = YX_UpdatePost($id, $form_data_array, 'poll');
    }
}
$polls = $yx['polls'] = YX_GetPost($_GET['id'], $page, 'polls');

if (empty($polls)) {
    header("Location:" . YX_Link('404'));
    exit();
}
$yx['is_post_owner'] = YX_IsPostOwner($polls['id'], 'polls');
if ($polls['viewable'] == 0) {
    if ($yx['loggedin'] == false) {
        header("Location:" . YX_Link('404'));
        exit();
    } 
    else if ($yx['is_post_owner'] === false) {
        header("Location:" . YX_Link('404'));
        exit();
    }
}
if ($polls['active'] == 0) {
    if ($yx['loggedin'] == false) {
        header("Location:" . YX_Link('404'));
        exit();
    } else if ($yx['is_post_owner'] === false) {
        header("Location:" . YX_Link('404'));
        exit();
    }
}
if (empty($polls['entries'][0])) {
    header("Location:" . YX_Link('404'));
    exit();
}

$update_views = YX_UpdateViews('polls', $polls['id']);

$next_link = "";
$back_link = "";

if ($polls['entries_per_page'] > 0) {
    if ($page != $polls['entries']['totalPages'] && $polls['entries']['totalPages'] != 0) {
        $next_link = "<a class='btn btn-main' href='?page=" . ($page + 1) . "'><i class='fa fa-arrow-right'></i> " . $lang['next_page'] . "</a>";
    }
    if (($page != 1) || ($page == $polls['entries']['totalPages'] && $page > 1)) {
        $back_link = "<a class='btn btn-main' href='?page=" . ($page - 1) . "'><i class='fa fa-arrow-left'></i> " . $lang['previous_page'] . "</a>";
    }
}

$yx['next_link']   = $next_link;
$yx['back_link']   = $back_link;
$yx['title']       = $polls['title'];
$yx['description'] = $polls['description'];
$yx['page']        = 'polls';
$yx['keywords']    = $polls['tags'];

$create_session    = YX_CreateSession();

$lists_admin_option = '';
$publish_button = '';

if ($yx['is_post_owner'] == true) {
	$lists_admin_option = YX_LoadPage("story/admin-options");
	if ($yx['polls']['active'] == 0 && $yx['polls']['viewable'] == 1) {
		$publish_button = '&nbsp; |<span class="btn approve"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22C6.47,22 2,17.5 2,12A10,10 0 0,1 12,2M12.5,7V12.25L17,14.92L16.25,16.15L11,13V7H12.5Z"></path></svg> ' . $lang['waiting_for_approval'] . '</span>';
	} else if ($yx['polls']['viewable'] == 0 && $yx['polls']['active'] == 0) {
        $publish_button = '&nbsp; |<a class="btn publish" href="?publish=true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M5,4V6H19V4H5M5,14H9V20H15V14H19L12,7L5,14Z"></path></svg> ' . $lang['publish'] . '</a>';
	}
}

$share_onclick = "YX_ShareLink(this.href, event,"  . $yx['polls']['id'] . ", 'polls');";
$share_twitter_onclick = "YX_ShareLink('none', event,"  . $yx['polls']['id'] . ", 'polls');";
$tags = '';
foreach (YX_GetPostTags($yx['polls']['tags']) as $key => $tag) {
    $tags .= '<a class="tags polls" href="{{LINK tags/' . $tag . '}}">' . $tag . '</a> ';
}
$yx['story']['is_active'] = 0;
if ($yx['polls']['active'] == 1) {
   $yx['story']['is_active'] = 1;
}

if ($yx['loggedin'] === true) {
    $yx['reported'] = false;
    $id             = @end(explode('-', $_GET['id']));

    if (!is_numeric($id)) {
        preg_match('/^(?:.+)\-(?P<id>[0-9]+)(?:\.html|)$/',$id,$matches);
        if (!empty($matches['id']) && is_numeric($matches['id'])) {
            $id = $matches['id'];
        }
    }

    $table       = T_REPORTS;
    $page        = $yx['page']; 
    $user_id     = $yx['user']['user_id'];
    $where       = array();
    $query_cols  = array('`post_id`' => $id,'`user_id`' => $user_id,'`type`' => $page);
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
foreach (YX_GetStoryComments(array('post_id' => $yx['polls']['id'],'page' => $yx['page'])) as $key => $yx['comment']) {
    $replies            = '';
    foreach ($yx['comment']['replies'] as $yx['reply']) {
        $replies           .=  YX_LoadPage("comment/comment-reply", array(
        'REPLY_ID'           => $yx['reply']['id'],
        'COMM_ID'            => $yx['reply']['comment'],
        'POST_ID'            => $yx['reply']['news_id'],
        'REPLY_TEXT'         => $yx['reply']['text'],
        'REPLY_TIME'         => YX_Time_Elapsed_String($yx['reply']['time']),
        'REPLY_USER_NAME'    => $yx['reply']['user_data']['name'],
        'USER_VERIFIED'      => ($yx['reply']['user_data']['verified'] == 1) ? '<span class="verified-icon"><i class="fa fa-check-circle"></i></span>' : '',
        'REPLY_USER_URL'     => $yx['reply']['user_data']['url'],
        'REPLY_USER_AVATAR'  => $yx['reply']['user_data']['avatar'],
        ));
    }

    $comments          .=  YX_LoadPage("comment/comment-content", array(
    'COMM_ID'           => $yx['comment']['id'],
    'POST_ID'           => $yx['polls']['id'],
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

$yx['user_reactions']  = YX_GetPercentageOfReactions($yx['polls']['id'],$yx['page']);
$user_reactions        = YX_LoadPage('story/user-reactions',array(
    'STORY_ID' => $yx['polls']['id'],
    'STORY_PAGE' => $yx['page']
));

$page_context = array(
    'STORY_ID' => $yx['polls']['id'],
    'STORY_TITLE' => $yx['polls']['title'],
    'STORY_THUMB' => YX_GetMedia($yx['polls']['image']),
    'STORY_DESC' => $yx['polls']['description'],
    'STORY_ADMIN_OPTIONS' => $lists_admin_option,
    'STORY_VIEWS' => $yx['polls']['views'],
    'STORY_ENTRIES' => YX_ForEachEntries($yx['polls']['entries']),
    'STORY_AD' => YX_GetAd('between', false),
    'CATEGORY_NAME' => (!empty($yx['poll_categories'][$yx['polls']['category']])) ? $yx['poll_categories'][$yx['polls']['category']]: '',
    'CATEGORY_LINK' => '{{LINK latest-polls/' . $yx['polls']['category'] . '}}',
    'STORY_POSTED_TIME' => $yx['polls']['posted'],
    'STORY_UPDATED_TIME' => $yx['polls']['updated'],
    'STORY_LINK' => $yx['polls']['url'],
    'STORY_ENCODED_URL' => urlencode($yx['polls']['url']),
    'STORY_SHARES' => $yx['polls']['shares'],

    'NEXT_LINK' => $next_link,
    'BACK_LINK' => $back_link,
    
    'EDIT_BUTTON' => YX_Link('edit-post/' . $yx['polls']['id'] . '?type=polls'),
    'DELETE_BUTTON' => YX_Link('delete-post/' . $yx['polls']['id'] . '?type=polls'),
    'PUBISH_BUTTON' => $publish_button,
    
    'PUBLISHER_NAME' => $yx['polls']['publisher']['name'],
    'PUBLISHER_AVATAR' => $yx['polls']['publisher']['avatar'],
    'PUBLISHER_VERIFIED' => ($yx['polls']['publisher']['verified'] == 1) ? '<span class="verified-icon"><i class="fa fa-check-circle"></i></span>' : '',
    'PUBLISHER_URL' => $yx['polls']['publisher']['url'],
    'PUBLISHER_GENDER' => ($yx['polls']['publisher']['gender'] == 'male') ? $yx['lang']['male'] : $yx['lang']['female'],
    'PUBLISHER_COUNTRY' => (!empty($yx['polls']['publisher']['country_id'])) ? ', Lives in ' . $yx['countries_name'][$yx['polls']['publisher']['country_id']] : '',

    'NEW_SESSION_HASH' => $create_session,
    'SIDEBAR_HTML' => YX_LoadPage('sidebar/content', array('SIDEBAR_AD' => YX_GetAd('sidebar', false))),
    'MY_ID' => $yx['my_id'],
    'SHARE_BUTTON_ONE' => $share_onclick,
    'SHARE_BUTTON_TWITTER' => $share_twitter_onclick,

    'STORY_TAGS' => $tags,
    'STORY_COMMENT_TOTAL' => YX_CountPostComments($yx['polls']['id'],$yx['page']),
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
