<?php
define('EmpireCMSAdmin','1');
require("../class/connect.php");
require("../class/db_sql.php");
require("../class/functions.php");
require LoadLang("pub/fun.php");
require("../data/dbcache/class.php");
$link=db_connect();
$empire=new mysqlquery();
//���ҥΤ�
$lur=is_login();
$logininid=$lur['userid'];
$loginin=$lur['username'];
$loginrnd=$lur['rnd'];
$loginlevel=$lur['groupid'];
$loginadminstyleid=$lur['adminstyleid'];
//ehash
$ecms_hashur=hReturnEcmsHashStrAll();

$classid=(int)$_GET['classid'];
$bclassid=(int)$class_r[$classid]['bclassid'];
//���o��ئW
if(!$class_r[$classid][classid]||!$class_r[$classid][tbname])
{
	printerror("ErrorUrl","history.go(-1)");
}
//�����v��
$doselfinfo=CheckLevel($logininid,$loginin,$classid,"news");
//���o�ҫ���
$fieldexp="<!--field--->";
$recordexp="<!--record-->";
//��^�j���r�q�C��
function ReturnSearchOptions($enter,$field,$record){
	global $modid,$emod_r;
	$r=explode($record,$enter);
	$count=count($r)-1;
	for($i=0;$i<$count;$i++)
	{
		if($i==0)
		{
			$or="";
		}
		else
		{
			$or=" or ";
		}
		$r1=explode($field,$r[$i]);
		if($r1[1]=="special.field"||strstr($emod_r[$modid]['tbdataf'],','.$r1[1].','))
		{
			continue;
		}
		if($r1[1]=="id")
		{
			$sr['searchallfield'].=$or.$r1[1]."='[!--key--]'";
			$sr['select'].="<option value=\"".$r1[1]."\">".$r1[0]."</option>";
			continue;
		}
		$sr['searchallfield'].=$or.$r1[1]." like '%[!--key--]%'";
		$sr['select'].="<option value=\"".$r1[1]."\">".$r1[0]."</option>";
	}
	return $sr;
}
$modid=(int)$class_r[$classid][modid];
$infomod_r=$empire->fetch1("select enter,tbname,sonclass,listfile from {$dbtbpre}enewsmod where mid=".$modid);
if(empty($infomod_r['tbname']))
{
	printerror("ErrorUrl","history.go(-1)");
}
$infomod_r['enter'].='�o�G��<!--field--->username<!--record-->ID<!--field--->id<!--record-->';
$searchoptions_r=ReturnSearchOptions($infomod_r['enter'],$fieldexp,$recordexp);
//�f�֪�
$search='';
$search.=$ecms_hashur['ehref'];
$addecmscheck='';
$ecmscheck=(int)$_GET['ecmscheck'];
$indexchecked=1;
if($ecmscheck)
{
	$search.='&ecmscheck='.$ecmscheck;
	$addecmscheck='&ecmscheck='.$ecmscheck;
	$indexchecked=0;
}
$infotb=ReturnInfoMainTbname($class_r[$classid][tbname],$indexchecked);
//�ɯ�
$url=AdminReturnClassLink($classid).'&nbsp;>&nbsp;�޲z�k��&nbsp;&nbsp;(<a href="AddNews.php?enews=AddNews&bclassid='.$bclassid.'&classid='.$classid.$addecmscheck.$ecms_hashur['ehref'].'">�W�[�H��</a>)';
$start=0;
$page=(int)$_GET['page'];
$page=RepPIntvar($page);
$line=intval($public_r['hlistinfonum']);//�C�����
$page_line=16;
$offset=$page*$line;
$search.="&bclassid=$bclassid&classid=$classid";
$add='';
$ewhere='';
//�j��
$sear=RepPostStr($_GET['sear'],1);
if($sear)
{
	$keyboard=RepPostVar2($_GET['keyboard']);
	$show=RepPostVar($_GET['show']);
	//����r
	if($keyboard)
	{
		//�j������
		if(!$show)
		{
			$add=" and (".str_replace("[!--key--]",$keyboard,$searchoptions_r['searchallfield']).")";
		}
		//�j���r�q
		elseif($show&&strstr($infomod_r['enter'],"<!--field--->".$show."<!--record-->"))
		{
			$add=$show!="id"?" and (".$show." like '%$keyboard%')":" and (".$show."='$keyboard')";
			$searchoptions_r['select']=str_replace(" value=\"".$show."\">"," value=\"".$show."\" selected>",$searchoptions_r['select']);
		}
	}
	//���D����
	$ttid=(int)$_GET['ttid'];
	if($ttid)
	{
		$add.=" and ttid='$ttid'";
	}
	$search.="&sear=1&keyboard=$keyboard&show=$show&ttid=$ttid";
}
//�u��s��ۤv���H��
if($doselfinfo['doselfinfo'])
{
	$add.=" and userid='$logininid' and ismember=0";
}
//����س��
$singletable=0;
if($infomod_r[sonclass]=='|'.$classid.'|')
{
	$singletablenum=$empire->gettotal("select count(*) as total from {$dbtbpre}enewsclass where tbname='".$class_r[$classid][tbname]."' and islast=1");
	$singletable=$singletablenum==1?1:0;
}
if($infomod_r[sonclass]=='|'.$classid.'|'&&$singletable==1)
{
	$ewhere=$add?' where '.substr($add,5):'';
}
else
{
	$ewhere=" where classid='$classid'".$add;
}
//�έp
$totalquery="select count(*) as total from {$dbtbpre}ecms_".$class_r[$classid][tbname]."_doc".$ewhere;
$totalnum=(int)$_GET['totalnum'];
if($totalnum<1)
{
	$num=$empire->gettotal($totalquery);//���o�`����
}
else
{
	$num=$totalnum;
}
//�Ƨ�
$myorder=RepPostStr($_GET['myorder'],1);
if($myorder==1)//�ɶ�
{$doorder="newstime desc";}
elseif($myorder==2)//���׼�
{$doorder="plnum desc";}
elseif($myorder==3)//�H��
{$doorder="onclick desc";}
elseif($myorder==4)//ID��
{$doorder="id desc";}
else//�q�{�Ƨ�
{
	$thisclassr=$empire->fetch1("select listorder from {$dbtbpre}enewsclass where classid='$classid'");
	if(empty($thisclassr[listorder]))
	{
		$doorder="id desc";
	}
	else
	{
		$doorder=$thisclassr[listorder];
	}
}
$search.="&totalnum=$num";
$search1=$search;
$search.="&myorder=$myorder";
$returnpage=page2($num,$line,$page_line,$start,$page,$search);
$phpmyself=urlencode(eReturnSelfPage(1));
//���D����
$tts='';
$ttsql=$empire->query("select typeid,tname from {$dbtbpre}enewsinfotype where mid='$modid' order by myorder");
while($ttr=$empire->fetch($ttsql))
{
	$select='';
	if($ttr[typeid]==$ttid)
	{
		$select=' selected';
	}
	$tts.="<option value='$ttr[typeid]'".$select.">$ttr[tname]</option>";
}
$stts=$tts?"<select name='ttid'><option value='0'>���D����</option>$tts</select>":"";
//����챵
$getcurlr['classid']=$classid;
$classurl=sys_ReturnBqClassname($getcurlr,9);
//�ɤJ����
$deftempfile=ECMS_PATH.'e/data/html/list/doclistinfo.php';
if($infomod_r[listfile])
{
	$tempfile=ECMS_PATH.'e/data/html/list/doc'.$infomod_r[listfile].'.php';
	if(!file_exists($tempfile))
	{
		$tempfile=$deftempfile;
	}
}
else
{
	$tempfile=$deftempfile;
}
require($tempfile);
db_close();
$empire=null;
?>