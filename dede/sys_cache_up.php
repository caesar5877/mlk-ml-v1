<?php
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_ArcBatch');
if(empty($dopost))
{
	$dopost = '';
}
if(empty($step))
{
	$step = 1;
}
if($dopost=="ok")
{
	if(empty($uparc))
	{
		$uparc = 0;
	}
	if($step==-1)
	{
		if($uparc==0)
		{
			sleep(1);
		}
		ShowMsg("成功更新所有缓存！","javascript:;");
		exit();
	}

	//更新栏目缓存
	else if($step==1)
	{
		UpDateCatCache();
		ShowMsg("成功更新栏目缓存，准备更新枚举缓存...","sys_cache_up.php?dopost=ok&step=2&uparc=$uparc");
		exit();
	}

	//更新枚举缓存
	else if($step==2)
	{
		ShowMsg("成功更新枚举缓存，准备更新调用缓存...","sys_cache_up.php?dopost=ok&step=3&uparc=$uparc");
		exit();
	}

	//清理arclist调用缓存、过期会员访问历史、过期短信
	else if($step==3)
	{
		echo '<meta http-equiv="Content-Type" content="text/html; charset='.$cfg_soft_lang.'">';
		$dsql->ExecuteNoneQuery("Delete From `#@__arccache`");
		echo "\n成功更新arclist调用缓存，准备清理过期会员访问历史...<hr />";
		$oldtime = time() - (90 * 24 * 3600);
		$dsql->ExecuteNoneQuery("Delete From `#@__member_pms` where sendtime<'$oldtime' ");
		echo "成功清理过期短信，准备修正错误文档，这可能要占较长的时间...";
		if($uparc==1)
		{
			echo "<script language='javascript'>location='sys_cache_up.php?dopost=ok&step=9';</script>";
		}
		else
		{
			echo "<script language='javascript'>location='sys_cache_up.php?dopost=ok&step=-1&uparc=$uparc';</script>";
		}
		exit();
	}
	//修正错误文档
	else if($step==9)
	{
		ShowMsg('修正错误文档操作已经取消...','sys_cache_up.php?dopost=ok&step=-1&uparc=1',0,5000);
	  exit();
	}
}
include DedeInclude('templets/sys_cache_up.htm');

?>