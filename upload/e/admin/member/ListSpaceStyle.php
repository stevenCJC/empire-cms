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
CheckLevel($logininid,$loginin,$classid,"spacestyle");

//��^�|����
function ReturnSpaceStyleMemberGroup($membergroup){
	$count=count($membergroup);
	if($count==0)
	{
		return '';
	}
	$mg='';
	for($i=0;$i<$count;$i++)
	{
		$mg.=$membergroup[$i].',';
	}
	if($mg)
	{
		$mg=','.$mg;
	}
	return $mg;
}

//�W�[�|���Ŷ��ҪO
function AddSpaceStyle($add,$userid,$username){
	global $empire,$dbtbpre;
	if(empty($add[stylename])||empty($add[stylepath]))
	{
		printerror('EmptySpaceStyle','history.go(-1)');
	}
	$add[stylepath]=RepPathStr($add[stylepath]);
	$add['stylepath']=RepPostStr($add['stylepath'],1);
	//�ؿ��O�_�s�b
	if(!file_exists("../../space/template/".$add[stylepath]))
	{
		printerror("EmptySpaceStylePath","history.go(-1)");
	}
	$mg=ReturnSpaceStyleMemberGroup($add['membergroup']);
	$sql=$empire->query("insert into {$dbtbpre}enewsspacestyle(stylename,stylepic,stylesay,stylepath,isdefault,membergroup) values('$add[stylename]','$add[stylepic]','$add[stylesay]','$add[stylepath]',0,'$mg');");
	if($sql)
	{
		$styleid=$empire->lastid();
		insert_dolog("styleid=$styleid&stylename=$add[stylename]");//�ާ@��x
		printerror("AddSpaceStyleSuccess","AddSpaceStyle.php?enews=AddSpaceStyle".hReturnEcmsHashStrHref2(0));
	}
	else
	{
		printerror("DbError","history.go(-1)");
	}
}

//�ק�|���Ŷ��ҪO
function EditSpaceStyle($add,$userid,$username){
	global $empire,$dbtbpre;
	$styleid=intval($add[styleid]);
	if(empty($add[stylename])||empty($add[stylepath])||!$styleid)
	{
		printerror('EmptySpaceStyle','history.go(-1)');
	}
	$add[stylepath]=RepPathStr($add[stylepath]);
	$add['stylepath']=RepPostStr($add['stylepath'],1);
	//�ؿ��O�_�s�b
	if(!file_exists("../../space/template/".$add[stylepath]))
	{
		printerror("EmptySpaceStylePath","history.go(-1)");
	}
	$mg=ReturnSpaceStyleMemberGroup($add['membergroup']);
	$sql=$empire->query("update {$dbtbpre}enewsspacestyle set stylename='$add[stylename]',stylepic='$add[stylepic]',stylesay='$add[stylesay]',stylepath='$add[stylepath]',membergroup='$mg' where styleid='$styleid'");
	if($sql)
	{
		insert_dolog("styleid=$styleid&stylename=$add[stylename]");//�ާ@��x
		printerror("EditSpaceStyleSuccess","ListSpaceStyle.php".hReturnEcmsHashStrHref2(1));
	}
	else
	{
		printerror("DbError","history.go(-1)");
	}
}

//�R���|���Ŷ��ҪO
function DelSpaceStyle($add,$userid,$username){
	global $empire,$dbtbpre;
	$styleid=intval($add[styleid]);
	if(!$styleid)
	{
		printerror('EmptySpaceStyleid','history.go(-1)');
	}
	$r=$empire->fetch1("select stylename,isdefault from {$dbtbpre}enewsspacestyle where styleid='$styleid'");
	if($r[isdefault])
	{
		printerror('NotDelDefSpaceStyle','history.go(-1)');
	}
	$sql=$empire->query("delete from {$dbtbpre}enewsspacestyle where styleid='$styleid'");
	if($sql)
	{
		insert_dolog("styleid=$styleid&stylename=$r[stylename]");//�ާ@��x
		printerror("DelSpaceStyleSuccess","ListSpaceStyle.php".hReturnEcmsHashStrHref2(1));
	}
	else
	{
		printerror("DbError","history.go(-1)");
	}
}

//�q�{�|���Ŷ��ҪO
function DefSpaceStyle($add,$userid,$username){
	global $empire,$dbtbpre;
	$styleid=intval($add[styleid]);
	if(!$styleid)
	{
		printerror('EmptyDefSpaceStyleid','history.go(-1)');
	}
	$r=$empire->fetch1("select stylename from {$dbtbpre}enewsspacestyle where styleid='$styleid'");
	$usql=$empire->query("update {$dbtbpre}enewsspacestyle set isdefault=0");
	$sql=$empire->query("update {$dbtbpre}enewsspacestyle set isdefault=1 where styleid='$styleid'");
	$upsql=$empire->query("update {$dbtbpre}enewspublic set defspacestyleid='$styleid'");
	if($sql)
	{
		GetConfig();
		insert_dolog("styleid=$styleid&stylename=$r[stylename]");//�ާ@��x
		printerror("DefSpaceStyleSuccess","ListSpaceStyle.php".hReturnEcmsHashStrHref2(1));
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
if($enews=="AddSpaceStyle")
{
	AddSpaceStyle($_POST,$logininid,$loginin);
}
elseif($enews=="EditSpaceStyle")
{
	EditSpaceStyle($_POST,$logininid,$loginin);
}
elseif($enews=="DelSpaceStyle")
{
	DelSpaceStyle($_GET,$logininid,$loginin);
}
elseif($enews=="DefSpaceStyle")
{
	DefSpaceStyle($_GET,$logininid,$loginin);
}

$search=$ecms_hashur['ehref'];
$page=(int)$_GET['page'];
$page=RepPIntvar($page);
$start=0;
$line=16;//�C����ܱ���
$page_line=25;//�C������챵��
$offset=$page*$line;//�`�����q
$query="select * from {$dbtbpre}enewsspacestyle";
$totalquery="select count(*) as total from {$dbtbpre}enewsspacestyle";
$num=$empire->gettotal($totalquery);//���o�`����
$query=$query." order by styleid desc limit $offset,$line";
$sql=$empire->query($query);
$returnpage=page2($num,$line,$page_line,$start,$page,$search);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">
<link href="../adminstyle/<?=$loginadminstyleid?>/adminstyle.css" rel="stylesheet" type="text/css">
<title>�|���Ŷ��ҪO</title>
</head>

<body>
<table width="100%" border="0" align="center" cellpadding="3" cellspacing="1">
  <tr> 
    <td width="50%" height="25">��m�G<a href="ListSpaceStyle.php<?=$ecms_hashur['whehref']?>">�޲z�|���Ŷ��ҪO</a></td>
    <td><div align="right" class="emenubutton">
        <input type="button" name="Submit5" value="�W�[�|���Ŷ��ҪO" onclick="self.location.href='AddSpaceStyle.php?enews=AddSpaceStyle<?=$ecms_hashur['ehref']?>';">
      </div></td>
  </tr>
</table>

<br>
<table width="100%" border="0" align="center" cellpadding="3" cellspacing="1" class="tableborder">
  <tr class="header"> 
    <td width="10%" height="25"> <div align="center">ID</div></td>
    <td width="56%" height="25"> <div align="center">�ҪO�W��</div></td>
    <td width="34%" height="25"> <div align="center">�ާ@</div></td>
  </tr>
  <?php
  while($r=$empire->fetch($sql))
  {
  	$color="#ffffff";
	$movejs=' onmouseout="this.style.backgroundColor=\'#ffffff\'" onmouseover="this.style.backgroundColor=\'#C3EFFF\'"';
  	if($r[isdefault])
	{
		$color="#DBEAF5";
		$movejs='';
	}
  ?>
  <tr bgcolor="<?=$color?>"<?=$movejs?>> 
    <td height="25"> <div align="center"> 
        <?=$r[styleid]?>
      </div></td>
    <td height="25"> <div align="center"> 
        <?=$r[stylename]?>
      </div></td>
    <td height="25"> <div align="center">[<a href="ListSpaceStyle.php?enews=DefSpaceStyle&styleid=<?=$r[styleid]?><?=$ecms_hashur['href']?>">�]���q�{</a>] [<a href="AddSpaceStyle.php?enews=EditSpaceStyle&styleid=<?=$r[styleid]?><?=$ecms_hashur['ehref']?>">�ק�</a>]&nbsp;[<a href="ListSpaceStyle.php?enews=DelSpaceStyle&styleid=<?=$r[styleid]?><?=$ecms_hashur['href']?>" onclick="return confirm('�T�{�n�R���H');">�R��</a>]</div></td>
  </tr>
  <?php
  }
  ?>
  <tr bgcolor="#FFFFFF"> 
    <td height="25" colspan="3">&nbsp;&nbsp;&nbsp; 
      <?=$returnpage?>
    </td>
  </tr>
</table>
</body>
</html>
<?php
db_close();
$empire=null;
?>