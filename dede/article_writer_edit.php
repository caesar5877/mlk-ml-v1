<?php
require_once(dirname(__FILE__).'/config.php');
require_once(DEDEINC.'/oxwindow.class.php');
CheckPurview('sys_Writer');
if(empty($dopost))
{
	$dopost = '';
}
if(empty($allwriter))
{
	$allwriter = '';
}
else
{
	$allwriter = stripslashes($allwriter);
}
$m_file = DEDEDATA."/admin/writer.txt";

//保存
if($dopost=="save")
{
	$fp = fopen($m_file,'w');
	flock($fp,3);
	fwrite($fp,$allwriter);
	fclose($fp);
	echo "<script>alert('Save OK!');</script>";
}

//读出
if(empty($allwriter) && filesize($m_file)>0)
{
	$fp = fopen($m_file,'r');
	$allwriter = fread($fp,filesize($m_file));
	fclose($fp);
}
$wintitle = "文章作者管理";
$wecome_info = "文章作者管理";
$win = new OxWindow();
$win->Init('article_writer_edit.php','js/blank.js','POST');
$win->AddHidden('dopost','save');
$win->AddTitle("把作者姓名用半角逗号“,”分开，更改结果后需重载档案发布页面。");
$win->AddMsgItem("<textarea name='allwriter' id='allwriter' style='width:100%;height:300px'>$allwriter</textarea>");
$winform = $win->GetWindow('ok');
$win->Display();

?>