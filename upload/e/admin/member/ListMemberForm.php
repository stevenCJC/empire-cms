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
CheckLevel($logininid,$loginin,$classid,"memberf");
$search=$ecms_hashur['ehref'];
$page=(int)$_GET['page'];
$page=RepPIntvar($page);
$start=0;
$line=30;//�C����ܱ���
$page_line=23;//�C������챵��
$offset=$page*$line;//�`�����q
$query="select fid,fname from {$dbtbpre}enewsmemberform";
$totalquery="select count(*) as total from {$dbtbpre}enewsmemberform";
$num=$empire->gettotal($totalquery);//���o�`����
$query=$query." order by fid desc limit $offset,$line";
$sql=$empire->query($query);
$returnpage=page2($num,$line,$page_line,$start,$page,$search);
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
    <td width="50%">��m�G<a href="ListMemberForm.php<?=$ecms_hashur['whehref']?>">�޲z�|������</a></td>
    <td><div align="right" class="emenubutton">
        <input type="button" name="Submit5" value="�W�[�|������" onclick="self.location.href='AddMemberForm.php?enews=AddMemberForm<?=$ecms_hashur['ehref']?>';">
		&nbsp;&nbsp;
		<input type="button" name="Submit5" value="�޲z�|���r�q" onclick="self.location.href='ListMemberF.php<?=$ecms_hashur['whehref']?>';">
      </div></td>
  </tr>
</table>
<br>
<table width="100%" border="0" align="center" cellpadding="3" cellspacing="1" class="tableborder">
  <tr class="header"> 
    <td width="8%"><div align="center">ID</div></td>
    <td width="64%" height="25"><div align="center">����W��</div></td>
    <td width="28%" height="25"><div align="center">�ާ@</div></td>
  </tr>
  <?php
  while($r=$empire->fetch($sql))
  {
  ?>
  <tr bgcolor="#FFFFFF" onmouseout="this.style.backgroundColor='#ffffff'" onmouseover="this.style.backgroundColor='#C3EFFF'"> 
    <td><div align="center"> 
        <?=$r[fid]?>
      </div></td>
    <td height="25"> <div align="center"> 
        <?=$r[fname]?>
      </div></td>
    <td height="25"><div align="center">[<a href="AddMemberForm.php?enews=EditMemberForm&fid=<?=$r[fid]?><?=$ecms_hashur['ehref']?>">�ק�</a>] 
        [<a href="AddMemberForm.php?enews=AddMemberForm&fid=<?=$r[fid]?>&docopy=1<?=$ecms_hashur['ehref']?>">�ƻs</a>] 
        [<a href="../ecmsmember.php?enews=DelMemberForm&fid=<?=$r[fid]?><?=$ecms_hashur['href']?>" onclick="return confirm('�T�{�n�R��?');">�R��</a>] 
      </div></td>
  </tr>
  <?php
  }
  db_close();
  $empire=null;
  ?>
  <tr bgcolor="#FFFFFF"> 
    <td height="25" colspan="3"> 
      <?=$returnpage?>
    </td>
  </tr>
</table>
</body>
</html>