<?php
if(!defined('DEDEINC'))
{
	//exit('dedecms');
}

/****************************************
class DedeTag 标记的数据结构描述
function c____DedeTag();
**************************************/
class DedeTag
{
	var $IsReplace=FALSE; //标记是否已被替代，供解析器使用
	var $TagName=""; //标记名称
	var $InnerText=""; //标记之间的文本
	var $StartPos=0; //标记起始位置
	var $EndPos=0; //标记结束位置
	var $CAttribute=""; //标记属性描述,即是class DedeAttribute
	var $TagValue=""; //标记的值
	var $TagID = 0;

	//获取标记的名称和值
	function GetName()
	{
		return strtolower($this->TagName);
	}

	function GetValue()
	{
		return $this->TagValue;
	}

	//下面两个成员函数仅是为了兼容旧版
	function GetTagName()
	{
		return strtolower($this->TagName);
	}

	function GetTagValue()
	{
		return $this->TagValue;
	}

	//获取标记的指定属性
	function IsAttribute($str)
	{
		return $this->CAttribute->IsAttribute($str);
	}

	function GetAttribute($str)
	{
		return $this->CAttribute->GetAtt($str);
	}

	function GetAtt($str)
	{
		return $this->CAttribute->GetAtt($str);
	}

	function GetInnerText()
	{
		return $this->InnerText;
	}
}

/**********************************************
//DedeTagParse Dede织梦模板类
function c____DedeTagParse();
***********************************************/
class DedeTagParse
{
	var $NameSpace = 'dede'; //标记的名字空间
	var $TagStartWord = '{'; //标记起始
	var $TagEndWord = '}'; //标记结束
	var $TagMaxLen = 64; //标记名称的最大值
	var $CharToLow = true; // true表示对属性和标记名称不区分大小写
	var $IsCache = FALSE; //是否使用缓冲
	var $TempMkTime = 0;
	var $CacheFile = '';
	var $SourceFile = '';
	var $SourceString = '';//模板字符串
	var $CTags = '';		 //标记集合
	var $Count = -1;		 //$Tags标记个数
	var $refObj = '';   //引用当前模板类的对象
	var $tplLang = 'gb2312';
	var $handLang = '';

	function __construct()
	{
		if(!isset($GLOBALS['cfg_tplcache']))
		{
			$GLOBALS['cfg_tplcache'] = 'N';
		}
		if($GLOBALS['cfg_tplcache']=='Y')
		{
			$this->IsCache = true;
		}
		else
		{
			$this->IsCache = FALSE;
		}
		$this->NameSpace = 'dede';
		$this->TagStartWord = '{';
		$this->TagEndWord = '}';
		$this->TagMaxLen = 64;
		$this->CharToLow = true;
		$this->SourceString = '';
		$this->CTags = Array();
		$this->Count = -1;
		$this->TempMkTime = 0;
		$this->CacheFile = '';
	}

	function DedeTagParse()
	{
		$this->__construct();
	}

	//设置标记的命名空间，默认为dede
	function SetNameSpace($str,$s="{",$e="}")
	{
		$this->NameSpace = strtolower($str);
		$this->TagStartWord = $s;
		$this->TagEndWord = $e;
	}

	//重置成员变量或Clear
	function SetDefault()
	{
		$this->SourceString = '';
		$this->SourceFile = '';
		$this->CTags = '';
		$this->Count=-1;
		$this->tplLang = 'gb2312';
		$this->handLang = '';
	}

	function GetCount()
	{
		return $this->Count+1;
	}

	function Clear()
	{
		$this->SetDefault();
	}

	//检测模板缓冲
	function LoadCache($filename)
	{
		global $cfg_tplcache,$cfg_tplcache_dir;
		if(!$this->IsCache)
		{
			return false;
		}
		$cdir = dirname($filename);
		$cachedir = DEDEROOT.$cfg_tplcache_dir;
		$ckfile = str_replace($cdir,'',$filename).substr(md5($filename),0,16).'.inc';
		$ckfullfile = $cachedir.'/'.$ckfile;
		$ckfullfile_t = $cachedir.'/'.$ckfile.'.txt';
		$this->CacheFile = $ckfullfile;
		$this->TempMkTime = filemtime($filename);
		if(!file_exists($ckfullfile)||!file_exists($ckfullfile_t))
		{
			return false;
		}

		//检测模板最后更新时间
		$fp = fopen($ckfullfile_t,'r');
		$time_info = trim(fgets($fp,64));
		fclose($fp);
		if($time_info != $this->TempMkTime)
		{
			return false;
		}

		//引入缓冲数组
		include($this->CacheFile);

		//把缓冲数组内容读入类
		if(isset($z) && is_array($z))
		{
			foreach($z as $k=>$v)
			{
				$this->Count++;
				$ctag = new DedeTAg();
				$ctag->CAttribute = new DedeAttribute();
				$ctag->IsReplace = FALSE;
				$ctag->TagName = $v[0];
				$ctag->InnerText = $v[1];
				$ctag->StartPos = $v[2];
				$ctag->EndPos = $v[3];
				$ctag->TagValue = '';
				$ctag->TagID = $k;
				if(isset($v[4]) && is_array($v[4]))
				{
					$i = 0;
					foreach($v[4] as $k=>$v)
					{
						$ctag->CAttribute->Count++;
						$ctag->CAttribute->Items[$k]=$v;
					}
				}
				$this->CTags[$this->Count] = $ctag;
			}
		}
		else
		{
			//模板没有缓冲数组
			$this->CTags = '';
			$this->Count = -1;
		}
		return true;
	}

	//写入缓冲
	function SaveCache()
	{
		$fp = fopen($this->CacheFile.'.txt',"w");
		fwrite($fp,$this->TempMkTime."\n");
		fclose($fp);
		$fp = fopen($this->CacheFile,"w");
		flock($fp,3);
		fwrite($fp,'<'.'?php'."\r\n");
		if(is_array($this->CTags))
		{
			foreach($this->CTags as $tid=>$ctag)
			{
				$arrayValue = 'Array("'.$ctag->TagName.'",';
				$arrayValue .= '"'.str_replace('$','\$',str_replace("\r","\\r",str_replace("\n","\\n",str_replace('"','\"',str_replace("\\","\\\\",$ctag->InnerText))))).'"';
				$arrayValue .= ",{$ctag->StartPos},{$ctag->EndPos});";
				fwrite($fp,"\$z[$tid]={$arrayValue}\n");
				if(is_array($ctag->CAttribute->Items))
				{
					foreach($ctag->CAttribute->Items as $k=>$v)
					{
						$v = str_replace("\\","\\\\",$v);
						$v = str_replace('"',"\\".'"',$v);
						$v = str_replace('$','\$',$v);
						$k = trim(str_replace("'","",$k));
						if($k=="")
						{
							continue;
						}
						if($k!='tagname')
						{
							fwrite($fp,"\$z[$tid][4]['$k']=\"$v\";\n");
						}
					}
				}
			}
		}
		fwrite($fp,"\n".'?'.'>');
		fclose($fp);
	}

	//载入模板文件
	function LoadTemplate($filename)
	{
		$this->SetDefault();
		$this->SourceFile = $filename;
		$fp = @fopen($filename, 'r') or die(' Template Not Found! '.$filename.'<br />');
		while($line = fgets($fp,1024))
		{
			$this->SourceString .= $line;
		}
		fclose($fp);
		if($this->LoadCache($filename))
		{
			$this->tplLang = $this->GetTplLang();
			return true;
		}
		else
		{
			$this->ParseTemplet();
			$this->tplLang = $this->GetTplLang();
			return true;
		}
	}
	
	//检测模板语言
	function GetTplLang()
	{
		$lang = '';
		if(!is_array($this->CTags)) return '';
		foreach($this->CTags as $ctag)
		{
			if($ctag->GetName()=='tpllang') {
				$lang = $ctag->GetAtt('name');
				break;
			}
		}
		if($lang=='') {
			$lang = 'gb2312';
		}
		return $lang;
	}
	

	function LoadTemplet($filename)
	{
		$this->LoadTemplate($filename);
	}

	function LoadFile($filename)
	{
		$this->LoadTemplate($filename);
	}

	//载入模板字符串
	function LoadSource($str)
	{
		$this->SetDefault();
		$this->SourceString = $str;
		$this->IsCache = FALSE;
		$this->ParseTemplet();
	}

	function LoadString($str)
	{
		$this->LoadSource($str);
	}

	//获得指定名称的Tag的ID(如果有多个同名的Tag,则取没有被取代为内容的第一个Tag)
	function GetTagID($str)
	{
		if($this->Count==-1)
		{
			return -1;
		}
		if($this->CharToLow)
		{
			$str=strtolower($str);
		}
		foreach($this->CTags as $id=>$CTag)
		{
			if($CTag->TagName==$str && !$CTag->IsReplace)
			{
				return $id;
				break;
			}
		}
		return -1;
	}

	//获得指定名称的CTag数据类(如果有多个同名的Tag,则取没有被分配内容的第一个Tag)
	function GetTag($str)
	{
		if($this->Count==-1)
		{
			return '';
		}
		if($this->CharToLow)
		{
			$str=strtolower($str);
		}
		foreach($this->CTags as $id=>$CTag)
		{
			if($CTag->TagName==$str && !$CTag->IsReplace)
			{
				return $CTag;
				break;
			}
		}
		return '';
	}

	function GetTagByName($str)
	{
		return $this->GetTag($str);
	}

	//获得指定ID的CTag数据类
	function GetTagByID($id)
	{
		if(isset($this->CTags[$id]))
		{
			return $this->CTags[$id];
		}
		else
		{
			return '';
		}
	}

	//给_vars数组传递一个元素
	function AssignVar($vname,$vvalue)
	{
		if(!isset($_sys_globals['define']))
		{
			$_sys_globals['define'] = 'yes';
		}
		$_sys_globals[$vname] = $vvalue;
	}

	//分配指定ID的标记的值
	function Assign($i,$str,$runfunc = true)
	{
		if(isset($this->CTags[$i]))
		{
			$this->CTags[$i]->IsReplace = true;
			$this->CTags[$i]->TagValue = $str;

			if( $this->CTags[$i]->GetAtt('function')!='' && $runfunc )
			{
				$this->CTags[$i]->TagValue = $this->EvalFunc( $str, $this->CTags[$i]->GetAtt('function'),$this->CTags[$i] );
			}
		}
	}

	//分配指定名称的标记的值，如果标记包含属性，请不要用此函数
	function AssignName($tagname,$str)
	{
		foreach($this->CTags as $id=>$CTag)
		{
			if($CTag->TagName==$tagname)
			{
				$this->Assign($id,$str);
			}
		}
	}

	//处理特殊标记
	function AssignSysTag()
	{
		global $_sys_globals;
		for($i=0;$i<=$this->Count;$i++)
		{
			$CTag = $this->CTags[$i];
			$str = '';

			//获取一个外部变量
			if( $CTag->TagName == 'global' )
			{
				$str = $this->GetGlobals($CTag->GetAtt('name'));
				if( $this->CTags[$i]->GetAtt('function')!='' )
				{
					$str = $this->EvalFunc( $this->CTags[$i]->TagValue, $this->CTags[$i]->GetAtt('function'),$this->CTags[$i] );
				}
				$this->CTags[$i]->IsReplace = true;
				$this->CTags[$i]->TagValue = $str;
			}
			//获取语言变量
			else if( $CTag->TagName == 'lang' )
			{
				$this->CTags[$i]->IsReplace = true;
				//文档类指定的语言比模板里指定的语言优先
				if($this->handLang!='') 
					$this->CTags[$i]->TagValue = GetLang($this->CTags[$i]->GetAtt('name'), $this->handLang);
				else
					$this->CTags[$i]->TagValue = GetLang($this->CTags[$i]->GetAtt('name'), $this->tplLang);
			}
			//引入静态文件
			else if( $CTag->TagName == 'include' )
			{
				$filename = ($CTag->GetAtt('file')=='' ? $CTag->GetAtt('filename') : $CTag->GetAtt('file') );
				$str = $this->IncludeFile($filename,$CTag->GetAtt('ismake'));
				$this->CTags[$i]->IsReplace = true;
				$this->CTags[$i]->TagValue = $str;
			}

			//循环一个普通数组
			else if( $CTag->TagName == 'foreach' )
			{
				$arr = $this->CTags[$i]->GetAtt('array');
				if(isset($GLOBALS[$arr]))
				{
					foreach($GLOBALS[$arr] as $k=>$v)
					{
						$istr = '';
						$istr .= preg_replace("/\[field:key([\r\n\t\f ]+)\/\]/is",$k,$this->CTags[$i]->InnerText);
						$str .= preg_replace("/\[field:value([\r\n\t\f ]+)\/\]/is",$v,$istr);
					}
				}
				$this->CTags[$i]->IsReplace = true;
				$this->CTags[$i]->TagValue = $str;
			}

			//设置/获取变量值
			else if( $CTag->TagName == 'var' )
			{
				$vname = $this->CTags[$i]->GetAtt('name');
				if($vname=='')
				{
					$str = '';
				}
				else if($this->CTags[$i]->GetAtt('value')!='')
				{
					$_vars[$vname] = $this->CTags[$i]->GetAtt('value');
				}
				else
				{
					$str = (isset($_vars[$vname]) ? $_vars[$vname] : '');
				}
				$this->CTags[$i]->IsReplace = true;
				$this->CTags[$i]->TagValue = $str;
			}

			//运行PHP接口
			if( $CTag->GetAtt('runphp') == 'yes' )
			{
				$this->RunPHP($CTag,$i);
			}
			if(is_array($this->CTags[$i]->TagValue))
			{
				$this->CTags[$i]->TagValue = 'array';
			}
		}
	}

	//运行PHP代码
	function RunPHP(&$refObj,$i)
	{
		$DedeMeValue = $phpcode = '';
		if($refObj->GetAtt('source')=='value')
		{
			$phpcode = $this->CTags[$i]->TagValue;
		}
		else
		{
			$DedeMeValue = $this->CTags[$i]->TagValue;
			$phpcode = $refObj->GetInnerText();
		}
        $phpcode = preg_replace("/'@me'|\"@me\"|@me/i", '$DedeMeValue', $phpcode);
		@eval($phpcode); //or die("<xmp>$phpcode</xmp>");

		$this->CTags[$i]->TagValue = $DedeMeValue;
		$this->CTags[$i]->IsReplace = true;
	}

	//把分析模板输出到一个字符串中
	//不替换没被处理的值
	function GetResultNP()
	{
		$ResultString = '';
		if($this->Count==-1)
		{
			return $this->SourceString;
		}
		$this->AssignSysTag();
		$nextTagEnd = 0;
		$strok = "";
		for($i=0;$i<=$this->Count;$i++)
		{
			if($this->CTags[$i]->GetValue()!="")
			{
				if($this->CTags[$i]->GetValue()=='#@Delete@#')
				{
					$this->CTags[$i]->TagValue = "";
				}
				$ResultString .= substr($this->SourceString,$nextTagEnd,$this->CTags[$i]->StartPos-$nextTagEnd);
				$ResultString .= $this->CTags[$i]->GetValue();
				$nextTagEnd = $this->CTags[$i]->EndPos;
			}
		}
		$slen = strlen($this->SourceString);
		if($slen>$nextTagEnd)
		{
			$ResultString .= substr($this->SourceString,$nextTagEnd,$slen-$nextTagEnd);
		}
		return $ResultString;
	}

	//把分析模板输出到一个字符串中,并返回
	function GetResult()
	{
		$ResultString = '';
		if($this->Count==-1)
		{
			return $this->SourceString;
		}
		$this->AssignSysTag();
		$nextTagEnd = 0;
		$strok = "";
		for($i=0;$i<=$this->Count;$i++)
		{
			$ResultString .= substr($this->SourceString,$nextTagEnd,$this->CTags[$i]->StartPos-$nextTagEnd);
			$ResultString .= $this->CTags[$i]->GetValue();
			$nextTagEnd = $this->CTags[$i]->EndPos;
		}
		$slen = strlen($this->SourceString);
		if($slen>$nextTagEnd)
		{
			$ResultString .= substr($this->SourceString,$nextTagEnd,$slen-$nextTagEnd);
		}
		return $ResultString;
	}

	//直接输出分析模板
	function Display()
	{
		echo $this->GetResult();
	}

	//把分析模板输出为文件
	function SaveTo($filename)
	{
		$fp = @fopen($filename,"w") or die("DedeTag Engine Create File False");
		fwrite($fp,$this->GetResult());
		fclose($fp);
	}

	//解析模板
	function ParseTemplet()
	{
		$TagStartWord = $this->TagStartWord;
		$TagEndWord = $this->TagEndWord;
		$sPos = 0; $ePos = 0;
		$FullTagStartWord =  $TagStartWord.$this->NameSpace.":";
		$sTagEndWord =  $TagStartWord."/".$this->NameSpace.":";
		$eTagEndWord = "/".$TagEndWord;
		$tsLen = strlen($FullTagStartWord);
		$sourceLen=strlen($this->SourceString);
		
		if( $sourceLen <= ($tsLen + 3) )
		{
			return;
		}
		$cAtt = new DedeAttributeParse();
		$cAtt->charToLow = $this->CharToLow;

		//遍历模板字符串，请取标记及其属性信息
		for($i=0; $i < $sourceLen; $i++)
		{
			$tTagName = '';

			//如果不进行此判断，将无法识别相连的两个标记
			if($i-1 >= 0)
			{
				$ss = $i-1;
			}
			else
			{
				$ss = 0;
			}
			$sPos = strpos($this->SourceString,$FullTagStartWord,$ss);
			$isTag = $sPos;
			if($i==0)
			{
				$headerTag = substr($this->SourceString,0,strlen($FullTagStartWord));
				if($headerTag==$FullTagStartWord)
				{
					$isTag=true; $sPos=0;
				}
			}
			if($isTag===FALSE)
			{
				break;
			}
			//判断是否已经到倒数第三个字符(可能性几率极小，取消此逻辑)
			/*
			if($sPos > ($sourceLen-$tsLen-3) )
			{
				break;
			}
			*/
			for($j=($sPos+$tsLen);$j<($sPos+$tsLen+$this->TagMaxLen);$j++)
			{
				if($j>($sourceLen-1))
				{
					break;
				}
				else if( preg_match("/[\/ \t\r\n]/", $this->SourceString[$j]) || $this->SourceString[$j] == $this->TagEndWord )
				{
					break;
				}
				else
				{
					$tTagName .= $this->SourceString[$j];
				}
			}
			if($tTagName!='')
			{
				$i = $sPos+$tsLen;
				$endPos = -1;
				$fullTagEndWordThis = $sTagEndWord.$tTagName.$TagEndWord;
				
				$e1 = strpos($this->SourceString,$eTagEndWord, $i);
				$e2 = strpos($this->SourceString,$FullTagStartWord, $i);
				$e3 = strpos($this->SourceString,$fullTagEndWordThis,$i);
				
				//$eTagEndWord = /} $FullTagStartWord = {tag: $fullTagEndWordThis = {/tag:xxx]
				
				$e1 = trim($e1); $e2 = trim($e2); $e3 = trim($e3);
				$e1 = ($e1=='' ? '-1' : $e1);
				$e2 = ($e2=='' ? '-1' : $e2);
				$e3 = ($e3=='' ? '-1' : $e3);
				//not found '{/tag:'
				if($e3==-1) 
				{
					$endPos = $e1;
					$elen = $endPos + strlen($eTagEndWord);
				}
				//not found '/}'
				else if($e1==-1) 
				{
					$endPos = $e3;
					$elen = $endPos + strlen($fullTagEndWordThis);
				}
				//found '/}' and found '{/dede:'
				else
				{
					//if '/}' more near '{dede:'、'{/dede:' , end tag is '/}', else is '{/dede:'
					if($e1 < $e2 &&  $e1 < $e3 )
					{
						$endPos = $e1;
						$elen = $endPos + strlen($eTagEndWord);
					}
					else
					{
						$endPos = $e3;
						$elen = $endPos + strlen($fullTagEndWordThis);
					}
				}

				//not found end tag , error
				if($endPos==-1)
				{
					echo "Character postion $sPos, '$tTagName' Error！<br />\r\n";
					break;
				}
				$i = $elen;
				$ePos = $endPos;

				//分析所找到的标记位置等信息
				$attStr = '';
				$innerText = '';
				$startInner = 0;
				for($j=($sPos+$tsLen);$j < $ePos;$j++)
				{
					if($startInner==0 && ($this->SourceString[$j]==$TagEndWord && $this->SourceString[$j-1]!="\\") )
					{
						$startInner=1;
						continue;
					}
					if($startInner==0)
					{
						$attStr .= $this->SourceString[$j];
					}
					else
					{
						$innerText .= $this->SourceString[$j];
					}
				}
				//echo "<xmp>$attStr</xmp>\r\n";
				$cAtt->SetSource($attStr);
				if($cAtt->cAttributes->GetTagName()!='')
				{
					$this->Count++;
					$CDTag = new DedeTag();
					$CDTag->TagName = $cAtt->cAttributes->GetTagName();
					$CDTag->StartPos = $sPos;
					$CDTag->EndPos = $i;
					$CDTag->CAttribute = $cAtt->cAttributes;
					$CDTag->IsReplace = FALSE;
					$CDTag->TagID = $this->Count;
					$CDTag->InnerText = $innerText;
					$this->CTags[$this->Count] = $CDTag;
				}
			}
			else
			{
				$i = $sPos+$tsLen;
				break;
			}
		}//结束遍历模板字符串

		if($this->IsCache)
		{
			$this->SaveCache();
		}
	}

	//处理某字段的函数
	function EvalFunc($fieldvalue,$functionname,&$refObj)
	{
		$DedeFieldValue = $fieldvalue;
		$functionname = str_replace("{\"","[\"",$functionname);
		$functionname = str_replace("\"}","\"]",$functionname);
        $functionname = preg_replace("/'@me'|\"@me\"|@me/i",'$DedeFieldValue',$functionname);
		$functionname = "\$DedeFieldValue = ".$functionname;
		@eval($functionname.";"); //or die("<xmp>$functionname</xmp>");
		if(empty($DedeFieldValue))
		{
			return '';
		}
		else
		{
			return $DedeFieldValue;
		}
	}

	//获得一个外部变量
	function GetGlobals($varname)
	{
		$varname = trim($varname);

		//禁止在模板文件读取数据库密码
		if($varname=="dbuserpwd"||$varname=="cfg_dbpwd")
		{
			return "";
		}

		//正常情况
		if(isset($GLOBALS[$varname]))
		{
			return $GLOBALS[$varname];
		}
		else
		{
			return "";
		}
	}

	//引入文件
	function IncludeFile($filename,$ismake='no')
	{
		global $cfg_df_style;
		if($filename=='') return '';
		$restr = '';

		//查找路径
		//echo DEDEROOT.'/templets/'.$cfg_df_style.'/'.$this->tplLang.'/'.$filename;
		if(file_exists(DEDEROOT.'/templets/'.$cfg_df_style.'/'.$this->tplLang.'/'.$filename) ) 
		{
			$okfile = DEDEROOT.'/templets/'.$cfg_df_style.'/'.$this->tplLang.'/'.$filename;
		}
		else if(file_exists(DEDEROOT.'/templets/'.$cfg_df_style.'/en/'.$filename) ) 
		{
			$okfile = DEDEROOT.'/templets/'.$cfg_df_style.'/en/'.$filename;
		}
		else if( file_exists(DEDEROOT."/templets/".$filename) )
		{
			$okfile = DEDEROOT."/templets/".$filename;
		}
		else {
			return "无法在这个位置找到： $filename";
		}

		//编译
		if($ismake != 'no')
		{
			require_once(DEDEINC."/channelunit.func.php");
			$dtp = new DedeTagParse();
			$dtp->LoadTemplet($okfile);
			$dtp->tplLang = $this->tplLang;
			$dtp->handLang = $this->handLang;
			MakeOneTag($dtp,$this->refObj);
			$restr = $dtp->GetResult();
		}
		else
		{
			$fp = @fopen($okfile, 'r');
			while($line = fgets($fp,1024)) $restr .= $line;
			fclose($fp);
		}
		return $restr;
	}
}

/**********************************************
//class DedeAttribute Dede模板标记属性集合
function c____DedeAttribute();
**********************************************/
//属性的数据描述
class DedeAttribute
{
	var $Count = -1;
	var $Items = ""; //属性元素的集合
	//获得某个属性
	function GetAtt($str)
	{
		if($str=="")
		{
			return "";
		}
		if(isset($this->Items[$str]))
		{
			return $this->Items[$str];
		}
		else
		{
			return "";
		}
	}

	//同上
	function GetAttribute($str)
	{
		return $this->GetAtt($str);
	}

	//判断属性是否存在
	function IsAttribute($str)
	{
		if(isset($this->Items[$str])) return true;
		else return false;
	}

	//获得标记名称
	function GetTagName()
	{
		return $this->GetAtt("tagname");
	}

	// 获得属性个数
	function GetCount()
	{
		return $this->Count+1;
	}
}

/*******************************
//属性解析器(本版本中已经支持使用\'这种语法,和用.间隔表示name属性,如 field.body)
function c____DedeAttributeParse();
********************************/
class DedeAttributeParse
{
	var $sourceString = "";
	var $sourceMaxSize = 1024;
	var $cAttributes = "";
	var $charToLow = true;
	function SetSource($str='')
	{
		$this->cAttributes = new DedeAttribute();
		$strLen = 0;
		$this->sourceString = trim(preg_replace("/[ \r\n\t]{1,}/"," ",$str));
		
		//为了在function内能使用数组，这里允许对[ ]进行转义使用
		$this->sourceString = str_replace('\]',']',$this->sourceString);
		$this->sourceString = str_replace('[','[',$this->sourceString);
		/*
		$this->sourceString = str_replace('\>','>',$this->sourceString);
		$this->sourceString = str_replace('<','>',$this->sourceString);
		$this->sourceString = str_replace('{','{',$this->sourceString);
		$this->sourceString = str_replace('\}','}',$this->sourceString);
		*/
		
		$strLen = strlen($this->sourceString);
		if($strLen>0 && $strLen <= $this->sourceMaxSize)
		{
			$this->ParseAttribute();
		}
	}

	//解析属性
	function ParseAttribute()
	{
		$d = '';
		$tmpatt = '';
		$tmpvalue = '';
		$startdd = -1;
		$ddtag = '';
		$hasAttribute=false;
		$strLen = strlen($this->sourceString);

		// 获得Tag的名称，解析到 cAtt->GetAtt('tagname') 中
		for($i=0; $i<$strLen; $i++)
		{
			if($this->sourceString[$i]==' ')
			{
				$this->cAttributes->Count++;
				$tmpvalues = explode('.', $tmpvalue);
				$this->cAttributes->Items['tagname'] = ($this->charToLow ? strtolower($tmpvalues[0]) : $tmpvalues[0]);
				if(isset($tmpvalues[1]) && $tmpvalues[1]!='')
				{
					$this->cAttributes->Items['name'] = $tmpvalues[1];
				}
				$tmpvalue = '';
				$hasAttribute = true;
				break;
			}
			else
			{
				$tmpvalue .= $this->sourceString[$i];
			}
		}

		//不存在属性列表的情况
		if(!$hasAttribute)
		{
			$this->cAttributes->Count++;
			$tmpvalues = explode('.', $tmpvalue);
			$this->cAttributes->Items['tagname'] = ($this->charToLow ? strtolower($tmpvalues[0]) : $tmpvalues[0]);
			if(isset($tmpvalues[1]) && $tmpvalues[1]!='')
			{
				$this->cAttributes->Items['name'] = $tmpvalues[1];
			}
			return ;
		}
		$tmpvalue = '';

		//如果字符串含有属性值，遍历源字符串,并获得各属性
		for($i; $i<$strLen; $i++)
		{
			$d = $this->sourceString[$i];
			//查找属性名称
			if($startdd==-1)
			{
				if($d != '=')
				{
					$tmpatt .= $d;
				}
				else
				{
					if($this->charToLow)
					{
						$tmpatt = strtolower(trim($tmpatt));
					}
					else
					{
						$tmpatt = trim($tmpatt);
					}
					$startdd=0;
				}
			}

			//查找属性的限定标志
			else if($startdd==0)
			{
				switch($d)
				{
					case ' ':
						break;
					case '"':
						$ddtag = '"';
						$startdd = 1;
						break;
					case '\'':
						$ddtag = '\'';
						$startdd = 1;
						break;
					default:
						$tmpvalue .= $d;
						$ddtag = ' ';
						$startdd = 1;
						break;
				}
			}
			else if($startdd==1)
			{
				if($d==$ddtag && ( isset($this->sourceString[$i-1]) && $this->sourceString[$i-1]!="\\") )
				{
					$this->cAttributes->Count++;
					$this->cAttributes->Items[$tmpatt] = trim($tmpvalue);
					$tmpatt = '';
					$tmpvalue = '';
					$startdd = -1;
				}
				else
				{
					$tmpvalue .= $d;
				}
			}
		}//for

		//最后一个属性的给值
		if($tmpatt != '')
		{
			$this->cAttributes->Count++;
			$this->cAttributes->Items[$tmpatt] = trim($tmpvalue);
		}
		//print_r($this->cAttributes->Items);
	}// end func
}
?>