<?php
require(dirname(__FILE__)."/config.php");
CheckPurview('plus_文件管理器');
if(!isset($activepath))
{
	$activepath=$cfg_cmspath;
}
$inpath = "";
$activepath = str_replace("..","",$activepath);
$activepath = preg_replace("#^\/{1,}#", "/", $activepath);
if($activepath == "/")
{
	$activepath = "";
}
if($activepath == "")
{
	$inpath = $cfg_basedir;
}
else
{
	$inpath = $cfg_basedir.$activepath;
}
$activeurl = $activepath;
if(preg_match("#".$cfg_templets_dir."#i", $activepath))
{
	$istemplets = true;
}
else
{
	$istemplets = false;
}
include DedeInclude('templets/file_manage_main.htm');

?>