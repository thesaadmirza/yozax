<?php 
$fetch_top_polls_data_array    = array(
    'table' => T_POLLS_PAGES,
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
            'function_name' => 'YX_GetPolls',
            'column' => 'id',
            'name' => 'poll'
        )
    )
);

$top_polls      = $yx['top_polls'] = YX_FetchDataFromDB($fetch_top_polls_data_array);
$top_polls_html = '';

if (empty($top_polls)) {
    unset($fetch_top_polls_data_array['where'][1]);
    $top_polls   = $yx['top_polls'] = YX_FetchDataFromDB($fetch_top_polls_data_array);
}

foreach ($yx['top_polls'] as $key => $yx['top_polls_data']) {
    $image_to_use = $yx['top_polls_data']['poll']['image'];
    if ($yx['top_polls_data']['poll']['hd'] == 1) {
        $url2_explode = explode('.', $image_to_use);
        $url2 = $url2_explode[0] . '_hd.' . $url2_explode[1];
        $yx['top_polls_data']['poll']['image'] = $url2;
    }
    $context_data = array(
        'TOP_POLL_URL' => $yx['top_polls_data']['poll']['url'],
        'TOP_POLL_IMAGE' => YX_GetMedia($yx['top_polls_data']['poll']['image']),
        'TOP_POLL_TITLE' => $yx['top_polls_data']['poll']['title'],
        'TOP_POLL_DESC' => $yx['top_polls_data']['poll']['description'],
        'TOP_POLL_POSTED' => $yx['top_polls_data']['poll']['posted'],
        'TOP_POLL_PUBLISHER__NAME' => $yx['top_polls_data']['poll']['publisher']['name'],
        'VERIFIED' => '',
    );

    if ($yx['top_polls_data']['poll']['publisher']['verified'] == 1) {
        $context_data['VERIFIED'] = '<span class="verified-icon"><i class="fa fa-check-circle"></i></span>';
    }

    $top_polls_html .= YX_Loadpage('home/lists/top-polls',$context_data);
}


?>