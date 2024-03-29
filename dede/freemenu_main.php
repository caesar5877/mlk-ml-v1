<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('c_FreeList');
require_once DEDEINC.'/channelunit.func.php';
setcookie("ENV_GOBACK_URL",$dedeNowurl,time()+3600,"/");
if(empty($pagesize))
{
	$pagesize = 18;
}
if(empty($pageno))
{
	$pageno = 1;
}
if(empty($dopost))
{
	$dopost = '';
}
if(empty($orderby))
{
	$orderby = 'aid';
}
if(empty($keyword))
{
	$keyword = '';
	$addget = '';
	$addsql = '';
}else
{
	$addget = '&keyword='.urlencode($keyword);
	$addsql = " where title like '%$keyword%' ";
}

//重载列表
if($dopost=='getlist')
{
	AjaxHead();
	GetTagList($dsql,$pageno,$pagesize,$orderby);
	exit();
}

//删除字段
else if($dopost=='del')
{
    $aid = preg_replace("#[^0-9]#", "", $aid);
	$dsql->ExecuteNoneQuery("Delete From #@__freelist where aid='$aid'; ");
	AjaxHead();
	GetTagList($dsql,$pageno,$pagesize,$orderby);
	exit();
}

//第一次进入这个页面
if($dopost=='')
{
	$row = $dsql->GetOne("Select count(*) as dd From #@__freelist $addsql ");
	$totalRow = $row['dd'];
	include(DEDEADMIN."/templets/freemenu_main.htm");
}

/**
 * 获得特定的Tag列表
 *
 * @param unknown_type $dsql
 * @param unknown_type $pageno
 * @param unknown_type $pagesize
 * @param unknown_type $orderby
 */
function GetTagList($dsql,$pageno,$pagesize,$orderby='aid')
{
	global $cfg_phpurl,$addsql;
	$start = ($pageno-1) * $pagesize;
	$printhead ="<table width='98%' border='0' cellpadding='1' cellspacing='1' align='center'  class='tbtitle' style='background:#E2F5BC;margin-bottom:5px;'>
		<tr align='center' bgcolor='#FBFCE2'>
          <td width='5%' class='tbsname'><a href='#' onclick=\"ReloadPage('aid')\"><u>ID</u></a></td>
		  <td width='20%' class='tbsname'>列表名称</td>
		  <td width='20%' class='tbsname'>模板文件</td>
		  <td width='5%' class='tbsname'><a href='#' onclick=\"ReloadPage('click')\"><u>点击</u></a></td>
		  <td width='15%' class='tbsname'>创建时间</td>
		  <td class='tbsname'>管理</td>
  		  </tr>\r\n";
	echo $printhead;
	$dsql->SetQuery("Select aid,title,templet,click,edtime,namerule,listdir,defaultpage,nodefault From #@__freelist $addsql order by $orderby desc limit $start,$pagesize ");
	$dsql->Execute();
	while($row = $dsql->GetArray())
	{
		$listurl = GetFreeListUrl($row['aid'],$row['namerule'],$row['listdir'],$row['defaultpage'],$row['nodefault']);
		$line = "
	<tr align='center' bgcolor='#FFFFFF' onMouseMove=\"javascript:this.bgColor='#FCFEDA';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\">
        <td>{$row['aid']}</td>
        <td> <a href='$listurl' target='_blank'>{$row['title']}</a> </td>
        <td> {$row['templet']} </td>
        <td> {$row['click']} </td>
        <td>".MyDate("y-m-d",$row['edtime'])."</td>
        <td> <a href='#' onclick='EditNote({$row['aid']})'>更改</a> |
    	<a href='#' onclick='CreateNote({$row['aid']})'>更新</a> |
     	<a href='#' onclick='DelNote({$row['aid']})'>删除</a>
	</td>
  </tr>";
		echo $line;
	}
	echo "</table>\r\n";
}

?>