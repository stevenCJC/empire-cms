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
CheckLevel($logininid,$loginin,$classid,"pubvar");

//�W�[����
function AddPubVarClass($add,$userid,$username){
	global $empire,$dbtbpre;
	if(!$add[classname])
	{
		printerror("EmptyPubVarClass","history.go(-1)");
	}
	//�����v��
	CheckLevel($userid,$username,$classid,"pubvar");
	$add['classname']=hRepPostStr($add['classname'],1);
	$add['classsay']=hRepPostStr($add['classsay'],1);
	$sql=$empire->query("insert into {$dbtbpre}enewspubvarclass(classname,classsay) values('".$add[classname]."','".$add[classsay]."');");
	$lastid=$empire->lastid();
	if($sql)
	{
		//�ާ@��x
		insert_dolog("classid=".$lastid."<br>classname=".$add[classname]);
		printerror("AddPubVarClassSuccess","PubVarClass.php".hReturnEcmsHashStrHref2(1));
	}
	else
	{printerror("DbError","history.go(-1)");}
}

//�ק����
function EditPubVarClass($add,$userid,$username){
	global $empire,$dbtbpre;
	$classid=(int)$add[classid];
	if(!$add[classname]||!$classid)
	{
		printerror("EmptyPubVarClass","history.go(-1)");
	}
	//�����v��
	CheckLevel($userid,$username,$classid,"pubvar");
	$add['classname']=hRepPostStr($add['classname'],1);
	$add['classsay']=hRepPostStr($add['classsay'],1);
	$sql=$empire->query("update {$dbtbpre}enewspubvarclass set classname='".$add[classname]."',classsay='".$add[classsay]."' where classid='$classid'");
	if($sql)
	{
		//�ާ@��x
		insert_dolog("classid=".$classid."<br>classname=".$add[classname]);
		printerror("EditPubVarClassSuccess","PubVarClass.php".hReturnEcmsHashStrHref2(1));
	}
	else
	{printerror("DbError","history.go(-1)");}
}

//�R������
function DelPubVarClass($classid,$userid,$username){
	global $empire,$dbtbpre;
	$classid=(int)$classid;
	if(!$classid)
	{
		printerror("NotDelPubVarClassid","history.go(-1)");
	}
	//�����v��
	CheckLevel($userid,$username,$classid,"pubvar");
	$r=$empire->fetch1("select classname from {$dbtbpre}enewspubvarclass where classid='$classid'");
	$sql=$empire->query("delete from {$dbtbpre}enewspubvarclass where classid='$classid'");
	//�R���ܶq
	$delsql=$empire->query("delete from {$dbtbpre}enewspubvar where classid='$classid'");
	if($sql)
	{
		GetConfig();
		//�ާ@��x
		insert_dolog("classid=".$classid."<br>classname=".$r[classname]);
		printerror("DelPubVarClassSuccess","PubVarClass.php".hReturnEcmsHashStrHref2(1));
	}
	else
	{printerror("DbError","history.go(-1)");}
}

$enews=$_POST['enews'];
if(empty($enews))
{$enews=$_GET['enews'];}
if($enews)
{
	hCheckEcmsRHash();
}
if($enews=="AddPubVarClass")//�W�[����
{
	AddPubVarClass($_POST,$logininid,$loginin);
}
elseif($enews=="EditPubVarClass")//�ק����
{
	EditPubVarClass($_POST,$logininid,$loginin);
}
elseif($enews=="DelPubVarClass")//�R������
{
	$classid=$_GET['classid'];
	DelPubVarClass($classid,$logininid,$loginin);
}
else
{}

$sql=$empire->query("select classid,classname,classsay from {$dbtbpre}enewspubvarclass order by classid desc");
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
    <td width="50%">��m�G<a href="ListPubVar.php<?=$ecms_hashur['whehref']?>">�޲z�X�i�ܶq</a> &gt; <a href="PubVarClass.php<?=$ecms_hashur['whehref']?>">�޲z�ܶq����</a></td>
    <td><div align="right" class="emenubutton">
        <input type="button" name="Submit5" value="�޲z�X�i�ܶq" onclick="self.location.href='ListPubVar.php<?=$ecms_hashur['whehref']?>';">
      </div></td>
  </tr>
</table>
<form name="form1" method="post" action="PubVarClass.php">
  <table width="100%" border="0" align="center" cellpadding="3" cellspacing="1" class="tableborder">
  <?=$ecms_hashur['form']?>
    <tr class="header"> 
      <td height="25">�W�[�ܶq����: 
        <input name=enews type=hidden id="enews" value=AddPubVarClass> </td>
    </tr>
    <tr> 
      <td height="25" bgcolor="#FFFFFF"> �����W��: 
        <input name="classname" type="text" id="classname">
        ��������: <input name="classsay" type="text" id="classsay" size="42"> <input type="submit" name="Submit" value="�W�["></td>
    </tr>
  </table>
</form>
<table width="100%" border="0" align="center" cellpadding="3" cellspacing="1" class="tableborder">
  <tr class="header"> 
    <td width="6%"><div align="center">ID</div></td>
    <td width="29%" height="25">�����W��</td>
    <td width="49%">��������</td>
    <td width="16%" height="25"><div align="center">�ާ@</div></td>
  </tr>
  <?php
  while($r=$empire->fetch($sql))
  {
  ?>
  <form name=form2 method=post action=PubVarClass.php>
	  <?=$ecms_hashur['form']?>
    <input type=hidden name=enews value=EditPubVarClass>
    <input type=hidden name=classid value=<?=$r[classid]?>>
    <tr bgcolor="#FFFFFF" onmouseout="this.style.backgroundColor='#ffffff'" onmouseover="this.style.backgroundColor='#C3EFFF'"> 
      <td><div align="center">
          <?=$r[classid]?>
        </div></td>
      <td height="25"> <input name="classname" type="text" id="classname" value="<?=$r[classname]?>">
        [<a href="ListPubVar.php?classid=<?=$r[classid]?><?=$ecms_hashur['ehref']?>" target="_blank">�ܶq�C��</a>]</td>
      <td><input name="classsay" type="text" id="classsay" value="<?=$r[classsay]?>" size="42"></td>
      <td height="25"><div align="center"> 
          <input type="submit" name="Submit3" value="�ק�">
          &nbsp; 
          <input type="button" name="Submit4" value="�R��" onclick="if(confirm('�R���|�R�������U���Ҧ��ܶq�A�T�{�n�R��?')){self.location.href='PubVarClass.php?enews=DelPubVarClass&classid=<?=$r[classid]?><?=$ecms_hashur['href']?>';}">
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