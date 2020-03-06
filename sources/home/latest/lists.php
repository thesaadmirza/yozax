<?php   
$fetch_latest_lists_data_array = array(
    'table' => T_LISTS,
    'column' => 'id',
    'limit' => 8,
    'order' => array(
        'type' => 'desc',
        'column' => 'id'
    ),
    'where' => array(
        array(
            'column' => 'active',
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
$latest_lists                  = $yx['latest_lists'] = YX_FetchDataFromDB($fetch_latest_lists_data_array);


 ?>