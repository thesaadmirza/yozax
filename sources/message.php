<?php 
$MyID = GetUserSession();
$to = 0;
if(!empty($_GET['to'])){
    $to = $_GET['to'];
}
 


$page_context=array(
    'ToID' =>$to
);
$yx['content']   = YX_LoadPage("message/content",$page_context);

?>



