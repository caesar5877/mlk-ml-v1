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
  <m:item name='网站栏目管理' link='catalog_main.php' ischannel='1' addalt='创建栏目' linkadd='catalog_add.php?listtype=all' rank='t_List,t_AccList' target='main' />
  <m:item name='人才招聘管理' link='job_main.php' rank='info_Job' target='main' />
  <m:item name='自定义资料' link='lang_main.php' rank='info_Lang' target='main' />
  <m:item name='评论管理' link='feedback_main.php' rank='sys_Feedback' target='main' />
  <m:item name='留言本管理' link='catalog_do.php?dopost=guestbook' rank='plus_留言簿模块' target='main' />
	<m:item name='所有文档列表' link='content_list.php' rank='a_List,a_AccList' target='main' />
  <m:item name='等审核的文档' link='content_list.php?arcrank=-1' rank='a_Check,a_AccCheck' target='main' />
  <m:item name='我发布的文档' link='content_list.php?mid=".$cuserLogin->getUserID()."' rank='a_List,a_AccList,a_MyList' target='main' />
</m:top>

<m:top item='1_' name='内容管理' display='block'>
  $addset
  <m:item name='内容回收站' link='recycling.php' ischannel='1' addalt='清空回收站' addico='img/gtk-del.png' linkadd='archives_do.php?dopost=clear&aid=no' rank='a_List' target='main' />
</m:top>

<m:top item='1_' name='会员管理' display='none' rank='member_List,member_Type'>
  <m:item name='注册会员列表' link='member_main.php' rank='member_List' target='main' />
  <m:item name='会员短信管理' link='member_pm.php' rank='member_Type' target='main' />
  <m:item name='会员等级管理' link='member_rank.php' rank='member_Type' target='main' />
</m:top>

<m:top item='1_' name='附件管理' display='none' rank='sys_Upload,sys_MyUpload,plus_文件管理器'>
  <m:item name='上传新文件' link='media_add.php' rank='' target='main' />
  <m:item name='附件数据管理' link='media_main.php' rank='sys_Upload,sys_MyUpload' target='main' />
  <m:item name='文件式管理器' link='media_main.php?dopost=filemanager' rank='plus_文件管理器' target='main' />
</m:top>

<m:top item='1_5' name='批量维护' display='block'>
  <m:item name='搜索关键词维护' link='search_keywords_main.php' rank='sys_Keyword' target='main' />
  <m:item name='更新系统缓存' link='sys_cache_up.php' rank='sys_ArcBatch' target='main' />
</m:top>

<m:top item='1_' name='系统帮助' display='block'>
  <m:item name='参考文档' link='http://help.dedecms.com' rank='' target='_blank' />
  <m:item name='官方交流论坛' link='http://bbs.dedecms.com' rank='' target='_blank' />
</m:top>

<m:top item='5_' name='辅助插件' display='block'>
  <m:item name='插件管理器' link='plus_main.php' rank='sys_Edit' target='main' />
  $plusset
</m:top>

<m:top item='5_' name='采集管理' display='none' rank='co_NewRule,co_ListNote,co_ViewNote,co_Switch,co_GetOut'>
  <m:item name='采集节点管理' link='co_main.php' rank='co_ListNote' target='main' />
  <m:item name='临时内容管理' link='co_url.php' rank='co_ViewNote' target='main' />
  <m:item name='导入采集规则' link='co_get_corule.php' rank='co_GetOut' target='main'/>
  <m:item name='监控采集模式' link='co_gather_start.php' rank='co_GetOut' target='main'/>
</m:top>

-----------------------------------------------
";

?>