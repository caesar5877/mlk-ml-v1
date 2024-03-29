<?php
if(!defined('DEDEINC'))
{
	exit("Request Error!");
}
require_once(DEDEINC."/channelunit.func.php");

class TypeUnit
{
	var $dsql;
	var $artDir;
	var $baseDir;
	var $idCounter;
	var $idArrary;
	var $shortName;
	var $CatalogNums;

	//php5构造函数
	function __construct()
	{
		$this->idCounter = 0;
		$this->artDir = $GLOBALS['cfg_cmspath'].$GLOBALS['cfg_arcdir'];
		$this->baseDir = $GLOBALS['cfg_basedir'];
		$this->shortName = $GLOBALS['art_shortname'];
		$this->idArrary = "";
		$this->dsql = 0;
	}

	function TypeUnit()
	{
		$this->__construct();
	}

	//清理类
	function Close()
	{
	}

	//获取所有栏目的文档ID数
	function UpdateCatalogNum()
	{
		$this->dsql->SetQuery("SELECT typeid,count(typeid) as dd FROM `#@__arctiny` where arcrank <>-2 group by typeid");
		$this->dsql->Execute();
		while($row = $this->dsql->GetArray())
		{
			$this->CatalogNums[$row['typeid']] = $row['dd'];
		}
	}

	function GetTotalArc($tid)
	{
		if(!is_array($this->CatalogNums))
		{
			$this->UpdateCatalogNum();
		}
		if(!isset($this->CatalogNums[$tid]))
		{
			return 0;
		}
		else
		{
			$totalnum = 0;
			$ids = explode(',',GetSonIds($tid));
			foreach($ids as $tid)
			{
				if(isset($this->CatalogNums[$tid]))
				{
					$totalnum += $this->CatalogNums[$tid];
				}
			}
			return $totalnum;
		}
	}

	//----读出所有分类,在类目管理页(list_type)中使用----------
	function ListAllType($channel=0,$nowdir=0)
	{
		$this->dsql = $GLOBALS['dsql'];

		$this->dsql->SetQuery("Select id,typedir,typename,ispart,sortrank From `#@__arctype` where reid=0 order by sortrank");
		$this->dsql->Execute(0);
		while($row=$this->dsql->GetObject(0))
		{
			$typeDir = $row->typedir;
			$typeName = $row->typename;
			$ispart = $row->ispart;
			$id = $row->id;
			$rank = $row->sortrank;
			$nss = "";
			echo "<table width='100%' border='0' cellspacing='0' cellpadding='2'>\r\n";
			if($channel==0||$channel==$id)
			{
				//普通列表
				if($ispart==0)
				{
					echo "  <tr bgcolor='#F5FAE4'>\r\n";
					echo "  <td width='2%' class='bline'><img style='cursor:pointer' onClick=\"LoadSuns('suns".$id."',$id);\" src='img/dedeexplode.gif' width='11' height='11'></td>\r\n";
					echo "  <td class='bline'><table width='98%' border='0' cellspacing='0' cellpadding='0'><tr><td width='50%'><input class='np' type='checkbox' name='tids[]' value='{$id}'><a href='catalog_do.php?cid=".$id."&dopost=listArchives' oncontextmenu=\"CommonMenu(event,this,$id,'".urlencode($typeName)."')\">{$nss}".$typeName."[ID:".$id."]</a>(文档：".$this->GetTotalArc($id).")  <a href=\"catalog_edit.php?id={$id}\"><img src='img/write2.gif'/></a>";
					echo "    </td><td align='right'>";
					echo "<a href='{$GLOBALS['cfg_phpurl']}/list.php?tid={$id}' target='_blank'>预览</a>";
					echo "|<a href='catalog_do.php?cid={$id}&dopost=listArchives'>内容</a>";
					echo "|<a href='catalog_add.php?id={$id}'>增加子类</a>";
					echo "|<a href='catalog_edit.php?id={$id}'>更改</a>";
					//echo "|<a href='catalog_move.php?job=movelist&typeid={$id}'>移动</a>";
					echo "|<a href='catalog_del.php?id={$id}&typeoldname=".urlencode($typeName)."'>删除</a>";
					echo "&nbsp; <input type='text' name='sortrank{$id}' value='{$rank}' style='width:25;height:16'></td></tr></table></td></tr>\r\n";
				}

				//带封面的频道
				else if($ispart==1)
				{
					echo "  <tr bgcolor='#F5FAE4'>\r\n";
					echo "  <td width='2%' class='bline'><img style='cursor:pointer' onClick=\"LoadSuns('suns".$id."',$id);\" src='img/dedeexplode.gif' width='11' height='11'></td>\r\n";
					echo "  <td class='bline'><table width='98%' border='0' cellspacing='0' cellpadding='0'><tr><td width='50%'><input class='np' type='checkbox' name='tids[]' value='{$id}'><a href='catalog_do.php?cid=".$id."&dopost=listArchives' oncontextmenu=\"CommonMenuPart(event,this,$id,'".urlencode($typeName)."')\">{$nss}".$typeName."[ID:".$id."]</a>  <a href=\"catalog_edit.php?id={$id}\"> <img src='img/write2.gif'/> </a>";
					echo "    </td><td align='right'>";
					echo "<a href='{$GLOBALS['cfg_phpurl']}/list.php?tid={$id}' target='_blank'>预览</a>";
					echo "|<a href='catalog_do.php?cid={$id}&dopost=listArchives'>内容</a>";
					echo "|<a href='catalog_add.php?id={$id}'>增加子类</a>";
					echo "|<a href='catalog_edit.php?id={$id}'>更改</a>";
					//echo "|<a href='catalog_move.php?job=movelist&typeid={$id}'>移动</a>";
					echo "|<a href='catalog_del.php?id={$id}&typeoldname=".urlencode($typeName)."'>删除</a>";
					echo "&nbsp; <input type='text' name='sortrank{$id}' value='{$rank}' style='width:25;height:16'></td></tr></table></td></tr>\r\n";
				}

				//独立页面
				else if($ispart==2)
				{
					echo "  <tr height='24' bgcolor='#F5FAE4'>\r\n";
					echo "  <td width='2%' class='bline2'><img style='cursor:pointer' onClick=\"LoadSuns('suns".$id."',$id);\" src='img/dedeexplode.gif' width='11' height='11'></td>\r\n";
					echo "  <td class='bline2'><table width='98%' border='0' cellspacing='0' cellpadding='0'><tr><td width='50%'><input class='np' type='checkbox' name='tids[]' value='{$id}'><a href='catalog_edit.php?id=".$id."' oncontextmenu=\"SingleMenu(event,this,$id,'".urlencode($typeName)."')\">{$nss}".$typeName."[ID:".$id."]</a>  <a  href=\"catalog_edit.php?id={$id}\"><img src='img/write2.gif'/></a>";
					echo "    </td><td align='right'>";
					echo "<a href='{$typeDir}' target='_blank'>预览</a>";
					echo "|<a href='catalog_edit.php?id={$id}'>更改</a>";
					//echo "|<a href='catalog_move.php?job=movelist&typeid={$id}'>移动</a>";
					echo "|<a href='catalog_del.php?id={$id}&typeoldname=".urlencode($typeName)."'>删除</a>";
					echo "&nbsp; <input type='text' name='sortrank{$id}' value='{$rank}' style='width:25;height:16'></td></tr></table></td></tr>\r\n";
				}
			}
			else
			{
				//普通列表
				if($ispart==0)
				{
					echo "  <tr bgcolor='#F5FAE4'>\r\n";
					echo "  <td width='2%' class='bline'><img src='img/dedeexplode2.gif' width='11' height='11'></td>\r\n";
					echo "  <td class='bline'><table width='98%' border='0' cellspacing='0' cellpadding='0'><tr><td width='50%'><a href='catalog_do.php?cid=".$id."&dopost=listArchives'>{$nss}".$typeName."[ID:".$id."]</a>(文档：".$this->GetTotalArc($id).")";
					echo "    </td><td align='right'>";
					echo "<a href='{$GLOBALS['cfg_phpurl']}/list.php?tid={$id}' target='_blank'>预览</a>";
					echo "|<a href='catalog_do.php?cid={$id}&dopost=listArchives'>内容</a>";
					echo "&nbsp; </td></tr></table></td></tr>\r\n";
				}
				//带封面的频道
				else if($ispart==1)
				{
					echo "  <tr bgcolor='#F5FAE4'>\r\n";
					echo "  <td width='2%' class='bline'><img src='img/dedeexplode2.gif' width='11' height='11'></td>\r\n";
					echo "  <td class='bline'><table width='98%' border='0' cellspacing='0' cellpadding='0'><tr><td width='50%'><a href='catalog_do.php?cid=".$id."&dopost=listArchives'>{$nss}".$typeName."[ID:".$id."]</a>";
					echo "    </td><td align='right'>";
					echo "<a href='{$GLOBALS['cfg_phpurl']}/list.php?tid={$id}' target='_blank'>预览</a>";
					echo "|<a href='catalog_do.php?cid={$id}&dopost=listArchives'>内容</a>";
					echo "&nbsp; </td></tr></table></td></tr>\r\n";
				}
				//跳转网址
				else if($ispart==2)
				{
					echo "  <tr height='24' bgcolor='#F5FAE4'>\r\n";
					echo "  <td width='2%' class='bline2'><img src='img/dedeexplode2.gif' width='11' height='11'></td>\r\n";
					echo "  <td class='bline2'><table width='98%' border='0' cellspacing='0' cellpadding='0'><tr><td width='50%'><a href='catalog_edit.php?id=".$id."'>{$nss}".$typeName."[ID:".$id."]</a>";
					echo "    </td><td align='right'>";
					echo "<a href='{$typeDir}' target='_blank'>预览</a>";
					echo "&nbsp; </td></tr></table></td></tr>\r\n";
				}
				//独立页面
				else if($ispart==3)
				{
					echo "  <tr height='24' bgcolor='#F5FAE4'>\r\n";
					echo "  <td width='2%' class='bline2'><img src='img/dedeexplode2.gif' width='11' height='11'></td>\r\n";
					echo "  <td class='bline2'><table width='98%' border='0' cellspacing='0' cellpadding='0'><tr><td width='50%'><a href='catalog_edit.php?id=".$id."'>{$nss}".$typeName."[ID:".$id."]</a>";
					echo "    </td><td align='right'>";
					echo "<a href='{$GLOBALS['cfg_phpurl']}/list.php?tid={$id}' target='_blank'>预览</a>";
					echo "&nbsp; </td></tr></table></td></tr>\r\n";
				}
			}
			echo "  <tr><td colspan='2' id='suns".$id."'>";
			$lastid = GetCookie('lastCid');
			if($channel==$id || $lastid==$id || !empty($GLOBALS['exallct']))
			{
				echo "    <table width='100%' border='0' cellspacing='0' cellpadding='0'>\r\n";
				$this->LogicListAllSunType($id,"　");
				echo "    </table>\r\n";
			}
			echo "</td></tr>\r\n</table>\r\n";
		}
	}

	//获得子类目的递归调用
	function LogicListAllSunType($id,$step)
	{
		$fid = $id;
		$this->dsql->SetQuery("Select id,reid,typedir,typename,ispart,sortrank From `#@__arctype` where reid='".$id."' order by sortrank");
		$this->dsql->Execute($fid);
		if($this->dsql->GetTotalRow($fid)>0)
		{
			while($row=$this->dsql->GetObject($fid))
			{
				$typeDir = $row->typedir;
				$typeName = $row->typename;
				$reid = $row->reid;
				$id = $row->id;
				$ispart = $row->ispart;
				if($step=="　") $stepdd = 2;
				else $stepdd = 3;
				$rank = $row->sortrank;
				$nss = "";

				//普通列表
				if($ispart==0)
				{
					echo "<tr height='24' oncontextmenu=\"CommonMenu(event,this,$id,'".urlencode($typeName)."')\">\r\n";
					echo "<td class='nbline'>";
					echo "<table width='98%' border='0' cellspacing='0' cellpadding='0'>";
					echo "<tr onMouseMove=\"javascript:this.bgColor='#FAFCE0';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\"><td width='50%'>";
					echo "<input class='np' type='checkbox' name='tids[]' value='{$id}'><a href='catalog_do.php?cid=".$id."&dopost=listArchives'>$step ·{$nss}".$typeName."[ID:".$id."]</a>(文档：".$this->GetTotalArc($id).")  <a  href=\"catalog_edit.php?id={$id}\"><img src='img/write2.gif'/></a>";
					echo "</td><td align='right'>";
					echo "<a href='{$GLOBALS['cfg_phpurl']}/list.php?tid={$id}' target='_blank'>预览</a>";
					echo "|<a href='catalog_do.php?cid={$id}&dopost=listArchives'>内容</a>";
					echo "|<a href='catalog_add.php?id={$id}'>增加子类</a>";
					echo "|<a href='catalog_edit.php?id={$id}'>更改</a>";
					//echo "|<a href='catalog_move.php?job=movelist&typeid={$id}'>移动</a>";
					echo "|<a href='catalog_del.php?id={$id}&typeoldname=".urlencode($typeName)."'>删除</a>";
					echo "&nbsp; <input type='text' name='sortrank{$id}' value='{$rank}' style='width:25;height:16'></td></tr></table></td></tr>\r\n";
				}

				//封面频道
				else if($ispart==1)
				{
					echo " <tr height='24' oncontextmenu=\"CommonMenu(event,this,$id,'".urlencode($typeName)."')\">\r\n";
					echo "<td class='nbline'><table width='98%' border='0' cellspacing='0' cellpadding='0'><tr onMouseMove=\"javascript:this.bgColor='#FAFCE0';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\"><td width='50%'>";
					echo "<input class='np' type='checkbox' name='tids[]' value='{$id}'><a href='catalog_do.php?cid=".$id."&dopost=listArchives'>$step ·{$nss}".$typeName."[ID:".$id."]</a>  <a href=\"catalog_edit.php?id={$id}\"><img src='img/write2.gif'/></a>";
					echo "</td><td align='right'>";
					echo "<a href='{$GLOBALS['cfg_phpurl']}/list.php?tid={$id}' target='_blank'>预览</a>";
					echo "|<a href='catalog_do.php?cid={$id}&dopost=listArchives'>内容</a>";
					echo "|<a href='catalog_add.php?id={$id}'>增加子类</a>";
					echo "|<a href='catalog_edit.php?id={$id}'>更改</a>";
					//echo "|<a href='catalog_move.php?job=movelist&typeid={$id}'>移动</a>";
					echo "|<a href='catalog_del.php?id={$id}&typeoldname=".urlencode($typeName)."'>删除</a>";
					echo "&nbsp; <input type='text' name='sortrank{$id}' value='{$rank}' style='width:25;height:16'></td></tr></table></td></tr>\r\n";
				}
				//跳转页面
				else if($ispart==2)
				{
					echo "<tr height='24' oncontextmenu=\"SingleMenu(event,this,$id,'".urlencode($typeName)."')\">\r\n";
					echo "<td class='bline2'><table width='98%' border='0' cellspacing='0' cellpadding='0'>";
					echo "<tr onMouseMove=\"javascript:this.bgColor='#FAFCE0';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\"><td width='50%'>";
					echo "<input class='np' type='checkbox' name='tids[]' value='{$id}'><a href='catalog_do.php?cid=".$id."&dopost=listArchives'>$step ·{$nss}".$typeName."[ID:".$id."]</a>  <a href=\"catalog_edit.php?id={$id}\"><img src='img/write2.gif'/></a>";
					echo "</td><td align='right'>";
					echo "<a href='{$typeDir}' target='_blank'>预览</a>";
					echo "|<a href='catalog_edit.php?id={$id}'>更改</a>";
					//echo "|<a href='catalog_move.php?job=movelist&typeid={$id}'>移动</a>";
					echo "|<a href='catalog_del.php?id={$id}&typeoldname=".urlencode($typeName)."'>删除</a>";
					echo "&nbsp; <input type='text' name='sortrank{$id}' value='{$rank}' style='width:25;height:16'></td></tr></table></td></tr>\r\n";
				}
				//独立页面
				else if($ispart==3)
				{
					echo "<tr height='24' oncontextmenu=\"SingleMenu(event,this,$id,'".urlencode($typeName)."')\">\r\n";
					echo "<td class='bline2'><table width='98%' border='0' cellspacing='0' cellpadding='0'>";
					echo "<tr onMouseMove=\"javascript:this.bgColor='#FAFCE0';\" onMouseOut=\"javascript:this.bgColor='#FFFFFF';\"><td width='50%'>";
					echo "<input class='np' type='checkbox' name='tids[]' value='{$id}'><a href='catalog_do.php?cid=".$id."&dopost=listArchives'>$step ·{$nss}".$typeName."[ID:".$id."]</a>  <a href=\"catalog_edit.php?id={$id}\"><img src='img/write2.gif'/></a>";
					echo "</td><td align='right'>";
					echo "<a href='{$GLOBALS['cfg_phpurl']}/list.php?tid={$id}' target='_blank'>预览</a>";
					echo "|<a href='catalog_edit.php?id={$id}'>更改</a>";
					//echo "|<a href='catalog_move.php?job=movelist&typeid={$id}'>移动</a>";
					echo "|<a href='catalog_del.php?id={$id}&typeoldname=".urlencode($typeName)."'>删除</a>";
					echo "&nbsp; <input type='text' name='sortrank{$id}' value='{$rank}' style='width:25;height:16'></td></tr></table></td></tr>\r\n";
				}
				$this->LogicListAllSunType($id,$step."　");
			}
		}
	}

	//-----返回与某个目相关的下级目录的类目ID列表(删除类目或文章时调用)
	function GetSunTypes($id,$channel=0)
	{
		$this->dsql = $GLOBALS['dsql'];
		$this->idArray[$this->idCounter]=$id;
		$this->idCounter++;
		$fid = $id;
		$csql = ($channel!=0 ? " And channeltype=$channel " : "");
		$this->dsql->SetQuery("Select id From `#@__arctype` where reid=$id $csql");
		$this->dsql->Execute("gs".$fid);
		while($row=$this->dsql->GetObject("gs".$fid))
		{
			$nid = $row->id;
			$this->GetSunTypes($nid,$channel);
		}
		return $this->idArray;
	}

	//删除类目
	function DelType($id,$isDelFile)
	{
		$this->idCounter = 0;
		$this->idArray = "";
		$this->GetSunTypes($id);
		$query = "
		Select #@__arctype.*,#@__channeltype.typename as ctypename,
		#@__channeltype.addtable
		From `#@__arctype` left join #@__channeltype
		on #@__channeltype.id=#@__arctype.channeltype
		where #@__arctype.id='$id'
		";
		$typeinfos = $this->dsql->GetOne($query);
		if(!is_array($typeinfos))
		{
			return false;
		}
		$indir = $typeinfos['typedir'];
		$addtable = $typeinfos['addtable'];
		$ispart = $typeinfos['ispart'];
		$defaultname = $typeinfos['defaultname'];

		//删除数据库里的相关记录
		foreach($this->idArray as $id)
		{
			$myrow = $this->dsql->GetOne("Select * From `#@__arctype` where id='$id'");
			//删除数据库信息
			$this->dsql->ExecuteNoneQuery("Delete From `#@__arctype` where id='$id'");
			$this->dsql->ExecuteNoneQuery("Delete From `#@__arctiny` where typeid='$id'");
			$this->dsql->ExecuteNoneQuery("Delete From `#@__archives` where typeid='$id'");
			$this->dsql->ExecuteNoneQuery("Delete From `#@__feedback` where typeid='$id'");
			if($addtable != "") {
				$this->dsql->ExecuteNoneQuery("Delete From $addtable where typeid='$id'");
			}
		}
		//删除单独页面
		if($ispart==3)
		{
			 if( $indir=="" && is_file($this->baseDir."/".$defaultname) ) {
				  @unlink($this->baseDir."/".$defaultname);
			 }
		}
		@reset($this->idArray);
		$this->idCounter = 0;
		return true;
	}

	//---- 删除指定目录的所有文件
	function RmDirFile($indir)
	{
		if(!file_exists($indir)) return;
		$dh = dir($indir);
		while($file = $dh->read())
		{
			if($file == "." || $file == "..")
			{
				continue;
			}
			else if(is_file("$indir/$file"))
			{
				@unlink("$indir/$file");
			}
			else
			{
				$this->RmDirFile("$indir/$file");
			}
			if(is_dir("$indir/$file"))
			{
				@rmdir("$indir/$file");
			}
		}
		$dh->close();
		return(1);
	}
}
?>