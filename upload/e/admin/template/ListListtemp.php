<?php
define('EmpireCMSAdmin','1');
require("../../class/connect.php");
require("../../class/db_sql.php");
require("../../class/functions.php");
require "../".LoadLang("pub/fun.php");
$link=db_connect();
$empire=new mysqlquery();
$editor=1;
//���ҥΤ�
$lur=is_login();
$logininid=$lur['userid'];
$loginin=$lur['username'];
$loginrnd=$lur['rnd'];
$loginlevel=$lur['groupid'];
$loginadminstyleid=$lur['adminstyleid'];
//ehash
$ecms_hashur=hReturnEcmsHashStrAll();
//�����v��
CheckLevel($logininid,$loginin,$classid,"template");

//�W�[�C���ҪO
function AddListtemp($add,$userid,$username){
	global $empire,$dbtbpre;
	if(!$add[tempname]||!$add[temptext]||!$add[listvar]||!$add[modid])
	{printerror("EmptyListTempname","history.go(-1)");}
	//�ާ@�v��
	CheckLevel($userid,$username,$classid,"template");
    $classid=(int)$add['classid'];
	$add[tempname]=hRepPostStr($add[tempname],1);
    $add[temptext]=RepPhpAspJspcode($add[temptext]);
	$add[listvar]=RepPhpAspJspcode($add[listvar]);
	if($add['autorownum'])
	{
		$add[rownum]=substr_count($add[temptext],'<!--list.var');
	}
	$add[subnews]=(int)$add[subnews];
	$add[rownum]=(int)$add[rownum];
	$add[modid]=(int)$add[modid];
	$add[subtitle]=(int)$add[subtitle];
	$docode=(int)$add[docode];
	$gid=(int)$add['gid'];
	$sql=$empire->query("insert into ".GetDoTemptb("enewslisttemp",$gid)."(tempname,temptext,subnews,listvar,rownum,modid,showdate,subtitle,classid,isdefault,docode) values('$add[tempname]','".eaddslashes2($add[temptext])."',$add[subnews],'".eaddslashes2($add[listvar])."',$add[rownum],'$add[modid]','$add[showdate]',$add[subtitle],$classid,0,'$docode');");
	$tempid=$empire->lastid();
	//�ƥ��ҪO
	AddEBakTemp('listtemp',$gid,$tempid,$add[tempname],$add[temptext],$add[subnews],0,$add[listvar],$add[rownum],$add[modid],$add[showdate],$add[subtitle],$classid,$docode,$userid,$username);
	if($sql)
	{
		//�ާ@��x
		insert_dolog("tempid=".$tempid."<br>tempname=".$add[tempname]."&gid=$gid");
		printerror("AddListTempSuccess","AddListtemp.php?enews=AddListtemp&gid=$gid".hReturnEcmsHashStrHref2(0));
	}
	else
	{
		printerror("DbError","history.go(-1)");
	}
}

//�ק�C���ҪO
function EditListtemp($add,$userid,$username){
	global $empire,$dbtbpre,$public_r;
	$add[tempid]=(int)$add[tempid];
	if(!$add[tempname]||!$add[temptext]||!$add[listvar]||!$add[modid]||!$add[tempid])
	{printerror("EmptyListTempname","history.go(-1)");}
	//�ާ@�v��
	CheckLevel($userid,$username,$classid,"template");
    $classid=(int)$add['classid'];
	$add[tempname]=hRepPostStr($add[tempname],1);
    $add[temptext]=RepPhpAspJspcode($add[temptext]);
	$add[listvar]=RepPhpAspJspcode($add[listvar]);
	if($add['autorownum'])
	{
		$add[rownum]=substr_count($add[temptext],'<!--list.var');
	}
	$add[subnews]=(int)$add[subnews];
	$add[rownum]=(int)$add[rownum];
	$add[modid]=(int)$add[modid];
	$add[subtitle]=(int)$add[subtitle];
	$docode=(int)$add[docode];
	$gid=(int)$add['gid'];
	$sql=$empire->query("update ".GetDoTemptb("enewslisttemp",$gid)." set subnews=$add[subnews],tempname='$add[tempname]',temptext='".eaddslashes2($add[temptext])."',listvar='".eaddslashes2($add[listvar])."',rownum=$add[rownum],modid=$add[modid],showdate='$add[showdate]',subtitle=$add[subtitle],classid=$classid,docode='$docode' where tempid='$add[tempid]'");
	//�ƥ��ҪO
	AddEBakTemp('listtemp',$gid,$add[tempid],$add[tempname],$add[temptext],$add[subnews],0,$add[listvar],$add[rownum],$add[modid],$add[showdate],$add[subtitle],$classid,$docode,$userid,$username);
	if($gid==$public_r['deftempid']||(!$public_r['deftempid']&&($gid==1||$gid==0)))
	{
		//�R���ʺA�ҪO�w�s���
		DelOneTempTmpfile('list'.$add[tempid]);
	}
	if($sql)
	{
		//�ާ@��x
		insert_dolog("tempid=".$add[tempid]."<br>tempname=".$add[tempname]."&gid=$gid");
		printerror("EditListTempSuccess","ListListtemp.php?classid=$add[cid]&modid=$add[mid]&gid=$gid".hReturnEcmsHashStrHref2(0));
	}
	else
	{
		printerror("DbError","history.go(-1)");
	}
}

//�R���C���ҪO
function DelListtemp($tempid,$add,$userid,$username){
	global $empire,$dbtbpre,$public_r;
	$tempid=(int)$tempid;
	if(!$tempid)
	{printerror("NotDelTemplateid","history.go(-1)");}
	//�ާ@�v��
	CheckLevel($userid,$username,$classid,"template");
	$gid=(int)$add['gid'];
	$tr=$empire->fetch1("select tempname from ".GetDoTemptb("enewslisttemp",$gid)." where tempid='$tempid'");
	$sql=$empire->query("delete from ".GetDoTemptb("enewslisttemp",$gid)." where tempid='$tempid'");
	//�R���ƥ��O��
	DelEbakTempAll('listtemp',$gid,$tempid);
	if($gid==$public_r['deftempid']||(!$public_r['deftempid']&&($gid==1||$gid==0)))
	{
		//�R���ʺA�ҪO�w�s���
		DelOneTempTmpfile('list'.$tempid);
	}
	if($sql)
	{
		//�ާ@��x
		insert_dolog("tempid=".$tempid."<br>tempname=".$tr[tempname]."&gid=$gid");
		printerror("DelListTempSuccess","ListListtemp.php?classid=$add[cid]&modid=$add[mid]&gid=$gid".hReturnEcmsHashStrHref2(0));
	}
	else
	{
		printerror("DbError","history.go(-1)");
	}
}
$enews=$_POST['enews'];
if(empty($enews))
{$enews=$_GET['enews'];}
if($enews)
{
	hCheckEcmsRHash();
	include("../../class/tempfun.php");
}
//�W�[�C���ҪO
if($enews=="AddListtemp")
{
	AddListtemp($_POST,$logininid,$loginin);
}
//�ק�C���ҪO
elseif($enews=="EditListtemp")
{
	EditListtemp($_POST,$logininid,$loginin);
}
//�R���C���ҪO
elseif($enews=="DelListtemp")
{
	$tempid=$_GET['tempid'];
	DelListtemp($tempid,$_GET,$logininid,$loginin);
}

$gid=(int)$_GET['gid'];
$gname=CheckTempGroup($gid);
$urlgname=$gname."&nbsp;>&nbsp;";
$search="&gid=$gid".$ecms_hashur['ehref'];
$url=$urlgname."<a href=ListListtemp.php?gid=$gid".$ecms_hashur['ehref'].">�޲z�C���ҪO</a>";
$page=(int)$_GET['page'];
$page=RepPIntvar($page);
$start=0;
$line=25;//�C����ܱ���
$page_line=12;//�C������챵��
$offset=$page*$line;//�`�����q
$query="select tempid,tempname,modid from ".GetDoTemptb("enewslisttemp",$gid);
$totalquery="select count(*) as total from ".GetDoTemptb("enewslisttemp",$gid);
//���O
$add="";
$classid=(int)$_GET['classid'];
if($classid)
{
	$add=" where classid=$classid";
	$search.="&classid=$classid";
}
//�ҫ�
$modid=(int)$_GET['modid'];
if($modid)
{
	if(empty($add))
	{
		$add=" where modid=$modid";
	}
	else
	{
		$add.=" and modid=$modid";
	}
	$search.="&modid=$modid";
}
$query.=$add;
$totalquery.=$add;
$num=$empire->gettotal($totalquery);//���o�`����
$query=$query." order by tempid desc limit $offset,$line";
$sql=$empire->query($query);
$returnpage=page2($num,$line,$page_line,$start,$page,$search);
//����
$cstr="";
$csql=$empire->query("select classid,classname from {$dbtbpre}enewslisttempclass order by classid");
while($cr=$empire->fetch($csql))
{
	$select="";
	if($cr[classid]==$classid)
	{
		$select=" selected";
	}
	$cstr.="<option value='".$cr[classid]."'".$select.">".$cr[classname]."</option>";
}
//�ҫ�
$mstr="";
$msql=$empire->query("select mid,mname from {$dbtbpre}enewsmod where usemod=0 order by myorder,mid");
while($mr=$empire->fetch($msql))
{
	$select="";
	if($mr[mid]==$modid)
	{
		$select=" selected";
	}
	$mstr.="<option value='".$mr[mid]."'".$select.">".$mr[mname]."</option>";
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">
<title>�޲z�C���ҪO</title>
<link href="../adminstyle/<?=$loginadminstyleid?>/adminstyle.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="100%" border="0" align="center" cellpadding="3" cellspacing="1">
  <tr> 
    <td width="50%">��m�G 
      <?=$url?>
    </td>
    <td><div align="right" class="emenubutton">
        <input type="button" name="Submit5" value="�W�[�C���ҪO" onclick="self.location.href='AddListtemp.php?enews=AddListtemp&gid=<?=$gid?><?=$ecms_hashur['ehref']?>';">
        &nbsp;&nbsp; 
        <input type="button" name="Submit5" value="�޲z�C���ҪO����" onclick="self.location.href='ListtempClass.php?gid=<?=$gid?><?=$ecms_hashur['ehref']?>';">
      </div></td>
  </tr>
</table>
<table width="100%" border="0" align="center" cellpadding="3" cellspacing="1">
  <form name="form1" method="get" action="ListListtemp.php">
  <?=$ecms_hashur['eform']?>
  <input type=hidden name=gid value="<?=$gid?>">
    <tr> 
      <td height="25">������ܡG 
        <select name="classid" id="classid" onchange="document.form1.submit()">
          <option value="0">��ܩҦ�����</option>
		  <?=$cstr?>
        </select>
        <select name="modid" id="modid" onchange="document.form1.submit()">
          <option value="0">��ܩҦ��t�μҫ�</option>
		  <?=$mstr?>
        </select>
      </td>
    </tr>
	</form>
  </table>
<br>
<table width="100%" border="0" align="center" cellpadding="3" cellspacing="1" class="tableborder">
  <tr class="header"> 
    <td width="8%" height="25"><div align="center">ID</div></td>
    <td width="43%" height="25"><div align="center">�ҪO�W</div></td>
    <td width="30%"><div align="center">���ݨt�μҫ�</div></td>
    <td width="19%" height="25"><div align="center">�ާ@</div></td>
  </tr>
  <?php
  while($r=$empire->fetch($sql))
  {
  $modr=$empire->fetch1("select mid,mname from {$dbtbpre}enewsmod where mid=$r[modid]");
  ?>
  <tr bgcolor="ffffff" onmouseout="this.style.backgroundColor='#ffffff'" onmouseover="this.style.backgroundColor='#C3EFFF'"> 
    <td height="25"><div align="center"> 
        <?=$r[tempid]?>
      </div></td>
    <td height="25"><div align="center"> 
        <?=$r[tempname]?>
      </div></td>
    <td><div align="center">[<a href="ListListtemp.php?classid=<?=$classid?>&modid=<?=$modr[mid]?>&gid=<?=$gid?><?=$ecms_hashur['ehref']?>"><?=$modr[mname]?></a>]</div></td>
    <td height="25"><div align="center"> [<a href="AddListtemp.php?enews=EditListtemp&tempid=<?=$r[tempid]?>&cid=<?=$classid?>&mid=<?=$modid?>&gid=<?=$gid?><?=$ecms_hashur['ehref']?>">�ק�</a>] 
        [<a href="AddListtemp.php?enews=AddListtemp&docopy=1&tempid=<?=$r[tempid]?>&cid=<?=$classid?>&mid=<?=$modid?>&gid=<?=$gid?><?=$ecms_hashur['ehref']?>">�ƻs</a>] 
        [<a href="ListListtemp.php?enews=DelListtemp&tempid=<?=$r[tempid]?>&cid=<?=$classid?>&mid=<?=$modid?>&gid=<?=$gid?><?=$ecms_hashur['href']?>" onclick="return confirm('�T�{�n�R���H');">�R��</a>]</div></td>
  </tr>
  <?php
  }
  ?>
  <tr bgcolor="ffffff"> 
    <td height="25" colspan="4">&nbsp;<?=$returnpage?></td>
  </tr>
</table>
</body>
</html>
<?php
db_close();
$empire=null;
?>