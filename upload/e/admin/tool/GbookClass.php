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
CheckLevel($logininid,$loginin,$classid,"gbook");
$enews=$_POST['enews'];
if(empty($enews))
{$enews=$_GET['enews'];}
if($enews)
{
	hCheckEcmsRHash();
	include("../../class/com_functions.php");
}
if($enews=="AddGbookClass")
{
	AddGbookClass($_POST,0,$logininid,$loginin);
}
elseif($enews=="EditGbookClass")
{
	EditGbookClass($_POST,0,$logininid,$loginin);
}
elseif($enews=="DelGbookClass")
{
	$bid=$_GET['bid'];
	DelGbookClass($bid,0,$logininid,$loginin);
}
else
{}
$sql=$empire->query("select bid,bname,checked,groupid from {$dbtbpre}enewsgbookclass order by bid desc");
//----------�|����
$sql1=$empire->query("select groupid,groupname from {$dbtbpre}enewsmembergroup order by level");
while($l_r=$empire->fetch($sql1))
{
	$membergroup.="<option value='".$l_r[groupid]."'>".$l_r[groupname]."</option>";
}
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
    <td>��m�G<a href="gbook.php<?=$ecms_hashur['whehref']?>">�޲z�d��</a>&nbsp;&nbsp;&gt;&nbsp;&nbsp;<a href="GbookClass.php<?=$ecms_hashur['whehref']?>">�޲z�d������</a></td>
  </tr>
</table>
<form name="form1" method="post" action="GbookClass.php">
  <table width="100%" border="0" align="center" cellpadding="3" cellspacing="1" class="tableborder">
  <?=$ecms_hashur['form']?>
    <tr class="header">
      <td height="25">�W�[�d������: 
        <input name=enews type=hidden id="enews" value=AddGbookClass>
        </td>
    </tr>
    <tr> 
      <td height="25" bgcolor="#FFFFFF"> �����W��: 
        <input name="bname" type="text" id="bname">
        <select name="groupid" id="groupid">
          <option value="0">�C��</option>
          <?=$membergroup?>
        </select>
        <input name="checked" type="checkbox" id="checked" value="1">
        �ݭn�f�� 
        <input type="submit" name="Submit" value="�W�[">
        <input type="reset" name="Submit2" value="���m"></td>
    </tr>
  </table>
</form>
<table width="100%" border="0" align="center" cellpadding="3" cellspacing="1" class="tableborder">
  <tr class="header"> 
    <td width="7%"><div align="center">ID</div></td>
    <td width="39%" height="25"><div align="center">�����W��</div></td>
    <td width="39%"><div align="center">�d���O�a�}</div></td>
    <td width="15%" height="25"><div align="center">�ާ@</div></td>
  </tr>
  <?php
  while($r=$empire->fetch($sql))
  {
   $gourl=$public_r[newsurl]."e/tool/gbook/?bid=".$r[bid];
   $checked="";
   if($r[checked])
   {
   	$checked=" checked";
   }
   $thismembergroup=str_replace("<option value='".$r[groupid]."'>","<option value='".$r[groupid]."' selected>",$membergroup);
  ?>
  <form name=form2 method=post action=GbookClass.php>
	  <?=$ecms_hashur['form']?>
    <input type=hidden name=enews value=EditGbookClass>
    <input type=hidden name=bid value=<?=$r[bid]?>>
    <tr bgcolor="#FFFFFF" onmouseout="this.style.backgroundColor='#ffffff'" onmouseover="this.style.backgroundColor='#C3EFFF'"> 
      <td><div align="center">
          <?=$r[bid]?>
        </div></td>
      <td height="25"> <div align="center"> 
          <input name="bname" type="text" id="bname" value="<?=$r[bname]?>">
          <select name="groupid" id="groupid">
            <option value="0">�C��</option>
            <?=$thismembergroup?>
          </select>
          <input name="checked" type="checkbox" id="checked" value="1"<?=$checked?>>
          �f��</div></td>
      <td><div align="center">
          <input name="textfield" type="text" size="32" value="<?=$gourl?>">
          [<a href="<?=$gourl?>" target="_blank">�X��</a>]</div></td>
      <td height="25"><div align="center"> 
          <input type="submit" name="Submit3" value="�ק�">
          &nbsp; 
          <input type="button" name="Submit4" value="�R��" onclick="self.location.href='GbookClass.php?enews=DelGbookClass&bid=<?=$r[bid]?><?=$ecms_hashur['href']?>';">
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