<?php 	
$fetch_top_lists_data_array    = array(
    'table' => T_LISTS,
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
            'function_name' => 'YX_GetLists',
            'column' => 'id',
            'name' => 'list'
        )
    )
);

$top_lists      = $yx['top_lists'] = YX_FetchDataFromDB($fetch_top_lists_data_array);
$top_lists_html = '';

if (empty($top_lists)) {
    unset($fetch_top_lists_data_array['where'][1]);
    $top_lists   = $yx['top_lists'] = YX_FetchDataFromDB($fetch_top_lists_data_array);
}

foreach ($yx['top_lists'] as $key => $yx['top_lists_data']) {
    $image_to_use = $yx['top_lists_data']['list']['image'];
    if ($yx['top_lists_data']['list']['hd'] == 1) {
        $url2_explode = explode('.', $image_to_use);
        $url2 = $url2_explode[0] . '_hd.' . $url2_explode[1];
        $yx['top_lists_data']['list']['image'] = $url2;
    }
    $context_data = array(
        'TOP_LIST_URL' => $yx['top_lists_data']['list']['url'],
        'TOP_LIST_IMAGE' => YX_GetMedia($yx['top_lists_data']['list']['image']),
        'TOP_LIST_TITLE' => $yx['top_lists_data']['list']['title'],
        'TOP_LIST_SHORT_TITLE' => $yx['top_lists_data']['list']['short_title'],
        'TOP_LIST_DESC' => $yx['top_lists_data']['list']['description'],
        'TOP_LIST_POSTED' => $yx['top_lists_data']['list']['posted'],
        'TOP_LIST_PUBLISHER__NAME' => $yx['top_lists_data']['list']['publisher']['name'],
        'VERIFIED' => '',
    );

    if ($yx['top_lists_data']['list']['publisher']['verified'] == 1) {
        $context_data['VERIFIED'] = '<span class="verified-icon"><i class="fa fa-check-circle"></i></span>';
    }

    $top_lists_html .= YX_Loadpage('home/lists/top-lists', $context_data);
}
?>