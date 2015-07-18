<?php
define('EmpireCMSAdmin','1');
require("../class/connect.php");
require("../class/db_sql.php");
require("../class/functions.php");
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

$enews=ehtmlspecialchars($_GET['enews']);
$classid=(int)$_GET['classid'];
if(empty($class_r[$classid][classid]))
{
	printerror("ErrorUrl","history.go(-1)");
}
//�����v��
$doselfinfo=CheckLevel($logininid,$loginin,$classid,"news");
if(!$class_r[$classid][tbname]||!$class_r[$classid][classid])
{
	printerror("ErrorUrl","history.go(-1)");
}
//�D�׷����
if(!$class_r[$classid]['islast'])
{
	printerror("AddInfoErrorClassid","history.go(-1)");
}
$fun_r['AdminInfo']='�޲z�H��';
$bclassid=$class_r[$classid][bclassid];
$id=(int)$_GET['id'];
//�������ҽX
if($enews=="AddNews")
{
	if(!$doselfinfo['doaddinfo'])//�W�[�v��
	{
		printerror("NotAddInfoLevel","history.go(-1)");
	}
	$filepass=time();
	$word='�W�[�H��';
	$ecmsfirstpost=1;
}
else
{
	if(!$doselfinfo['doeditinfo'])//�s���v��
	{
		printerror("NotEditInfoLevel","history.go(-1)");
	}
	$filepass=$id;
	$word='�ק�H��';
	$ecmsfirstpost=0;
}
//�f��
$ecmscheck=(int)$_GET['ecmscheck'];
$addecmscheck='';
$indexchecked=1;
if($ecmscheck)
{
	$addecmscheck='&ecmscheck='.$ecmscheck;
	$indexchecked=0;
}
//�ҫ�
$modid=$class_r[$classid][modid];
$enter=$emod_r[$modid]['enter'];
//�ɯ�
$url=AdminReturnClassLink($classid).'&nbsp;>&nbsp;'.$word;
//�|����
$sql1=$empire->query("select groupid,groupname from {$dbtbpre}enewsmembergroup order by level");
while($l_r=$empire->fetch($sql1))
{
	$ygroup.="<option value=".$l_r[groupid].">".$l_r[groupname]."</option>";
}
if($enews=="AddNews")
{
	$group=str_replace(" value=".$class_r[$classid][groupid].">"," value=".$class_r[$classid][groupid]." selected>",$ygroup);
}
//��l�Ƽƾ�
$r=array();
$newstime=time();
$r[newstime]=date("Y-m-d H:i:s");
$todaytime=$r[newstime];
$r[checked]=$class_r[$classid][checked];
$r[newspath]=date($class_r[$classid][newspath]);
$r[onclick]=0;
$r[userfen]=0;
$titlefontb="";
$titlefonti="";
$titlefonts="";
$voteeditnum=8;
$voter[width]=500;
$voter[height]=300;
$voter[dotime]='0000-00-00';
$r[dokey]=1;
$titleurl='';
if($public_r['onclickrnd'])
{
	$onclick_rndr=explode(',',$public_r['onclickrnd']);
	$r[onclick]=rand(intval($onclick_rndr[0]),intval($onclick_rndr[1]));
	$r[totaldown]=$r[onclick];
}
else
{
	$r[totaldown]=0;
	$r[onclick]=0;
}
//----------- �S���ҫ���l�� -----------
//�U���a�}�e��
if(strstr($enter,',downpath,')||strstr($enter,',onlinepath,'))
{
	$downurlqz="";
	$newdownqz="";
	$downsql=$empire->query("select urlname,url,urlid from {$dbtbpre}enewsdownurlqz order by urlid");
	while($downr=$empire->fetch($downsql))
	{
		$downurlqz.="<option value='".$downr[url]."'>".$downr[urlname]."</option>";
		$newdownqz.="<option value='".$downr[urlid]."'>".$downr[urlname]."</option>";
	}
}
//html�s�边
if($emod_r[$modid]['editorf']&&$emod_r[$modid]['editorf']!=',')
{
	include('ecmseditor/infoeditor/fckeditor.php');
}

//�w�]�벼
if($enews=="AddNews")
{
	$infoclassr=$empire->fetch1("select definfovoteid from {$dbtbpre}enewsclass where classid='$classid'");
	$definfovoteid=0;
	if($infoclassr['definfovoteid'])
	{
		$definfovoteid=$infoclassr['definfovoteid'];
	}
	elseif($emod_r[$modid]['definfovoteid'])
	{
		$definfovoteid=$emod_r[$modid]['definfovoteid'];
	}
	if($definfovoteid)
	{
		//�벼
		$voter=$empire->fetch1("select * from {$dbtbpre}enewsvotemod where voteid='$definfovoteid'");
		if($voter['voteid']&&$voter[votetext])
		{
			$d_record=explode("\r\n",$voter[votetext]);
			for($i=0;$i<count($d_record);$i++)
			{
				$j=$i+1;
				$d_field=explode("::::::",$d_record[$i]);
				$allvote.="<tr><td width='9%'><div align=center>".$j."</div></td><td width='65%'><input name=vote_name[] type=text value='".$d_field[0]."' size=30></td><td width='26%'><input name=vote_num[] type=text value='".$d_field[1]."' size=6></td></tr>";
			}
			$voteeditnum=$j;
			$allvote="<table width='100%' border=0 cellspacing=1 cellpadding=3>".$allvote."</table>";
		}
	}
}

//-----------------------------------------�ק�H��
if($enews=="EditNews")
{
	//���ު�
	$index_r=$empire->fetch1("select id,classid,checked from {$dbtbpre}ecms_".$class_r[$classid][tbname]."_index where id='$id' limit 1");
	if(!$index_r['id']||$index_r['classid']!=$classid)
	{
		printerror("ErrorUrl","history.go(-1)");
	}
	//��^��
	$infotb=ReturnInfoMainTbname($class_r[$classid][tbname],$index_r['checked']);
	//�D��
	$r=$empire->fetch1("select * from ".$infotb." where id='$id' limit 1");
	//ñ�o��
	if($r[isqf])
	{
		$wfinfor=$empire->fetch1("select tstatus,checktno from {$dbtbpre}enewswfinfo where id='$id' and classid='$classid' limit 1");
	}
	//�u��s��ۤv���H��
	if($doselfinfo['doselfinfo']&&($r[userid]<>$logininid||$r[ismember]))
	{
		printerror("NotDoSelfinfo","history.go(-1)");
	}
	//�w�f�֫H�����i�ק�
	if($doselfinfo['docheckedit']&&$index_r['checked'])
	{
		printerror("NotEditCheckInfoLevel","history.go(-1)");
	}
	//��^���H��
	$infodatatb=ReturnInfoDataTbname($class_r[$classid][tbname],$index_r['checked'],$r['stb']);
	//�ƪ�
	$finfor=$empire->fetch1("select ".ReturnSqlFtextF($modid)." from ".$infodatatb." where id='$id' limit 1");
	$r=array_merge($r,$finfor);
	//�ɶ�
	$r['checked']=$index_r['checked'];
	$newstime=$r['newstime'];
	$r['newstime']=date("Y-m-d H:i:s",$r['newstime']);
	//�챵�a�}
	$titleurl=$r['titleurl'];
	if(!$r['isurl'])
	{
		$r['titleurl']='';
	}
	//�|����
	$group=str_replace(" value=".$r[groupid].">"," value=".$r[groupid]." selected>",$ygroup);
	//���e�s�奻
	$savetxtf=$emod_r[$modid]['savetxtf'];
	$newstext_url='';
	if($savetxtf)
	{
		$newstext_url=$r[$savetxtf];
		$r[$savetxtf]=GetTxtFieldText($r[$savetxtf]);
    }
	//���D�ݩ�
	if(strstr($r[titlefont],','))
	{
		$tfontr=explode(',',$r[titlefont]);
		$r[titlecolor]=$tfontr[0];
		$r[titlefont]=$tfontr[1];
	}
	if(strstr($r[titlefont],"b|"))
	{
		$titlefontb=" checked";
	}
	if(strstr($r[titlefont],"i|"))
	{
		$titlefonti=" checked";
	}
	if(strstr($r[titlefont],"s|"))
	{
		$titlefonts=" checked";
	}
	//�벼
	$pubid=ReturnInfoPubid($classid,$id);
	$voter=$empire->fetch1("select * from {$dbtbpre}enewsinfovote where pubid='$pubid' limit 1");
	if($voter['id']&&$voter[votetext])
	{
		$d_record=explode("\r\n",$voter[votetext]);
		for($i=0;$i<count($d_record);$i++)
		{
			$j=$i+1;
			$d_field=explode("::::::",$d_record[$i]);
			$allvote.="<tr><td width='9%'><div align=center>".$j."</div></td><td width='65%'><input name=vote_name[] type=text value='".$d_field[0]."' size=30></td><td width='26%'><input name=vote_num[] type=text value='".$d_field[1]."' size=6><input type=hidden name=vote_id[] value=".$j."><input type=checkbox name=delvote_id[] value=".$j.">�R��</td></tr>";
		}
		$voteeditnum=$j;
		$allvote="<table width='100%' border=0 cellspacing=1 cellpadding=3>".$allvote."</table>";
	}
}
//���D����
$cttidswhere='';
$tts='';
$caddr=$empire->fetch1("select ttids from {$dbtbpre}enewsclassadd where classid='$classid'");
if($caddr['ttids']!='-')
{
	if($caddr['ttids']&&$caddr['ttids']!=',')
	{
		$cttidswhere=' and typeid in ('.substr($caddr['ttids'],1,-1).')';
	}
	$ttsql=$empire->query("select typeid,tname from {$dbtbpre}enewsinfotype where mid='$modid'".$cttidswhere." order by myorder");
	while($ttr=$empire->fetch($ttsql))
	{
		$select='';
		if($ttr[typeid]==$r[ttid])
		{
			$select=' selected';
		}
		$tts.="<option value='$ttr[typeid]'".$select.">$ttr[tname]</option>";
	}
}
//���e�ҪO
$t_sql=$empire->query("select tempid,tempname from ".GetTemptb("enewsnewstemp")." order by modid,tempid");
while($nt=$empire->fetch($t_sql))
{
	if($nt[tempid]==$r[newstempid])
	{
		$select=" selected";
	}
	else
	{
		$select="";
	}
	$newstemp.="<option value=".$nt[tempid].$select.">".$nt[tempname]."</option>";
}
//�ҪO
$votetemp="";
$vtsql=$empire->query("select tempid,tempname from ".GetTemptb("enewsvotetemp")." order by tempid");
while($vtr=$empire->fetch($vtsql))
{
	if($voter[tempid]==$vtr[tempid])
	{
		$select=" selected";
	}
	else
	{
		$select="";
	}
	$votetemp.="<option value='".$vtr[tempid]."'".$select.">".$vtr[tempname]."</option>";
}
//�P�ɵo�G
if(empty($voter['copyids'])||$voter['copyids']=='1')
{
	$copyclassidshowiframe='<IFRAME frameBorder="0" id="showclassnav" name="showclassnav" scrolling="no" src="ShowClassNav.php?ecms=1'.$ecms_hashur['ehref'].'" style="HEIGHT:0;VISIBILITY:inherit;WIDTH:0;Z-INDEX:1"></IFRAME>';
	$copyclassids='<table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#DBEAF5">
              <tr>
                <td>�P�ɵo�G��H�U���: <input type="checkbox" name="copyinfotitleurl" value="1">�ĥί����챵</td>
              </tr>
              <tr>
                <td height="25" bgcolor="#FFFFFF" id="copyinfoshowclassnav"></td>
              </tr>
            </table>';
}
else
{
	$copyclassidshowiframe='';
	$copyclassids='<table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#DBEAF5">
              <tr>
                <td>�P�ɵo�G��H�U���:</td>
              </tr>
              <tr>
                <td height="25" bgcolor="#FFFFFF" id="copyinfoshowclassnav">���H���w�P�B�o�G���L���,�H��ID:<br>'.$voter[copyids].'</td>
              </tr>
            </table>';
}
//������
$modfile="../data/html/".$modid.".php";
//����챵
$getcurlr['classid']=$classid;
$classurl=sys_ReturnBqClassname($getcurlr,9);
//���e�ϥΪ��ҪO��
$thegid=GetDoTempGid();
$phpmyself=urlencode(eReturnSelfPage(1));
//��^�Y���M���˯ŧO�W��
$ftnr=ReturnFirsttitleNameList($r['firsttitle'],$r['isgood']);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<title><?=$word?></title>
<link rel="stylesheet" href="adminstyle/<?=$loginadminstyleid?>/adminstyle.css" type="text/css">
<link id="luna-tab-style-sheet" type="text/css" rel="stylesheet" href="adminstyle/<?=$loginadminstyleid?>/tab.winclassic.css" disabled="disabled" /> 
<!-- the id is not needed. It is used here to be able to change css file at runtime -->
<style type="text/css"> 
   .dynamic-tab-pane-control .tab-page { 
          width:                100%;
 } 
  .dynamic-tab-pane-control .tab-page .dynamic-tab-pane-control .tab-page { 
         height:                150px; 
 } 
  form { 
         margin:        0; 
         padding:        0; 
 } 
  /* over ride styles from webfxlayout */ 
  .dynamic-tab-pane-control h2 { 
         font-size:12px;
		 font-weight:normal;
		 text-align:        center; 
         width:                auto;
		 height:            20; 
 } 
   .dynamic-tab-pane-control h2 a { 
         display:        inline; 
         width:                auto; 
 } 
  .dynamic-tab-pane-control a:hover { 
         background: transparent; 
 } 
  </style>
 <script type="text/javascript" src="../data/images/tabpane.js"></script> <script type="text/javascript"> 
  function setLinkSrc( sStyle ) { 
         document.getElementById( "luna-tab-style-sheet" ).disabled = sStyle != "luna"; 
  
         //document.documentElement.style.background = "";
         //document.body.style.background = sStyle == "webfx" ? "white" : "ThreeDFace"; 
 } 
function chgBg(obj,color){
 if (document.all || document.getElementById)
   obj.style.backgroundColor=color;
 else if (document.layers)
   obj.bgColor=color;
}
  setLinkSrc( "luna" ); 
</script>
<script>
function dovoteadd(){
	var i;
	var str="";
	var oldi=0;
	var j=0;
	oldi=parseInt(document.add.v_editnum.value);
	for(i=1;i<=document.add.v_vote_num.value;i++)
	{
		j=i+oldi;
		str=str+"<tr><td width='9%' height=20> <div align=center>"+j+"</div></td><td width='65%'> <div align=center><input type=text name=vote_name[] size=30></div></td><td width='26%'> <div align=center><input type=text name=vote_num[] value=0 size=6></div></td></tr>";
	}
	document.getElementById('addvote').innerHTML="<table width='100%' border=0 cellspacing=1 cellpadding=3>"+str+"</table>";
}

function doSpChangeFile(name,url,filesize,filetype,idvar){
	document.getElementById(idvar).value=url;
	if(document.add.filetype!=null)
	{
		if(document.add.filetype.value=='')
		{
			document.add.filetype.value=filetype;
		}
	}
	if(document.add.filesize!=null)
	{
		if(document.add.filesize.value=='')
		{
			document.add.filesize.value=filesize;
		}
	}
}

function SpOpenChFile(type,field){
	window.open('ecmseditor/FileMain.php?<?=$ecms_hashur['ehref']?>&classid=<?=$classid?>&infoid=<?=$id?><?=$addecmscheck?>&filepass=<?=$filepass?>&type='+type+'&sinfo=1&tranfrom=2&field='+field,'','width=700,height=550,scrollbars=yes');
}

//�W�Ǧh�����,�϶�
function eTranMoreForMorepic(htmlstr,fnum){
	var morepicdiv=document.getElementById("defmorepicid").innerHTML;
	var thismorepicnum=parseInt(document.add.morepicnum.value);
	var enews="<?=$enews?>";
	if(enews=='AddNews')
	{
		if(document.add.havetmpic.value==0)
		{
			document.getElementById("defmorepicid").innerHTML+=htmlstr;
			document.add.morepicnum.value=thismorepicnum+fnum;
		}
		else
		{
			document.getElementById("defmorepicid").innerHTML+=htmlstr;
			document.add.morepicnum.value=thismorepicnum+fnum;
		}
	}
	else
	{
		document.getElementById("defmorepicid").innerHTML+=htmlstr;
		document.add.morepicnum.value=thismorepicnum+fnum;
	}
	document.getElementById("addpicdown").innerHTML="";
	document.add.havetmpic.value=1;
}

</script>
<script src="ecmseditor/fieldfile/setday.js"></script>
<script src="../data/html/postinfo.js"></script>
<script>
function bs(){
	var f=document.add;
	if(f.title.value.length==0){alert("���D�٨S�g");f.title.focus();return false;}
}
function foreColor(){
  if(!Error())	return;
  var arr = showModalDialog("../data/html/selcolor.html", "", "dialogWidth:18.5em; dialogHeight:17.5em; status:0");
  if (arr != null) document.add.titlecolor.value=arr;
  else document.add.titlecolor.focus();
}
function FieldChangeColor(obj){
  if(!Error())	return;
  var arr = showModalDialog("../data/html/selcolor.html", "", "dialogWidth:18.5em; dialogHeight:17.5em; status:0");
  if (arr != null) obj.value=arr;
  else obj.focus();
}
</script>
</head>

<body bgcolor="#FFFFFF" text="#000000" onload="document.add.title.focus();">
<table width="100%" border="0" align="center" cellpadding="3" cellspacing="1">
  <tr> 
    <td width="62%" height="25">��m�G 
      <?=$url?>
    </td>
    <td width="38%"><div align="right">
	<?=$enews=='EditNews'?'[<a href="user/ListDolog.php?classid='.$classid.'&id='.$id.$ecms_hashur['ehref'].'" target="_blank">�d�ݥ��H���ާ@��x</a>]':''?>
      </div></td>
  </tr>
</table>
<table width="100%" border="0" cellspacing="1" cellpadding="3" class="tableborder">
  <form name="searchinfo" method="GET" action="ListNews.php">
  <?=$ecms_hashur['eform']?>
    <tr> 
      <td width="42%" title="�W�[�H����ϥΥ��ާ@�N�H����ܨ�e�x"> <select name="dore">
          <option value="1">��s���e���</option>
          <option value="2">��s����</option>
          <option value="3">��s�����</option>
          <option value="4">��s���e��ػP�����</option>
          <option value="5">��s����ػP����</option>
          <option value="6" selected>��s���e��ءB����ػP����</option>
        </select> <input type="button" name="Submit12" value="����" onclick="self.location.href='ecmsinfo.php?<?=$ecms_hashur['href']?>&enews=AddInfoToReHtml<?=$addecmscheck?>&classid=<?=$classid?>&dore='+document.searchinfo.dore.value;"> 
      </td>
      <td width="58%"><div align="right">[<font color="#ffffff"><a href=../../ target=_blank>�w������</a></font>] 
          [<font color="#ffffff"><a href="<?=$classurl?>" target=_blank>�w�����</a></font>] 
          [<font color="#ffffff"><a href="file/ListFile.php?type=9&classid=<?=$classid?><?=$ecms_hashur['ehref']?>">����޲z</a></font>] 
          [<a href="AddClass.php?enews=EditClass&classid=<?=$classid?><?=$ecms_hashur['ehref']?>">��س]�m</a>] 
          [<a href="ecmschtml.php?enews=ReAllNewsJs&from=<?=$phpmyself?><?=$ecms_hashur['ehref']?>">��s�Ҧ��H��JS</a>] 
        </div></td>
    </tr>
	</form>
	<?php
	if($enews=='EditNews')
	{
	?>
	<form name="doinfoform" method="post" action="ecmsinfo.php" onsubmit="return confirm('�T�{�n���榹�ާ@?');">
		<?=$ecms_hashur['form']?>
    <tr bgcolor="#FFFFFF"> 
      <td height="32" colspan="2"> <div align="right">��]: 
          <input name="causetext" type="text" id="causetext">
          <input type="submit" name="Submit3" value="�f�ֳq�L" onclick="document.doinfoform.doing.value='2';">
          <input type="submit" name="Submit32" value="�����f��" onclick="document.doinfoform.doing.value='3';">
          <input type="submit" name="Submit33" value="�R��" onclick="document.doinfoform.doing.value='1';">
          <font color="#666666">�]�����]���ܤ��q���|���^</font>
          <input name="enews" type="hidden" id="enews" value="DoInfoAndSendNotice">
		  <input name="bclassid" type="hidden" id="bclassid" value="<?=$bclassid?>">
          <input name="classid" type="hidden" id="classid" value="<?=$classid?>">
          <input name="id" type="hidden" id="id" value="<?=$id?>">
          <input name="ecmsfrom" type="hidden" id="ecmsfrom" value="<?=RepPostStrUrl($_SERVER['HTTP_REFERER'])?>">
          <input name="doing" type="hidden" id="doing">
          <input name="ecmscheck" type="hidden" id="ecmscheck" value="<?=$ecmscheck?>">
        </div></td>
    </tr>
	</form>
	<?php
	}
	?>
</table>
<br>
<form name="add" method="post" enctype="multipart/form-data" action="ecmsinfo.php" onsubmit="return EmpireCMSInfoPostFun(document.add,'<?=$modid?>');">
<?=$ecms_hashur['form']?>
<div class="tab-pane" id="TabPane1">
	<script type="text/javascript">
	tb1 = new WebFXTabPane( document.getElementById( "TabPane1" ) );
	</script>
	<div class="tab-page" id="baseinfo">        
		<h2 class="tab">&nbsp;<font class=tabcolor>�򥻫H��</font>&nbsp;</h2>
		<script type="text/javascript">tb1.addTabPage( document.getElementById( "baseinfo" ) );</script>
		<table width="100%" align="center" cellpadding="3" cellspacing="1" class="tableborder">
			<tr class="header"> 
				<td width="16%" height="25">
					<div align="left"><?=$word?></div>
				</td>
				<td>
					<input type="submit" name="addnews2" value="����"> <input type="reset" name="Submit23" value="���m">
					<input type=hidden value=<?=$enews?> name=enews> <input type=hidden value=<?=$classid?> name=classid> 
					<input type=hidden value=<?=$bclassid?> name=bclassid> <input name=id type=hidden value=<?=$id?>> 
					<input type=hidden value="<?=$filepass?>" name=filepass> <input type=hidden value="<?=$r[username]?>" name=username> 
					<input name="oldfilename" type="hidden" value="<?=$r[filename]?>">  
					<input name="oldgroupid" type="hidden" value="<?=$r[groupid]?>"> 
					<input name="oldchecked" type="hidden" value="<?=$r[checked]?>">  
					<input name="newstext_url" type="hidden" value="<?=$newstext_url?>">
					<input name="ecmsfrom" type="hidden" value="<?=RepPostStrUrl($_SERVER['HTTP_REFERER'])?>">
					<input name="ecmsnfrom" type="hidden" value="<?=RepPostStrUrl($_GET['ecmsnfrom'])?>">
					<input name="fstb" type="hidden" value="<?=$r[fstb]?>">
					<input name="oldttid" type="hidden" value="<?=$r[ttid]?>">
					<input name="ecmscheck" type="hidden" id="ecmscheck" value="<?=$ecmscheck?>">
            <input name="ztids" type="hidden" id="ztids">
            <input name="zcids" type="hidden" id="zcids">
            <input name="oldztids" type="hidden" id="oldztids">
            <input name="oldzcids" type="hidden" id="oldzcids">
			<input type="hidden" name="havetmpic" value="0"></td>
			</tr>
		</table>
		<?php
		include($modfile);
		?>
	</div>
	<div class="tab-page" id="spsetting"> 
		<h2 class="tab">&nbsp;<font class=tabcolor>�ﶵ�]�m</font>&nbsp;</h2>
        <script type="text/javascript">tb1.addTabPage( document.getElementById( "spsetting" ) );</script>
		<table width=100% align=center cellpadding=3 cellspacing=1 class="tableborder">
			<tr><td class=header>�ﶵ�]�m</td></tr>
			<tr>
				<td bgcolor='#ffffff'> 
					<table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#DBEAF5">
              <tr> 
                <td height="25" bgcolor="#FFFFFF">�m���ŧO: 
                  <select name="istop">
                    <option value="0"<?=$r[istop]==0?' selected':''?>>���m��</option>
                    <option value="1"<?=$r[istop]==1?' selected':''?>>�@�Ÿm��</option>
                    <option value="2"<?=$r[istop]==2?' selected':''?>>�G�Ÿm��</option>
                    <option value="3"<?=$r[istop]==3?' selected':''?>>�T�Ÿm��</option>
                    <option value="4"<?=$r[istop]==4?' selected':''?>>�|�Ÿm��</option>
                    <option value="5"<?=$r[istop]==5?' selected':''?>>���Ÿm��</option>
                    <option value="6"<?=$r[istop]==6?' selected':''?>>���Ÿm��</option>
                    <option value="7"<?=$r[istop]==7?' selected':''?>>�C�Ÿm��</option>
                    <option value="8"<?=$r[istop]==8?' selected':''?>>�K�Ÿm��</option>
					<option value="9"<?=$r[istop]==9?' selected':''?>>�E�Ÿm��</option>
                  </select>
                  ���e�ҪO: 
                  <select name="newstempid">
                    <option value="0"<?=$r[newstempid]==0?' selected':''?>>�ϥ��q�{�ҪO</option>
                    <?=$newstemp?>
                  </select> <input type="button" name="Submit62222" value="�޲z���e�ҪO" onclick="window.open('template/ListNewstemp.php?gid=<?=$thegid?><?=$ecms_hashur['ehref']?>');"> 
                </td>
              </tr>
              <tr> 
                <td height="25" bgcolor="#FFFFFF">�v���]�m: 
                  <select name="groupid">
                    <option value="0">�C��</option>
                    <?=$group?>
                  </select>
                  �d�ݦ����I��: 
                  <input name="userfen" type="text" value="<?=$r[userfen]?>" size="6">
                  , 
                  <input type=checkbox name=closepl value=1<?=$r[closepl]==1?" checked":""?>>
                  �������� </td>
              </tr>
              <tr> 
                <td height="25" bgcolor="#FFFFFF">�I����&nbsp;&nbsp;&nbsp;: 
                  <input name="onclick" type="text" id="onclick" value="<?=$r[onclick]?>">
                  �U����&nbsp;&nbsp;&nbsp;: 
                  <input name="totaldown" type="text" id="totaldown" value="<?=$r[totaldown]?>"></td>
              </tr>
              <tr> 
                <td height="25" bgcolor="#FFFFFF"> ���W&nbsp;&nbsp;&nbsp;: 
                  <input name="newspath" type="text" id="newspath" value="<?=$r[newspath]?>">
                  / 
                  <input name="filename" type="text" value="<?=$r[filename]?>">
                  <font color="#666666">(����ؿ�/���W)</font></td>
              </tr>
              <?php
			  if(strstr($public_r['usetags'],','.$modid.','))
			  {
			  	$infotag_readonly='';
				$infotag_copykeyboard='&nbsp;&nbsp;<input type="button" name="Submit3" value="�ƻs����r" onclick="document.add.infotags.value=document.add.keyboard.value;">';
			  	if(strstr($public_r['chtags'],','.$modid.','))
				{
					$infotag_readonly=' readonly';
					$infotag_copykeyboard='';
				}
			  ?>
              <tr> 
                <td height="25" bgcolor="#FFFFFF">TAGS&nbsp;&nbsp;&nbsp;&nbsp;: 
                  <input name="infotags" type="text" id="infotags" value="<?=$r[infotags]?>" size="32"<?=$infotag_readonly?>> 
                  <input type="button" name="Submit" value="���" onclick="window.open('tags/ChangeTags.php?form=add&field=infotags<?=$ecms_hashur['ehref']?>','','width=700,height=550,scrollbars=yes');">
                  <?=$infotag_copykeyboard?>
                  <input name="oldinfotags" type="hidden" id="oldinfotags" value="<?=$r[infotags]?>">
                  <font color="#333333">(�h�ӥ�&quot;,&quot;�r����})</font></td>
              </tr>
              <?php
			  }
			  ?>
			  <?php
			  if($class_r[$classid]['link_num'])
			  {
			  ?>
			  <tr>
                <td height="25" bgcolor="#FFFFFF">�����챵:
                  <input type="radio" name="info_diyotherlink" value="0"<?=$voter[diyotherlink]==0?' checked':''?>>
                  ������r�d�� 
                  <input type="radio" name="info_diyotherlink" value="1"<?=$voter[diyotherlink]==1?' checked':''?>>
                  <a href="#empirecms" title="�d�ݩM�W�[�������챵" onclick="if(document.getElementsByName('info_diyotherlink')[1].checked==true){window.open('info/OtherLink.php?<?=$ecms_hashur['ehref']?>&classid=<?=$classid?>&id=<?=$id?>&enews=<?=$enews?>&form=add&field=info_keyid&keyid='+document.add.info_keyid.value+'&keyboard='+document.add.keyboard.value+'&title='+document.add.title.value,'','width=780,height=550,scrollbars=yes,resizable=yes');}else{alert('�Х���ܤ�ʬ����챵');}">��ʬ����챵</a>
                  <input name="info_keyid" type="hidden" id="info_keyid" value="<?=$r[keyid]?>"></td>
              </tr>
			  <?php
			  }
			  ?>
            </table>
					<table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#DBEAF5">
						<tr> 
							
                <td>���ݱM�D</td>
						</tr>
						<tr> 
							<td height="25" bgcolor="#FFFFFF"><a href="#empirecms" onclick="window.open('special/PushToZt.php?sinfo=1&classid=<?=$classid?>&id=<?=$id?><?=$ecms_hashur['ehref']?>','PushToZt','width=360,height=500,scrollbars=yes,left=300,top=150,resizable=yes');">�I����ܫH�����ݱM�D</a></td>
						</tr>
					</table>
			<table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#DBEAF5">
              <tr> 
                <td>�w�ɵo�G</td>
              </tr>
              <tr> 
                <td height="25" bgcolor="#FFFFFF"><table width="100%" border="0" cellspacing="1" cellpadding="3">
                    <tr> 
                      <td>�W�u�ɶ��G <input name="info_infouptime" type="text" id="info_infouptime" value="<?=$voter[infouptime]?date('Y-m-d H:i:s',$voter[infouptime]):''?>">
                        [<a href="#empirecms" onclick="document.add.info_infouptime.value='<?=$todaytime?>'">���e�ɶ�</a>]</td>
                    </tr>
                    <tr> 
                      <td>�U�u�ɶ��G <input name="info_infodowntime" type="text" id="info_infodowntime" value="<?=$voter[infodowntime]?date('Y-m-d H:i:s',$voter[infodowntime]):''?>">
                        [<a href="#empirecms" onclick="document.add.info_infodowntime.value='<?=$todaytime?>'">���e�ɶ�</a>]</td>
                    </tr>
                  </table></td>
              </tr>
            </table>
            <?php
			if($wfinfor[checktno]==101)
			{
			?>
            <table width="100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#DBEAF5">
              <tr> 
                <td>�H���e�f</td>
              </tr>
              <tr> 
                <td height="25" bgcolor="#FFFFFF"><input name="reworkflow" type="checkbox" value="1">
                  ���s�e�f<font color="#333333">�]�H���Q��u��A�ק��i��ܭ��s�e�f�^</font> </td>
              </tr>
            </table>
			<?php
			}
			?>
					<?=$copyclassids?>
				</td>
			</tr>
		</table>
	</div>
	<div class="tab-page" id="votesetting">       
		<h2 class="tab">&nbsp;<font class=tabcolor>�벼�]�m</font>&nbsp;</h2>
        <script type="text/javascript">tb1.addTabPage( document.getElementById( "votesetting" ) );</script>
		<table width="100%" border="0" align="center" cellpadding="3" cellspacing="1" class="tableborder">
			<tr class="header"> 
				<td height="25" colspan="2">�벼�]�m</td>
			</tr>
			<tr bgcolor="#FFFFFF"> 
				<td width="21%" height="25">�D�D���D</td>
				<td width="79%" height="25"> <input name="vote_title" type="text" size="60" value="<?=$voter[title]?>"> 
				</td>
			</tr>
			<tr bgcolor="#FFFFFF"> 
				<td height="25" valign="top">�벼����</td>
				<td height="25">
					<table width="100%" border="0" cellspacing="1" cellpadding="3">
						<tr> 
							<td>
								<table width="100%" border="0" cellspacing="1" cellpadding="3">
									<tr bgcolor="#DBEAF5"> 
										<td width="9%" height="20"> <div align="center">�s��</div></td>
										<td width="65%"> <div align="center">���ئW��</div></td>
										<td width="26%"> <div align="center">�벼��</div></td>
									</tr>
								</table>
								<?php
								if(($voter['id']&&$voter[votetext])||$definfovoteid)
								{
									echo"$allvote";
								}
								else
								{
								?>
									<table width="100%" border="0" cellspacing="1" cellpadding="3">
										<tr> 
											<td height="24" width="9%"> <div align="center">1</div></td>
											<td height="24" width="65%"> <div align="center"> 
											<input name="vote_name[]" type="text" size="30">
											</div></td>
											<td height="24" width="26%"> <div align="center"> 
											<input name="vote_num[]" type="text" value="0" size="6">
											</div></td>
                    </tr>
                    <tr> 
                      <td height="24"> <div align="center">2</div></td>
                      <td height="24"> <div align="center"> 
                          <input name="vote_name[]" type="text" size="30">
                        </div></td>
                      <td height="24"> <div align="center"> 
                          <input name="vote_num[]" type="text" value="0" size="6">
                        </div></td>
                    </tr>
                    <tr> 
                      <td height="24"> <div align="center">3</div></td>
                      <td height="24"> <div align="center"> 
                          <input name="vote_name[]" type="text" size="30">
                        </div></td>
                      <td height="24"> <div align="center"> 
                          <input name="vote_num[]" type="text" value="0" size="6">
                        </div></td>
                    </tr>
                    <tr> 
                      <td height="24"> <div align="center">4</div></td>
                      <td height="24"> <div align="center"> 
                          <input name="vote_name[]" type="text" size="30">
                        </div></td>
                      <td height="24"> <div align="center"> 
                          <input name="vote_num[]" type="text" value="0" size="6">
                        </div></td>
                    </tr>
                    <tr> 
                      <td height="24"> <div align="center">5</div></td>
                      <td height="24"> <div align="center"> 
                          <input name="vote_name[]" type="text" size="30">
                        </div></td>
                      <td height="24"> <div align="center"> 
                          <input name="vote_num[]" type="text" value="0" size="6">
                        </div></td>
                    </tr>
                    <tr> 
                      <td height="24"> <div align="center">6</div></td>
                      <td height="24"> <div align="center"> 
                          <input name="vote_name[]" type="text" size="30">
                        </div></td>
                      <td height="24"> <div align="center"> 
                          <input name="vote_num[]" type="text" value="0" size="6">
                        </div></td>
                    </tr>
                    <tr> 
                      <td height="24"> <div align="center">7</div></td>
                      <td height="24"> <div align="center"> 
                          <input name="vote_name[]" type="text" size="30">
                        </div></td>
                      <td height="24"> <div align="center"> 
                          <input name="vote_num[]" type="text" value="0" size="6">
                        </div></td>
                    </tr>
                    <tr> 
                      <td height="24"> <div align="center">8</div></td>
                      <td height="24"> <div align="center"> 
                          <input name="vote_name[]" type="text" size="30">
                        </div></td>
                      <td height="24"> <div align="center"> 
                          <input name="vote_num[]" type="text" value="0" size="6">
                        </div></td>
                    </tr>
                  </table>
                  <?php
			  }
			  ?>
                </td>
              </tr>
              <tr> 
                <td>�벼�X�i�ƶq: 
                  <input name="v_vote_num" type="text" value="1" size="6"> <input type="button" name="Submit52" value="��X�a�}" onclick="javascript:dovoteadd();"> 
                  <input name="v_editnum" type="hidden" value="<?=$voteeditnum?>"> 
                </td>
              </tr>
              <tr> 
                <td id="addvote"></td>
              </tr>
            </table></td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
          <td height="25">�벼����</td>
          <td height="25"><input name="vote_class" type="radio" value="0"<?=$voter['voteclass']==0?' checked':''?>>
            ��� 
            <input type="radio" name="vote_class" value="1"<?=$voter['voteclass']==1?' checked':''?>>
            �h��</td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
          <td height="25">����IP</td>
          <td height="25"><input type="radio" name="dovote_ip" value="0"<?=$voter['doip']==0?' checked':''?>>
            ������ 
            <input name="dovote_ip" type="radio" value="1"<?=$voter['doip']==1?' checked':''?>>
            ����(�����P�@IP�u���@����)</td>
        </tr>
        <tr bgcolor="#FFFFFF"> 
          <td height="25">�L���ɶ�</td>
          <td height="25"> <input name="vote_olddotime" type=hidden value="<?=$voter[dotime]?>"> 
            <input name="vote_dotime" type="text" value="<?=$voter[dotime]?>" size="12" onClick="setday(this)">
            (�W�L������,�N����벼,0000-00-00��������)</td>
        </tr>
		<tr bgcolor="#FFFFFF"> 
      	  <td height="25">�d�ݧ벼���f</td>
      	<td height="25">�e��: 
        <input name="vote_width" type="text" value="<?=$voter[width]?>" size="6">
        ����: 
        <input name="vote_height" type="text" value="<?=$voter[height]?>" size="6"></td>
    	</tr>
        <tr bgcolor="#FFFFFF"> 
          <td height="25">��ܼҪO</td>
          <td height="25"><select name="vote_tempid">
              <?=$votetemp?>
            </select> <input type="button" name="Submit62223" value="�޲z�벼�ҪO" onclick="window.open('template/ListVotetemp.php?gid=<?=$thegid?><?=$ecms_hashur['ehref']?>');"> 
          </td>
        </tr>
      </table>
	</div>
</div>
  <table width="100%" border="0" align="center" cellpadding="3" cellspacing="1">
    <tr>
      <td width="16%">&nbsp;</td>
      <td><input type="submit" name="addnews" value=" �� �� "> &nbsp;&nbsp;&nbsp;<input type="reset" name="Submit2" value="���m"></td>
    </tr>
  </table>
</form>
<?=$copyclassidshowiframe?>
</body>
</html>
<?php
db_close();
$empire=null;
?>