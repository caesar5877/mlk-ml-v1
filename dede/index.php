<?php
require_once(dirname(__FILE__)."/config.php");
require_once(DEDEINC.'/dedetag.class.php');
$defaultIcoFile = DEDEROOT.'/data/admin/quickmenu.txt';
$myIcoFile = DEDEROOT.'/data/admin/quickmenu-'.$cuserLogin->getUserID().'.txt';
if(!file_exists($myIcoFile)) $myIcoFile = $defaultIcoFile;
require(DEDEADMIN.'/inc/inc_menu_map.php');

if($cuserLogin->userType >= 10)
{
	include(DEDEADMIN.'/templets/index2.htm');
	exit();
}
else
{
	include(DEDEADMIN.'/templets/index1.htm');
	exit();
}

?>
