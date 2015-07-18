<?php
define('EmpireCMSAdmin','1');
require("../../class/connect.php");
require("../../class/db_sql.php");
require("../../class/functions.php");
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
CheckLevel($logininid,$loginin,$classid,"adminstyle");

//��s�˦��w�s
function UpAdminstyle(){
	global $empire,$dbtbpre;
	$adminstyle=',';
	$sql=$empire->query("select path from {$dbtbpre}enewsadminstyle");
	while($r=$empire->fetch($sql))
	{
		$adminstyle.=$r['path'].',';
	}
	$empire->query("update {$dbtbpre}enewspublic set adminstyle='$adminstyle'");
	GetConfig();
}

//�W�[��x�˦�
function AddAdminstyle($add,$userid,$username){
	global $empire,$dbtbpre;
	$path=RepPathStr($add['path']);
	$path=(int)$path;
	if(empty($path)||empty($add['stylename']))
	{
		printerror("EmptyAdminStyle","history.go(-1)");
	}
	//�����v��
	CheckLevel($userid,$username,$classid,"adminstyle");
	//�ؿ��O�_�s�b
	if(!file_exists("../adminstyle/".$path))
	{
		printerror("EmptyAdminStylePath","history.go(-1)");
	}
	$sql=$empire->query("insert into {$dbtbpre}enewsadminstyle(stylename,path,isdefault) values('$add[stylename]',$path,0);");
	if($sql)
	{
		UpAdminstyle();
		$styleid=$empire->lastid();
		//�ާ@��x
		insert_dolog("styleid=$styleid&stylename=$add[stylename]");
		printerror("AddAdminStyleSuccess","AdminStyle.php".hReturnEcmsHashStrHref2(1));
	}
	else
	{
		printerror("DbError","history.go(-1)");
	}
}

//�ק��x�˦�
function EditAdminStyle($add,$userid,$username){
	global $empire,$dbtbpre;
	$styleid=(int)$add['styleid'];
	$path=RepPathStr($add['path']);
	$path=(int)$path;
	if(!$styleid||empty($path)||empty($add['stylename']))
	{
		printerror("EmptyAdminStyle","history.go(-1)");
	}
	//�����v��
	CheckLevel($userid,$username,$classid,"adminstyle");
	//�ؿ��O�_�s�b
	if(!file_exists("../adminstyle/".$path))
	{
		printerror("EmptyAdminStylePath","history.go(-1)");
	}
	$sql=$empire->query("update {$dbtbpre}enewsadminstyle set stylename='$add[stylename]',path=$path where styleid=$styleid");
	if($sql)
	{
		UpAdminstyle();
		//�ާ@��x
		insert_dolog("styleid=$styleid&stylename=$add[stylename]");
		printerror("EditAdminStyleSuccess","AdminStyle.php".hReturnEcmsHashStrHref2(1));
	}
	else
	{
		printerror("DbError","history.go(-1)");
	}
}

//�q�{��x�˦�
function DefAdminStyle($styleid,$userid,$username){
	global $empire,$dbtbpre;
	$styleid=(int)$styleid;
	if(!$styleid)
	{
		printerror("EmptyAdminStyleid","history.go(-1)");
	}
	//�����v��
	CheckLevel($userid,$username,$classid,"adminstyle");
	$r=$empire->fetch1("select stylename,path from {$dbtbpre}enewsadminstyle where styleid=$styleid");
	$usql=$empire->query("update {$dbtbpre}enewsadminstyle set isdefault=0");
	$sql=$empire->query("update {$dbtbpre}enewsadminstyle set isdefault=1 where styleid=$styleid");
	$upsql=$empire->query("update {$dbtbpre}enewspublic set defadminstyle='$r[path]' limit 1");
	if($sql)
	{
		GetConfig();
		//�ާ@��x
		insert_dolog("styleid=$styleid&stylename=$r[stylename]");
		printerror("DefAdminStyleSuccess","AdminStyle.php".hReturnEcmsHashStrHref2(1));
	}
	else
	{
		printerror("DbError","history.go(-1)");
	}
}

//�R����x�˦�
function DelAdminStyle($styleid,$userid,$username){
	global $empire,$dbtbpre;
	$styleid=(int)$styleid;
	if(!$styleid)
	{
		printerror("EmptyAdminStyleid","history.go(-1)");
	}
	//�����v��
	CheckLevel($userid,$username,$classid,"adminstyle");
	$r=$empire->fetch1("select stylename,path,isdefault from {$dbtbpre}enewsadminstyle where styleid=$styleid");
	if($r['isdefault'])
	{
		printerror("NotDelDefAdminStyle","history.go(-1)");
	}
	$sql=$empire->query("delete from {$dbtbpre}enewsadminstyle where styleid=$styleid");
	if($sql)
	{
		UpAdminstyle();
		//�ާ@��x
		insert_dolog("styleid=$styleid&stylename=$r[stylename]");
		printerror("DelAdminStyleSuccess","AdminStyle.php".hReturnEcmsHashStrHref2(1));
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
}
//�W�[��x�˦�
if($enews=="AddAdminStyle")
{
	AddAdminstyle($_POST,$logininid,$loginin);
}
//�ק��x�˦�
elseif($enews=="EditAdminStyle")
{
	EditAdminStyle($_POST,$logininid,$loginin);
}
//�q�{��x�˦�
elseif($enews=="DefAdminStyle")
{
	DefAdminStyle($_GET['styleid'],$logininid,$loginin);
}
//�R����x�˦�
elseif($enews=="DelAdminStyle")
{
	DelAdminStyle($_GET['styleid'],$logininid,$loginin);
}
$sql=$empire->query("select styleid,stylename,path,isdefault from {$dbtbpre}enewsadminstyle order by styleid");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">
<title></title>
<link href="../adminstyle/<?=$loginadminstyleid?>/adminstyle.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="100%" border="0" align="center" cellpadding="3" cellspacing="1">
  <tr>
    <td><p>��m�G<a href="AdminStyle.php<?=$ecms_hashur['whehref']?>">�޲z��x�˦�</a></p>
      </td>
  </tr>
</table>
<form name="form1" method="post" action="AdminStyle.php">
  <table width="100%" border="0" align="center" cellpadding="3" cellspacing="1" class="tableborder">
  <?=$ecms_hashur['form']?>
    <tr class="header">
      <td height="25">�W�[��x�˦�: 
        <input name=enews type=hidden id="enews" value=AddAdminStyle>
        </td>
    </tr>
    <tr> 
      <td height="25" bgcolor="#FFFFFF"> �˦��W��: 
        <input name="stylename" type="text" id="stylename">
        �˦��ؿ�:adminstyle/ 
        <input name="path" type="text" id="path" size="6">
        (�ж�g�Ʀr) 
        <input type="submit" name="Submit" value="�W�[">
        <input type="reset" name="Submit2" value="���m"></td>
    </tr>
  </table>
</form>
<table width="100%" border="0" align="center" cellpadding="3" cellspacing="1" class="tableborder">
  <tr class="header"> 
    <td width="7%"><div align="center">ID</div></td>
    <td width="29%" height="25"><div align="center">�˦��W��</div></td>
    <td width="30%"><div align="center">�˦��ؿ�</div></td>
    <td width="34%" height="25"><div align="center">�ާ@</div></td>
  </tr>
  <?php
  while($r=$empire->fetch($sql))
  {
  	$bgcolor="#FFFFFF";
	$movejs=' onmouseout="this.style.backgroundColor=\'#ffffff\'" onmouseover="this.style.backgroundColor=\'#C3EFFF\'"';
  	if($r[isdefault])
	{
		$bgcolor="#DBEAF5";
		$movejs='';
	}
  ?>
  <form name=form2 method=post action=AdminStyle.php>
	  <?=$ecms_hashur['form']?>
    <input type=hidden name=enews value=EditAdminStyle>
    <input type=hidden name=styleid value=<?=$r[styleid]?>>
    <tr bgcolor="<?=$bgcolor?>"<?=$movejs?>> 
      <td><div align="center">
          <?=$r[styleid]?>
        </div></td>
      <td height="25"> <div align="center"> 
          <input name="stylename" type="text" id="stylename" value="<?=$r[stylename]?>">
        </div></td>
      <td><div align="center">adminstyle/ 
          <input name="path" type="text" id="path" value="<?=$r[path]?>" size="6">
        </div></td>
      <td height="25"><div align="center">
          <input type="button" name="Submit4" value="�]���q�{" onclick="self.location.href='AdminStyle.php?enews=DefAdminStyle&styleid=<?=$r[styleid]?><?=$ecms_hashur['href']?>';"> 
		  &nbsp;
          <input type="submit" name="Submit3" value="�ק�">
          &nbsp; 
          <input type="button" name="Submit4" value="�R��" onclick="self.location.href='AdminStyle.php?enews=DelAdminStyle&styleid=<?=$r[styleid]?><?=$ecms_hashur['href']?>';">
        </div></td>
    </tr>
  </form>
  <?php
  }
  db_close();
  $empire=null;
  ?>
</table>
</body>
</html>