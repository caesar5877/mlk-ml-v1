<?php
@ob_start();
@set_time_limit(3600);
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_Keyword');
if(empty($dojob)) $dojob = '';
if($dojob=='')
{
	include DedeInclude("templets/article_description_main.htm");
	exit();
}
else
{
	if(empty($startdd))
	{
		$startdd = 0;
	}
	if(empty($pagesize))
	{
		$pagesize = 100;
	}
	if(empty($totalnum))
	{
		$totalnum = 0;
	}
	if(empty($sid))
	{
		$sid = 0;
	}
	if(empty($eid))
	{
		$eid = 0;
	}
	if(empty($dojob))
	{
		$dojob = 'des';
	}
    $table = preg_replace("#[^0-9a-zA-Z_\#@]#", "", $table);
    $field = preg_replace("#[^0-9a-zA-Z_\[\]]#", "", $field);
	$channel = intval($channel);
	if($dsize>250)
	{
		$dsize = 250;
	}

	$tjnum = 0;

	//获取自动摘要
	if($dojob=='des')
	{

		if(empty($totalnum))
		{
			$addquery  = "";
			if($sid!=0)
			{
				$addquery  .= " And id>='$sid' ";
			}
			if($eid!=0)
			{
				$addquery  .= " And id<='$eid' ";
			}
			$tjQuery = "Select count(*) as dd From #@__archives where channel='{$channel}' $addquery";
			$row = $dsql->GetOne($tjQuery);
			$totalnum = $row['dd'];
		}
		if($totalnum > 0)
		{
			$addquery  = "";
			if($sid!=0)
			{
				$addquery  .= " And #@__archives.id>='$sid' ";
			}
			if($eid!=0)
			{
				$addquery  .= " And #@__archives.id<='$eid' ";
			}
			$fquery = "Select #@__archives.id,#@__archives.title,#@__archives.description,{$table}.{$field}
          From #@__archives left join {$table} on {$table}.aid=#@__archives.id
          where #@__archives.channel='{$channel}' $addquery limit $startdd,$pagesize ; ";
			$dsql->SetQuery($fquery);
			$dsql->Execute();
			while($row=$dsql->GetArray())
			{
				$body = $row[$field];
				$description = $row['description'];
				if(strlen($description)>10 || $description=='-')
				{
					continue;
				}
				$bodytext = preg_replace("/#p#|#e#|副标题|分页标题/isU","",Html2Text($body));
				if(strlen($bodytext) < $msize)
				{
					continue;
				}
				$des = trim(addslashes(cn_substr($bodytext,$dsize)));
				if(strlen($des)<3)
				{
					$des = "-";
				}
				$dsql->ExecuteNoneQuery("Update #@__archives set description='{$des}' where id='{$row['id']}';");
			}

			//返回进度信息
			$startdd = $startdd + $pagesize;
			if($totalnum > $startdd)
			{
				$tjlen = ceil( ($startdd/$totalnum) * 100 );
			}else
			{
				$tjlen=100;
				ShowMsg('完成所有任务', 'javascript:;');
				exit();
			}
			$dvlen = $tjlen * 2;
			$tjsta = "<div style='width:200;height:15;border:1px solid #898989;text-align:left'><div style='width:$dvlen;height:15;background-color:#829D83'></div></div>";
			$tjsta .= "<br/>完成处理文档总数的：$tjlen %，继续执行任务...";
			$nurl = "article_description_main.php?totalnum=$totalnum&startdd={$startdd}&pagesize=$pagesize&table={$table}&field={$field}&dsize={$dsize}&msize={$msize}&channel={$channel}&dojob={$dojob}";
			ShowMsg($tjsta,$nurl,0,500);
			exit();
		}
		else
		{
			ShowMsg('完成所有任务', 'javascript:;');
			exit();
		}
	}//获取自动摘要代码结束

	//更新自动分页
	if($dojob=='page')
	{
		require_once(DEDEADMIN."/inc/inc_archives_functions.php");

		$addquery  = "";
		if($sid!=0)
		{
			$addquery  .= " and aid>='$sid' ";
		}
		if($eid!=0)
		{
			$addquery  .= " and aid<='$eid' ";
		}

		//统计记录总数
		if($totalnum==0)
		{
			$sql = "Select count(*) as dd From $table where 1 $addquery";
			$row = $dsql->GetOne($sql);
			$totalnum = $row['dd'];
		}

		//获取记录，并分析
		if($totalnum > $startdd+$pagesize)
		{
			$limitSql = " limit $startdd,$pagesize";
		}
		else if(($totalnum-$startdd)>0)
		{
			$limitSql = " limit $startdd,".($totalnum - $startdd);
		}
		else
		{
			$limitSql = "";
		}
		$tjnum = $startdd;
		if($limitSql!="")
		{
			$fquery = "Select aid,$field From $table where 1 $addquery $limitSql ;";
			$dsql->SetQuery($fquery);
			$dsql->Execute();
			while($row=$dsql->GetArray())
			{
				$tjnum++;
				$body = $row[$field];
				$aid = $row['aid'];
				if(strlen($body) < $msize)
				{
					continue;
				}
				if(!preg_match("/#p#/iU",$body))
				{
					$body = SpLongBody($body,$cfg_arcautosp_size*1024,"#p#分页标题#e#");
					$body = addslashes($body);
					$dsql->ExecuteNoneQuery("Update $table set $field='$body' where aid='$aid' ; ");
				}
			}
		}//end if limit

		//返回进度提示
		if($totalnum>0)
		{
			$tjlen = ceil( ($tjnum/$totalnum) * 100 );
		}
		else
		{
			$tjlen=100;
		}

		$dvlen = $tjlen * 2;

		$tjsta = "<div style='width:200;height:15;border:1px solid #898989;text-align:left'><div style='width:$dvlen;height:15;background-color:#829D83'></div></div>";
		$tjsta .= "<br/>完成处理文档总数的：$tjlen %，继续执行任务...";

		if($tjnum < $totalnum)
		{
			$nurl = "article_description_main.php?totalnum=$totalnum&startdd=".($startdd+$pagesize)."&pagesize=$pagesize&table={$table}&field={$field}&dsize={$dsize}&msize={$msize}&channel={$channel}&dojob={$dojob}";
			ShowMsg($tjsta,$nurl,0,500);
			exit();
		}
		else
		{
			ShowMsg('完成所有任务', 'javascript:;');
			exit();
		}
	}//更新自动分页处理代码结束
}
?>