<?php
require_once(dirname(__FILE__)."/../config.php");

//载入可发布频道
$addset = '';
$dsql->SetQuery("Select id,typename,addcon,mancon From `#@__channeltype` where id<>-1 And isshow=1 order by id asc");
$dsql->Execute();
while($row = $dsql->GetObject())
{
	$addset .= "  <m:item name='{$row->typename}' ischannel='1' link='{$row->mancon}?channelid={$row->id}' linkadd='{$row->addcon}?channelid={$row->id}' channelid='{$row->id}' rank='' target='main' />\r\n";
}

//载入插件菜单
$plusset = '';
$dsql->SetQuery("Select * From `#@__plus` where isshow=1 order by aid asc");
$dsql->Execute();
while($row = $dsql->GetObject()) {
	$plusset .= $row->menustring."\r\n";
}

$menusMain = "
-----------------------------------------------

<m:top item='1_' name='常用操作' display='block'>
  <m:item name='网站栏目管理' link='catalog_main.php' rank='t_List,t_AccList' target='main' />
  $addset
  <m:item name='人才招聘管理' link='job_main.php' rank='info_Job' target='main' />
  <m:item name='自定义资料' link='lang_main.php' rank='info_Lang' target='main' />
  <m:item name='留言本管理' link='catalog_do.php?dopost=guestbook' rank='plus_留言簿模块' target='main' />
  <m:item name='评论管理' link='feedback_main.php' rank='sys_Feedback' target='main' />
  <m:item name='附件数据管理' link='media_main.php' rank='sys_Upload,sys_MyUpload' target='main' />
  <m:item name='所有文档列表' link='content_list.php' rank='a_List,a_AccList' target='main' />
  <m:item name='内容回收站' link='recycling.php' ischannel='1' addalt='清空回收站' addico='img/gtk-del.png' linkadd='archives_do.php?dopost=clear&aid=no' rank='a_List' target='main' />
</m:top>

<m:top item='1_' name='会员管理' display='block' rank='member_List,member_Type'>
  <m:item name='注册会员列表' link='member_main.php' rank='member_List' target='main' />
  <m:item name='会员短信管理' link='member_pm.php' rank='member_Type' target='main' />
</m:top>

<m:top item='1_' name='辅助插件' display='block'>
  $plusset
</m:top>

<m:top item='1_' name='HTML更新' display='block' rank='sys_MakeHtml'>
  <m:item name='一键更新网站' link='makehtml_all.php' rank='sys_MakeHtml' target='main' />
  <m:item name='更新栏目HTML' link='makehtml_list.php' rank='sys_MakeHtml' target='main' />
  <m:item name='更新文档HTML' link='makehtml_archives.php' rank='sys_MakeHtml' target='main' />
</m:top>

-----------------------------------------------
";

?>