<?php 

/***********************************************************************************************************************************
	Author - Prokna ( http://github.com/prokna )
	PHP class Example for facecook Graph API
***********************************************************************************************************************************/

session_start();
require_once("fb_graph.class.php"); 

$app_id			=	"###############";					//application Id
$app_secret		=	"#######################";			//application secret
$callback_url	=	"###########################";		//redirected url


$fb=new fbgraph($app_id,$app_secret,$callback_url);

$permissons ='publish_stream'; //Set your permissions here REF:- http://developers.facebook.com/docs/authentication/permissions/

if($_SESSION['auth_token']=='' OR $_REQUEST["code"]!=''){
	$_SESSION['auth_token'] = $fb->login($permissons);
}
else{
$fb->setToken($_SESSION['auth_token']);
}

$usr_info  = $fb->me();
echo'<pre>';
print_r($usr_info);
?>