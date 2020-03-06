<?php
$yx['page'] = 'mobile';
$yx['title'] = $lang['mobile'] . ' | ' . $yx['config']['title'];
$yx['description'] = $yx['config']['description'];
$yx['keywords'] = $yx['config']['keywords'];


//Function YX_IsMobileSubscriber() does also exist
//We are just not currently using it

if (YX_IsOnMobileWaitlist() == false){
$yx['content'] = YX_LoadPage('mobile/landing');
}
//else if already mobile subscriber - show dashboard
else{
    $yx['content'] = YX_LoadPage('mobile/waitlist');
}


// Here we will create a handler:
// 1. is user a mobile subscriber?
// 2. is waiting?
// if waiting - show waitlist
// if not show dashboard
// at the moment we just check if is subscriber - this needs to change to is waiting
// or somehow otherwise integrate the onboarding
