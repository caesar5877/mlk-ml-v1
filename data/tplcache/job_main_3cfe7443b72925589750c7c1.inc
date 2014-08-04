<?php
if(!isset($GLOBALS['_vars'])) $GLOBALS['_vars'] = array(); 
$fields = array();
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
<title>招聘信息管理</title>
<link href='img/base.css' rel='stylesheet' type='text/css'>
<script language='javascript'>
function Sel(stype)
{
	  for(var i=0;i < document.getElementsByName('aids').length;i++)
	  {
		  if(stype=='all') document.getElementsByName('aids')[i].checked=true;
		  else document.getElementsByName('aids')[i].checked=false;
	  }
}
function checkSubmit()
{
	var str = '';
	for(var i=0;i < document.getElementsByName('aids').length;i++)
	{
		  if(document.getElementsByName('aids')[i].checked){
		  	if(str=='') str += document.getElementsByName('aids')[i].value;
		  	else str += ',' + document.getElementsByName('aids')[i].value;
		  }
	}
	if(str=='')
	{
		alert('你没选择任何内容！');
		return false;
	}
	else
	{
		location="job_edit.php?aids="+str+"&dopost=delall";
	}
}
</script>
</head>
<body background='img/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#D1DDAA">
  <form name='form1' method='post' action='job_edit.php' onSubmit='return checkSubmit();'>
  	<input type='hidden' name='dopost' value='delall'/>
  <tr>
    <td height="28" background="img/tbg.gif" colspan="7">
  <div style="float:left">
  	<b>招聘信息管理</b>	</div>
	<div style="float:right;padding-right:6px;">
		[<a href="job_add.php"><u>增加新招聘信息</u></a>] 	 	</div>  </td>
</tr>
  <tr align="center" bgcolor="#F9FCEF" height="26">
          <td width="6%">选择</td>
          <td width="23%">职位名称</td>
          <td width="15%">部门</td>
          <td width="11%">语言</td>
          <td width="15%">发布时间</td>
          <td width="15%">过期时间</td>
          <td width="15%">管理</td>
        </tr>
        <?php
$atts = array();
$atts['tagname'] = 'datalist';
$blockValue = $this->refObj->GetArcList($atts,$this->refObj,$fields); 
foreach( $blockValue as $key=>$fields )
{
?>
        <tr align="center" bgcolor="#FFFFFF" height="26" onMouseMove="javascript:this.bgColor='#FCFDEE';"
        	 onMouseOut="javascript:this.bgColor='#FFFFFF';">
          <td> <input type='checkbox' name='aids' value='<?php echo $fields['id']; ?>' class='np' /> </td>
          <td> <a href='job_edit.php?id=<?php echo $fields['id']; ?>&dopost=getedit'><?php echo $fields['jobname']; ?></a> </td>
          <td> <?php echo $fields['needpart']; ?> </td>
          <td> <?php echo $lang_map_arr[$fields['lang']]; ?>(<?php echo $fields['lang']; ?>) </td>
          <td> <?php echo MyDate('Y-m-d', $fields['sendtime']); ?> </td>
          <td> <?php echo MyDate('Y-m-d', $fields['exptime']); ?> </td>
          <td>
          	<a href='job_edit.php?id=<?php echo $fields['id']; ?>&dopost=getedit'>[更改]</a>
		    <a href='job_edit.php?id=<?php echo $fields['id']; ?>&dopost=delete'>[删除]</a>	
		  </td>
        </tr>
		<?php
}
?>
     <tr bgcolor="#FDFEEF" height="28">
      <td colspan="7">
        <a href='#' onclick='Sel("all")' class='np coolbg'>[全选]</a>
      	<a href='#' onclick='Sel("none")' class='np coolbg'>[取消]</a>
      	<a href='#' onclick='checkSubmit()' class='np coolbg'>批量删除</a>
	  </td>
     </tr>
     <tr align="center" bgcolor="#F9FCEF" height="28">
      <td colspan="7">
      	<?php
$atts = array();
$atts['tagname'] = 'pagelist';
$atts['listsize'] = '6';
 echo $this->refObj->GetPageList($atts,$this->refObj,$fields); 
?>

	  </td>
     </tr>
    </form>
   </table>
   </td>
</tr>
</table>
</body>
</html>