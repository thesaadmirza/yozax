<?php 

$story_page_ads = yx_get_user_ads(2,2);
$index          = 1;

foreach ($story_page_ads as $yx['ad']) {

    $yx['ad']   = YX_ObjectToArray($yx['ad']);
    $ad_context = array(
        'URL' => $yx['ad']['url'],
        'NAME' => $yx['ad']['title'],
        'IMAGE' => YX_GetMedia($yx['ad']['media_file']),
        'ATTR_ID' => '',
        'AD' => '',
    );

    if (!yx_adcon_exists($yx['ad']['id'])) {
        $ad_context['ATTR_ID'] = sprintf('data-id="%u"',$yx['ad']['id']);
        $ad_context['AD'] = ' ad';
    }

    $page_context["SP_UAD_$index"] = YX_LoadPage("ads/ad",$ad_context);

    $index++;
}

$sidebar_ads = yx_get_user_ads(1,3);
$index       = 1;

foreach ($sidebar_ads as $yx['ad']) {

	$yx['ad']   = YX_ObjectToArray($yx['ad']);
    $ad_context = array(
        'URL' => $yx['ad']['url'],
        'NAME' => $yx['ad']['title'],
        'IMAGE' => YX_GetMedia($yx['ad']['media_file']),
        'ATTR_ID' => '',
        'AD' => '',
    );

    if (!yx_adcon_exists($yx['ad']['id'])) {
        $ad_context['ATTR_ID'] = sprintf('data-id="%u"',$yx['ad']['id']);
        $ad_context['AD'] = ' ad';
    }

    $page_context["SB_UAD_$index"] = YX_LoadPage("ads/ad",$ad_context);

    $index++;
}
