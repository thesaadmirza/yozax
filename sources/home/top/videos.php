<?php 	
$fetch_top_videos_data_array    = array(
    'table' => T_VIDEOS,
    'column' => 'id',
    'limit' => 1,
    'order' => array(
        'type' => 'rand',
        'column' => 'id'
    ),
    'where' => array(
        array(
            'column' => 'active',
            'value' => '1',
            'mark' => '='
        ),
        array(
            'column' => 'featured',
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

$top_videos       = $yx['top_videos'] = YX_FetchDataFromDB($fetch_top_videos_data_array);

if (empty($top_videos)) {
    unset($fetch_top_videos_data_array['where'][1]);
    $top_videos   = $yx['top_videos'] = YX_FetchDataFromDB($fetch_top_videos_data_array);
}

$top_videos_html  = '';
foreach ($yx['top_videos'] as $key => $yx['top_video_data']) {
    $image_to_use = $yx['top_video_data']['video']['image'];
    if ($yx['top_video_data']['video']['hd'] == 1) {
        $url2_explode = explode('.', $image_to_use);
        $url2 = $url2_explode[0] . '_hd.' . $url2_explode[1];
        $yx['top_video_data']['video']['image'] = $url2;
    }
    $context_data = array(
        'TOP_VIDEO_URL' => $yx['top_video_data']['video']['url'],
        'TOP_VIDEO_IMAGE' => YX_GetMedia($yx['top_video_data']['video']['image']),
        'TOP_VIDEO_TITLE' => $yx['top_video_data']['video']['title'],
        'TOP_VIDEO_DESC' => $yx['top_video_data']['video']['description'],
        'TOP_VIDEO_POSTED' => $yx['top_video_data']['video']['posted'],
        'TOP_VIDEO_PUBLISHER__NAME' => $yx['top_video_data']['video']['publisher']['name'],
        'VERIFIED' => '',
    );

    if ($yx['top_video_data']['video']['publisher']['verified'] == 1) {
        $context_data['VERIFIED'] = '<span class="verified-icon"><i class="fa fa-check-circle"></i></span>';
    }

    $top_videos_html .= YX_Loadpage('home/lists/top-videos',$context_data);
}
?>