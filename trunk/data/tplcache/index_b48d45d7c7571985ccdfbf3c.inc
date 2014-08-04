<?php
if(!isset($GLOBALS['_vars'])) $GLOBALS['_vars'] = array(); 
$fields = array();
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo GetLang('membercenter'); ?> - <?php echo $cfg_webname; ?></title>
<?php include(DEDEMEMBER."/templets/head_inc.htm"); ?>
<script type="text/javascript" src="js/box.js"></script>
</head>
<body>
<?php include(DEDEMEMBER."/templets/head.htm"); ?>
<div class="wrapper lineT">
  <div class="mainBox">
    <div class="main">
      <?php include(DEDEMEMBER."/templets/menu.php"); ?>
      <div class="dedeCenter" id="personal">
        <!--个人信息 -->
        <div class="message">
          <div class="visualize fLeft">
          	<img class="mB10" src="<?php echo (!empty($cfg_ml->fields['face']) ? $cfg_ml->fields['face'] : "images/nopic.gif"); ?>" width="150" height="150" alt="Your face" />
            <ul class="black">
              <li><a class="icon upImage" href="myface.php" title="<?php echo GetLang('faceset'); ?>"><?php echo GetLang('faceset'); ?></a></li>
              <li><a class="icon mySetting" href="myinfo.php" title="<?php echo GetLang('personaldata'); ?>"><?php echo GetLang('information'); ?></a></li>
            </ul>
            <div class="clear"></div>
          </div>
          <div class="datum fLeft mT10">
            <h3 class="userName">
            <?php echo $cfg_ml->M_LoginID;?>
            <em class="mL10"></em></h3>
            <div class="userState mTB10"><span class="fLeft"><?php echo $cfg_ml->GetSta($dsql); ?></span>
              <div class="clear"></div>
            </div>
            <ul class="entry">
              <li><a href="pm.php"><?php echo GetLang('shortmessage'); ?></a></li>
              <li><a href="mystow.php"><?php echo GetLang('myfavorites'); ?></a></li> 
              <li><a href="myfiles.php"><?php echo GetLang('mydocuments'); ?></a></li>
            </ul>
            <div>
            	<span class="fRight" style='background-color:#F8FEDA;clear:both;padding:3px 5px 3px 5px;margin-top:12px;'>
            	【 Wecome enter Member Center! 】
              </span>
            </div>
            <div class="clear"></div>
          </div>
        </div>
        <div class="titleBar bgGreen lineT">
          <h5 class="fLeft icon titFangle"><?php echo GetLang('newproduct'); ?></h5>
          <div class="fRight"><a href="myfiles.php"><?php echo GetLang('more'); ?></a></div>
          <div class="clear"></div>
        </div>
        <div class="fangle">
          <dl class="artList mL10 mR10">
            <?php
foreach ($archives as $archive)
{
?>
            <dd>
            	<em><?php echo MyDate('m-d H:i', $archive['senddate']);?></em>
            	<span><a href="<?php echo $archive['htmlurl'];?>" title="<?php echo $archive['title'];?>" target="_blank"><?php echo $archive['title'];?></a></span>
            </dd>
            <?php
}
?>
          </dl>
        </div>
        
        <div class="whoSeeMe">
          <div class="clear"></div>
          <div class="titleBar bgGreen lineT">
            <h5 class="fLeft icon titStow"><?php echo GetLang('myfavorites'); ?></h5>
            <div class="fRight"><a href="mystow.php"><?php echo GetLang('more'); ?></a></div>
            <div class="clear"></div>
          </div>
          <div class="Stow">
            <dl class="artList mL10 mR10">
              <?php
foreach ($favorites as $favorite)
{
?>
              <dd><em><?php echo MyDate('m-d H:i', $favorite['addtime']);?></em><span><a href="archives_do.php?dopost=viewArchives&aid=<?php echo $favorite['aid'];?>" title="<?php echo $favorite['title'];?>" target="_blank"><?php echo $favorite['title'];?></a></span> </dd>
              <?php
}
?>
              <dt>&nbsp;</dt>
            </dl>
          </div>
        </div>
      </div>
      <div class="clear"></div>
    </div>
    <div class="clear"></div>
  </div>
</div>
<?php include(DEDEMEMBER."/templets/foot.htm"); ?>
</body>
</html>